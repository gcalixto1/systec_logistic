<?php
header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=anexos_contables_" . date('Y-m-d') . ".csv");

require_once "conexionfin.php";

$output = fopen("php://output", "w");

$anio = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y');
$mes = isset($_GET['mes']) ? intval($_GET['mes']) : date('n');
$filtroc = isset($_GET['filtroc']) ? $_GET['filtroc'] : '0';

// ==== CONDICIÓN PARA FILTRO DE COMPROBANTE ====
$condicionComprobante = "";
if ($filtroc != "0") {
    $condicionComprobante = " AND JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.tipoDte')) = '$filtroc' ";
}

$sql = "
SELECT 
    r.jsondte,
    r.selloRecibido,
    JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.numeroControl')) AS numeroControl,
    JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.codigoGeneracion')) AS codigoGeneracion,
    JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.tipoDte')) AS tipoDte
FROM 
    respuestadte r
INNER JOIN 
    factura f ON f.id = r.id_factura
LEFT JOIN 
    invalidaciones i ON i.numeroControl = JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.numeroControl'))
WHERE 
    r.estado = 'PROCESADO'
    AND r.selloRecibido IS NOT NULL
    AND TRIM(r.selloRecibido) <> ''
    AND r.descripcionMsg = 'RECIBIDO'
    AND YEAR(f.fechafactura) = $anio
    AND MONTH(f.fechafactura) = $mes
    AND i.numeroControl IS NULL
    $condicionComprobante
ORDER BY 
    CAST(JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.numeroControl')) AS UNSIGNED) ASC
";


$result = $conexion->query($sql);

if (!$result) {
    die("Error en la consulta: " . $conexion->error);
}

// ====== RECORRER Y EXPORTAR ======
while ($row = $result->fetch_assoc()) {
    $data = json_decode($row['jsondte'], true);
    $ident = $data['identificacion'] ?? [];
    $resumen = $data['resumen'] ?? [];
    $receptor = $data['receptor'] ?? [];

    // === A. Fecha de emisión ===
    $fechaEmision = !empty($ident['fecEmi']) ? date('d/m/Y', strtotime($ident['fecEmi'])) : '';

    // === B. Clase de Documento === (DTE = 4)
    $claseDoc = "4";

    // === C. Tipo de Documento ===
    $tipoDoc = str_pad($ident['tipoDte'] ?? '', 2, "0", STR_PAD_LEFT);

    // === D. Número de Resolución ===
    $numeroResolucion = preg_replace('/[^A-Za-z0-9]/', '', $row['numeroControl']);

    // === E. Número de Serie de Documento === (sello de Hacienda)
    $numeroSerie = $row['selloRecibido'];

    // === F. Número de Documento === (codigoGeneracion sin guiones)
    $numeroDocumento = preg_replace('/[^A-Za-z0-9]/', '', $row['codigoGeneracion']);

    // === G. Número de Control Interno === (en DTE queda vacío)
    $numeroControlInterno = "";

    // === H. NIT o NRC del Cliente ===
    $nitNrc = "";
    if (!empty($receptor['nit'])) {
        $nitNrc = preg_replace('/[^0-9]/', '', $receptor['nit']);
    } elseif (!empty($receptor['nrc'])) {
        $nitNrc = ltrim(preg_replace('/[^0-9]/', '', $receptor['nrc']), "0");
    }

    // === I. Nombre o Razón Social ===
    $nombreCliente = $receptor['nombre'] ?? '';

    // === J–O Montos ===
    $ventasExentas = number_format($resumen['totalExenta'] ?? 0, 2, '.', '');
    $ventasNoSuj = number_format($resumen['totalNoSuj'] ?? 0, 2, '.', '');
    $ventasGrav = number_format($resumen['totalGravada'] ?? 0, 2, '.', '');
    $debitoFiscal = number_format($resumen['iva'] ?? 0, 2, '.', '');
    $ventasTerceros = "0.00";
    $debitoFiscalTerceros = "0.00";

    // === P. Total Ventas ===
    $totalVentas = number_format($resumen['totalPagar'] ?? 0, 2, '.', '');

    // === Q. DUI ===
    $dui = "";
    if (!empty($receptor['dui']) && $anio >= 2022) {
        $dui = preg_replace('/[^0-9]/', '', $receptor['dui']);
        $nitNrc = ""; // si hay DUI, NIT/NRC queda vacío
    }

    // === R. Tipo de Operación === (por defecto 1 Gravada)
    $tipoOperacion = "1";

    // === S. Tipo de Ingreso === (por defecto 3 Comercial)
    $tipoIngreso = "3";

    // === T. Número de Anexo === (siempre 1)
    $numAnexo = "1";

    // === ESCRIBIR EN CSV ===
    fputcsv($output, [
        $fechaEmision,           // A
        $claseDoc,               // B
        $tipoDoc,                // C
        $numeroResolucion,       // D
        $numeroSerie,            // E
        $numeroDocumento,        // F
        $numeroControlInterno,   // G
        $nitNrc,                 // H
        $nombreCliente,          // I
        $ventasExentas,          // J
        $ventasNoSuj,            // K
        $ventasGrav,             // L
        $debitoFiscal,           // M
        $ventasTerceros,         // N
        $debitoFiscalTerceros,   // O
        $totalVentas,            // P
        $dui,                    // Q
        $tipoOperacion,          // R
        $tipoIngreso,            // S
        $numAnexo                // T
    ], ";");
}

fclose($output);
?>