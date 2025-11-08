<?php
require('factura/fpdf/fpdf.php');
include('conexionfin.php');

function esc($text) { return utf8_decode($text); }

class PDF_OP extends FPDF {

    function RoundedRect($x, $y, $w, $h, $r, $style = '') {
        $k = $this->k;
        $hp = $this->h;
        if ($style == 'F') $op = 'f';
        elseif ($style == 'FD' || $style == 'DF') $op = 'B';
        else $op = 'S';
        $MyArc = 4/3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m', ($x+$r)*$k, ($hp-$y)*$k ));
        $xc = $x+$w-$r; $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k, ($hp-$y)*$k ));
        $this->_Arc($xc+$r*$MyArc, $yc-$r, $xc+$r, $yc-$r*$MyArc, $xc+$r, $yc);
        $xc = $x+$w-$r; $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l', ($x+$w)*$k, ($hp-$yc)*$k ));
        $this->_Arc($xc+$r, $yc+$r*$MyArc, $xc+$r*$MyArc, $yc+$r, $xc, $yc+$r);
        $xc = $x+$r; $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l', ($xc)*$k, ($hp-($y+$h))*$k ));
        $this->_Arc($xc-$r*$MyArc, $yc+$r, $xc-$r, $yc+$r*$MyArc, $xc-$r, $yc+$r);
        $xc = $x+$r; $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l', ($x)*$k, ($hp-($yc))*$k ));
        $this->_Arc($xc-$r, $yc-$r*$MyArc, $xc-$r*$MyArc, $yc-$r, $xc, $yc-$r);
        $this->_out($op);
    }

    function _Arc($x1,$y1,$x2,$y2,$x3,$y3) {
        $k=$this->k; $hp=$this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
            $x1*$k,($hp-$y1)*$k,$x2*$k,($hp-$y2)*$k,$x3*$k,($hp-$y3)*$k));
    }

    function Header() {
        $margin = 8;
        $radius = 6;

        // ▪️ Borde con radio
        $this->SetLineWidth(0.2);
        $this->SetDrawColor(50,50,50);
        $this->RoundedRect($margin, $margin, $this->GetPageWidth() - 2*$margin, $this->GetPageHeight() - 2*$margin, $radius);

        // ▪️ Logo
        $logoPath = __DIR__ . '/logo.png';
        if (file_exists($logoPath)) {
            $this->Image($logoPath, $margin + 4, $margin + 4, 25);
        }

        // ▪️ Título general
        $this->SetFont('Arial','B',15);
        $this->SetXY($margin + 35, $margin + 6);
        $this->Cell($this->GetPageWidth() - 2*($margin + 35), 8, esc('ORDEN DE PRODUCCIÓN'), 0, 1, 'C');
    }

    function Footer() {
        $this->SetY(-12);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,esc('Página ').$this->PageNo().'/{nb}',0,0,'C');
    }

    function NbLines($w,$txt) {
        $cw=&$this->CurrentFont['cw'];
        if($w==0) $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 && $s[$nb-1]=="\n") $nb--;
        $sep=-1; $i=0; $j=0; $l=0; $nl=1;
        while($i<$nb){
            $c=$s[$i];
            if($c=="\n"){ $i++; $sep=-1; $j=$i; $l=0; $nl++; continue; }
            if($c==' ') $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax){
                if($sep==-1){ if($i==$j) $i++; }
                else $i=$sep+1;
                $sep=-1; $j=$i; $l=0; $nl++;
            } else $i++;
        }
        return $nl;
    }
}

// ▪️ Recibir ID de pedido
$idPedido = isset($_GET['id']) ? intval($_GET['id']) : 0;

// ▪️ Obtener datos generales de la OP
$sqlCab = "
SELECT 
    op.numero_op AS id_op,
    m.maquina as nombre_maquina,
    u.nombre AS programado_por,
    DATE_FORMAT(op.fecha_programacion, '%Y-%m-%d %H:%i') AS fecha_creacion
FROM orden_produccion op
INNER JOIN lista_maquina m ON m.id = op.maquina_id
INNER JOIN usuario u ON u.usuario = op.usuario_programo
WHERE op.pedido_id = $idPedido
LIMIT 1;
";
$cab = $conexion->query($sqlCab);
if(!$cab) die('Error al obtener cabecera: '.$conexion->error);
$cabData = $cab->fetch_assoc();

// ▪️ Obtener detalles de la orden
$sqlDet = "
SELECT 
    c.nombre_cliente,
    op.id AS id_op,
    op.cantidad_programada
