<?php
include('conexionfin.php');

$filtro_maquina = $_POST['maquina'] ?? '';
$filtro_desde = $_POST['desde'] ?? '';
$filtro_hasta = $_POST['hasta'] ?? '';
$filtro_estado = $_POST['estado'] ?? '';

$condiciones = [];

if ($filtro_maquina != '') $condiciones[] = "op.maquina_id = '$filtro_maquina'";
if ($filtro_estado != '') $condiciones[] = "op.estado = '$filtro_estado'";
if ($filtro_desde != '' && $filtro_hasta != '') $condiciones[] = "DATE(op.fecha_creacion) BETWEEN '$filtro_desde' AND '$filtro_hasta'";
if ($filtro_desde != '' && $filtro_hasta == '') $condiciones[] = "DATE(op.fecha_creacion) >= '$filtro_desde'";
if ($filtro_hasta != '' && $filtro_desde == '') $condiciones[] = "DATE(op.fecha_creacion) <= '$filtro_hasta'";

$where = count($condiciones) > 0 ? 'WHERE ' . implode(' AND ', $condiciones) : '';

$sql = "SELECT 
            op.numero_op,
            op.pedido_id,
            p.descripcion AS producto,
            lm.maquina,
            op.cantidad_programada,
            op.peso_total_kg,
            op.estado,
            op.usuario_programo,
            DATE_FORMAT(op.fecha_programacion, '%Y-%m-%d %H:%i') AS fecha_creacion
        FROM orden_produccion op
        INNER JOIN producto p ON op.producto_id = p.id_producto
        INNER JOIN lista_maquina lm ON op.maquina_id = lm.id
        $where
        ORDER BY op.fecha_programacion DESC";

$result = $conexion->query($sql);

$ordenes = [];
while ($row = $result->fetch_assoc()) {
    $ordenes[] = $row;
}

echo json_encode($ordenes);
?>
