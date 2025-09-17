<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=anexos_contables_" . date('Y-m-d') . ".xls");

require_once "conexionfin.php";

$anio = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y');
$mes = isset($_GET['mes']) ? intval($_GET['mes']) : date('n');
$filtroc = isset($_GET['filtroc']) ? $_GET['filtroc'] : '0'; // nuevo

// ==== CONDICIÓN PARA FILTRO DE COMPROBANTE ====
$condicionComprobante = "";
if ($filtroc != "0") {
    $condicionComprobante = " AND JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.tipoDte')) = '$filtroc' ";
}

$sql = "
SELECT 
    DATE(f.fechafactura) AS fechaEmision,
    MIN(CAST(JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.codigoGeneracion')) AS CHAR)) AS primer_codigo_generacion,
    MAX(CAST(JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.codigoGeneracion')) AS CHAR)) AS ultimo_codigo_generacion,
    SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.resumen.totalGravada')) AS DECIMAL(18,2))) AS totalGravada,
    SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.resumen.totalPagar')) AS DECIMAL(18,2))) AS totalPagar
FROM 
    respuestadte r
INNER JOIN 
    factura f ON f.id = r.id_factura
WHERE 
    r.estado = 'PROCESADO'
    AND r.selloRecibido IS NOT NULL
    AND TRIM(r.selloRecibido) <> ''
    AND r.descripcionMsg = 'RECIBIDO'
    AND YEAR(f.fechafactura) = $anio
    AND MONTH(f.fechafactura) = $mes
    $condicionComprobante
GROUP BY 
    DATE(f.fechafactura)
ORDER BY 
    DATE(f.fechafactura) ASC
";

$result = $conexion->query($sql);

if (!$result) {
    die("Error en la consulta: " . $conexion->error);
}

echo "<table border='1'>";
echo "<tr>
    <th>FECHA DE EMISIÓN</th>
    <th>CLASE DE DOCUMENTO</th>
    <th>TIPO DE DOCUMENTO</th>
    <th>NÚMERO DE RESOLUCIÓN</th>
    <th>SERIE DE DOCUMENTO</th>
    <th>N° CONTROL INTERNO (DEL)</th>
    <th>N° CONTROL INTERNO (AL)</th>
    <th>N° DOCUMENTO (DEL)</th>
    <th>N° DOCUMENTO (AL)</th>
    <th>N° MAQUINA REGISTRADORA</th>
    <th>VENTAS EXENTAS</th>
    <th>VENTAS INTERNAS EXENTAS NO SUJETAS A PROP.</th>
    <th>VENTAS NO SUJETAS</th>
    <th>VENTAS GRAVADAS LOCALES</th>
    <th>EXPORT. DENTRO ÁREA C.A.</th>
    <th>EXPORT. FUERA ÁREA C.A.</th>
    <th>EXPORT. DE SERVICIOS</th>
    <th>VENTAS ZONAS FRANCAS Y DPA</th>
    <th>VENTAS A CUENTA DE TERCEROS NO DOM.</th>
    <th>TOTAL VENTAS</th>
    <th>TIPO DE OPERACIÓN (Renta)</th>
    <th>TIPO DE INGRESO (Renta)</th>
    <th>NÚMERO DE ANEXO</th>
</tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . date('d/m/Y', strtotime($row['fechaEmision'])) . "</td>";
    echo "<td>4</td>";
    echo "<td>01</td>";
    echo "<td>N/A</td>";
    echo "<td>N/A</td>";
    echo "<td>N/A</td>";
    echo "<td>N/A</td>";
    echo "<td>" . htmlspecialchars($row['primer_codigo_generacion'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['ultimo_codigo_generacion'] ?? '') . "</td>";
    echo "<td>0.00</td>";
    echo "<td>0.00</td>";
    echo "<td>0.00</td>";
    echo "<td>0.00</td>";
    echo "<td>" . number_format($row['totalGravada'] ?? 0, 2) . "</td>";
    echo "<td>0.00</td>";
    echo "<td>0.00</td>";
    echo "<td>0.00</td>";
    echo "<td>0.00</td>";
    echo "<td>0.00</td>";
    echo "<td>" . number_format($row['totalPagar'] ?? 0, 2) . "</td>";
    echo "<td>1</td>";
    echo "<td>3</td>";
    echo "<td></td>";
    echo "</tr>";
}

echo "</table>";
?>