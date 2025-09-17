<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
require_once __DIR__ . '/../config/config.php';

function leerTokenCache()
{
    if (!file_exists(TOKEN_CACHE_FILE))
        return null;
    $data = json_decode(file_get_contents(TOKEN_CACHE_FILE), true);
    if (!$data || !isset($data['token'], $data['expires_at']))
        return null;
    return (time() < $data['expires_at']) ? $data['token'] : null;
}

function obtenerToken()
{
    $token = leerTokenCache();
    if (!$token) {
        $resp = file_get_contents(__DIR__ . '/token.php');
        $data = json_decode($resp, true);
        $token = $data['token'] ?? null;
    }
    return $token;
}

function validarDTE($dte)
{
    $errores = [];

    if (!isset($dte['version'])) {
        $errores[] = "Falta el campo 'version'";
    }

    if (!isset($dte['ambiente'])) {
        $errores[] = "Falta el campo 'ambiente'";
    }

    if (!isset($dte['documento']) || !is_array($dte['documento'])) {
        $errores[] = "Falta el objeto 'documento'";
        return $errores;
    }

    $doc = $dte['documento'];

    if (!isset($doc['tipoDte'])) {
        $errores[] = "Falta 'tipoDte' en 'documento'";
    }

    if (!isset($doc['fechaEmision'])) {
        $errores[] = "Falta 'fechaEmision' en 'documento'";
    }

    if (!isset($doc['emisor']) || !is_array($doc['emisor'])) {
        $errores[] = "Falta el objeto 'emisor' en 'documento'";
        return $errores;
    }

    $emisor = $doc['emisor'];

    if (empty($emisor['nit'])) {
        $errores[] = "Falta 'nit' del emisor";
    }

    if (empty($emisor['nrc'])) {
        $errores[] = "Falta 'nrc' del emisor";
    }

    if (empty($emisor['nombre'])) {
        $errores[] = "Falta 'nombre' del emisor";
    }

    return $errores;
}

// Validar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

// Leer el DTE JSON
$input = file_get_contents("php://input");
$dte = json_decode($input, true);

if (!$dte) {
    http_response_code(400);
    echo json_encode(["error" => "JSON inválido o vacío"]);
    exit;
}

// Validar estructura del DTE
$errores = validarDTE($dte);
if (count($errores) > 0) {
    http_response_code(422); // Unprocessable Entity
    echo json_encode(["error" => "DTE inválido", "detalles" => $errores]);
    exit;
}

// Obtener token
$token = obtenerToken();
if (!$token) {
    http_response_code(401);
    echo json_encode(["error" => "Token no disponible"]);
    exit;
}

// Enviar a Hacienda
$headers = [
    "Authorization: Bearer " . $token,
    "Content-Type: application/json",
    "Accept: application/json"
];

$ch = curl_init(MH_ENVIO_DTE_URL);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($dte),
    CURLOPT_HTTPHEADER => $headers
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode(["error" => curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);

// Devolver respuesta de Hacienda
http_response_code($httpCode);
echo $response;