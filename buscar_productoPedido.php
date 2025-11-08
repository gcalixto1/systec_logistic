<?php
require_once "includes/class.php"; // tu clase Action o similar

$filtro = isset($_GET['q']) ? $_GET['q'] : '';

$obj = new Action();
$productos = $obj->listarproductoautopedido($filtro);

$resultado = [];
foreach ($productos as $p) {
    $resultado[] = [
        "id" => $p['id_producto'], 
        "text" => $p['cod_producto'] . " - " . $p['descripcion'], 
        "tipo" => $p['tipo'],     // 👈 enviamos el precio unitario
        "ref1" => $p['ref_1'],     // 👈 enviamos el precio unitario
        "ref2" => $p['ref_2'],     // 👈 enviamos el precio unitario
        "relacion" => $p['relacion'],     // 👈 enviamos el precio unitario
        "precio" => $p['precio_lista_5'],     // 👈 enviamos el precio unitario
        "precioRemi" => $p['precio_remision_lista_5'],     // 👈 enviamos el precio unitario
        "unidades" => $p['und_embalaje_minima'],     // 👈 enviamos el precio unitario
        "calibre" => $p['calibre']      // 👈 si quieres, también la unidad de medida
    ];
}

echo json_encode(["results" => $resultado]);