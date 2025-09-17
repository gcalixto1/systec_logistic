<?php

$url = MH_ENVIO_DTE_URL;

// LEER JSON FIRMADO DEL ARCHIVO TEMPORAL
$jsonFirmado = file_get_contents('cache_firmador.json');
$jsonDTEFINAL = file_get_contents('cache_estructura.json');


$dteFirmado = json_decode($jsonFirmado, true);
if (!isset($dteFirmado['body'])) {
    echo json_encode(['success' => false, 'origen' => 'php', 'message' => 'El documento firmado no contiene el campo body']);
    exit;
}

$JSONFinalArmado = json_decode($jsonDTEFINAL, true);
if (!$JSONFinalArmado) {
    echo json_encode(['success' => false, 'origen' => 'php', 'message' => 'No se encontró el archivo cache_firmador.json']);
    exit;
}

if (!isset($JSONFinalArmado['dteJson'])) {
    echo json_encode(['success' => false, 'origen' => 'php', 'message' => 'El documento firmado no contiene el campo dteJson']);
    exit;
}
// LEER TOKEN DESDE CACHE
$contenido = file_get_contents('storage/token_cache.json');
$data = json_decode($contenido, true);
$tokenHacienda = isset($data['token']) ? $data['token'] : null;

if (!$tokenHacienda) {
    echo json_encode(['success' => false, 'origen' => 'php', 'message' => 'No se pudo obtener token de Hacienda']);
    exit;
}

// Generar código de envío si lo necesitas (aunque no se usa aquí)
$codigoGeneracion = strtoupper(vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4)));

$token_hacienda = "Bearer " . $tokenHacienda;
$documento_firmado = $dteFirmado['body']; // <-- AQUÍ ESTABA EL ERROR

$json_DTE_Estructurado = $JSONFinalArmado['dteJson'];
$data = [
    "ambiente" => MH_AMBIENTE,
    "idEnvio" => "1",
    "version" => "3",
    "tipoDte" => "03",
    "documento" => $documento_firmado
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: $token_hacienda",
    "User-Agent: PHP-cURL"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo json_encode(['success' => false, 'origen' => 'php', 'message' => curl_error($ch)]);
} else {
    // DECODIFICAR RESPUESTA
    $respuesta = json_decode($response, true);

    if (!$respuesta || !isset($respuesta['estado'])) {
        echo json_encode(['success' => false, 'origen' => 'php', 'message' => 'Respuesta inválida de Hacienda', 'response' => $response]);
        exit;
    }

    // CONEXIÓN A BASE DE DATOS (ajusta los datos a los tuyos)
    include 'conexionfin.php';

    // FORMATEAR fecha/hora de procesamiento
    $fechaFormateada = null;
    if (isset($respuesta['fhProcesamiento'])) {
        $fechaOriginal = str_replace("/", "-", $respuesta['fhProcesamiento']); // "24/04/2025 19:45:03" → "24-04-2025 19:45:03"
        $fechaFormateada = date("Y-m-d H:i:s", strtotime($fechaOriginal));     // "2025-04-24 19:45:03"
    }

    // ID de factura (ajusta de dónde lo estás obteniendo)
    $id_factura = $_POST['idfactura'] ?? ($_GET['idfactura'] ?? null);

    // INSERTAR EN LA TABLA
    $stmt = $conexion->prepare("INSERT INTO respuestadte (
        id_factura, version, ambiente, versionApp, estado, codigoGeneracion, selloRecibido,
        fhProcesamiento, clasificaMsg, codigoMsg, descripcionMsg, observaciones,jsondte
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $version = $respuesta['version'] ?? null;
    $ambiente = $respuesta['ambiente'] ?? null;
    $versionApp = $respuesta['versionApp'] ?? null;
    $estado = $respuesta['estado'] ?? null;
    $codigoGeneracion = $respuesta['codigoGeneracion'] ?? null;
    $selloRecibido = $respuesta['selloRecibido'] ?? null;
    $clasificaMsg = $respuesta['clasificaMsg'] ?? null;
    $codigoMsg = $respuesta['codigoMsg'] ?? null;
    $descripcionMsg = $respuesta['descripcionMsg'] ?? null;
    $observaciones = isset($respuesta['observaciones']) ? json_encode($respuesta['observaciones']) : null;
    $jsondte = json_encode($json_DTE_Estructurado) ?? null;


    $stmt->bind_param(
        "iisssssssssss",
        $id_factura,
        $version,
        $ambiente,
        $versionApp,
        $estado,
        $codigoGeneracion,
        $selloRecibido,
        $fechaFormateada,
        $clasificaMsg,
        $codigoMsg,
        $descripcionMsg,
        $observaciones,
        $jsondte
    );

    if ($stmt->execute()) {
        // echo json_encode(['success' => true, 'message' => 'Respuesta de Hacienda almacenada correctamente', 'response' => $respuesta]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar respuesta en la base de datos', 'error' => $stmt->error]);
    }

    $stmt->close();
    $conexion->close();

}
curl_close($ch);