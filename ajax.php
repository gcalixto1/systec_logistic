<?php
ob_start();
header('Content-Type: application/json; charset=utf-8');
$action = $_POST['action'] ?? $_GET['action'] ?? '';
include 'includes/class.php';
$crud = new Action();
#region Login
if ($action == 'login') {
	$login = $crud->login();
	if ($login)
		echo $login;
}
if ($action == 'logout') {
	$logout = $crud->logout();
	if ($logout)
		echo $logout;
}
#endregion

#region Configuracion
if ($action == 'save_configuracion') {
	$configuracion = $crud->save_configuracion();
	if ($configuracion)
		echo $configuracion;
}
#endregion

#region Usuarios
if ($action == 'save_usuarios') {
	$save = $crud->save_usuario();
	if ($save)
		echo $save;
}
if ($action == 'delete_usuario') {
	$save = $crud->delete_usuario();
	if ($save)
		echo $save;
}
#endregion

#region Clientes
if ($action == 'save_clientes') {
	$save = $crud->save_cliente();
	if ($save)
		echo $save;
}
if ($action == 'delete_cliente') {
	$save = $crud->delete_cliente();
	if ($save)
		echo $save;
}
#endregion

#region Proveedores
if ($action == 'save_proveedores') {
	$save = $crud->save_proveedor();
	if ($save)
		echo $save;
}
if ($action == 'delete_proveedor') {
	$save = $crud->delete_proveedor();
	if ($save)
		echo $save;
}
#endregion

#region Productos
if ($action == 'save_productos') {
	$save = $crud->save_productos();
	if ($save)
		echo $save;
}
if ($action == 'save_categorias') {
	$save = $crud->save_categoria();
	if ($save)
		echo $save;
}
if ($action == 'delete_categoria') {
	$save = $crud->delete_categoria();
	if ($save)
		echo $save;
}
if ($action == 'save_tipos') {
	$save = $crud->save_tipo();
	if ($save)
		echo $save;
}
if ($action == 'delete_tipos') {
	$save = $crud->delete_tipo();
	if ($save)
		echo $save;
}
if ($action == 'save_umbs') {
	$save = $crud->save_umb();
	if ($save)
		echo $save;
}
if ($action == 'delete_umb') {
	$save = $crud->delete_umb();
	if ($save)
		echo $save;
}
if ($action == 'save_relaciones') {
	$save = $crud->save_relacion();
	if ($save)
		echo $save;
}
if ($action == 'delete_relacion') {
	$save = $crud->delete_relacion();
	if ($save)
		echo $save;
}
if ($action == 'save_etiquetas') {
	$save = $crud->save_etiqueta();
	if ($save)
		echo $save;
}
if ($action == 'delete_etiquetas') {
	$save = $crud->delete_etiqueta();
	if ($save)
		echo $save;
}
if ($action == 'save_presentacion') {
	$save = $crud->save_presentacion();
	if ($save)
		echo $save;
}
if ($action == 'save_stock') {
	$save = $crud->save_stocks();
	if ($save)
		echo $save;
}
if ($action == 'delete_producto') {
	$save = $crud->delete_producto();
	if ($save)
		echo $save;
}
#endregion

#region Caja
if ($action == 'save_apertura_caja') {
	$save = $crud->save_apertura();
	if ($save)
		echo $save;
}
if ($action == 'save_cierre_caja') {
	$save = $crud->save_cierre();
	if ($save)
		echo $save;
}
if ($action == 'movimientos_caja') {
	$save = $crud->movimientos_caja();
	if ($save)
		echo $save;
}
#endregion

#region Facturacion

if ($action == 'obtenerFactura') {
	$save = $crud->facturas();
	echo $save;
}

if ($action == 'save_venta_completa') {
	$save = $crud->save_ventacompleta();
	if ($save)
		echo $save;
}
#endregion

