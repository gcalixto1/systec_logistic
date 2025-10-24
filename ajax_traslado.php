<?php
include("conexionfin.php");
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);

try {
    // Recibir y validar
    $producto_id       = isset($_POST['producto_id']) ? intval($_POST['producto_id']) : null;
    $almacen_origen    = isset($_POST['almacen_origen']) ? intval($_POST['almacen_origen']) : null;
    $almacen_destino   = isset($_POST['almacen_destino']) ? intval($_POST['almacen_destino']) : null;
    $lote              = trim($_POST['lote'] ?? '');
    $cantidad_unidades = floatval($_POST['cantidad_unidades'] ?? 0);
    $cantidad_cajas    = floatval($_POST['cantidad_cajas'] ?? 0);
    $usuario           = $_SESSION['login_idusuario'] ?? null;
    $cliente_proveedor = $_POST['cliente_proveedor'] ?? ''; // opcional

    if (!$producto_id || !$almacen_origen || !$almacen_destino || $lote === '' || $cantidad_unidades <= 0) {
        throw new Exception("Datos incompletos o inválidos.");
    }
    if ($almacen_origen == $almacen_destino) {
        throw new Exception("El almacén destino debe ser diferente al de origen.");
    }

    // Obtener datos del producto (más completos)
    $qProd = $conexion->prepare("
        SELECT str_id, descripcion, calibre, umb, ref_1, ref_2, und_embalaje_minima, peso_kg_paca_caja, relacion
        FROM producto
        WHERE id_producto = ? LIMIT 1
    ");
    $qProd->bind_param("i", $producto_id);
    $qProd->execute();
    $resProd = $qProd->get_result();
    if ($resProd->num_rows == 0) {
        throw new Exception("Producto no encontrado.");
    }
    $prod = $resProd->fetch_assoc();
    $und_embalaje = floatval($prod['und_embalaje_minima'] ?? 1);
    // si relacion = KG usar peso_kg_paca_caja como factor
    if (isset($prod['relacion']) && $prod['relacion'] === 'KG' && !empty($prod['peso_kg_paca_caja'])) {
        $emb_factor = floatval($prod['peso_kg_paca_caja']);
    } else {
        $emb_factor = $und_embalaje > 0 ? $und_embalaje : 1;
    }
    $qProd->close();

    // Verificar stock disponible en origen (por producto, almacen y lote)
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
    $sqlCheck->close();

    // verificar disponibilidad en unidades (ya están en unidades en stock_unidades_kg)
    if (floatval($invOrigen['stock_unidades_kg']) < $cantidad_unidades) {
        throw new Exception("Stock insuficiente. Disponible: {$invOrigen['stock_unidades_kg']} unidades/kg.");
    }
    $costo_unitario = floatval($invOrigen['costo_unitario'] ?? 0.0);
    $costo_total = $costo_unitario * $cantidad_unidades;

    // Iniciar transacción
    $conexion->begin_transaction();

    // Restar en inventario origen
    $sqlRestar = $conexion->prepare("
        UPDATE inventario 
        SET 
            stock_unidades_kg = stock_unidades_kg - ?, 
            stock_caja_paca_bobinas = stock_caja_paca_bobinas - ?,
            fecha_actualizacion = NOW()
        WHERE producto_id=? AND almacen_id=? AND lote=?
    ");
    $sqlRestar->bind_param("ddiis", $cantidad_unidades, $cantidad_cajas, $producto_id, $almacen_origen, $lote);
    if (!$sqlRestar->execute()) throw new Exception("Error al actualizar inventario origen: " . $sqlRestar->error);
    $sqlRestar->close();

    // Sumar o insertar en inventario destino
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
        if (!$sqlSumar->execute()) throw new Exception("Error al actualizar inventario destino: " . $sqlSumar->error);
        $sqlSumar->close();
    } else {
        $sqlInsertDest = $conexion->prepare("
            INSERT INTO inventario (producto_id, almacen_id, lote, stock_unidades_kg, stock_caja_paca_bobinas, costo_unitario, fecha_actualizacion)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $sqlInsertDest->bind_param("iisddd", $producto_id, $almacen_destino, $lote, $cantidad_unidades, $cantidad_cajas, $costo_unitario);
        if (!$sqlInsertDest->execute()) throw new Exception("Error al insertar inventario destino: " . $sqlInsertDest->error);
        $sqlInsertDest->close();
    }
    $sqlCheckDest->close();

    // Registrar movimiento SALIDA (traslado)
    $sqlMovOut = $conexion->prepare("
        INSERT INTO movimientos_inventario
        (id_producto, id_interno, descripcion, cantidad, unidades, tipo_movimiento, num_documento, 
         costo_unitario, costo_total, lote, almacen_id, cliente_proveedor, ref1, ref2, calibre, umb, usuario_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $tipoOut = 'Traslado Salida';
    $numDocOut = ''; // si usas documento
    $unidades_out = $cantidad_unidades; // campo unidades
    $sqlMovOut->bind_param(
        "issddsdddsisssssi",
        $producto_id,
        $prod['str_id'],
        $prod['descripcion'],
        $cantidad_cajas,     // cantidad (en cajas si esa es tu convención) - ajustar según tu definición
        $unidades_out,      // unidades (en unidades/kg)
        $tipoOut,
        $numDocOut,
        $costo_unitario,
        $costo_total,
        $lote,
        $almacen_origen,
        $cliente_proveedor,
        $prod['ref_1'],
        $prod['ref_2'],
        $prod['calibre'],
        $prod['umb'],
        $usuario
    );
    if (!$sqlMovOut->execute()) throw new Exception("Error al insertar movimiento salida: " . $sqlMovOut->error);
    $sqlMovOut->close();

    // Registrar movimiento ENTRADA (traslado)
    $sqlMovIn = $conexion->prepare("
        INSERT INTO movimientos_inventario
        (id_producto, id_interno, descripcion, cantidad, unidades, tipo_movimiento, num_documento, 
         costo_unitario, costo_total, lote, almacen_id, cliente_proveedor, ref1, ref2, calibre, umb, usuario_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $tipoIn = 'Traslado Entrada';
    $numDocIn = ''; // si aplicara
    $unidades_in = $cantidad_unidades;
    $sqlMovIn->bind_param(
        "issddsdddsisssssi",
        $producto_id,
        $prod['str_id'],
        $prod['descripcion'],
        $cantidad_cajas,
        $unidades_in,
        $tipoIn,
        $numDocIn,
        $costo_unitario,
        $costo_total,
        $lote,
        $almacen_destino,
        $cliente_proveedor,
        $prod['ref_1'],
        $prod['ref_2'],
        $prod['calibre'],
        $prod['umb'],
        $usuario
    );
    if (!$sqlMovIn->execute()) throw new Exception("Error al insertar movimiento entrada: " . $sqlMovIn->error);
    $sqlMovIn->close();

    // Commit
    $conexion->commit();

    echo json_encode([
        "success" => true,
        "message" => "✅ Traslado realizado correctamente."
    ]);
} catch (Exception $e) {
    if ($conexion->innodb_transaction_status ?? true) { /* ignore */ }
    $conexion->rollback();
    echo json_encode([
        "success" => false,
        "message" => "❌ Error: " . $e->getMessage()
    ]);
}
