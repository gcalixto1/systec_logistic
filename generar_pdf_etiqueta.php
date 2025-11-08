<?php
require('factura/fpdf/fpdf.php');
require('conexionfin.php');

// ------------------------------------
// VALIDAR PRODUCTO
if (!isset($_POST['producto_id'])) {
    die("Error: No se especificó producto.");
}
$producto_id = intval($_POST['producto_id']);

// ------------------------------------
// CONSULTA SQL
$sql = "SELECT 
    CONCAT(
        LPAD(dp.id, 2, '0'), ' - ',
        'L', pr.calibre,
        pr.str_id,
        pr.ref_1,
        pr.ref_2,
        pr.relacion,
        DATE_FORMAT(pr.fecha_creacion, '%d%m%y')
    ) AS Campo_1,
    UPPER(c.nombre_cliente) AS Campo_2,
    CONCAT(
        pr.str_id, ' ', pr.relacion
    ) AS Campo_3,
    UPPER(c.ciudad) AS Campo_4,
    pr.descripcion,
    pr.calibre,
    pr.umb,
    pr.und_embalaje_minima,
    pr.peso_kg_paca_caja,
    p.numero_pedido,
    u.nombre AS usuario,
    pr.fecha_creacion
FROM detalle_pedidos dp
INNER JOIN pedidos p ON p.id = dp.pedido_id
INNER JOIN producto pr ON pr.id_producto = dp.producto_id
INNER JOIN clientes c ON c.id = p.cliente_id
INNER JOIN usuario u ON u.idusuario = p.usuario_id
WHERE pr.id_producto = $producto_id
ORDER BY p.fecha_pedido DESC
LIMIT 1";

$resultado = $conexion->query($sql);
if ($resultado->num_rows == 0) {
    die("No se encontraron datos para este producto.");
}
$datos = $resultado->fetch_assoc();

// ------------------------------------
// FORMATO DE FECHA
$fecha = new DateTime($datos['fecha_creacion']);
$fecha_formato = $fecha->format('d/m/Y');

// -------------------- CLASE PDF PERSONALIZADA --------------------
class PDF extends FPDF {
    function Header() {
        if (file_exists('img/logo.png')) {
            $this->Image('img/logo.png', 3, 3, 9);
        }
        $this->Ln(2);
    }
    function Footer() {
        // sin pie
    }
}

// -------------------- CREACIÓN DEL PDF --------------------
// 10 cm ancho x 5 cm alto = 100 mm x 50 mm
$pdf = new PDF('L', 'mm', array(100, 50));
$pdf->AddPage();
$pdf->SetMargins(4, 4, 4);
$pdf->SetAutoPageBreak(false);
$pdf->SetTextColor(0, 0, 0);

// ============================================================
// BLOQUE 1: ENCABEZADO PRINCIPAL
// ============================================================
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(0, 4, utf8_decode($datos['Campo_1']), 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 5, utf8_decode($datos['Campo_2']), 0, 1, 'C');

$pdf->SetFont('Arial', '', 7);
$pdf->Cell(0, 4, utf8_decode($datos['Campo_3']), 0, 1, 'C');

// $pdf->SetFont('Arial', 'I', 6.5);
// $pdf->Cell(0, 3.5, utf8_decode($datos['Campo_4']), 0, 1, 'C');
// $pdf->Ln(1);

// ============================================================
// BLOQUE 2: DESCRIPCIÓN Y DETALLES TÉCNICOS
// ============================================================
$pdf->SetFont('Arial', '', 6.5);
$pdf->SetTextColor(40, 40, 40);

// Descripción centrada con salto automático
$pdf->MultiCell(0, 3.2, utf8_decode("{$datos['descripcion']}"), 0, 'C');
$pdf->Ln(0.5);

// Línea técnica 1
$pdf->Cell(50, 3.2, utf8_decode("Calibre: {$datos['calibre']}"), 0, 0, 'L');
$pdf->Cell(45, 3.2, utf8_decode("UMB: {$datos['umb']}"), 0, 1, 'R');

// Línea técnica 2
$pdf->Cell(50, 3.2, utf8_decode("Embalaje: {$datos['und_embalaje_minima']} und."), 0, 0, 'L');
$pdf->Cell(45, 3.2, utf8_decode("Peso: {$datos['peso_kg_paca_caja']} kg"), 0, 1, 'R');

// Línea técnica 3
$pdf->Cell(50, 3.2, utf8_decode("Pedido #: {$datos['numero_pedido']}"), 0, 0, 'L');
$pdf->Cell(45, 3.2, utf8_decode("Fecha: {$fecha_formato}"), 0, 1, 'R');

// Línea técnica 4 (usuario)
$pdf->Cell(0, 3.2, utf8_decode("Usuario: {$datos['usuario']}"), 0, 1, 'C');

// ============================================================
// BLOQUE 3: DECORACIÓN (línea o detalle visual opcional)
// ============================================================
$pdf->SetDrawColor(180, 180, 180);
$pdf->Line(5, 45, 95, 45); // línea gris al final como separador

// -------------------- SALIDA --------------------
$pdf->Output('I', 'Etiqueta_Produccion_'.$producto_id.'.pdf');
?>
