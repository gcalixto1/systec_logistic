<?php
include("conexionfin.php");
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
ini_set('display_errors', 1);
try {
    // 🔹 Datos recibidos del formulario
    $producto_id      = $_POST['producto_id'] ?? null;
    $almacen_origen   = $_POST['almacen_origen'] ?? null;
    $almacen_destino  = $_POST['almacen_destino'] ?? null;
    $lote             = trim($_POST['lote'] ?? '');
    $cantidad_unidades = floatval($_POST['cantidad_unidades'] ?? 0);
    $cantidad_cajas    = floatval($_POST['cantidad_cajas'] ?? 0);
    $usuario = $_SESSION['login_idusuario'];
    if (!$producto_id || !$almacen_origen || !$almacen_destino || !$lote || $cantidad_unidades <= 0) {
        throw new Exception("Datos incompletos o inválidos.");
    }

    if ($almacen_origen == $almacen_destino) {
        throw new Exception("El almacén destino debe ser diferente al de origen.");
    }

    // 🔹 Obtener factor de embalaje
    $qProd = $conexion->prepare("SELECT und_embalaje_minima FROM producto WHERE id_producto = ? LIMIT 1");
    $qProd->bind_param("i", $producto_id);
    $qProd->execute();
    $resProd = $qProd->get_result();
    $producto = $resProd->fetch_assoc();
    $und_embalaje = floatval($producto['und_embalaje_minima'] ?? 1);
    $qProd->close();

    // 🔹 Verificar stock disponible en origen
    $sqlCheck = $conexion->prepare("
        SELECT stock_unidades_kg, stock_caja_paca_bobinas, costo_unitario 
        FROM inventario 
        WHERE producto_id=? AND almacen_id=? AND lote=? LIMIT 1
    ");
    $sqlCheck->bind_param("iis", $producto_id, $almacen_origen, $lote);
    $sqlCheck->execute();
    $res = $sqlCheck->get_result();

    if ($res->num_rows == 0) {
        throw new Exception("No existe stock del lote seleccionado en el almacén origen.");
    }

    $invOrigen = $res->fetch_assoc();
    if ($invOrigen['stock_unidades_kg'] < $cantidad_unidades) {
        throw new Exception("Stock insuficiente. Disponible: {$invOrigen['stock_unidades_kg']} unidades/kg.");
    }
    $costo_unitario = $invOrigen['costo_unitario'];
    $sqlCheck->close();

    $conexion->begin_transaction();

    // 🔹 Restar en almacén origen
    $sqlRestar = $conexion->prepare("
        UPDATE inventario 
        SET 
            stock_unidades_kg = stock_unidades_kg - ?, 
            stock_caja_paca_bobinas = stock_caja_paca_bobinas - ?,
            fecha_actualizacion = NOW()
        WHERE producto_id=? AND almacen_id=? AND lote=?
    ");
    $sqlRestar->bind_param("ddiis", $cantidad_unidades, $cantidad_cajas, $producto_id, $almacen_origen, $lote);
    $sqlRestar->execute();

    // 🔹 Sumar (o crear si no existe) en almacén destino
    $sqlCheckDest = $conexion->prepare("
        SELECT id_inventario FROM inventario WHERE producto_id=? AND almacen_id=? AND lote=? LIMIT 1
    ");
    $sqlCheckDest->bind_param("iis", $producto_id, $almacen_destino, $lote);
    $sqlCheckDest->execute();
    $resDest = $sqlCheckDest->get_result();

    if ($resDest->num_rows > 0) {
        $sqlSumar = $conexion->prepare("
            UPDATE inventario 
            SET 
                stock_unidades_kg = stock_unidades_kg + ?, 
                stock_caja_paca_bobinas = stock_caja_paca_bobinas + ?,
                fecha_actualizacion = NOW()
            WHERE producto_id=? AND almacen_id=? AND lote=?
        ");
        $sqlSumar->bind_param("ddiis", $cantidad_unidades, $cantidad_cajas, $producto_id, $almacen_destino, $lote);
        $sqlSumar->execute();
    } else {
        $sqlInsertDest = $conexion->prepare("
            INSERT INTO inventario (producto_id, almacen_id, lote, stock_unidades_kg, stock_caja_paca_bobinas, costo_unitario)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $sqlInsertDest->bind_param("iisddd", $producto_id, $almacen_destino, $lote, $cantidad_unidades, $cantidad_cajas, $costo_unitario);
        $sqlInsertDest->execute();
    }

    // 🔹 Registrar movimiento (Salida)
    $costo_total = $costo_unitario * $cantidad_unidades;
    $sqlMovOut = $conexion->prepare("
        INSERT INTO movimientos_inventario
        (id_producto, descripcion, cantidad, fecha_movimiento, tipo_movimiento, num_documento, 
         costo_unitario, costo_total, lote, almacen_id, ref1,usuario_id)
        VALUES (?, '', ?, NOW(), 'Traslado Salida', '', ?, ?, ?, ?, 'TRASLADO',?)
    ");
    $sqlMovOut->bind_param("idddsii", $producto_id, $cantidad_unidades, $costo_unitario, $costo_total, $lote, $almacen_origen,$usuario);
    $sqlMovOut->execute();

    // 🔹 Registrar movimiento (Entrada)
    $sqlMovIn = $conexion->prepare("
        INSERT INTO movimientos_inventario
        (id_producto, descripcion, cantidad, fecha_movimiento, tipo_movimiento, num_documento, 
         costo_unitario, costo_total, lote, almacen_id, ref1,usuario_id)
        VALUES (?, '', ?, NOW(), 'Traslado Entrada', '', ?, ?, ?, ?, 'TRASLADO',?)
    ");
    $sqlMovIn->bind_param("idddsii", $producto_id, $cantidad_unidades, $costo_unitario, $costo_total, $lote, $almacen_destino,$usuario);
    $sqlMovIn->execute();

    $conexion->commit();

    echo json_encode([
        "success" => true,
        "message" => "✅ Traslado realizado correctamente."
    ]);
} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode([
        "success" => false,
        "message" => "❌ Error: " . $e->getMessage()
    ]);
}
