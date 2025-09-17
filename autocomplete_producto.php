<?php
// suggest.php

// Incluir el archivo que contiene la clase Action y la función listarclientes
require_once 'includes/class.php'; // Asegúrate de que la ruta sea correcta

// Obtener el término de búsqueda enviado desde el cliente
$searchTerm = $_GET['q'];

// Crear una instancia de la clase Action
$conexion = new Action();

// Obtener sugerencias de clientes utilizando la función listarclientes
$suggestions = $conexion->listarproductoauto($searchTerm);

// Devolver los resultados como JSON
echo json_encode($suggestions);
?>
