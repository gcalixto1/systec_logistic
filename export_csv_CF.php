<?php
header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=anexos_CF_" . date('Y-m-d') . ".csv");

require_once "conexionfin.php";

$output = fopen("php://output", "w");

$anio = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y');
$mes = isset($_GET['mes']) ? intval($_GET['mes']) : date('n');

// Solo documentos de consumidor final y similares
$sql = "
SELECT 
    DATE(f.fechafactura) as fecha,
    JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.tipoDte')) as tipoDte,
    JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.codigoGeneracion')) as codigoGeneracion,
    JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.resumen.totalExenta')) as totalExenta,
    JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.resumen.totalNoSuj')) as totalNoSuj,
    JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.resumen.totalGravada')) as totalGravada,
    JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.resumen.totalPagar')) as totalPagar
FROM respuestadte r
INNER JOIN factura f ON f.id = r.id_factura
WHERE 
    r.estado = 'PROCESADO'
    AND r.selloRecibido IS NOT NULL
    AND TRIM(r.selloRecibido) <> ''
    AND r.descripcionMsg = 'RECIBIDO'
    AND YEAR(f.fechafactura) = $anio
    AND MONTH(f.fechafactura) = $mes
    AND JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.tipoDte')) IN ('01','02','10','11')
ORDER BY fecha, codigoGeneracion ASC
";

$result = $conexion->query($sql);
if (!$result) {
    die("Error en consulta: " . $conexion->error);
}

$datosAgrupados = [];

// AGRUPAR POR FECHA Y TIPO DE DOCUMENTO
while ($row = $result->fetch_assoc()) {
    $fecha = $row['fecha'];
    $tipoDte = str_pad($row['tipoDte'], 2, "0", STR_PAD_LEFT);

    $key = $fecha . "_" . $tipoDte;

    if (!isset($datosAgrupados[$key])) {
        $datosAgrupados[$key] = [
            "fecha" => $fecha,
            "tipoDte" => $tipoDte,
            "primer" => $row['codigoGeneracion'],
            "ultimo" => $row['codigoGeneracion'],
            "exentas" => 0,
            "noSuj" => 0,
            "gravadas" => 0,
            "total" => 0
        ];
    } else {
        $datosAgrupados[$key]["ultimo"] = $row['codigoGeneracion'];
    }

    $datosAgrupados[$key]["exentas"] += floatval($row['totalExenta']);
    $datosAgrupados[$key]["noSuj"] += floatval($row['totalNoSuj']);
    $datosAgrupados[$key]["gravadas"] += floatval($row['totalGravada']);
    $datosAgrupados[$key]["total"] += floatval($row['totalPagar']);
}

// EXPORTAR A CSV
foreach ($datosAgrupados as $item) {
    $fechaEmision = date("d/m/Y", strtotime($item["fecha"]));
    $claseDoc = "4";
    $tipoDoc = $item["tipoDte"];
    $numResolucion = "N/A";
    $numSerie = "N/A";
    $controlInternoDel = "N/A";
    $controlInternoAl = "N/A";
    $numDocDel = $item["primer"];
    $numDocAl = $item["ultimo"];
    $numMaquina = "";

    $ventasExentas = number_format($item["exentas"], 2, '.', '');
    $ventasInternasExNoProp = "0.00"; // no se maneja en DTE
    $ventasNoSuj = number_format($item["noSuj"], 2, '.', '');
    $ventasGrav = number_format($item["gravadas"], 2, '.', '');
    $expCA = "0.00";
    $expFueraCA = "0.00";
    $expServicios = "0.00";
    $zonasFrancas = "0.00";
    $ventasTerceros = "0.00";
    $totalVentas = number_format($item["total"], 2, '.', '');

    $tipoOperacion = "1";
    $tipoIngreso = "3";
    $numAnexo = "2";

    fputcsv($output, [
        $fechaEmision,         // A
        $claseDoc,             // B
        $tipoDoc,              // C
        $numResolucion,        // D
        $numSerie,             // E
        $controlInternoDel,    // F
        $controlInternoAl,     // G
        $numDocDel,            // H
        $numDocAl,             // I
        $numMaquina,           // J
        $ventasExentas,        // K
        $ventasInternasExNoProp, // L
        $ventasNoSuj,          // M
        $ventasGrav,           // N
        $expCA,                // O
        $expFueraCA,           // P
        $expServicios,         // Q
        $zonasFrancas,         // R
        $ventasTerceros,       // S
        $totalVentas,          // T
        $tipoOperacion,        // U
        $tipoIngreso,          // V
        $numAnexo              // W
    ], ";");
}

fclose($output);
?>