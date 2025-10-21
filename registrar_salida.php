<?php
include("conexionfin.php");

$producto = intval($_POST['producto']);
$almacen  = intval($_POST['almacen']);
$filas    = json_decode($_POST['filas'], true);
$fecha    = date('Y-m-d H:i:s');
$usuario  = $_SESSION['login_idusuario'] ?? 1;

if(!$producto || !$almacen || empty($filas)){
    echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    exit;
}

foreach($filas as $fila){
    $lote = $conexion->real_escape_string($fila['lote']);
    $salida = floatval($fila['salida']);
    if($salida <= 0) continue;

    $conexion->query("INSERT INTO movimientos_inventario (id_producto, almacen_id, tipo_movimiento, cantidad, lote, fecha, usuario)
                      VALUES ($producto, $almacen, 'SALIDA MANUAL', -$salida, '$lote', '$fecha', '$usuario')");

            //           $conexion->dbh->query("INSERT INTO movimientos_inventario 
            // (id_producto, id_interno, descripcion, cantidad, lote, almacen_id, tipo_movimiento, cliente_proveedor, num_documento, costo_unitario, costo_total, fecha_movimiento,calibre, umb,ref1,ref2)
            // SELECT p.id_producto,p.str_id, p.descripcion,'$cantidad','$loteProd','$almacen_id','Salida manual','0','salida','$costo','$total',NOW(), p.calibre, p.umb,p.ref_1,p.ref_2 
			// FROM producto p  WHERE p.id_producto = '$producto'");
}

echo json_encode(['success' => true, 'message' => 'Las salidas fueron registradas correctamente.']);
?>
