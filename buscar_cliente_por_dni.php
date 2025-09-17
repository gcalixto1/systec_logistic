<?php
include('conexionfin.php');

$response = ['success' => false];

if (isset($_GET['dni']) && !empty($_GET['dni'])) {
    $dni = $conexion->real_escape_string($_GET['dni']);
    $query = $conexion->query("SELECT * FROM cliente LEFT JOIN cliente_direccion ON cliente_direccion.cliente_dni = cliente.dni WHERE cliente.dni = '$dni'");

    if ($query && $cliente = $query->fetch_assoc()) {
        $response = array_merge(['success' => true], $cliente);
    }
}

echo json_encode($response);