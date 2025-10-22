<?php
include("conexionfin.php");
header('Content-Type: application/json; charset=utf-8');

$producto_id = intval($_GET['producto_id'] ?? 0);
$almacen_id  = intval($_GET['almacen_id'] ?? 0);

if (!$producto_id || !$almacen_id) {
    echo json_encode([]);
    exit;
}

$sql = "
SELECT 
    lote,
    stock_unidades_kg AS stock
FROM inventario
WHERE producto_id = $producto_id AND almacen_id = $almacen_id AND stock_unidades_kg > 0
ORDER BY lote ASC
";

$res = $conexion->query($sql);
$lotes = [];

if ($res && $res->num_rows > 0) {
    while ($r = $res->fetch_assoc()) {
        $lotes[] = [
            'lote' => $r['lote'],
            'stock' => (float)$r['stock']
        ];
    }
}

echo json_encode($lotes);
?>