if ($action == 'saveNotasCredito') {
	$save = $crud->save_NotaCredito();
	if ($save)
		echo $save;
}
if ($action == 'saveNotasDebito') {
	$save = $crud->save_NotaDebito();
	if ($save)
		echo $save;
}

if ($action == 'saveInvalidacion') {
	$save = $crud->save_Invalidacion();
	if ($save)
		echo $save;
}
if ($action == 'save_sujetoExcluido') {
	$save = $crud->save_sujetoExcluido();
	if ($save)
		echo $save;
}
if ($action == 'save_contingencia') {
	$save = $crud->save_contingencia();
	if ($save)
		echo $save;
}

if ($action == 'save_pedido') {
	$save = $crud->save_pedido();
	if ($save)
		echo $save;
}
if ($action == 'save_almacen') {
	$save = $crud->save_almacen();
	if ($save)
		echo $save;
}
if ($action == 'delete_pedidos') {
	$save = $crud->delete_pedidos();
	if ($save)
		echo $save;
}
if ($action == 'delete_abono') {
	include 'conexionfin.php';
	$id = (int)$_POST['id'];
	$sql = "DELETE FROM cartera_abono WHERE id = '$id'";
	echo mysqli_query($conexion, $sql) ? '1' : '0';
	exit;
}
if ($action == 'delete_almacen') {
	include 'conexionfin.php';
	$id = (int)$_POST['id'];
	$sql = "DELETE FROM almacenes WHERE id = '$id'";
	echo mysqli_query($conexion, $sql) ? '1' : '0';
	exit;
}
if ($action == 'consecutivo') {
	include 'conexionfin.php';

	$prefix = 'OC'; // prefijo para el consecutivo
	$codigoConsecutivo = strtolower($prefix); // en tu tabla está 'oc'

	// Obtener el valor actual
	$stmt = $conexion->prepare("SELECT valor FROM consecutivos WHERE codigo_consecutivo = ? LIMIT 1");
	$stmt->bind_param('s', $codigoConsecutivo);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();

	// Si existe, incrementar; si no, empezar desde 1
	if ($row && isset($row['valor'])) {
		$currentValue = intval($row['valor']);
		$newValue = $currentValue + 1;
	} else {
		$newValue = 1;
	}

	// Actualizar el nuevo valor en la base de datos
	$update = $conexion->prepare("UPDATE consecutivos SET valor = ? WHERE codigo_consecutivo = ?");
	$update->bind_param('is', $newValue, $codigoConsecutivo);
	$update->execute();

	// Formatear salida (ejemplo: OC-000001)
	$formattedNumber = $prefix . '-' . str_pad($newValue, 6, '0', STR_PAD_LEFT);

	echo json_encode(["numero" => $formattedNumber]);
	exit;
}


if ($action == 'productos') {
	include 'conexionfin.php';
	$res = $conexion->query("SELECT id_producto, str_id, descripcion, umb, und_embalaje_minima FROM producto ORDER BY descripcion");
	$data = [];
	while ($r = $res->fetch_assoc()) $data[] = $r;
	echo json_encode($data);
	exit;
}

