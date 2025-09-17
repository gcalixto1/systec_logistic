<?php
include 'conexionfin.php';
require_once 'config/config.php'; // Asegúrate de que este archivo tenga las constantes necesarias

$tipoAnulacion = isset($_POST['tipoInvalidacion']) ? (int) $_POST['tipoInvalidacion'] : 0;
$numeroControl = $_POST['numeroControl'] ?? '';
$codigoGeneracion = $_POST['codigoGeneracion'] ?? '';
$tDcoResponsable = $_POST['tipoDoc2'] ?? '';
$nDcoResponsable = $_POST['documento2'] ?? '';
$nombreResponsable = $_POST['responsable'] ?? '';
$nombreSolicita = $_POST['solicitante'] ?? '';
$tDcoSolicita = $_POST['tipoDoc1'] ?? '';
$nDcoSolicita = $_POST['documento1'] ?? '';
// Validar tipo de anulación
$motivos_validos = [
    '1' => 'Error en la información del Documento Tributario Electrónico a invalidar',
    '2' => 'Recindir de la operación realizada',
    '3' => 'Otro'
];

if (!isset($motivos_validos[$tipoAnulacion])) {
    echo json_encode([
        'success' => false,
        'message' => 'Tipo de anulación no válido.'
    ]);
    exit;
}

$motivo_final = $motivos_validos[$tipoAnulacion];

if (!empty($codigoGeneracion)) {

    $query = $conexion->query("SELECT factura.id, factura.fechafactura, factura.numerofactura, consecutivos.descripcionconse, 
                respuestadte.codigoGeneracion,respuestadte.selloRecibido, cliente.nombre, cliente.dni,factura.totalpagar, medio_pago.medio_pago,respuestadte.jsondte
          FROM factura
          INNER JOIN cliente ON cliente.idcliente = factura.idcliente
          INNER JOIN consecutivos ON consecutivos.codigo_consecutivo = factura.tipofactura
          INNER JOIN respuestadte ON respuestadte.id_factura = factura.id
          INNER JOIN medio_pago ON medio_pago.codigo = factura.forma_pago
          WHERE respuestadte.codigoGeneracion = '$codigoGeneracion'");

    if ($query && $query->num_rows > 0) {
        $resultado = $query->fetch_assoc();
    } else {
        echo "No se encontró información para el código: $codigoGeneracion";
    }
} else {
    echo "Código de generación no proporcionado.";
}

$json = $resultado['jsondte'];

// Verifica que el JSON sea válido
$data = json_decode($json, true);
if (!$data || json_last_error() !== JSON_ERROR_NONE) {
    die("Error al decodificar el JSON");
}

$ident = $data['identificacion'];
$emisor = $data['emisor'];
$receptor = $data['receptor'];
$cuerpo = $data['cuerpoDocumento'];
$resumen = $data['resumen'];
$documentosRelacionados = $data['documentoRelacionado'] ?? [];
$ivaInvalidar = 0;
if ($ident['tipoDte'] == '01') {
    $ivaInvalidar = $resumen['totalIva'] ?? 0;
} elseif ($ident['tipoDte'] == '03') {
    $ivaInvalidar = $resumen['tributos']['valor'] ?? 0;
}

$queryEmpresa = $conexion->query("SELECT * FROM configuracion WHERE id = 1");
$Empresa = $queryEmpresa->fetch_assoc();
// OBTENER TOKEN DE HACIENDA
require_once 'api/token.php';
$token = leerTokenCache();
if (!$token) {
    $token = obtenerTokenDesdeAPI();
}

// GENERAR CÓDIGO Y FECHA
$codigoGeneracion = strtoupper(vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4)));
date_default_timezone_set("America/El_Salvador");
$fechaAnula = date("Y-m-d");  // Formato: 2025-04-18
$horaAnula = date("H:i:s");

// ARMAR JSON PARA EL FIRMADOR
$facturaJson = [
    "nit" => MH_USER,
    "activo" => "true",
    "passwordPri" => MH_PWD_DTE,
    "dteJson" => [
        "identificacion" => [
            "version" => 2,
            "ambiente" => MH_AMBIENTE,
            "codigoGeneracion" => $codigoGeneracion,
            "fecAnula" => $fechaAnula,
            "horAnula" => $horaAnula
        ],
        "emisor" => [
            "nit" => MH_USER,
            "nombre" => $Empresa['nombre'],
            "tipoEstablecimiento" => "02",
            "nomEstablecimiento" => $Empresa['razon_social'],
            "telefono" => $Empresa['telefono'],
            "correo" => $Empresa['email'],
            "codEstableMH" => "M001",
            "codEstable" => null,
            "codPuntoVentaMH" => "P001",
            "codPuntoVenta" => null
        ],
        "documento" => [
            "tipoDte" => $ident['tipoDte'],
            "codigoGeneracion" => $ident['codigoGeneracion'],
            "selloRecibido" => $resultado['selloRecibido'],
            "numeroControl" => $ident['numeroControl'],
            "fecEmi" => $ident['fecEmi'],
            "montoIva" => $ivaInvalidar,
            "codigoGeneracionR" => null,
            "tipoDocumento" => $receptor['tipo'],
            "numDocumento" => ($receptor['tipo'] == 13)
                ? $receptor['numDocumento']
                : (($receptor['tipo'] == 36) ? $receptor['nit'] : null),
            "nombre" => $receptor['nombre'],
            "telefono" => $receptor['telefono'],
            "correo" => $receptor['correo'],
        ],
        "motivo" => [
            "tipoAnulacion" => $tipoAnulacion,
            "motivoAnulacion" => $motivo_final,
            "nombreResponsable" => $nombreResponsable,
            "tipDocResponsable" => $tDcoResponsable,
            "numDocResponsable" => $nDcoResponsable,
            "nombreSolicita" => $nombreSolicita,
            "tipDocSolicita" => $tDcoSolicita,
            "numDocSolicita" => $nDcoSolicita,
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
    include 'recepciondteAN.php';
    echo json_encode([
        'success' => true,
    ]);
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Error al firmar documento', 'detalle' => $response]);
}