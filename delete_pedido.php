<?php
include("conexionfin.php");

if (isset($_POST['idpedido'])) {
    $id = intval($_POST['idpedido']);

    // primero eliminamos el detalle
    $conexion->query("DELETE FROM detalle_pedidos WHERE pedido_id = $id");

    // luego el pedido
    $delete = $conexion->query("DELETE FROM pedidos WHERE id = $id");

    if ($delete) {
        echo 1; // éxito
    } else {
        echo 0; // error
    }
} else {
    echo 0;
}
