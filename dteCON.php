<?php
include 'conexionfin.php';
require_once 'config/config.php'; // Asegúrate de que este archivo tenga las constantes necesarias

$codigoGeneracion = isset($_POST['codigo']) ? $_POST['codigo'] : '';
$fechaIni = isset($_POST['fechaIni']) ? $_POST['fechaIni'] : '';
$fechaFin = isset($_POST['fechaFin']) ? $_POST['fechaFin'] : '';
$horaIni = isset($_POST['horaIni']) ? $_POST['horaIni'] : '';
$horaFin = isset($_POST['horaFin']) ? $_POST['horaFin'] : '';
$responsable = isset($_POST['responsable']) ? $_POST['responsable'] : '';
$Doc = isset($_POST['documento2']) ? $_POST['documento2'] : '';
$tipoDoc = isset($_POST['tipoDoc']) ? $_POST['tipoDoc'] : '';
$tcontingencia = isset($_POST['tcontingencia']) ? intval($_POST['tcontingencia']) : 0;
$tipoF = isset($_POST['tipoF']) ? $_POST['tipoF'] : '';
// echo "<pre>";
// print_r($codigoGeneracion);
// echo "</pre>";
if ($tipoF === "Comprobante de Credito Fiscal") {
    $tipoF = "03";
} elseif ($tipoF === "Factura") {
    $tipoF = "01";
}
// OBTENER FACTURA
// $query = $conexion->query("SELECT *
//                                   FROM lista_contingencia_dte 
//                                   WHERE lista_contingencia_dte.codigoGeneracion = $codigoGeneracion");
// $factura = $query->fetch_assoc();

$queryEmpresa = $conexion->query("SELECT * FROM configuracion WHERE id = 1");
$Empresa = $queryEmpresa->fetch_assoc();

$querycatalogo = $conexion->query("SELECT valores FROM cta_contingencias WHERE codigo = $tcontingencia");
$tipoContingencia = $querycatalogo->fetch_assoc();
// OBTENER DETALLE DE PRODUCTOS
$items = [];
$numItem = 1;

$items[] = [
    "noItem" => $numItem++,
    "codigoGeneracion" => $codigoGeneracion,
    "tipoDoc" => $tipoF,
];

// OBTENER TOKEN DE HACIENDA
require_once 'api/token.php';
$token = leerTokenCache();
if (!$token) {
    $token = obtenerTokenDesdeAPI();
}

// GENERAR CÓDIGO Y FECHA
date_default_timezone_set("America/El_Salvador");
$fechaTransmision = date("Y-m-d");  // Formato: 2025-04-18
$horaTransmision = date("H:i:s");
// ARMAR JSON PARA EL FIRMADOR
$facturaJson = [
    "nit" => MH_USER,
    "activo" => "true",
    "passwordPri" => MH_PWD_DTE,
    "dteJson" => [
        "identificacion" => [
            "version" => 3,
            "ambiente" => MH_AMBIENTE,
            "codigoGeneracion" => $codigoGeneracion,
            "fTransmision" => $fechaTransmision,
            "hTransmision" => $horaTransmision
        ],
        "emisor" => [
            "nit" => MH_USER,
            "nombre" => $Empresa['nombre'],
            "nombreResponsable" => $responsable,
            "tipoDocResponsable" => $tipoDoc,
            "numeroDocResponsable" => $Doc,
            "tipoEstablecimiento" => '02',
            "telefono" => $Empresa['telefono'],
            "codPuntoVenta" => null,
            "codEstableMH" => null,
            "correo" => $Empresa['email']
        ],
        "detalleDTE" => $items,
        "motivo" => [
            "fInicio" => $fechaIni,
            "fFin" => $fechaFin,
            "hInicio" => $horaIni,
            "hFin" => $horaFin,
            "tipoContingencia" => $tcontingencia,
            "motivoContingencia" => $tipoContingencia['valores'],
        ]
    ]
];

// ENVIAR A FIRMADOR
$curl = curl_init(MH_API_FIRMADOR);
curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($facturaJson, JSON_UNESCAPED_UNICODE),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ]
]);
file_put_contents('cache_estructura.json', json_encode($facturaJson, JSON_UNESCAPED_UNICODE));
$response = curl_exec($curl);

if (curl_errno($curl)) {
    echo json_encode(['success' => false, 'message' => 'CURL error: ' . curl_error($curl)]);
    curl_close($curl);
    exit;
}

// GUARDAR RESPUESTA DEL FIRMADOR EN CACHE
file_put_contents('cache_firmador.json', $response);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

// SI FIRMA CORRECTAMENTE, ENVIAR A HACIENDA
if ($httpCode === 200) {
    include 'recepciondteCON.php'; // Enviar automáticamente a Hacienda después de firmar
    echo json_encode([
        'success' => true,
        'codigo_generacion' => $codigoGeneracion
    ]);
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Error al firmar documento', 'detalle' => $response]);
}