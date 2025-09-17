<?php
require_once 'factura/fpdf/fpdf.php';
require('conexionfin.php'); // Tu archivo de conexión

$num_apertura = $_GET['num_apertura']; // puedes cambiar a POST si lo deseas

// CONSULTA PRINCIPAL DE DATOS DE CORTE
$query = "
SELECT
    ac.idcaja,
    ac.num_apertura,
    ac.saldo_inicial,
    ac.fch_hora_apertura,
    ac.fch_hora_cierre,
    ac.usuario,
    ac.caja,
    ac.saldo_venta_total,
    ac.gasto,
    ac.notas,
    ac.saldo_tarjeta,
    ac.saldo_credito,
    ac.entradas,
    ac.total_completo,

    COUNT(f.id) AS total_ventas,
    SUM(f.totalpagar) AS ventas_totales,
        SUM(CASE WHEN f.forma_pago = '01' THEN f.totalpagar ELSE 0 END) AS efectivo,
    SUM(CASE WHEN f.forma_pago IN ('02', '03') THEN f.totalpagar ELSE 0 END) AS tarjeta,
    SUM(CASE WHEN f.forma_pago = '05' THEN f.totalpagar ELSE 0 END) AS transferencias,
    SUM(CASE WHEN f.tipofactura = 'ccf' THEN 1 ELSE 0 END) AS creditos_fiscal,
    SUM(CASE WHEN f.tipofactura = 'fcf' THEN 1 ELSE 0 END) AS consumidor_final,
    
    MIN(CASE WHEN f.tipofactura = 'ccf' THEN f.numerofactura END) AS ccf_inicial,
    MAX(CASE WHEN f.tipofactura = 'ccf' THEN f.numerofactura END) AS ccf_final,

    MIN(CASE WHEN f.tipofactura = 'fcf' THEN f.numerofactura END) AS tick_inicial,
    MAX(CASE WHEN f.tipofactura = 'fcf' THEN f.numerofactura END) AS tick_final

FROM apertura_caja ac
LEFT join usuario u ON u.usuario = ac.usuario
LEFT JOIN factura f ON f.idusuario = u.idusuario
    AND f.fechafactura BETWEEN ac.fch_hora_apertura AND ac.fch_hora_cierre
WHERE ac.num_apertura = ?
GROUP BY ac.idcaja
";

$stmt = $conexion->prepare($query);
$stmt->bind_param("s", $num_apertura);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// FPDF INICIALIZACIÓN
$pdf = new FPDF('P', 'mm', array(80, 300));
$pdf->AddPage();
$pdf->SetFont('Courier', 'B', 10);
$pdf->Cell(0, 5, 'CORTE DEL TURNO', 0, 1, 'C');
$pdf->SetFont('Courier', '', 9);
$pdf->Cell(0, 5, 'CORTE DE TURNO #' . $data['num_apertura'], 0, 1, 'C');
$pdf->Ln(2);

// DATOS GENERALES
$pdf->Cell(0, 5, 'REALIZADO: ' . date("d/M/Y h:i A", strtotime($data['fch_hora_cierre'])), 0, 1);
$pdf->Cell(0, 5, 'CAJERO:     ' . $data['usuario'], 0, 1);
$pdf->Cell(0, 5, 'VENTAS TOTALES:   $' . number_format($data['ventas_totales'], 2), 0, 1);
$pdf->Cell(0, 5, 'GANANCIA:         $' . number_format($data['saldo_venta_total'], 2), 0, 1);
$pdf->Ln(1);
$pdf->Cell(0, 5, $data['total_ventas'] . ' VENTAS EN EL TURNO.', 0, 1);
$pdf->Ln(2);

// DINERO EN CAJA
$pdf->SetFont('Courier', 'B', 10);
$pdf->Cell(0, 5, '== DINERO EN CAJA ==', 0, 1, 'C');
$pdf->SetFont('Courier', '', 9);
$pdf->Cell(0, 5, 'FONDO DE CAJA:       $' . number_format($data['saldo_inicial'], 2), 0, 1);
$pdf->Cell(0, 5, 'VENTAS EN EFECTIVO: +$' . number_format($data['efectivo'], 2), 0, 1);
$pdf->Cell(0, 5, 'ABONOS EN EFECTIVO: +$' . number_format($data['entradas'], 2), 0, 1);
$pdf->Cell(0, 5, 'ENTRADAS:           +$' . number_format($data['entradas'], 2), 0, 1);
$pdf->Cell(0, 5, 'SALIDAS:            -$' . number_format($data['gasto'], 2), 0, 1);
$pdf->Cell(0, 0, str_repeat('-', 35), 0, 1);
$pdf->Ln(2);
$efectivo_en_caja = $data['saldo_inicial'] + $data['efectivo'] + $data['entradas'] - $data['gasto'];
$pdf->Cell(0, 5, 'EFECTIVO EN CAJA = $' . number_format($efectivo_en_caja, 2), 0, 1);
$pdf->Ln(2);

