<?php
require_once 'conexionfin.php'; // Asegúrate de incluir tu conexión

// Recibir parámetros GET
$anioFiltro = isset($_GET['anio']) ? (int) $_GET['anio'] : date("Y");
$mesFiltro = isset($_GET['mes']) ? (int) $_GET['mes'] : date("n");
$filtroc = isset($_GET['filtroc']) ? $_GET['filtroc'] : '0';

// Condición para tipo de comprobante
$condicionComprobante = "";
if ($filtroc != "0") {
    $condicionComprobante = " AND JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.tipoDte')) = '$filtroc' ";
}

// Consulta
$sql = "
SELECT 
    r.id,
    r.jsondte,
    r.selloRecibido,
    JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.numeroControl')) AS numeroControl,
    JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.codigoGeneracion')) AS codigoGeneracion,
    (
        SELECT JSON_UNQUOTE(JSON_EXTRACT(r2.jsondte, '$.identificacion.codigoGeneracion'))
        FROM respuestadte r2
        INNER JOIN factura f2 ON f2.id = r2.id_factura
        WHERE 
            r2.estado = 'PROCESADO'
            AND r2.selloRecibido IS NOT NULL
            AND TRIM(r2.selloRecibido) <> ''
            AND r2.descripcionMsg = 'RECIBIDO'
            AND YEAR(f2.fechafactura) = YEAR(f.fechafactura)
            AND MONTH(f2.fechafactura) = MONTH(f.fechafactura)
        ORDER BY JSON_UNQUOTE(JSON_EXTRACT(r2.jsondte, '$.identificacion.codigoGeneracion')) ASC
        LIMIT 1
    ) AS primer_codigo_generacion,
    (
        SELECT JSON_UNQUOTE(JSON_EXTRACT(r3.jsondte, '$.identificacion.codigoGeneracion'))
        FROM respuestadte r3
        INNER JOIN factura f3 ON f3.id = r3.id_factura
        WHERE 
            r3.estado = 'PROCESADO'
            AND r3.selloRecibido IS NOT NULL
            AND TRIM(r3.selloRecibido) <> ''
            AND r3.descripcionMsg = 'RECIBIDO'
            AND YEAR(f3.fechafactura) = YEAR(f.fechafactura)
            AND MONTH(f3.fechafactura) = MONTH(f.fechafactura)
        ORDER BY JSON_UNQUOTE(JSON_EXTRACT(r3.jsondte, '$.identificacion.codigoGeneracion')) DESC
        LIMIT 1
    ) AS ultimo_codigo_generacion
FROM respuestadte r
INNER JOIN factura f ON f.id = r.id_factura
WHERE 
    r.estado = 'PROCESADO'
    AND r.selloRecibido IS NOT NULL
    AND TRIM(r.selloRecibido) <> ''
    AND r.descripcionMsg = 'RECIBIDO'
    AND YEAR(f.fechafactura) = $anioFiltro
    AND MONTH(f.fechafactura) = $mesFiltro
    $condicionComprobante
    AND JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.codigoGeneracion'))  IN (
        SELECT codigoGeneracion FROM invalidaciones
    )
ORDER BY CAST(JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.numeroControl')) AS UNSIGNED) ASC
";

$result = $conexion->query($sql);

// Encabezados para CSV
header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename=invalidaciones_$anioFiltro-$mesFiltro.csv");

// Salida
$output = fopen('php://output', 'w');


// Filas
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['numeroControl'],
        '4',
        '0',
        '0',
        ($filtroc != "0" ? $filtroc : ""),
        'D',
        $row['selloRecibido'],
        '0',
        '0',
        $row['codigoGeneracion']
    ]);
}

fclose($output);
?>