<?php
include_once 'includes/functions.php';
include "conexionfin.php";
// datos Empresa
$dni = '';
$nombre_empresa = '';
$razonSocial = '';
$emailEmpresa = '';
$telEmpresa = '';
$dirEmpresa = '';
$igv = '';


$qry_top_products = mysqli_query($conexion, "SELECT p.descripcion, coalesce(SUM(df.cantidad), 0) AS cantidad FROM detallefactura df INNER JOIN producto p ON df.cod_producto = p.codproducto  GROUP BY p.descripcion ORDER BY cantidad DESC LIMIT 5;");
$product_names = array();
$product_quantities = array();

// Recorrer los resultados de los productos más vendidos
while ($row = mysqli_fetch_assoc($qry_top_products)) {
	$product_names[] = $row['descripcion'];
	$product_quantities[] = $row['cantidad'];
}


$venta = new obtenerMesaño($conexion);
$venmes = $venta->SumaVentas();

$query_empresa = mysqli_query($conexion, "SELECT * FROM configuracion");
$row_empresa = mysqli_num_rows($query_empresa);
if ($row_empresa > 0) {
	if ($infoEmpresa = mysqli_fetch_assoc($query_empresa)) {
		$dni = $infoEmpresa['dni'];
		$nombre_empresa = $infoEmpresa['nombre'];
		$razonSocial = $infoEmpresa['razon_social'];
		$telEmpresa = $infoEmpresa['telefono'];
		$emailEmpresa = $infoEmpresa['email'];
		$dirEmpresa = $infoEmpresa['direccion'];
		$igv = $infoEmpresa['igv'];
		$impresion = $infoEmpresa['impresion'];
	}
}
$query_data = mysqli_query($conexion, "SELECT 
    (SELECT COUNT(*) FROM usuario) AS usuarios,
    (SELECT COUNT(*) FROM cliente) AS clientes,
    (SELECT COUNT(*) FROM proveedor) AS proveedores,
    (SELECT COUNT(*) FROM producto) AS productos,
    (SELECT COUNT(*) FROM factura WHERE estado = 'Pagado' and fechafactura >= CURDATE()) AS ventas,
    (SELECT IFNULL(SUM(totalpagar), 0.00) FROM factura WHERE estado = 'Pagado' and DATE(fechafactura) = CURDATE()) AS total_dia,
    (SELECT IFNULL(SUM(totalpagar), 0.00) FROM factura WHERE estado = 'Pagado' and YEAR(fechafactura) = YEAR(CURDATE()) AND MONTH(fechafactura) = MONTH(CURDATE())) AS total_mes,
    (SELECT COUNT(*) FROM producto WHERE existencia < exis_min) AS producto_minimo;");
$result_data = mysqli_num_rows($query_data);
if ($result_data > 0) {
	$data = mysqli_fetch_assoc($query_data);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Punto de venta</title>
</head>

<body id="page-top">
    <?php
	include "conexionfin.php";

	$query_data = mysqli_query($conexion, "
	SELECT 
    	(SELECT COUNT(*) FROM usuario) AS usuarios,
    	(SELECT COUNT(*) FROM cliente) AS clientes,
    	(SELECT COUNT(*) FROM proveedor) AS proveedores,
    	(SELECT COUNT(*) FROM producto) AS productos,
    	(SELECT COUNT(*) FROM factura WHERE estado = 'Pagado' and fechafactura >= CURDATE()) AS ventas,
    	(SELECT IFNULL(SUM(totalpagar), 0.00) FROM factura WHERE estado = 'Pagado' and DATE(fechafactura) = CURDATE()) AS total_dia,
    	(SELECT IFNULL(SUM(totalpagar), 0.00) FROM factura WHERE estado = 'Pagado' and YEAR(fechafactura) = YEAR(CURDATE()) AND MONTH(fechafactura) = MONTH(CURDATE())) AS total_mes,
    	(SELECT COUNT(*) FROM producto WHERE existencia < exis_min) AS producto_minimo,
		GROUP_CONCAT(JSON_OBJECT('semana', semana_mes, 'ventas', total_ventas)) AS ventas_semanales
	FROM (
		SELECT 
			WEEK(fechafactura, 0) - WEEK(DATE_SUB(fechafactura, INTERVAL DAY(fechafactura) - 1 DAY), 0) + 1 AS semana_mes,
			SUM(totalpagar) AS total_ventas
		FROM factura
		WHERE 
			YEAR(fechafactura) = YEAR(CURDATE()) 
			AND MONTH(fechafactura) = MONTH(CURDATE())
		GROUP BY semana_mes
		ORDER BY semana_mes
	) AS subconsulta;
	");

	$result_data = mysqli_num_rows($query_data);
	if ($result_data > 0) {
		$data = mysqli_fetch_assoc($query_data);
		// Decodificar ventas_semanales
		$ventas_semanales = json_decode("[" . $data['ventas_semanales'] . "]", true);
	}

	?>