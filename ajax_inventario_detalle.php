<?php
include("conexionfin.php");

$almacen = $_GET['almacen'] ?? '';

$sql = "
SELECT 
    p.str_id,
    p.cod_producto,
    p.descripcion,
    IFNULL(m.lote,'') AS lote,
    SUM(CASE WHEN m.tipo_movimiento LIKE 'Entrada%' THEN m.cantidad ELSE 0 END) -
    SUM(CASE WHEN m.tipo_movimiento LIKE 'Salida%' THEN m.cantidad ELSE 0 END) AS stock_unidades_kg,
    FLOOR(SUM(CASE WHEN m.tipo_movimiento LIKE 'Entrada%' THEN m.cantidad ELSE 0 END) / p.und_embalaje_minima) AS stock_caja_paca_bobinas,
    AVG(m.costo_unitario) AS costo_unitario,
    SUM(CASE WHEN m.tipo_movimiento LIKE 'Entrada%' THEN m.costo_total ELSE 0 END) - SUM(CASE WHEN m.tipo_movimiento LIKE 'Salida%' THEN m.costo_total ELSE 0 END) AS costo_total,
    a.nombre AS nombre_almacen
FROM producto p
LEFT JOIN movimientos_inventario m ON m.id_producto = p.id_producto
LEFT JOIN almacenes a ON a.id = m.almacen_id
";

// Filtro por almacén
if(!empty($almacen)){
    $sql .= " WHERE m.almacen_id = '".intval($almacen)."' ";
}

$sql .= "
GROUP BY p.id_producto, m.lote, m.almacen_id
ORDER BY p.descripcion, m.lote
";

$res = mysqli_query($conexion, $sql);

$data = [];
while($row = mysqli_fetch_assoc($res)){
    $data[] = $row;
}

echo json_encode(['data'=>$data]);
