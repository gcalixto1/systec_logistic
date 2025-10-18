<?php
include("conexionfin.php");

$numero_orden = $_POST['numero_orden'];
$idproveedor = $_POST['idproveedor'];
$fecha = $_POST['fecha'];
$observacion = $_POST['observacion'];
$productos = $_POST['productos'];

$subtotal = 0;
foreach($productos as $p){
  $subtotal += $p['cantidad'] * $p['costo_unit'];
}
$iva = $subtotal * 0.19;
$total = $subtotal + $iva;

// Insertar cabecera
mysqli_query($conexion, "INSERT INTO ordenes_compra (numero_orden, idproveedor, fecha, observacion, subtotal, iva, total)
VALUES ('$numero_orden', '$idproveedor', '$fecha', '$observacion', '$subtotal', '$iva', '$total')");

$id_orden = mysqli_insert_id($conexion);

// Insertar detalle
foreach($productos as $p){
  $codigo = $p['codigo'];
  $desc = $p['descripcion'];
  $obs = $p['observacion'];
  $cant = $p['cantidad'];
  $cost = $p['costo_unit'];
  $tot = $cant * $cost;

  mysqli_query($conexion, "INSERT INTO detalle_orden_compra (id_orden, id_producto, descripcion, observacion, cantidad, costo_unitario, total)
  VALUES ('$id_orden', '$codigo', '$desc', '$obs', '$cant', '$cost', '$tot')");
}

// Actualizar consecutivo
mysqli_query($conexion, "UPDATE consecutivos SET valor = valor + 1 WHERE codigo_consecutivo = 'PO'");

// Generar PDF
include("doc_orden_compra.php");
doc_orden_compra($id_orden, $conexion);

echo "✅ Orden de compra guardada correctamente. Se generó el PDF.";
?>
