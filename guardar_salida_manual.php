<?php
include("conexionfin.php");
session_start();

// Validar que se reciban los datos esperados
if (!isset($_POST['productos'])) {
  echo "No se recibieron productos.";
  exit;
}

$productos   = json_decode($_POST['productos'], true);
$observacion = $conexion->real_escape_string($_POST['observacion'] ?? 'Salida manual');
$usuario_id  = $_SESSION['login_idusuario'] ?? null;

// Determinar tipo de movimiento
$tipo_movimiento = (stripos($observacion, 'entrada') !== false || stripos($observacion, 'ingreso') !== false)
  ? 'Entrada manual'
  : 'Salida manual';

foreach ($productos as $p) {
  $id_producto    = $conexion->real_escape_string($p['id_producto'] ?? '');
  $cantidad       = floatval($p['cajas'] ?? 0);
  $almacen_id     = intval($p['almacen_id'] ?? 0);
  $lote           = $conexion->real_escape_string($p['lote'] ?? '');
  $costo_unitario = floatval($p['costo_unitario'] ?? 0);
  $costo_total    = floatval($p['costo_total'] ?? 0);

  if (empty($cantidad) || empty($almacen_id) || empty($lote)) {
    echo "⚠️ Datos incompletos para el producto $id_producto.<br>";
    continue;
  }

  // Verificar si el producto existe
  $verificar = $conexion->prepare("SELECT COUNT(*) AS existe FROM producto WHERE id_producto = ?");
  $verificar->bind_param("s", $id_producto);
  $verificar->execute();
  $res = $verificar->get_result();
  $existe = $res->fetch_assoc()['existe'] ?? 0;
  $verificar->close();

  if ($existe == 0) {
    echo "⚠️ El producto con ID $id_producto no existe en la tabla producto.<br>";
    continue;
  }

  // === 1️⃣ Insertar movimiento en historial ===
  $sql = "
    INSERT INTO movimientos_inventario (
      id_producto, id_interno, descripcion, cantidad, lote, almacen_id, tipo_movimiento,
      cliente_proveedor, num_documento, costo_unitario, costo_total, fecha_movimiento,
      calibre, umb, ref1, ref2, unidades, usuario_id
    )
    SELECT 
      p.id_producto,
      p.str_id,
      p.descripcion,
      ?,
      ?,
      ?,
      ?,
      '',
      '',
      ?,
      ?,
      NOW(),
      p.calibre,
      p.umb,
      p.ref_1,
      p.ref_2,
      COALESCE(
        CASE 
          WHEN p.relacion = 'KG' THEN p.peso_kg_paca_caja
          ELSE p.und_embalaje_minima
        END,
      1) * ?,
      ?
    FROM producto p
    WHERE p.id_producto = ?
  ";


  $stmt = $conexion->prepare($sql);
  $stmt->bind_param("dsdsdddis",
    $cantidad,
    $lote,
    $almacen_id,
    $tipo_movimiento,
    $costo_unitario,
    $costo_total,
    $cantidad,
    $usuario_id,
    $id_producto
  );

  if (!$stmt->execute()) {
    echo "❌ Error insertando movimiento del producto $id_producto: " . $stmt->error . "<br>";
    $stmt->close();
    continue;
  }
  $stmt->close();

  // === 2️⃣ Actualizar inventario general ===
  $signo = ($tipo_movimiento == 'Salida manual') ? '-' : '+';

  $sqlCheck = "
    SELECT id_inventario 
    FROM inventario 
    WHERE producto_id = ? AND almacen_id = ? AND lote = ?
    LIMIT 1
  ";
  $stmtCheck = $conexion->prepare($sqlCheck);
  $stmtCheck->bind_param("sis", $id_producto, $almacen_id, $lote);
  $stmtCheck->execute();
  $resCheck = $stmtCheck->get_result();
  $existeInventario = $resCheck->num_rows > 0;
  $stmtCheck->close();

  if ($existeInventario) {
    // Actualiza el inventario existente
    $sqlUpdate = "
      UPDATE inventario i
      JOIN producto p ON p.id_producto = i.producto_id
      SET 
        i.stock_caja_paca_bobinas = i.stock_caja_paca_bobinas $signo ?,
        i.stock_unidades_kg = i.stock_unidades_kg $signo (
          CASE 
            WHEN p.relacion = 'KG' THEN (p.peso_kg_paca_caja * ?)
            ELSE (p.und_embalaje_minima * ?)
          END
        ),
        i.costo_unitario = ?,
        i.fecha_actualizacion = NOW()
      WHERE i.producto_id = ? AND i.almacen_id = ? AND i.lote = ?
    ";
    $stmtUpd = $conexion->prepare($sqlUpdate);
    $stmtUpd->bind_param("dddsiss", $cantidad, $cantidad, $cantidad, $costo_unitario, $id_producto, $almacen_id, $lote);
    if (!$stmtUpd->execute()) {
      echo "❌ Error actualizando inventario ($id_producto - lote $lote): " . $stmtUpd->error . "<br>";
    } else {
      echo "✅ Inventario actualizado para producto $id_producto (lote $lote)<br>";
    }
    $stmtUpd->close();

  } else {
    // No existe → si es entrada, crear nuevo inventario
    if ($tipo_movimiento == 'Entrada manual') {
      $sqlInsertInv = "
        INSERT INTO inventario (producto_id, almacen_id, lote, stock_unidades_kg, stock_caja_paca_bobinas, costo_unitario)
        SELECT 
          p.id_producto,
          ?,
          ?,
          CASE 
            WHEN p.relacion = 'KG' THEN (p.peso_kg_paca_caja * ?)
            ELSE (p.und_embalaje_minima * ?)
          END,
          ?,
          ?
        FROM producto p
        WHERE p.id_producto = ?
      ";
      $stmtIns = $conexion->prepare($sqlInsertInv);
      $stmtIns->bind_param("ssdddss", $almacen_id, $lote, $cantidad, $cantidad, $cantidad, $costo_unitario, $id_producto);
      if (!$stmtIns->execute()) {
        echo "❌ Error insertando nuevo inventario ($id_producto - lote $lote): " . $stmtIns->error . "<br>";
      } else {
        echo "✅ Inventario creado para producto $id_producto (lote $lote)<br>";
      }
      $stmtIns->close();
    } else {
      echo "⚠️ No existe inventario previo para restar (producto $id_producto - lote $lote).<br>";
    }
  }
}

echo "✅ Proceso completado correctamente.";
?>
