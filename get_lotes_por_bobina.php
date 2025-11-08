<?php
include('conexionfin.php');

$bobina_id = $_POST['bobina_id'] ?? '';

if (!$bobina_id) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT 
            l.lote
        FROM inventario l
        WHERE l.producto_id = '$bobina_id'";

$result = $conexion->query($sql);

$lotes = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $lotes[] = $row;
    }
}

echo json_encode($lotes);
?>
