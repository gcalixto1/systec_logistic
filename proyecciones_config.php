<?php
include("conexionfin.php");

if(isset($_POST['guardar'])){
    $id = $_POST['id'] ?? null;
    $porc = floatval($_POST['porcentaje']);

    if($id){
        $conexion->query("UPDATE proyecciones_ventas SET porcentaje_incremento=$porc WHERE id=$id");
    } else {
        $conexion->query("INSERT INTO proyecciones_ventas (porcentaje_incremento) VALUES ($porc)");
    }
    header("Location: index.php?page=proyeccion");
    exit;
}

if(isset($_GET['eliminar'])){
    $id = intval($_GET['eliminar']);
    $conexion->query("DELETE FROM proyecciones_ventas WHERE id=$id");
    header("Location: index.php?page=proyeccion");
    exit;
}
