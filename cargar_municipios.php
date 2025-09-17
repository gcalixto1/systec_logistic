<?php
require_once("includes/class.php");

if (isset($_GET['id_departamento'])) {
    $pro = new Action();
    $municipios = $pro->ListarMunicipios($_GET['id_departamento']);
    echo json_encode($municipios);
} else {
    echo json_encode([]);
}
?>