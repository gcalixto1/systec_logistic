<?php
include('conexionfin.php');

$sql = "SELECT 
            i.id_inventario,
            i.stock_unidades_kg,
            p.id_producto,
            p.cod_producto,
            p.descripcion,
            p.tipo,
            p.ref_tubo,
            p.peso_kg
        FROM inventario i
        INNER JOIN producto p ON i.producto_id = p.id_producto
        WHERE p.tipo LIKE '%bobina%'";

$result = $conexion->query($sql);

$bobinas = [];
while ($row = $result->fetch_assoc()) {
    $bobinas[] = $row;
}

echo json_encode($bobinas);
?>
