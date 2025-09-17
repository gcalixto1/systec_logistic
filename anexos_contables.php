<?php
// ==== CONFIGURAR RANGO DE AÑOS ====
$anio_actual = date("Y");
$mes_actual = date("n");

$anios = range($anio_actual, $anio_actual - 5);

// ==== LISTA DE MESES ====
$meses = [
    1 => "Enero",
    2 => "Febrero",
    3 => "Marzo",
    4 => "Abril",
    5 => "Mayo",
    6 => "Junio",
    7 => "Julio",
    8 => "Agosto",
    9 => "Septiembre",
    10 => "Octubre",
    11 => "Noviembre",
    12 => "Diciembre"
];

// ==== FILTROS DESDE GET O POR DEFECTO ====
$anioFiltro = isset($_GET['anio']) ? (int) $_GET['anio'] : $anio_actual;
$mesFiltro = isset($_GET['mes']) ? (int) $_GET['mes'] : $mes_actual;
$filtroc = isset($_GET['filtroc']) ? $_GET['filtroc'] : '0';

// ==== CONDICIÓN PARA FILTRO DE COMPROBANTE ====
$condicionComprobante = "";
if ($filtroc === "01") {
    $condicionComprobante = " AND JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.tipoDte')) = '01' ";
} elseif ($filtroc === "03") {
    $condicionComprobante = " AND JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.tipoDte')) = '03' ";
}

// ==== CONEXIÓN A BASE DE DATOS ====
include 'conexionfin.php';

// ==== OBTENER DETALLE DEL FILTRO ====
$sqlDetalle = "
    SELECT 
        COUNT(*) AS total_registros,
        MIN(JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.numeroControl'))) AS numero_control_inicial,
        MAX(JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.numeroControl'))) AS numero_control_final
    FROM respuestadte r
    WHERE YEAR(JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.fecEmi'))) = $anioFiltro
      AND MONTH(JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.fecEmi'))) = $mesFiltro
      $condicionComprobante
";
$resultadoDetalle = $conexion->query($sqlDetalle);
$detalle = $resultadoDetalle->fetch_assoc();
?>

<div class="container-fluid mt-4">
    <h4 class="mb-4">📄 Libro de Ventas Anexo Contable</h4>

    <!-- FILTROS -->
    <form id="filtroForm" method="GET" class="row g-3 mb-3">
        <div class="col-md-2">
            <label for="anio" class="form-label">Año</label>
            <select class="form-control" name="anio" id="anio">
                <?php foreach ($anios as $anio): ?>
                <option value="<?= $anio ?>" <?= ($anioFiltro == $anio) ? 'selected' : '' ?>>
                    <?= $anio ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <label for="mes" class="form-label">Mes</label>
            <select class="form-control" name="mes" id="mes">
                <?php foreach ($meses as $num => $nombre): ?>
                <option value="<?= $num ?>" <?= ($mesFiltro == $num) ? 'selected' : '' ?>>
                    <?= $nombre ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <label for="filtroc" class="form-label">Tipo de Comprobante</label>
            <select class="form-control" name="filtroc" id="filtroc">
                <option value="01" <?= ($filtroc == '01') ? 'selected' : '' ?>>Facturas Consumidor Final</option>
                <option value="03" <?= ($filtroc == '03') ? 'selected' : '' ?>>Comprobante de Crédito Fiscal</option>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">_</label>
            <div class="row g-2 align-items-end">
                <div class="col-auto">
                    <button type="button" id="btnFiltrar" class="btn btn-primary w-100">
                        <i class="bi bi-funnel"></i> Filtrar
                    </button>
                </div>
                <div class="col-auto">
                    <button type="button" id="btnExcel" class="btn btn-success w-100">
                        <i class="bi bi-file-earmark-excel"></i> Exportar Excel
                    </button>
                </div>
                <div class="col-auto">
                    <button type="button" id="btnCsv" class="btn btn-warning w-100">
                        <i class="bi bi-filetype-csv"></i> Exportar CSV
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- DETALLE DE REGISTROS -->
    <div class="alert alert-info">
        <strong>Detalle del filtro:</strong><br>
        Total de registros: <?= $detalle['total_registros'] ?><br>
        Número de control inicial: <?= $detalle['numero_control_inicial'] ?: 'N/A' ?><br>
        Número de control final: <?= $detalle['numero_control_final'] ?: 'N/A' ?>
    </div>

    <!-- TABLA DE DOCUMENTOS -->
    <table id="tablaDocumentos" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Fecha Emisión</th>
                <th>Tipo DTE</th>
                <th>Número Control</th>
                <th>NIT/Documento</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // ==== OBTENER REGISTROS PARA TABLA ====
            $sqlDocs = "
                SELECT 
                    JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.fecEmi')) AS fechaEmi,
                    JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.tipoDte')) AS tipoDte,
                    JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.numeroControl')) AS numeroControl,
                    JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.receptor.nit')) AS nit,
                    JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.totales.total')) AS total
                FROM respuestadte r
                WHERE YEAR(JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.fecEmi'))) = $anioFiltro
                  AND MONTH(JSON_UNQUOTE(JSON_EXTRACT(r.jsondte, '$.identificacion.fecEmi'))) = $mesFiltro
                  $condicionComprobante
                ORDER BY fechaEmi ASC
            ";
            $resultadoDocs = $conexion->query($sqlDocs);
            while ($doc = $resultadoDocs->fetch_assoc()):
                ?>
            <tr>
                <td><?= $doc['fechaEmi'] ?></td>
                <td><?= $doc['tipoDte'] ?></td>
                <td><?= $doc['numeroControl'] ?></td>
                <td><?= $doc['nit'] ?></td>
                <td><?= $doc['total'] ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    $('#tablaDocumentos').DataTable();
});
$('#tablaDocumentos').DataTable();

function redirigirFiltro() {
    var anio = document.getElementById('anio').value;
    var mes = document.getElementById('mes').value;
    var filtroc = document.getElementById('filtroc').value;
    window.location.href = "index.php?page=anexos_contables&anio=" + anio + "&mes=" + mes + "&filtroc=" + filtroc;
}

// Cambiar año o mes => redirigir
document.getElementById('anio').addEventListener('change', redirigirFiltro);
document.getElementById('mes').addEventListener('change', redirigirFiltro);

// Botón filtrar => redirigir
document.getElementById('btnFiltrar').addEventListener('click', redirigirFiltro);

// Exportar Excel con filtro
document.getElementById('btnExcel').addEventListener('click', function() {
    var anio = document.getElementById('anio').value;
    var mes = document.getElementById('mes').value;
    var filtroc = document.getElementById('filtroc').value;
    window.location.href = "export_excel.php?anio=" + anio + "&mes=" + mes + "&filtroc=" + filtroc;
});

// Exportar CSV con filtro
document.getElementById('btnCsv').addEventListener('click', function() {
    var anio = document.getElementById('anio').value;
    var mes = document.getElementById('mes').value;
    var filtroc = document.getElementById('filtroc').value;

    var archivo = "export_csv.php"; // por defecto
    if (filtroc === "01") {
        archivo = "export_csv_cf.php"; // consumidor final
    } else if (filtroc === "03") {
        archivo = "export_csv.php"; // comprobante crédito fiscal
    }

    window.location.href = archivo + "?anio=" + anio + "&mes=" + mes + "&filtroc=" + filtroc;
});
</script>