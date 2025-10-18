<?php
include("conexionfin.php");

$almacen = isset($_GET['almacen']) && $_GET['almacen'] !== '' ? intval($_GET['almacen']) : null;

// Construir condición por almacén
$condAlmacen = $almacen ? "AND m.almacen_id = $almacen" : "";

$sql = "
SELECT 
    p.str_id,
    p.cod_producto,
    p.descripcion,
    p.familia,
    p.micraje,
    p.relacion,
    p.calibre,
    p.und_embalaje_minima,
    COALESCE(SUM(CASE WHEN m.tipo_movimiento LIKE 'Entrada%' THEN m.cantidad ELSE 0 END) - 
             SUM(CASE WHEN m.tipo_movimiento LIKE 'Salida%' THEN m.cantidad ELSE 0 END),0) AS stock_unidades_kg,
    COALESCE(FLOOR(SUM(CASE WHEN m.tipo_movimiento LIKE 'Entrada%' THEN m.cantidad ELSE 0 END) / p.und_embalaje_minima),0) AS stock_caja,
    m.lote,
    COALESCE(AVG(m.costo_unitario),0) AS costo_unitario,
    COALESCE(SUM(CASE WHEN m.tipo_movimiento LIKE 'Entrada%' THEN m.costo_total ELSE 0 END) - 
             SUM(CASE WHEN m.tipo_movimiento LIKE 'Salida%' THEN m.costo_total ELSE 0 END),0) AS costo_total,
    a.nombre AS nombre_almacen
FROM producto p
LEFT JOIN movimientos_inventario m ON m.id_producto = p.id_producto $condAlmacen
LEFT JOIN almacenes a ON a.id = m.almacen_id
GROUP BY p.id_producto, m.lote, a.id
ORDER BY p.descripcion, m.lote
";

$res = mysqli_query($conexion, $sql);

$data = [];
while($row = mysqli_fetch_assoc($res)){
    $data[] = $row;
}

echo json_encode(['data'=>$data]);
