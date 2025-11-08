<?php
include('conexionfin.php');

if (!empty($_POST['orden']) && is_array($_POST['orden'])) {
    $orden = $_POST['orden'];
    $i = 1;

    $conexion->begin_transaction();

    try {
        foreach ($orden as $id) {
            $stmt = $conexion->prepare("UPDATE orden_produccion SET prioridad = ? WHERE id = ?");
            $stmt->bind_param("ii", $i, $id);
            $stmt->execute();
            $i++;
        }
        $conexion->commit();
        echo "OK";
    } catch (Exception $e) {
        $conexion->rollback();
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Sin datos recibidos";
}
?>