FROM orden_produccion op
INNER JOIN pedidos p ON p.id = op.pedido_id
INNER JOIN detalle_pedidos dp ON dp.pedido_id = op.pedido_id
INNER JOIN clientes c ON c.id = p.cliente_id
INNER JOIN producto pr ON pr.id_producto = dp.producto_id
WHERE p.id = $idPedido
GROUP BY c.nombre_cliente, op.id
ORDER BY c.nombre_cliente, op.id ASC;
";
$res = $conexion->query($sqlDet);
if(!$res) die('Error al obtener detalles: '.$conexion->error);

// ▪️ Crear PDF
$pdf = new PDF_OP('L','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 18);
$pdf->SetFont('Arial','',10);

// ▪️ Información de cabecera (formato profesional)
$pdf->SetXY(12, 30);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(60,8, esc('N° ORDEN PRODUCCIÓN:'),0,0,'L');
$pdf->SetFont('Arial','',11);
$pdf->Cell(120,8, esc($cabData['id_op'] ?? '---'),0,0,'L');

$pdf->SetFont('Arial','B',11);
$pdf->Cell(40,8, esc('MÁQUINA:'),0,0,'L');
$pdf->SetFont('Arial','',11);
$pdf->Cell(5,8, esc($cabData['nombre_maquina'] ?? '---'),0,0,'L');
$pdf->Ln(4);
$pdf->Ln(4);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(60,8, esc('PROGRAMADO POR:'),0,0,'L');
$pdf->SetFont('Arial','',11);
$pdf->Cell(50,8, esc($cabData['programado_por'] ?? '---'),0,1,'L');

$pdf->SetFont('Arial','B',10);
$pdf->Cell(60,8, esc('FECHA DE CREACIÓN:'),0,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->Cell(50,8, esc($cabData['fecha_creacion'] ?? date('Y-m-d H:i')),0,1,'L');

$pdf->Ln(4);

// ▪️ Tabla de detalle
$w = [
    'cliente'=>75, 'num_bobina'=>25, 'peso_bobina'=>28,
    'h_inicio'=>22, 'h_final'=>22, 'cantidad'=>20,
    'desecho'=>20, 'fecha'=>36, 'turno'=>22
];

$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(230,230,230);
$pdf->SetX(12);
foreach(['CLIENTE','BOBINA','PESO BOBINA','H. INICIO','H. FINAL','CANTIDAD','DESECHO','FECHA','TURNO'] as $i=>$h){
    $pdf->Cell(array_values($w)[$i],8,esc($h),1,0,'C',true);
}
$pdf->Ln();

$pdf->SetFont('Arial','',9);
$num = 1;
$prev_cliente = '';

while($row = $res->fetch_assoc()){
    $cliente = $row['nombre_cliente'];

    if ($cliente !== $prev_cliente) {
        $num = 1; // reinicia numerador
        $prev_cliente = $cliente;
    }

    $lines = $pdf->NbLines($w['cliente'], esc($cliente));
    $h = max(5, 8 * $lines);

    $y = $pdf->GetY();
    $pdf->SetX(12);
    $pdf->MultiCell($w['cliente'],8,esc($cliente),1);
    $pdf->SetXY(12 + $w['cliente'], $y);

    $pdf->Cell($w['num_bobina'], $h, $num++, 1, 0, 'C');
    $pdf->Cell($w['peso_bobina'], $h, '', 1, 0, 'C');
    $pdf->Cell($w['h_inicio'], $h, '', 1, 0, 'C');
    $pdf->Cell($w['h_final'], $h, '', 1, 0, 'C');
    $pdf->Cell($w['cantidad'], $h, '', 1, 0, 'C');
    $pdf->Cell($w['desecho'], $h, '', 1, 0, 'C');
    $pdf->Cell($w['fecha'], $h, '', 1, 0, 'C');
    $pdf->Cell($w['turno'], $h, '', 1, 1, 'C');
}

// ▪️ Observaciones y operador
$pdf->Ln(6);
$pdf->SetFont('Arial','B',9);
$pdf->SetX(12);
$pdf->Cell(180,8, esc('OBSERVACIONES'),1,0,'C',true);
$pdf->Cell(90,8, esc('OPERADOR'),1,1,'C',true);

$pdf->SetFont('Arial','',9);
for ($i=0;$i<5;$i++) {
    $pdf->SetX(12);
    $pdf->Cell(180,10,'',1,0);
    $pdf->Cell(90,10,'',1,1);
}

$pdf->Output('I','orden_produccion.pdf');
exit;
?>
