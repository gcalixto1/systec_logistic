<?php
include 'conexionfin.php';
require_once 'config/config.php';

$idfactura = isset($_POST['idfactura']) ? intval($_POST['idfactura']) : 0;

// OBTENER FACTURA
$query = $conexion->query("SELECT factura.id, factura.tipofactura, factura.numerofactura, 
                                    factura.subtotal, factura.iva_impuesto, factura.totalpagar, 
                                    factura.letras, factura.forma_pago, factura.fechafactura, 
                                    cliente.nombre, cliente.dni, cliente.telefono, cliente.correo,
                                    cliente.dato1, cliente.dato2, cliente.dato3,
                                    cliente_direccion.departamento, cliente_direccion.municipio, 
                                    cliente_direccion.complemento, cliente.tipoDocumento, medio_pago.medio_pago,
                                    CAST(TRIM(LEADING '0' FROM SUBSTRING(consecutivos.valor, 4)) AS UNSIGNED) AS numero_consecutivo
                                FROM factura 
                                INNER JOIN cliente ON cliente.idcliente = factura.idcliente
                                LEFT JOIN cliente_direccion ON cliente_direccion.cliente_dni = cliente.dni
                                INNER JOIN medio_pago ON medio_pago.codigo = factura.forma_pago
                                INNER JOIN consecutivos ON consecutivos.valor = factura.numerofactura
                                WHERE factura.id = $idfactura AND CAST(TRIM(LEADING '0' FROM SUBSTRING(consecutivos.valor, 4)) AS UNSIGNED) > 0");
$factura = $query->fetch_assoc();

$queryEmpresa = $conexion->query("SELECT * FROM configuracion WHERE id = 1");
$Empresa = $queryEmpresa->fetch_assoc();

$queryActividad = $conexion->query("SELECT descripcion FROM actividad_economica WHERE codigo = $Empresa[giro]");
$ActividaE = $queryActividad->fetch_assoc();

// OBTENER DETALLE DE PRODUCTOS
$items = [];
$detalle = $conexion->query("SELECT 
    df.cantidad, 
    p.descripcion, 
    df.precioventa, 
    p.exentas, 
    p.iva, 
    p.tipo_producto, 
    p.codBarra 
    FROM detallefactura df 
    INNER JOIN producto p ON p.codproducto = df.cod_producto 
    WHERE idfactura = $idfactura");

$numItem = 1;
$totalGravada = 0;
$totalExenta = 0;
$totalIva = 0;

while ($row = $detalle->fetch_assoc()) {
    $cantidad = floatval($row['cantidad']);
    $precioUnitario = floatval($row['precioventa']);
    $esExento = intval($row['exentas']) === 1;
    $ivaPorcentaje = isset($row['iva']) ? floatval($row['iva']) : 0;
    $ivaDecimal = $ivaPorcentaje / 100;

    $monto = $cantidad * $precioUnitario;

    if ($esExento) {
        $ventaExenta = round($monto, 2);
        $ivaItem = 0;
        $ventaGravada = 0;
        $totalExenta += $ventaExenta;
    } else {
        $montoSinIVA = round($monto / (1 + $ivaDecimal), 2);
        $ivaItem = round($monto - $montoSinIVA, 2);
        $ventaGravada = round($monto, 2);
        $ventaExenta = 0;
        $totalGravada += $ventaGravada;
        $totalIva += $ivaItem;
    }

    $items[] = [
        "numItem" => $numItem++,
        "numeroDocumento" => null,
        "tipoItem" => intval($row['tipo_producto']),
        "cantidad" => $cantidad,
        "codigo" => $row['codBarra'],
        "uniMedida" => 59,
        "descripcion" => $row['descripcion'],
        "precioUni" => round($precioUnitario, 2),
        "montoDescu" => 0,
        "codTributo" => null,
        "ventaNoSuj" => 0,
        "ventaExenta" => $ventaExenta,
        "ventaGravada" => $ventaGravada,
        "ivaItem" => $ivaItem,
        "tributos" => null,
        "psv" => $esExento ? $ventaExenta : $ventaGravada,
        "noGravado" => 0
    ];
}

// OBTENER TOKEN DE HACIENDA
require_once 'api/token.php';
$token = leerTokenCache();
if (!$token) {
    $token = obtenerTokenDesdeAPI();
}

$codigoGeneracion = strtoupper(vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4)));
date_default_timezone_set("America/El_Salvador");
$fechaEmision = date("Y-m-d");
$horaEmision = date("H:i:s");

