<?php
include('conexionfin.php');
header('Content-Type: application/json');

// 🟢 Verificar que se haya enviado información
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['productos'])) {
    echo json_encode(['status' => 'error', 'message' => 'No se recibieron datos válidos']);
    exit;
}

$maquina_id = $data['maquina_id'] ?? null;
$usuario = $data['usuario'] ?? 'Sistema';
$observaciones = $data['observaciones'] ?? '';
$productos = $data['productos'] ?? [];

if (!$maquina_id) {
    echo json_encode(['status' => 'error', 'message' => 'Debe seleccionar una máquina']);
    exit;
}

$responses = [];
$conexion->begin_transaction();

try {
    foreach ($productos as $p) {
        $pedido_id = $conexion->real_escape_string($p['pedido_id']);
        $producto_id = $conexion->real_escape_string($p['producto_id']);
        $peso_utilizar = floatval($p['peso_utilizar'] ?? 0);
        $cantidad_programada = floatval($p['cantidad_programada'] ?? 0);

        // 🔹 Generar número de orden único
        $numero_op = "OP-" . date('YmdHis') . "-" . substr(uniqid(), -4);

        // 🔹 Insertar en tabla orden_produccion
        $sql_insert_op = "
            INSERT INTO orden_produccion 
                (numero_op, pedido_id, producto_id, maquina_id, cantidad_programada, peso_total_kg, usuario_programo, observaciones, estado)
            VALUES 
                ('$numero_op', '$pedido_id', '$producto_id', '$maquina_id', '$cantidad_programada', '$peso_utilizar', '$usuario', '$observaciones', 'PROGRAMADA')
        ";

        if (!$conexion->query($sql_insert_op)) {
            throw new Exception("Error al guardar la O.P: " . $conexion->error);
        }

        // 🔹 Actualizar estatus del pedido
        $sql_update_pedido = "UPDATE pedidos SET estatus = 'PROGRAMADO' WHERE id = '$pedido_id'";
        if (!$conexion->query($sql_update_pedido)) {
            throw new Exception("Error al actualizar el estatus del pedido: " . $conexion->error);
        }

        // 🔹 Obtener la cantidad original del pedido
        $sql_cantidad = "SELECT cantidad FROM detalle_pedidos WHERE pedido_id = '$pedido_id' AND producto_id = '$producto_id' LIMIT 1";
        $res_cantidad = $conexion->query($sql_cantidad);
        $row = $res_cantidad->fetch_assoc();
        $cantidad_pedido = floatval($row['cantidad'] ?? 0);

        // 🔹 Actualizar reserva en detalle_pedidos
        $sql_update_detalle = "
            UPDATE detalle_pedidos 
            SET reservas = '$cantidad_pedido'
            WHERE pedido_id = '$pedido_id' AND producto_id = '$producto_id'
        ";
        if (!$conexion->query($sql_update_detalle)) {
            throw new Exception("Error al actualizar la reserva en detalle_pedidos: " . $conexion->error);
        }

        $responses[] = [
            'numero_op' => $numero_op,
            'producto_id' => $producto_id,
            'pedido_id' => $pedido_id
        ];
    }

    // 🔹 Confirmar transacción
    $conexion->commit();
    echo json_encode([
        'status' => 'success',
        'message' => 'Órdenes de producción programadas correctamente',
        'ordenes' => $responses
    ]);
} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
