<?php
// Conexión a tu base de datos
include 'conexionfin.php'; // ajusta según tu estructura

// Preparar y ejecutar la consulta
$term = $_GET['term'] ?? '';

$sql = "SELECT codigo, descripcion 
        FROM actividad_economica 
        WHERE descripcion LIKE ? OR codigo LIKE ? 
        LIMIT 20";

$stmt = $conexion->prepare($sql);
$search = "%$term%";
$stmt->bind_param("ss", $search, $search);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'id' => $row['codigo'],
        'text' => "{$row['codigo']} - {$row['descripcion']}"
    ];
}

header('Content-Type: application/json');
echo json_encode(['results' => $data]);
?>