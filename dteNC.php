<?php
include 'conexionfin.php';
require_once 'config/config.php'; // Asegúrate de que este archivo tenga las constantes necesarias

$codigo = $_POST['documentos'] ? json_decode($_POST['documentos'], true)[0]['codigo'] ?? '' : '';
$observaciones = $_POST['descripcion'] ?? '';
$monto = floatval($_POST['monto'] ?? 0);
$codigoGeneracionNC = $_POST['codigoGeneracion'] ?? '';

// OBTENER FACTURA
$query = $conexion->query("SELECT factura.id, factura.tipofactura, factura.numerofactura, 
                                    factura.subtotal, factura.iva_impuesto, factura.totalpagar, 
                                    factura.letras, factura.forma_pago, factura.fechafactura, 
                                    cliente.nombre, cliente.dni, cliente.telefono, cliente.correo,
                                    cliente.dato1, cliente.dato2, cliente.dato3,
                                    cliente_direccion.departamento, cliente_direccion.municipio, 
                                    cliente_direccion.complemento, cliente.tipoDocumento, medio_pago.medio_pago
                                FROM factura 
                                INNER JOIN cliente ON cliente.idcliente = factura.idcliente
                                LEFT JOIN cliente_direccion ON cliente_direccion.cliente_dni = cliente.dni
                                INNER JOIN medio_pago ON medio_pago.codigo = factura.forma_pago
                                INNER JOIN respuestadte ON respuestadte.id_factura = factura.id
                                WHERE respuestadte.codigoGeneracion= '$codigo' limit 1");
$factura = $query->fetch_assoc();

$queryEmpresa = $conexion->query("SELECT * FROM configuracion WHERE id = 1");
$Empresa = $queryEmpresa->fetch_assoc();

$queryActividad = $conexion->query("SELECT descripcion FROM actividad_economica WHERE codigo = $Empresa[giro]");
$ActividaE = $queryActividad->fetch_assoc();
// OBTENER DETALLE DE PRODUCTOS
$items = [];
// $detalle = $conexion->query("SELECT df.cantidad,p.descripcion, df.precioventa FROM detallefactura df inner join producto p ON p.codproducto = df.cod_producto WHERE idfactura = $idfactura");
$numItem = 1;
$totalGravada = 0;
$totalIva = 0;

$sql6 = "SELECT * FROM actividad_economica WHERE codigo = '" . $factura['dato3'] . "'";
$resultado6 = $conexion->query($sql6);
$row6 = $resultado6->fetch_assoc();

$sqlconsecutivo = "SELECT MAX(valor) AS last_id FROM consecutivos WHERE codigo_consecutivo = 'ndc'";
$resultadoconsecutivo = $conexion->query($sqlconsecutivo);
$rowconsecutivo = $resultadoconsecutivo->fetch_assoc();
$number = intval(substr($rowconsecutivo['last_id'], strlen("ndc")));
$newNumber = $number + 1;

$monto = $factura['totalpagar'];
$observaciones = $observaciones . " " . $factura['numerofactura'];
$montoiva = round($monto / 1.13, 2);
$ivaItem = round($montoiva * 0.13, 2);
$items[] = [
    "numItem" => $numItem++,
    "numeroDocumento" => $codigo,
    "tipoItem" => 2,
    "cantidad" => 1,
    "codigo" => null,
    "uniMedida" => 99,
    "codTributo" => null,
    "descripcion" => $observaciones,
    "precioUni" => round($monto / 1.13, 2),
    "montoDescu" => 0,
    "ventaNoSuj" => 0,
    "ventaExenta" => 0,
    "ventaGravada" => round($monto / 1.13, 2),
    "tributos" => ["20"]
];
$totalGravada += $monto;
$totalIva += $ivaItem;

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
$codigoGeneracion = $codigoGeneracionNC;
// ARMAR JSON PARA EL FIRMADOR
$facturaJson = [
    "nit" => MH_USER,
    "activo" => "true",
    "passwordPri" => MH_PWD_DTE,
    "dteJson" => [
        "identificacion" => [
            "version" => 3,
            "ambiente" => MH_AMBIENTE,
            "tipoDte" => "05",
            "numeroControl" => "DTE-05-M001P001-" . str_pad($newNumber, 15, "0", STR_PAD_LEFT),
            "codigoGeneracion" => $codigoGeneracion,
            "tipoModelo" => 1,
            "tipoOperacion" => 1,
            "tipoContingencia" => null,
            "motivoContin" => null,
            "fecEmi" => $fechaEmision,
            "horEmi" => $horaEmision,
            "tipoMoneda" => "USD"
        ],
        "documentoRelacionado" => [
            [
                "tipoDocumento" => "03",
                "tipoGeneracion" => 2,
                "numeroDocumento" => $codigo, // Este es un ejemplo, deberías obtener el número de documento real
                "fechaEmision" => date('Y-m-d', strtotime($factura['fechafactura']))
            ]

        ],
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
            "correo" => $Empresa['email']
        ],
        "receptor" => [
            "nit" => $factura['dni'],
            "nrc" => $factura['dato1'],
            "nombre" => $factura['nombre'],
            "nombreComercial" => $factura['dato2'],
            "codActividad" => $factura['dato3'],
            "descActividad" => $row6['descripcion'],
            "direccion" => [
                "departamento" => $factura['departamento'],
                "municipio" => $factura['municipio'],
                "complemento" => $factura['complemento']
            ],
            "telefono" => $factura['telefono'],
            "correo" => $factura['correo'],
        ],
        "ventaTercero" => null,
        "cuerpoDocumento" => $items,
        "resumen" => [
            "totalNoSuj" => 0,
            "totalExenta" => 0,
            "totalGravada" => round($monto / 1.13, 2),
            "subTotalVentas" => round($monto / 1.13, 2),
            "descuNoSuj" => 0,
            "descuExenta" => 0,
            "descuGravada" => 0,
            "totalDescu" => 0,
            "tributos" => [
                [
                    "codigo" => "20",
                    "descripcion" => "Impuesto al Valor Agregado 13%",
                    "valor" => round($totalIva, 2)
                ]
            ],
            "subTotal" => round($monto / 1.13, 2),
            "ivaPerci1" => 0,
            "ivaRete1" => 0,
            "reteRenta" => 0,
            "montoTotalOperacion" => round($monto / 1.13, 2),
            "totalLetras" => $factura['letras'],
            "condicionOperacion" => 1
        ],
        "extension" => null,
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

$response = [
    'success' => true,
    'message' => 'Nota de crédito creada exitosamente'
];
// SI FIRMA CORRECTAMENTE, ENVIAR A HACIENDA
if ($httpCode === 200) {
    include 'recepciondteNC.php';
    echo json_encode([
        'success' => true,
        'codigo_generacion' => $codigoGeneracion
    ]);
    exit; // Enviar automáticamente a Hacienda después de firmar
} else {
    echo json_encode(['success' => false, 'message' => 'Error al firmar documento', 'detalle' => $response]);
}
echo json_encode($response);

echo "<script>
window.onload = function () {
    setTimeout(() => window.close(), 500); // da tiempo a que se inicie impresión
};
</script>";