$facturaJson = [
    "nit" => MH_USER,
    "activo" => "true",
    "passwordPri" => MH_PWD_DTE,
    "dteJson" => [
        "identificacion" => [
            "version" => 1,
            "ambiente" => MH_AMBIENTE,
            "tipoDte" => "01",
            "numeroControl" => "DTE-01-M001P001-" . str_pad($factura['numero_consecutivo'], 15, "0", STR_PAD_LEFT),
            "codigoGeneracion" => $codigoGeneracion,
            "tipoModelo" => 1,
            "tipoOperacion" => 1,
            "tipoContingencia" => null,
            "motivoContin" => null,
            "fecEmi" => $fechaEmision,
            "horEmi" => $horaEmision,
            "tipoMoneda" => "USD"
        ],
        "documentoRelacionado" => null,
        "emisor" => [
            "nit" => MH_USER,
            "nrc" => str_replace('-', '', $Empresa['dato1']),
            "nombre" => $Empresa['nombre'],
            "codActividad" => $Empresa['giro'],
            "descActividad" => $ActividaE['descripcion'],
            "nombreComercial" => $Empresa['razon_social'],
            "tipoEstablecimiento" => "02",
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
        "receptor" => [
            "nrc" => null,
            "tipoDocumento" => $factura['tipoDocumento'],
            "numDocumento" => $factura['dni'],
            "nombre" => $factura['nombre'],
            "codActividad" => null,
            "descActividad" => null,
            "direccion" => [
                "departamento" => $factura['departamento'],
                "municipio" => $factura['municipio'],
                "complemento" => $factura['complemento']
            ],
            "telefono" => $factura['telefono'],
            "correo" => $factura['correo'],
        ],
        "otrosDocumentos" => null,
        "ventaTercero" => null,
        "cuerpoDocumento" => $items,
        "resumen" => [
            "totalNoSuj" => 0,
            "totalExenta" => round($totalExenta, 2),
            "totalGravada" => round($totalGravada, 2),
            "subTotalVentas" => round($totalExenta + $totalGravada, 2),
            "descuNoSuj" => 0,
            "descuExenta" => 0,
            "descuGravada" => 0,
            "totalDescu" => 0,
            "porcentajeDescuento" => 0,
            "tributos" => null,
            "subTotal" => round($totalExenta + $totalGravada, 2),
            "totalIva" => round($totalIva, 2),
            "ivaRete1" => 0,
            "reteRenta" => 0,
            "montoTotalOperacion" => round($totalExenta + $totalGravada, 2),
            "totalNoGravado" => 0,
            "totalPagar" => round($totalExenta + $totalGravada, 2),
            "totalLetras" => $factura['letras'],
            "saldoFavor" => 0,
            "condicionOperacion" => 1,
            "pagos" => [
                [
                    "codigo" => $factura['forma_pago'],
                    "montoPago" => round($totalExenta + $totalGravada, 2),
                    "referencia" => $factura['medio_pago'],
                    "plazo" => null,
                    "periodo" => null
                ]
            ],
            "numPagoElectronico" => null
        ],
        "extension" => null,
        "apendice" => null
    ]
];

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

file_put_contents('cache_firmador.json', $response);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

if ($httpCode === 200) {
    $jsonFirmado = file_get_contents('cache_firmador.json');
    $jsonDTEFINAL = file_get_contents('cache_estructura.json');

    $dteFirmado = json_decode($jsonFirmado, true);
    $JSONFinalArmado = json_decode($jsonDTEFINAL, true);

    $contenido = file_get_contents('storage/token_cache.json');
    $data = json_decode($contenido, true);
    $tokenHacienda = $data['token'] ?? null;

    $documento_firmado = $dteFirmado['body'];
    $json_DTE_Estructurado = $JSONFinalArmado['dteJson'];

    $data = [
        "ambiente" => MH_AMBIENTE,
        "idEnvio" => "1",
        "version" => "1",
        "tipoDte" => "01",
        "documento" => $documento_firmado
    ];

    $ch = curl_init(MH_ENVIO_DTE_URL);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $tokenHacienda",
        "User-Agent: PHP-cURL"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        echo json_encode(['success' => false, 'origen' => 'php', 'message' => curl_error($ch)]);
        exit;
    }

    $respuesta = json_decode($response, true);
    $selloRecibido = $respuesta['selloRecibido'] ?? null;

    // Insertar en la base de datos
    $stmt = $conexion->prepare("INSERT INTO respuestadte (
        id_factura, version, ambiente, versionApp, estado, codigoGeneracion, selloRecibido,
        fhProcesamiento, clasificaMsg, codigoMsg, descripcionMsg, observaciones,jsondte
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $version = $respuesta['version'] ?? null;
    $ambiente = $respuesta['ambiente'] ?? null;
    $versionApp = $respuesta['versionApp'] ?? null;
    $estado = $respuesta['estado'] ?? null;
    $codigoGeneracion = $respuesta['codigoGeneracion'] ?? null;
    $clasificaMsg = $respuesta['clasificaMsg'] ?? null;
    $codigoMsg = $respuesta['codigoMsg'] ?? null;
    $descripcionMsg = $respuesta['descripcionMsg'] ?? null;
    $observaciones = isset($respuesta['observaciones']) ? json_encode($respuesta['observaciones']) : null;
    $jsondte = json_encode($json_DTE_Estructurado) ?? null;
    $fhProcesamiento = isset($respuesta['fhProcesamiento']) ? date("Y-m-d H:i:s", strtotime(str_replace("/", "-", $respuesta['fhProcesamiento']))) : null;

    $stmt->bind_param(
        "iisssssssssss",
        $idfactura,
        $version,
        $ambiente,
        $versionApp,
        $estado,
        $codigoGeneracion,
        $selloRecibido,
        $fhProcesamiento,
        $clasificaMsg,
        $codigoMsg,
        $descripcionMsg,
        $observaciones,
        $jsondte
    );

    $stmt->execute();
    $stmt->close();
    $conexion->close();

    curl_close($ch);

    echo json_encode([
        'success' => true,
        'idfactura' => $idfactura,
        'selloRecibido' => $selloRecibido
    ]);
    exit;
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Hacienda rechazó el DTE',
        'detalle' => $descripcionMsg
    ]);
}
curl_close($curl);
exit;