// ENTRADAS EFECTIVO
$pdf->SetFont('Courier', 'B', 10);
$pdf->Cell(0, 5, '== ENTRADAS EFECTIVO ==', 0, 1, 'C');
$pdf->SetFont('Courier', '', 9);
$pdf->Cell(0, 5, 'ENTRADA DE DINERO $' . number_format($data['entradas'], 2), 0, 1);
$pdf->Cell(0, 5, 'TOTAL ENTRADAS     $' . number_format($data['entradas'], 2), 0, 1);
$pdf->Ln(2);

// SALIDAS EFECTIVO
$pdf->SetFont('Courier', 'B', 10);
$pdf->Cell(0, 5, '== SALIDAS EFECTIVO ==', 0, 1, 'C');
$pdf->SetFont('Courier', '', 9);
$pdf->Cell(0, 5, 'SALIDA DE CAJA      $' . number_format($data['gasto'], 2), 0, 1);
$pdf->Cell(0, 5, 'TOTAL SALIDAS       $' . number_format($data['gasto'], 2), 0, 1);
$pdf->Ln(2);

// VENTAS POR FORMA DE PAGO
$pdf->SetFont('Courier', 'B', 10);
$pdf->Cell(0, 5, '== VENTAS ==', 0, 1, 'C');
$pdf->SetFont('Courier', '', 9);
$pdf->Cell(0, 5, 'EN EFECTIVO     $' . number_format($data['efectivo'], 2), 0, 1);
$pdf->Cell(0, 5, 'CON TARJETA D/C   $' . number_format($data['tarjeta'], 2), 0, 1);
$pdf->Cell(0, 5, 'CON TRANSFERENCIAS       $' . number_format($data['transferencias'], 2), 0, 1);
$pdf->Cell(0, 0, str_repeat('-', 35), 0, 1);
$pdf->Ln(2);
$pdf->Cell(0, 5, 'TOTAL VENTAS    $' . number_format($data['ventas_totales'], 2), 0, 1);

$pdf->Ln(4);
$pdf->SetFont('Courier', 'B', 10);
$pdf->Cell(0, 5, '== DETALLE DE FACTURAS ==', 0, 1, 'C');
$pdf->SetFont('Courier', '', 9);

// DETALLE DE FACTURAS
$sql_facturas = "
SELECT 
    f.numerofactura AS numero,
    f.totalpagar AS monto,
    f.tipofactura AS tipo
FROM factura f
JOIN usuario u ON f.idusuario = u.idusuario
JOIN apertura_caja ac ON u.usuario = ac.usuario
    AND DATE(f.fechafactura) = DATE(ac.fch_hora_apertura)
WHERE ac.num_apertura = ?
";

// NOTAS DE CRÉDITO
$sql_nc = "
SELECT 
    nc.numeroDocumento AS numero,
    nc.monto AS monto,
    'ndc' AS tipo
FROM notas_credito nc
JOIN apertura_caja ac ON DATE(nc.fechaemisionnc) = DATE(ac.fch_hora_apertura)
WHERE ac.num_apertura = ?
";

// NOTAS DE DÉBITO
$sql_nd = "
SELECT 
    nd.numeroDocumento AS numero,
    nd.monto AS monto,
    'ndd' AS tipo
FROM notas_debito nd
JOIN apertura_caja ac ON DATE(nd.fechaemisionnd) = DATE(ac.fch_hora_apertura)
WHERE ac.num_apertura = ?
";

// SUJETOS EXCLUIDOS
$sql_se = "
SELECT 
    RIGHT(se.numero_control, 9) AS raw_numero,
    se.subtotal AS monto,
    'se' AS tipo
FROM sujetoexcluido_dte se
JOIN apertura_caja ac ON DATE(se.fechaemision) = DATE(ac.fch_hora_apertura)
WHERE ac.num_apertura = ?
";

// Unir todas las consultas con UNION
$sql_detalle = "
($sql_facturas)
UNION
($sql_nc)
UNION
($sql_nd)
UNION
($sql_se)
ORDER BY tipo, CAST(numero AS UNSIGNED)
";

$stmt_detalle = $conexion->prepare($sql_detalle);
$stmt_detalle->bind_param("ssss", $num_apertura, $num_apertura, $num_apertura, $num_apertura);
$stmt_detalle->execute();
$res_detalle = $stmt_detalle->get_result();

while ($row = $res_detalle->fetch_assoc()) {
    $numero = str_pad($row['numero'], 8, '0', STR_PAD_LEFT);
    $monto = number_format($row['monto'], 2);
    $tipo = strtolower($row['tipo']);

    if ($tipo === 'se') {
        // Concatenar el número como fse000047
        $numero = 'fse' . substr($numero, -6);
        $tipo = 'SE';
    } else {
        $tipo = strtoupper($tipo);
    }

    $pdf->Cell(0, 5, "$tipo #$numero     \$$monto", 0, 1);
}

$pdf->Output();