<?php
require('factura/fpdf/fpdf.php');
require('conexionfin.php');

if (!isset($_POST['producto_id'])) {
    die("Error: No se especificó producto.");
}

$producto_id = intval($_POST['producto_id']);

$sql = "SELECT 
    pr.cod_producto,
    pr.descripcion,
    c.nombre_cliente,
    p.numero_pedido,
    c.ciudad,
    pr.ref_2,
    pr.calibre,
    pr.umb,
    pr.und_embalaje_minima,
    pr.peso_kg_paca_caja
FROM detalle_pedidos dp
INNER JOIN pedidos p ON p.id = dp.pedido_id
INNER JOIN producto pr ON pr.id_producto = dp.producto_id
INNER JOIN clientes c ON c.id = p.cliente_id
WHERE pr.id_producto = $producto_id
LIMIT 1";

$data = $conexion->query($sql)->fetch_assoc();

if (!$data) {
    die("No se encontraron datos para el producto seleccionado.");
}

// ---------- Clase personalizada ----------
class PDF extends FPDF {
    function Header() {
        // Pequeño logo centrado arriba
        if(file_exists('img/logo.png')) {
            $this->Image('img/logo.png', 5, 3, 15); // más pequeño
        }
        $this->Ln(15);
    }

    function Footer() {
        // Sin footer, porque la etiqueta es muy pequeña
    }
}

// ---------- Crear PDF ----------
$pdf = new PDF('L', 'mm', array(100, 50)); // 10 cm x 5 cm (horizontal)
$pdf->AddPage();

// Configuración base
$pdf->SetMargins(3, 3, 3);
$pdf->SetAutoPageBreak(false);
$pdf->SetFont('Arial', '', 8);

// ---------- CONTENIDO ----------
$pdf->SetTextColor(0, 51, 102);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, utf8_decode("Etiqueta de Producto"), 0, 1, 'C');
$pdf->Ln(1);

// Caja de fondo suave
$pdf->SetFillColor(245, 247, 250);
$pdf->Rect(3, 10, 94, 35, 'F');

// Contenido
$pdf->SetXY(5, 12);
$pdf->SetTextColor(0, 0, 0);

// Código y referencia
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 5, utf8_decode($data['ref_2'] . ' - ' . $data['cod_producto']), 0, 1, 'C');

// Ciudad
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 4, utf8_decode($data['ciudad']), 0, 1, 'C');

// Descripción
$pdf->SetFont('Arial', '', 7);
$pdf->MultiCell(0, 3.5, utf8_decode("Desc: {$data['descripcion']}"), 0, 'C');

// Calibre y UMB
$pdf->Ln(1);
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(0, 4, utf8_decode("Calibre: {$data['calibre']}  |  UMB: {$data['umb']}"), 0, 1, 'C');

// Embalaje y peso
$pdf->Cell(0, 4, utf8_decode("Emb: {$data['und_embalaje_minima']} und.  |  Peso: {$data['peso_kg_paca_caja']} kg"), 0, 1, 'C');

// Cliente y pedido
$pdf->Ln(1);
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(0, 4, utf8_decode("Cliente: {$data['nombre_cliente']}"), 0, 1, 'C');
$pdf->Cell(0, 4, utf8_decode("Pedido #: {$data['numero_pedido']}"), 0, 1, 'C');

// ---------- Salida ----------
$pdf->Output('I', 'producto_'.$producto_id.'.pdf');
?>
