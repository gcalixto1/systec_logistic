<?php
require_once "includes/class.php";  // donde está listarclientes()

// Recibir filtro desde AJAX
$filtro = isset($_GET['q']) ? $_GET['q'] : '';

// Crear instancia
$obj = new Action();

// Buscar clientes
$clientes = $obj->listarclientes($filtro);

// Formatear respuesta en JSON para Select2
$resultado = [];
foreach ($clientes as $c) {
    $resultado[] = [
        "id" => $c['id'], // lo que guardarás en pedidos
        "text" => $c['id'] . " - " . $c['nit'] . " - " . $c['nombre_cliente']. " - " . $c['nombre_sede'], // lo que el usuario ve
        "nombre_sede" => $c['nombre_sede'],
        "plazo_pago" => $c['plazos_pago_dias'],
    ];
}

echo json_encode(["results" => $resultado]);    
