<?php
include("conexionfin.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_meta = $_POST['id_meta'];

    $sql = "DELETE FROM metas_vendedor WHERE id = '$id_meta'";
    if (mysqli_query($conexion, $sql)) {
        header("Location: index.php?page=kardex_productos"); // vuelve a tu página principal
        exit;
    } else {
        echo "Error al eliminar: " . mysqli_error($conexion);
    }
}
?>
