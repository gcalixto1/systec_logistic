<?php
include('conexionfin.php');
header('Content-Type: application/json; charset=utf-8');

if (!isset($_POST['pedido_id'])) {
    echo json_encode(["error" => "Falta pedido_id"]);
    exit;
}

$pedido_id = intval($_POST['pedido_id']);
$cod_producto = isset($_POST['cod_producto']) ? $conexion->real_escape_string($_POST['cod_producto']) : '';

$sql = "
SELECT p.id AS id_pedido,
 p.numero_pedido,
 c.nombre_cliente,
 c.ciudad,
 pr.id_producto,
 pr.cod_producto,
 pr.descripcion,
 pr.calibre,
 pr.ref_1,
 pr.ref_2,
 pr.relacion,
 pr.peso_kg,
 pr.etiqueta,
 dp.cantidad,
 peso_kg * 1000 as peso_gr,
 CASE WHEN relacion = 'KG' THEN peso_kg_paca_caja ELSE und_embalaje_minima END AS und_embalaje_minima,
 dp.peso_unitario,
 pr.ref_tubo
FROM pedidos p
INNER JOIN detalle_pedidos dp ON dp.pedido_id = p.id
INNER JOIN producto pr ON pr.id_producto = dp.producto_id
INNER JOIN clientes c ON c.id = p.cliente_id
WHERE p.id = $pedido_id 
  AND pr.cod_producto = '$cod_producto'
  AND (pr.tipo = 'Stretch' OR pr.familia LIKE '%Stretch%')
";

$res = $conexion->query($sql);

if (!$res) {
    echo json_encode(["error" => $conexion->error,"sql" => $sql]);
    exit;
}

$datos = [];
while ($row = $res->fetch_assoc()) {
    $datos[] = $row;
}

echo json_encode($datos,
 JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
