<?php
require('factura/fpdf/fpdf.php');
include("conexionfin.php");

// Datos generales
$usuario = "Gerson Calixto";
$fecha = date("d/m/Y H:i");

// Consulta del inventario
$sql = "
SELECT 
    p.str_id,
    p.cod_producto,
    p.descripcion,
    i.lote,
    i.stock_unidades_kg,
    i.stock_caja_paca_bobinas
FROM inventario i
INNER JOIN producto p ON i.producto_id = p.id_producto
WHERE i.stock_unidades_kg > 0 OR i.stock_caja_paca_bobinas > 0
ORDER BY p.cod_producto, i.lote
";
$result = $conexion->query($sql);

class PDF extends FPDF {
    var $widths;
    var $aligns;

    // --- Dibujar rectángulo con esquinas redondeadas ---
    function RoundedRect($x, $y, $w, $h, $r = 5, $style = '') {
        $k = $this->k;
        $hp = $this->h;
        if($style=='F') $op='f';
        elseif($style=='FD' || $style=='DF') $op='B';
        else $op='S';
        $MyArc = 4/3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m', ($x+$r)*$k, ($hp-$y)*$k ));
        $xc = $x + $w - $r; $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k, ($hp-$y)*$k ));
        $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
        $xc = $x + $w - $r; $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', ($x+$w)*$k, ($hp-$yc)*$k ));
        $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
        $xc = $x + $r; $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k, ($hp-($y+$h))*$k ));
        $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
        $xc = $x + $r; $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $x*$k, ($hp-$yc)*$k ));
        $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3) {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
            $x1*$this->k, ($h-$y1)*$this->k,
            $x2*$this->k, ($h-$y2)*$this->k,
            $x3*$this->k, ($h-$y3)*$this->k));
    }

    // --- Config tabla ajustable ---
    function SetWidths($w) { $this->widths = $w; }
    function SetAligns($a) { $this->aligns = $a; }

    function Row($data) {
        $nb = 0;
        for($i=0;$i<count($data);$i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 6 * $nb;
        $this->CheckPageBreak($h);
        for($i=0;$i<count($data);$i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Rect($x, $y, $w, $h);
            $this->MultiCell($w, 6, utf8_decode($data[$i]), 0, $a);
            $this->SetXY($x + $w, $y);
        }
        $this->Ln($h);
    }

    function CheckPageBreak($h) {
        if($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt) {
        $cw = &$this->CurrentFont['cw'];
        if($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2*$this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if($nb > 0 && $s[$nb - 1] == "\n") $nb--;
        $sep = -1; $i = 0; $j = 0; $l = 0; $nl = 1;
        while($i < $nb) {
            $c = $s[$i];
            if($c == "\n") { $i++; $sep = -1; $j = $i; $l = 0; $nl++; continue; }
            if($c == ' ') $sep = $i;
            $l += $cw[$c];
            if($l > $wmax) {
                if($sep == -1) { if($i == $j) $i++; }
                else $i = $sep + 1;
                $sep = -1; $j = $i; $l = 0; $nl++;
            } else $i++;
        }
        return $nl;
    }

    // --- Encabezado ---
    function Header() {
        global $fecha, $usuario;
        // Borde exterior redondeado
        $this->RoundedRect(5, 5, 287, 200, 5);

       

        // Título
        $this->SetFont('Arial','B',14);
        $this->Cell(0,8,utf8_decode('Reporte de Inventario Físico por Lote'),0,1,'C');
        $this->SetFont('Arial','',10);
        $this->Cell(0,6,utf8_decode("Fecha: $fecha    |   Usuario: $usuario"),0,1,'C');
        
 // Logo
        $this->Image('img/logo.png', 35, 10, 55);
        $this->Ln(4);
        $this->Ln(4);
        
        // Encabezado de tabla
        $this->SetFont('Arial','B',9);
        $this->SetFillColor(220,220,220);
        $this->Cell(20,12,'STR ID',1,0,'C',true);
        $this->Cell(30,12,'Codigo',1,0,'C',true);
        $this->Cell(100,12,utf8_decode('Descripcion'),1,0,'C',true);
        $this->Cell(25,12,'Lote',1,0,'C',true);
        $this->Cell(30,12,utf8_decode('Stock (Und/Kg)'),1,0,'C',true);
        $this->Cell(30,12,utf8_decode('Stock (Cajas)'),1,0,'C',true);
        $this->Cell(30,12,utf8_decode('Cant. Física'),1,1,'C',true);
    }

    // --- Pie de página ---
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,utf8_decode('Página ') . $this->PageNo() . '/{nb}',0,0,'C');
    }
}

// Crear PDF
$pdf = new PDF('L','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',9);
$pdf->SetWidths(array(20,30,100,25,30,30,30));
$pdf->SetAligns(array('C','C','L','C','R','R','C'));

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $pdf->Row(array(
            $row['str_id'],
            $row['cod_producto'],
            $row['descripcion'],
            $row['lote'],
            number_format($row['stock_unidades_kg'],2),
            number_format($row['stock_caja_paca_bobinas'],2),
            ''
        ));
    }
} else {
    $pdf->Cell(265,12,utf8_decode('No hay registros de inventario disponibles.'),1,1,'C');
}

$pdf->Output('I', 'inventario_fisico.pdf');
?>
