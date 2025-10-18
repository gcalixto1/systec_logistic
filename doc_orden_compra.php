<?php
require('factura/fpdf/fpdf.php');
include("conexionfin.php");

class PDF_MC_Table extends FPDF {

    function RoundedRect($x, $y, $w, $h, $r = 3, $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        $op = ($style == 'F') ? 'f' : (($style == 'FD' || $style == 'DF') ? 'B' : 'S');
        $MyArc = 4 / 3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m', ($x + $r) * $k, ($hp - $y) * $k));
        $xc = $x + $w - $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - $y) * $k));
        $this->_Arc($xc + $r * $MyArc, $yc - $r, $xc + $r, $yc - $r * $MyArc, $xc + $r, $yc);
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $yc) * $k));
        $this->_Arc($xc + $r, $yc + $r * $MyArc, $xc + $r * $MyArc, $yc + $r, $xc, $yc + $r);
        $xc = $x + $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - ($y + $h)) * $k));
        $this->_Arc($xc - $r * $MyArc, $yc + $r, $xc - $r, $yc + $r * $MyArc, $xc - $r, $yc);
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $x * $k, ($hp - $yc) * $k));
        $this->_Arc($xc - $r, $yc - $r * $MyArc, $xc - $r * $MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
            $x1 * $this->k, ($h - $y1) * $this->k,
            $x2 * $this->k, ($h - $y2) * $this->k,
            $x3 * $this->k, ($h - $y3) * $this->k));
    }

    function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', (string)$txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }
}

function doc_orden_compra($id_orden, $conexion)
{
    $pdf = new PDF_MC_Table();
    $pdf->AddPage();
    $pdf->SetAutoPageBreak(true, 15);

    // --- Borde general ---
    $pdf->RoundedRect(5, 5, 200, 287, 5);

    // --- Logo ---
    if (file_exists('img/logo.png')) {
        $pdf->Image('img/logo.png', 10, 10, 35);
    }

    // --- Consulta cabecera ---
    $query = $conexion->query("SELECT oc.*, p.nombre_proveedor 
                               FROM orden_compra oc 
                               LEFT JOIN proveedores p ON p.id = oc.proveedor_id 
                               WHERE oc.id_oc = '$id_orden'");
    $oc = $query->fetch_assoc();

    // --- Título ---
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 12, utf8_decode("ORDEN DE COMPRA"), 0, 1, 'C');
    $pdf->Ln(2);

    // --- Datos generales ---
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(100, 6, utf8_decode("Proveedor: " . $oc['nombre_proveedor']), 0, 0);
    $pdf->Cell(90, 6, utf8_decode("No. Orden: " . $oc['numero_oc']), 0, 1, 'R');
    $pdf->Cell(100, 6, utf8_decode("Fecha: " . $oc['fecha_oc']), 0, 1);
    $pdf->Ln(5);

    // --- Encabezado tabla ---
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell(80, 8, utf8_decode("Descripción"), 1, 0, 'C', true);
    $pdf->Cell(25, 8, "Cantidad", 1, 0, 'C', true);
    $pdf->Cell(30, 8, "Costo Unitario", 1, 0, 'C', true);
    $pdf->Cell(25, 8, "IVA", 1, 0, 'C', true);
    $pdf->Cell(30, 8, "Total", 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 10);

    // --- Detalle ---
    $result = $conexion->query("SELECT do.*,p.descripcion FROM orden_compra_detalle do inner join producto p ON p.id_producto = do.producto WHERE id_oc= '$id_orden'");
    while ($row = $result->fetch_assoc()) {

        $nb = max(
            $pdf->NbLines(80, utf8_decode($row['descripcion'])),
            $pdf->NbLines(25, $row['cantidad']),
            $pdf->NbLines(30, number_format($row['precio'], 2)),
            $pdf->NbLines(25, number_format($row['total'] * 0.13, 2)),
            $pdf->NbLines(30, number_format($row['total'], 2))
        );
        $h = 6 * $nb;
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        // --- Descripción con altura adaptada ---
        $pdf->MultiCell(80, 6, utf8_decode($row['descripcion']), 0, 'L');
        $pdf->SetXY($x + 80, $y);
        $pdf->Cell(25, $h, $row['cantidad'], 1, 0, 'C');
        $pdf->Cell(30, $h, number_format($row['precio'], 2), 1, 0, 'R');
        $pdf->Cell(25, $h, number_format($row['total'] * 0.13, 2), 1, 0, 'R');
        $pdf->Cell(30, $h, number_format($row['total'], 2), 1, 1, 'R');

        // --- Borde de la celda de descripción ---
        $pdf->Rect($x, $y, 80, $h);
        $pdf->SetY($y + $h);
    }

    $pdf->Ln(5);

    // --- Totales alineados a la tabla ---
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(125);
    $pdf->Cell(35, 8, "Subtotal:", 1, 0, 'R');
    $pdf->Cell(30, 8, number_format($oc['subtotal'], 2), 1, 1, 'R');
    $pdf->Cell(125);
    $pdf->Cell(35, 8, "IVA:", 1, 0, 'R');
    $pdf->Cell(30, 8, number_format($oc['iva'], 2), 1, 1, 'R');
    $pdf->Cell(125);
    $pdf->Cell(35, 8, "Total:", 1, 0, 'R');
    $pdf->Cell(30, 8, number_format($oc['total'], 2), 1, 1, 'R');

    // --- Observación ---
    if (!empty($oc['observacion'])) {
        $pdf->Ln(8);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 6, utf8_decode("Observaciones:"), 0, 1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->MultiCell(0, 6, utf8_decode($oc['observacion']), 0, 'L');
    }

    // --- Salida ---
    $pdf->Output("I", "Orden_Compra_" . $oc['numero_oc'] . ".pdf");
}

if (isset($_GET['id'])) {
    doc_orden_compra($_GET['id'], $conexion);
}
?>
