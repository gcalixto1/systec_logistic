<?php
ob_start();
$action = $_GET['action'];
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
if ($action == 'save_venta_previa') {
	$save = $crud->save_factura();
	echo $save;
}
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