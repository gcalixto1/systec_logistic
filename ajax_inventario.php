<?php
include("conexionfin.php");
header('Content-Type: application/json');

$almacen = $_GET['almacen'] ?? '';

$filtro = "";
if (!empty($almacen)) {
    $filtro = "AND i.almacen_id = '$almacen'";
}

$sql = "
SELECT 
    p.id_producto,
    p.str_id,
    p.cod_producto,
    p.descripcion,
    p.familia,
    p.micraje,
    p.relacion,
    p.calibre,
    -- Si la relación es KG se usa peso_kg_paca_caja, si no, und_embalaje_minima
    CASE 
        WHEN p.relacion = 'KG' THEN p.peso_kg_paca_caja
        ELSE p.und_embalaje_minima
    END AS und_embalaje_minima,
    
    i.stock_unidades_kg AS stock_unidades_kg,
    i.stock_caja_paca_bobinas AS stock_caja_paca_bobinas,
    i.lote,
    i.costo_unitario,
    (i.stock_unidades_kg * i.costo_unitario) AS costo_total,
    a.nombre AS nombre_almacen,
    a.id AS almacen_id

FROM inventario i
INNER JOIN producto p ON p.id_producto = i.producto_id
INNER JOIN almacenes a ON a.id = i.almacen_id
WHERE 1=1 and stock_caja_paca_bobinas <> '0.00' AND stock_unidades_kg<> '0.00' $filtro
ORDER BY p.descripcion ASC
";

$result = mysqli_query($conexion, $sql);
$data = [];

while ($r = mysqli_fetch_assoc($result)) {
    $data[] = $r;
}

echo json_encode(["data" => $data]);
?>
