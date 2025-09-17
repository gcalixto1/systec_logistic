<?php
$host = "localhost";
$user = "root";
$clave = "";

// Establecer la conexión al servidor MySQL
$conexion = new mysqli($host, $user, $clave);

// Verificar si hay errores de conexión
if ($conexion->connect_error) {
    die("No se pudo conectar al servidor MySQL: " . $conexion->connect_error);
}

// Nombre de la base de datos
$bd = "sis_venta";
//$bd = "systec_pos";

// Crear la base de datos
$sql = "CREATE DATABASE IF NOT EXISTS $bd";
if ($conexion->query($sql) === TRUE) {
} else {
    die("Error al crear la base de datos: " . $conexion->error);
}

// Seleccionar la base de datos
$conexion->select_db($bd);

// Establecer el conjunto de caracteres a utf8
mysqli_set_charset($conexion, "utf8");

// Consultar la lista de tablas
$sql = "SHOW TABLES";
$resultado = $conexion->query($sql);

if ($resultado->num_rows > 0) {
    $tiene_tablas = 1;
} else {
    $tiene_tablas = 0;
}

// Cerrar la conexión
$conexion->close();
header("Location: login.php?pv=$tiene_tablas");
?>