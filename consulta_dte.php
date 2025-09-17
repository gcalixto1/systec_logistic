<?php
include('conexionfin.php');
include('config/config.php');
// Token
$contenido = file_get_contents('storage/token_cache.json');
$data = json_decode($contenido, true);
$tokenHacienda = $data['token'] ?? null;

if (!$tokenHacienda) {
    echo "<div class='alert alert-danger'>No se pudo obtener token de Hacienda</div>";
    exit;
}

// Datos fijos + código de generación recibido por POST
$codigoGeneracion = $_POST['codigoGeneracion'] ?? '';
if (!$codigoGeneracion) {
    echo "<div class='alert alert-warning'>Debe ingresar un código de generación</div>";
    exit;
}

$body = json_encode([
    "nitEmisor" => MH_USER,
    "tdte" => "01",
    "codigoGeneracion" => $codigoGeneracion
]);

$url = "https://apitest.dtes.mh.gob.sv/fesv/recepcion/consultadte/";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $tokenHacienda",
    "Content-Type: application/json",
    "User-Agent: PHP-cURL"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpcode != 202 || !$response) {
    echo "<div class='alert alert-danger'>No se encontraron resultados</div>";
    exit;
}

$respuesta = json_decode($response, true);
?>

<table class="table table-bordered table-responsive">
    <colgroup>
        <col width="5%">
        <col width="10%">
        <col width="10%">
        <col width="35%">
        <col width="10%">
        <col width="35%">
    </colgroup>
    <thead class="thead-dark">
        <tr>
            <th>Versión</th>
            <th>Ambiente</th>
            <th>Estado</th>
            <th>Sello Recibido</th>
            <th>F. Proceso</th>
            <th>Mensaje</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?php echo $respuesta['version'] ?? ''; ?></td>
            <td><?php echo $respuesta['ambiente'] ?? ''; ?></td>
            <td><?php echo $respuesta['estado'] ?? ''; ?></td>
            <td><?php echo $respuesta['selloRecibido'] ?? ''; ?></td>
            <td><?php echo $respuesta['fhProcesamiento'] ?? ''; ?></td>
            <td><?php echo $respuesta['descripcionMsg'] ?? ''; ?></td>
        </tr>
    </tbody>
</table>