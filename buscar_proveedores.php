<?php
// Conexión a tu base de datos
include 'conexionfin.php'; // asegúrate de que define $conexion como un objeto mysqli válido

$term = $_GET['term'] ?? '';
$search = "%$term%";

// Verifica que la conexión esté bien
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

$sql = "SELECT idproveedor, proveedor FROM proveedor WHERE proveedor LIKE ?";

// Preparar la consulta
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("Error al preparar la consulta: " . $conexion->error);
}

// Ejecutar y procesar resultados
$stmt->bind_param("s", $search);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'id' => $row['idproveedor'],
        'text' => "{$row['proveedor']}"
    ];
}

// Respuesta JSON
header('Content-Type: application/json');
echo json_encode(['results' => $data]);

// Limpieza
$stmt->close();
$conexion->close();
?>