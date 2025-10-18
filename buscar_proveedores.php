<?php
include("conexionfin.php");

header('Content-Type: application/json; charset=utf-8');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

$results = [];
if ($q !== '') {
    $q = $conexion->real_escape_string($q);

    $sql = "SELECT *
            FROM proveedores 
            WHERE nombre_proveedor LIKE '%$q%' OR documento LIKE '%$q%' 
            ORDER BY nombre_proveedor 
            LIMIT 10";
} else {
    $sql = "SELECT *
            FROM proveedores 
            ORDER BY nombre_proveedor
            LIMIT 10";
}

$query = $conexion->query($sql);

if ($query) {
    while ($row = $query->fetch_assoc()) {
        $results[] = [
            "id" => $row["id"],
            "text" => $row["nombre_proveedor"] . " - NIT: " . $row["documento"]
        ];
    }
} else {
    // Si ocurre error, devuélvelo en consola para depuración
    $results[] = ["id" => 0, "text" => "Error SQL: " . $conexion->error];
}

echo json_encode(["results" => $results]);
?>
