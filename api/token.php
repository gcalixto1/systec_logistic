<?php
require_once __DIR__ . '/../config/config.php'; // Asegúrate de que este archivo tenga las constantes necesarias

/**
 * Lee el token desde el archivo cache
 *  
 * @return string|null Token válido o null si no hay o está expirado
 */
function leerTokenCache()
{
    if (!file_exists(TOKEN_CACHE_FILE)) {
        return null;
    }

    $data = json_decode(file_get_contents(TOKEN_CACHE_FILE), true);
    if (!$data || !isset($data['token'], $data['expires_at'])) {
        return null;
    }

    // Verificar si el token aún es válido
    if (time() < $data['expires_at']) {
        return $data['token'];
    }

    return null;
}

/**
 * Guarda el token en archivo cache
 * 
 * @param string $token
 */
function guardarTokenCache($token)
{
    $data = [
        'token' => $token,
        'expires_at' => time() + (TOKEN_CACHE_MINUTES * 60) // minutos definidos en config.php
    ];
    file_put_contents(TOKEN_CACHE_FILE, json_encode($data));
}

/**
 * Solicita un nuevo token a la API de Hacienda
 * 
 * @return string|null Token válido o null si falla
 */
function obtenerTokenDesdeAPI()
{
    $postData = http_build_query([
        'user' => MH_USER,
        'pwd' => MH_PWD,
        'grant_type' => MH_GRANT_TYPE
    ]);

    $headers = [
        "Accept: application/json",
        "User-Agent: WPFApp/1.0",
        "Content-Type: application/x-www-form-urlencoded",
        "Content-Length: " . strlen($postData)
    ];

    $ch = curl_init(MH_API_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return null;
    }

    curl_close($ch);

    if ($httpCode !== 200) {
        return null;
    }

    $json = json_decode($response, true);

    // Obtener el token desde el body
    $token = $json['body']['token'] ?? null;

    if (!$token || stripos($token, 'Bearer ') !== 0) {
        return null;
    }

    // Quitar "Bearer " del inicio
    $cleanToken = trim(substr($token, 7));
    guardarTokenCache($cleanToken);

    return $cleanToken;
}