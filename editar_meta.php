<?php
include("conexionfin.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_meta = $_POST['id_meta'];
    $nueva_meta = $_POST['nueva_meta'];

    $sql = "UPDATE metas_vendedor SET meta_ventas = '$nueva_meta' WHERE id = '$id_meta'";
    mysqli_query($conexion, $sql);

    header("Location: index.php?page=metas_ventas"); // vuelve a tu página principal
    exit;
}
