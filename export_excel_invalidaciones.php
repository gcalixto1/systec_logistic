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
    AND JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.codigoGeneracion')) IN (
        SELECT codigoGeneracion FROM invalidaciones
    )
ORDER BY CAST(JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.numeroControl')) AS UNSIGNED) ASC
";

$result = $conexion->query($sql);

// Encabezados para Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=invalidaciones_$anioFiltro-$mesFiltro.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Encabezado de la tabla
echo "NÚMERO DE RESOLUCIÓN\tCLASE DE DOCUMENTO\tDESDE (PREIMPRESO)\tHASTA (PREIMPRESO)\tTIPO DE DOCUMENTO\tTIPO DE DETALLE\tSERIE\tDESDE\tHASTA\tCÓDIGO DE GENERACIÓN\n";

// Filas
while ($row = $result->fetch_assoc()) {
    echo $row['numeroControl'] . "\t" .
        "4\t" . // CLASE DE DOCUMENTO
        "0\t" . // DESDE (PREIMPRESO)
        "0\t" . // HASTA (PREIMPRESO)
        ($filtroc != "0" ? $filtroc : "") . "\t" . // TIPO DE DOCUMENTO
        "D\t" . // TIPO DE DETALLE
        $row['selloRecibido'] . "\t" . // SERIE
        "0\t" . // DESDE
        "0\t" . // HASTA
        $row['codigoGeneracion'] . "\n";
}
?>