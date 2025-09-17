<?php
date_default_timezone_set('America/El_Salvador');

function fechaPeru()
{
	$mes = array(
		"",
		"Enero",
		"Febrero",
		"Marzo",
		"Abril",
		"Mayo",
		"Junio",
		"Julio",
		"Agosto",
		"Septiembre",
		"Octubre",
		"Noviembre",
		"Diciembre"
	);
	return date('d') . " de " . $mes[date('n')] . " de " . date('Y');
}
class obtenerMesaño
{
	private $dbh; // Objeto de conexión a la base de datos

	public function __construct($conexion)
	{
		$this->dbh = $conexion; // Asignar el objeto de conexión
	}
	// Método para obtener la suma de ventas por mes
	public function SumaVentas()
	{
		// Consulta SQL para obtener la suma de ventas por mes
		$p = array();
		$sql = "SELECT 
    IFNULL(MONTH(fechafactura), 0) AS mes, 
    IFNULL(SUM(IFNULL(totalpagar, 0)), 0) AS totalmes 
    FROM factura 
    WHERE YEAR(fechafactura) = YEAR(CURDATE()) 
    GROUP BY MONTH(fechafactura) 
    ORDER BY mes";

		foreach ($this->dbh->query($sql) as $row) {
			$p[] = $row;
		}

		$this->dbh = null;
		return $p;
	}
}
?>