<?php
ob_start();
include "conexionfin.php";
require('phpqrcode/qrlib.php');

if (empty($_REQUEST['id'])) {
    echo "No es posible generar la factura.";
    exit;
}

$id_factura = $_GET['id'];

$sql = "SELECT selloRecibido,jsondte FROM respuestadte WHERE id_factura = $id_factura";
$resultado = $conexion->query($sql);
$row = $resultado->fetch_assoc();
$json = $row['jsondte'];



// Verifica que el JSON sea válido
$data = json_decode($json, true);
if (!$data || json_last_error() !== JSON_ERROR_NONE) {
    die("Error al decodificar el JSON");
}

// // === Extraer campos importantes ===
$ident = $data['identificacion'];
$emisor = $data['emisor'];
$receptor = $data['receptor'];
$cuerpo = $data['cuerpoDocumento'];
$resumen = $data['resumen'];

// Obtener datos de configuración, factura, cliente y productos
$config = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM configuracion"));
$venta = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM factura WHERE id = $id_factura"));
$cliente = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM cliente WHERE idcliente = " . $venta['idcliente']));
$detalle = mysqli_query($conexion, "
    SELECT 
        d.cantidad, 
        p.descripcion, 
        p.precio 
    FROM detallefactura d 
    INNER JOIN producto p ON d.cod_producto = p.codproducto 
    WHERE d.idfactura = $id_factura
");

// === Generar QR ===
$contenidoQR = "https://admin.factura.gob.sv/consultaPublica?ambiente={$ident['codigoGeneracion']}&codGen={$ident['codigoGeneracion']}&fechaEmi={$ident['fecEmi']}";
$archivoQR = 'qr_temp.png';
QRcode::png($contenidoQR, $archivoQR, QR_ECLEVEL_H, 4);


// Incluir FPDF
require_once 'factura/fpdf/fpdf.php';

// Código QR


$pdf = new FPDF('P', 'mm', array(80, 200));
$pdf->AddPage();
$pdf->SetMargins(5, 5, 5);


// ===== ENCABEZADO =====
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, utf8_decode($config['nombre']), 0, 1, 'C');
$pdf->SetFont('Arial', '', 8);
$pdf->MultiCell(0, 4, utf8_decode("Dirección: " . $config['direccion']), 0, 'C');
$pdf->MultiCell(0, 4, utf8_decode("Giro: " . $config['giro']), 0, 'C');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 5, utf8_decode("N° Registro: " . $config['dato1'] . '     ' . 'NIT:' . $config['dni']), 0, 1, 'C');
$pdf->Cell(0, 3, utf8_decode("Resolución : " . $config['dato2'] . '     '), 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode("De : " . $config['dato3'] . '     ' . 'Al :' . $config['dato4']), 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode("Fecha de Resolución: " . $config['dato5']), 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode("Teléfono: " . $config['telefono'] . '     ' . 'Cel: 7962-2119'), 0, 1, 'C');

$pdf->Ln(1);
$pdf->Cell(0, 0, "-------------------------------------------------------------------------", 0, 1, 'C');
$pdf->Ln(2);

// ===== DATOS DE FACTURA =====
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(0, 5, utf8_decode("Documento N°: ") . $venta['numerofactura'], 0, 1, 'L');
$pdf->Cell(0, 1.5, "Fecha: " . $venta['fechafactura'], 0, 1, 'L');
$pdf->Ln(1);

// ===== DATOS DEL CLIENTE =====

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(0, 8, "Cliente:", 0, 1, 'L');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 0.1, utf8_decode($cliente['nombre']), 0, 1, 'L');
$pdf->Cell(0, 7, "Tel: " . $cliente['telefono'], 0, 1, 'L');
$pdf->Cell(0, 0, "---------------------------------------------------------------------------------", 0, 1, 'C');
$pdf->Ln(2);

// ===== DETALLE DE PRODUCTOS =====
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(40, 5, "Producto", 0, 0, 'L');
$pdf->Cell(10, 5, "Cant", 0, 0, 'C');
$pdf->Cell(10, 5, "Precio", 0, 0, 'R');
$pdf->Cell(10, 5, "Total", 0, 1, 'R');
$pdf->Cell(0, 0, "--------------------------------------------------------------------------", 0, 1, 'C');
$pdf->SetFont('Arial', '', 7);
while ($row = mysqli_fetch_assoc($detalle)) {
    $total = $row['cantidad'] * $row['precio'];
    $pdf->Cell(40, 5, utf8_decode($row['descripcion']), 0, 0, 'L');
    $pdf->Cell(10, 5, $row['cantidad'], 0, 0, 'C');
    $pdf->Cell(10, 5, number_format($row['precio'], 2), 0, 0, 'R');
    $pdf->Cell(10, 5, number_format($total, 2), 0, 1, 'R');
    $pdf->Cell(0, 0, "------------------------------------------------------------------------------------", 0, 1, 'C');
}
if ($venta['tipofactura'] == "fcf") {
    $pdf->Cell(0, 4, "", 0, 1, 'C');
    $pdf->Cell(0, 4, "SubTotal:     $ " . number_format($venta['totalpagar'], 2), 0, 1, 'R');
    $pdf->Cell(0, 4, "Descuento:       $ 0.00", 0, 1, 'R');
    $pdf->Cell(0, 4, "I.V.A:       $ 0.00", 0, 1, 'R');
    $pdf->Ln(1);
}
if ($venta['tipofactura'] == "ccf") {
    $pdf->Cell(0, 4, "", 0, 1, 'C');
    $pdf->Cell(0, 4, "SubTotal:     $ " . number_format($venta['subtotal'], 2), 0, 1, 'R');
    $pdf->Cell(0, 4, "Descuento:       $ 0.00", 0, 1, 'R');
    $pdf->Cell(0, 4, "I.V.A:      $ " . number_format($venta['iva_impuesto'], 2), 0, 1, 'R');
    $pdf->Ln(1);
}

// ===== TOTAL A PAGAR =====
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, "TOTAL: $ " . number_format($venta['totalpagar'], 2), 0, 1, 'R');
$pdf->Ln(1);
$pdf->SetFont('Arial', '', 6);
$pdf->Cell(0, 7, "Son : " . $venta['letras'], 0, 1, 'C');
$pdf->Ln(1);

// ===== MENSAJE DE CIERRE =====
$pdf->SetFont('Arial', '', 8);
$pdf->Image($archivoQR, 25, null, 30, 30, 'PNG');
$pdf->MultiCell(0, 5, utf8_decode("Gracias por su preferencia.\nVuelva pronto."), 0, 'C');

// Salida del PDF
ob_clean();
$pdf->Output("ticket_" . $id_factura . ".pdf", "I");
ob_end_flush();
?>