if ($action == "save_orden_compra") {
	include 'conexionfin.php';

	$proveedor_id = $_POST['proveedor_id'] ?? '';
	$fecha_oc = $_POST['fecha_oc'] ?? date('Y-m-d');
	$observacion = $_POST['observacion'] ?? '';
	$detalle = json_decode($_POST['detalle'] ?? '[]', true);

	if (!$proveedor_id || empty($detalle)) {
		echo json_encode(["success" => false, "message" => "Datos incompletos"]);
		exit;
	}

	// ======== 1. CONFIGURACIÓN DEL CONSECUTIVO ========
	$prefix = "OC";
	$codigo = strtolower($prefix);
	$longitud = 9; // Total de caracteres: ejemplo OC0000001 (9)

	// Buscar consecutivo existente
	$sqlCons = "SELECT valor FROM consecutivos WHERE codigo_consecutivo = '$codigo' LIMIT 1";
	$resCons = $conexion->query($sqlCons);

	if ($resCons && $resCons->num_rows > 0) {
		$rowCons = $resCons->fetch_assoc();
		$valorActual = $rowCons['valor']; // Ejemplo: OC0000012
	} else {
		// Si no existe, lo creamos desde el inicio
		$valorActual = $prefix . str_pad(1, $longitud - strlen($prefix), '0', STR_PAD_LEFT);
		$conexion->query("INSERT INTO consecutivos (codigo_consecutivo, valor) VALUES ('$codigo', '$valorActual')");
	}

	// ======== 2. CALCULAR EL NUEVO CONSECUTIVO ========
	// Extraer la parte numérica (después del prefijo)
	$numeroActual = intval(substr($valorActual, strlen($prefix)));
	$nuevoNumero = $numeroActual + 1;
	$nuevoValor = $prefix . str_pad($nuevoNumero, $longitud - strlen($prefix), '0', STR_PAD_LEFT); // Ejemplo OC0000013

	// ======== 3. GENERAR EL NÚMERO DE ORDEN DE COMPRA ========
	$numeroOC = $valorActual; // el actual se usa para la orden

	// ======== 4. CALCULAR TOTALES ========
	$subtotal = array_sum(array_column($detalle, 'subtotal'));
	$iva = array_sum(array_column($detalle, 'iva'));
	$total = array_sum(array_column($detalle, 'total'));

	// ======== 5. INSERTAR ORDEN ========
	$fecha_llegada = date('Y-m-d', strtotime($fecha_oc . ' +30 days')); // Sumar 30 días

	$stmt = $conexion->prepare("INSERT INTO orden_compra 
    (numero_oc, fecha_oc, proveedor_id, observacion, subtotal, iva, total, fecha_llegada)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

	$stmt->bind_param("ssissdds", $numeroOC, $fecha_oc, $proveedor_id, $observacion, $subtotal, $iva, $total, $fecha_llegada);
	$stmt->execute();
	$id_oc = $stmt->insert_id;

	// ======== 6. INSERTAR DETALLE ========
	$stmtDet = $conexion->prepare("INSERT INTO orden_compra_detalle (id_oc, producto, cantidad, precio, subtotal, iva, total)
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
	foreach ($detalle as $d) {
		$stmtDet->bind_param("isidddd", $id_oc, $d['producto'], $d['cantidad'], $d['precio'], $d['subtotal'], $d['iva'], $d['total']);
		$stmtDet->execute();
	}

	// ======== 7. ACTUALIZAR CONSECUTIVO ========
	$stmtUpdate = $conexion->prepare("UPDATE consecutivos SET valor = ? WHERE codigo_consecutivo = ?");
	$stmtUpdate->bind_param('ss', $nuevoValor, $codigo);
	$stmtUpdate->execute();

	// ======== 8. RESPUESTA ========
	echo json_encode([
		"success" => true,
		"id_oc" => $id_oc,
		"numero_oc" => $numeroOC,
		"message" => "Orden de compra guardada correctamente con número $numeroOC"
	]);
	exit;
}
if ($action == 'ingreso_orden_compra') {
	$ingreso = $crud->ingreso_orden_compra();
	if ($ingreso)
		echo $ingreso;
}
#region Ingreso Orden de Compra
if ($action == 'get_orden') {
	$get = $crud->get_orden();
	if ($get) echo $get;
}

if ($action == 'save_ingreso') {
	$save = $crud->save_ingreso();
	if ($save) echo $save;
}

if ($action == 'save_ingreso_manual') {
	$save = $crud->save_ingreso_manual();
	if ($save)
		echo $save;
}
if ($action == 'get_lotes_producto') {
	$save = $crud->get_lotes_producto();
	if ($save)
		echo $save;
}

#endregion
