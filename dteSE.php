<?php
include 'conexionfin.php';
require_once 'config/config.php'; // Asegúrate de que este archivo tenga las constantes necesarias

ini_set('display_errors', 1);
require 'vendor/autoload.php';

use Luecano\NumeroALetras\NumeroALetras;

$codigoGeneracion = $_POST['codigo_generacion'] ?? '';
$numeroControl = $_POST['numero_control'] ?? '';
$proveedor = $_POST['proveedor'] ?? '';

$queryEmpresa = $conexion->query("SELECT * FROM configuracion WHERE id = 1");
$Empresa = $queryEmpresa->fetch_assoc();

$queryProveedor = $conexion->query("SELECT proveedor.proveedor, proveedor.documento, proveedor.tipoDoc,
                                           cliente_direccion.departamento, cliente_direccion.municipio,cliente_direccion.complemento,
                                           proveedor.telefono, proveedor.correo 
                                           FROM proveedor 
                                           LEFT JOIN cliente_direccion ON cliente_direccion.cliente_dni = proveedor.documento 
                                           WHERE idproveedor = $proveedor");
$ProveedorR = $queryProveedor->fetch_assoc();

$queryActividad = $conexion->query("SELECT descripcion FROM actividad_economica WHERE codigo = $Empresa[giro]");
$ActividaE = $queryActividad->fetch_assoc();

// OBTENER DETALLE DE PRODUCTOS
$items = [];
$detalle = $conexion->query("SELECT cantidad,detalle,precio_unitario,renta_retenida,subtotal,forma_pago FROM sujetoexcluido_dte WHERE numero_control = '" . $numeroControl . "'");
$numItem = 1;
$totalGravada = 0;
$rentaRetenida = 0;
$rentaRetenidaFinal = 0;
$forma = '';
while ($row = $detalle->fetch_assoc()) {
    $monto = floatval($row['cantidad']) * floatval($row['precio_unitario']);
    $rentaRetenida = floatval($row['renta_retenida']);
    $items[] = [
        "numItem" => $numItem++,
        "tipoItem" => 1,
        "codigo" => null,
        "descripcion" => $row['detalle'],
        "cantidad" => floatval($row['cantidad']),
        "uniMedida" => 59,
        "precioUni" => floatval($row['precio_unitario']),
        "montoDescu" => 0,
        "compra" => $monto
    ];
    $totalGravada += $monto;
    $rentaRetenidaFinal += $rentaRetenida;
    $forma = $row['forma_pago'];
}

$queryForma = $conexion->query("SELECT * FROM medio_pago WHERE codigo = '" . $forma . "'");
$Pago = $queryForma->fetch_assoc();
// OBTENER TOKEN DE HACIENDA
require_once 'api/token.php';
$token = leerTokenCache();
if (!$token) {
    $token = obtenerTokenDesdeAPI();
}
// GENERAR CÓDIGO Y FECHA
date_default_timezone_set("America/El_Salvador");
$fechaEmision = date("Y-m-d");  // Formato: 2025-04-18
$horaEmision = date("H:i:s");

$numeroALetras = new NumeroALetras();

// ARMAR JSON PARA EL FIRMADOR
$facturaJson = [
    "nit" => MH_USER,
    "activo" => "true",
    "passwordPri" => MH_PWD_DTE,
    "dteJson" => [
        "identificacion" => [
            "version" => 1,
            "ambiente" => MH_AMBIENTE,
            "tipoDte" => "14",
            "numeroControl" => $numeroControl,
            "codigoGeneracion" => $codigoGeneracion,
            "tipoOperacion" => 1,
            "tipoModelo" => 1,
            "tipoContingencia" => null,
            "motivoContin" => null,
            "fecEmi" => $fechaEmision,
            "horEmi" => $horaEmision,
            "tipoMoneda" => "USD"
        ],
        "emisor" => [
            "nit" => MH_USER,
            "nrc" => str_replace('-', '', $Empresa['dato1']),
            "nombre" => $Empresa['nombre'],
            "codActividad" => $Empresa['giro'],
            "descActividad" => $ActividaE['descripcion'],
            "direccion" => [
                "departamento" => $Empresa['dato6'],
                "municipio" => $Empresa['dato7'],
                "complemento" => $Empresa['direccion']
            ],
            "telefono" => $Empresa['telefono'],
            "correo" => $Empresa['email'],
            "codEstable" => null,
            "codPuntoVenta" => null,
            "codEstableMH" => "M001",
            "codPuntoVentaMH" => "P001"
        ],
        "sujetoExcluido" => [
            "tipoDocumento" => $ProveedorR['tipoDoc'],
            "numDocumento" => str_replace('-', '', $ProveedorR['documento']),
            "nombre" => $ProveedorR['proveedor'],
            "codActividad" => null,
            "descActividad" => null,
            "direccion" => [
                "departamento" => $ProveedorR['departamento'],
                "municipio" => $ProveedorR['municipio'],
                "complemento" => $ProveedorR['complemento']
            ],
            "telefono" => $ProveedorR['telefono'],
            "correo" => $ProveedorR['correo'],
        ],
        "cuerpoDocumento" => $items,
        "resumen" => [
            "totalCompra" => $totalGravada,
            "descu" => 0,
            "totalDescu" => 0,
            "subTotal" => $totalGravada,
            "ivaRete1" => 0,
            "reteRenta" => $rentaRetenidaFinal,
            "totalPagar" => round($totalGravada - $rentaRetenidaFinal, 2),
            "totalLetras" => $numeroALetras->toMoney($totalGravada, 2, 'dolares', 'centavos'),
            "condicionOperacion" => 1,
            "pagos" => [
                [
                    "codigo" => $Pago['codigo'],
                    "montoPago" => round($totalGravada - $rentaRetenidaFinal, 2),
                    "referencia" => $Pago['medio_pago'],
                    "plazo" => null,
                    "periodo" => null
                ]
            ],
            "observaciones" => null,
        ],
        "apendice" => null
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
    include 'recepciondteSE.php'; // Enviar automáticamente a Hacienda después de firmar
    echo json_encode([
        'success' => true,
        'codigo_generacion' => $codigoGeneracion
    ]);
    exit;
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error al firmar documento',
        'detalle' => $response
    ]);
    exit;
}