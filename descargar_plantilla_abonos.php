<?php
include("conexionfin.php");

// Nombre del archivo
$filename = "plantilla_abonos_" . date("Ymd_His") . ".xls";

// Cabeceras para forzar descarga en Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// Imprimir encabezado de columnas
echo "id_pedido\tmonto_deuda\tmonto_abono\tbanco\tforma_pago\treferencia\n";

// Obtener pedidos pendientes
$sql = "
    SELECT 
        p.id AS id_pedido,
        SUM(dp.total) - COALESCE(SUM(a.monto_abono),0) AS monto_deuda
    FROM pedidos p
    INNER JOIN detalle_pedidos dp ON dp.pedido_id = p.id
    LEFT JOIN cartera_abono a ON a.id_pedido = p.id
    WHERE p.estatus <> 'CANCELADO'
    GROUP BY p.id
    HAVING monto_deuda > 0
";

$result = mysqli_query($conexion, $sql);

while($row = mysqli_fetch_assoc($result)) {
    // Formato: ID de pedido, deuda, abono (vacío), banco (vacío), forma_pago (vacío), referencia (vacío)
    echo $row['id_pedido'] . "\t" . number_format($row['monto_deuda'],2,'.','') . "\t\t\t\tCARGA MASIVA\n";
}

exit();
