<?php
if ($_SERVER['HTTP_HOST'] === 'localhost') {
    // LOCAL
    $host = "localhost";
    $user = "root";
    $clave = "";
    $bd = "systec_logistic";
} else {
    // SERVIDOR
    $host = "controlinterno.net";
    $user = "contr566_gallos";
    $clave = "Elcreador2025*";
    $bd = "contr566_compliance";
}

$conexion = new mysqli($host, $user, $clave, $bd);

if ($conexion->connect_error) {
    die("No se pudo conectar a la base de datos: " . $conexion->connect_error);
}
mysqli_set_charset($conexion, "utf8");
?>