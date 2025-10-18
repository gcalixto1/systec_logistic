<?php
if ($_SERVER['HTTP_HOST'] === 'localhost') {
    // LOCAL
    $host = "localhost";
    $user = "root";
    $clave = "";
    $bd = "systec_logistic";
} else {
    // SERVIDOR
    $host = "localhost";
    $user = "u424078311_systec";
    $clave = "Elcreador2025*";
    $bd = "u424078311_systec_logis";
}

$conexion = new mysqli($host, $user, $clave, $bd);

if ($conexion->connect_error) {
    die("No se pudo conectar a la base de datos: " . $conexion->connect_error);
}
mysqli_set_charset($conexion, "utf8");
?>