<?php
include("conexionfin.php");

if (!isset($_POST['productos'])) {
  echo "No se recibieron productos.";
  exit;
}

$productos = json_decode($_POST['productos'], true);
$observacion = $conexion->real_escape_string($_POST['observacion'] ?? 'Salida manual');

// Determinar tipo de movimiento
$tipo_movimiento = stripos($observacion, 'entrada') !== false || stripos($observacion, 'ingreso') !== false
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
  $verificar = $conexion->query("SELECT COUNT(*) AS existe FROM producto WHERE id_producto = '$id_producto'");
  $existe = $verificar->fetch_assoc()['existe'];

  if ($existe == 0) {
    echo "⚠️ El producto con ID $id_producto no existe en la tabla producto.<br>";
    continue;
  }

  // === 1️⃣ Insertar movimiento en historial ===
  $sql = "
    INSERT INTO movimientos_inventario 
      (id_producto, id_interno, descripcion, cantidad, lote, almacen_id, tipo_movimiento, cliente_proveedor, num_documento,
       costo_unitario, costo_total, fecha_movimiento, calibre, umb, ref1, ref2, unidades)
    SELECT 
      p.id_producto, 
      p.str_id, 
      p.descripcion, 
      $cantidad, 
      '$lote', 
      $almacen_id, 
      '$tipo_movimiento', 
      '', 
      '', 
      $costo_unitario, 
      $costo_total, 
      NOW(), 
      p.calibre, 
      p.umb, 
      p.ref_1, 
      p.ref_2,
      COALESCE(p.und_embalaje_minima,1) * $cantidad
    FROM producto p 
    WHERE p.id_producto = '$id_producto'
  ";

  if (!$conexion->query($sql)) {
    echo "❌ Error insertando movimiento del producto $id_producto: " . $conexion->error . "<br>";
    continue;
  }

  // === 2️⃣ Actualizar inventario general ===
  $signo = ($tipo_movimiento == 'Salida manual') ? '-' : '+';

  // Si ya existe un registro del mismo producto / almacén / lote → actualiza
  $sqlCheck = "
    SELECT id_inventario 
    FROM inventario 
    WHERE producto_id = '$id_producto' AND almacen_id = '$almacen_id' AND lote = '$lote'
    LIMIT 1
  ";
  $resCheck = $conexion->query($sqlCheck);

  if ($resCheck && $resCheck->num_rows > 0) {
    // Ya existe → actualiza stock
    $sqlUpdate = "
      UPDATE inventario
      SET 
        stock_caja_paca_bobinas = stock_caja_paca_bobinas $signo $cantidad,
        stock_unidades_kg = stock_unidades_kg $signo (COALESCE((SELECT und_embalaje_minima FROM producto WHERE id_producto = '$id_producto'),1) * $cantidad),
        costo_unitario = $costo_unitario,
        fecha_actualizacion = NOW()
      WHERE producto_id = '$id_producto' AND almacen_id = '$almacen_id' AND lote = '$lote'
    ";
    if (!$conexion->query($sqlUpdate)) {
      echo "❌ Error actualizando inventario ($id_producto - lote $lote): " . $conexion->error . "<br>";
    } else {
      echo "✅ Inventario actualizado para producto $id_producto (lote $lote)<br>";
    }
  } else {
    // No existe → solo si es una entrada, inserta nuevo registro
    if ($tipo_movimiento == 'Entrada manual') {
      $sqlInsertInv = "
        INSERT INTO inventario (producto_id, almacen_id, lote, stock_unidades_kg, stock_caja_paca_bobinas, costo_unitario)
        VALUES (
          '$id_producto',
          '$almacen_id',
          '$lote',
          COALESCE((SELECT und_embalaje_minima FROM producto WHERE id_producto = '$id_producto'),1) * $cantidad,
          $cantidad,
          $costo_unitario
        )
      ";
      if (!$conexion->query($sqlInsertInv)) {
        echo "❌ Error insertando nuevo inventario ($id_producto - lote $lote): " . $conexion->error . "<br>";
      } else {
        echo "✅ Inventario creado para producto $id_producto (lote $lote)<br>";
      }
    } else {
      echo "⚠️ No existe inventario previo para restar (producto $id_producto - lote $lote).<br>";
    }
  }
}

echo "✅ Proceso completado correctamente.";
?>
