<?php
include('conexionfin.php');

$codigo = $_POST['codigo'];

$stmt = $conexion->prepare("SELECT codproducto, descripcion, precio FROM producto WHERE codBarra = ?");
$stmt->bind_param("s", $codigo);
$stmt->execute();
$result = $stmt->get_result();

if ($producto = $result->fetch_assoc()) {
    echo json_encode([
        "success" => true,
        "producto" => $producto
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Producto no encontrado"
    ]);
}
?>