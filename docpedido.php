<?php
require('factura/fpdf/fpdf.php');
include("conexionfin.php");

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,'Pedido',0,1,'C');
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
    }

    function Row($data, $widths, $aligns = [])
    {
        $nb = 0;
        for($i=0;$i<count($data);$i++){
            $nb = max($nb, $this->NbLines($widths[$i], $data[$i]));
        }
        $h = 5 * $nb;

        for($i=0;$i<count($data);$i++){
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Rect($x, $y, $widths[$i], $h);
            $align = isset($aligns[$i]) ? $aligns[$i] : 'L';
            $this->MultiCell($widths[$i], 5, $data[$i], 0, $align);
            $this->SetXY($x + $widths[$i], $y);
        }
        $this->Ln($h);
    }

    function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if($w==0) $w = $this->w-$this->rMargin-$this->x;
        $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
        $s = str_replace("\r",'',$txt);
        $nb = strlen($s);
        if($nb>0 && $s[$nb-1]=="\n") $nb--;
        $sep = -1;
        $i = 0; $j = 0; $l = 0; $nl = 1;
        while($i<$nb){
            $c = $s[$i];
            if($c=="\n"){ $i++; $sep=-1; $j=$i; $l=0; $nl++; continue;}
            if($c==' ') $sep=$i;
            $l += $cw[$c];
            if($l>$wmax){
                if($sep==-1){ if($i==$j) $i++; } 
                else $i=$sep+1;
                $sep=-1; $j=$i; $l=0; $nl++;
            } else $i++;
        }
        return $nl;
    }
}

// Obtener ID del pedido
// $id_pedido = $_GET['id_pedido'] ?? 0;
$id_pedido = isset($_GET['id_pedido']) ? intval($_GET['id_pedido']) : null;
// $id_pedido =  33;
// Datos del pedido con cliente
$sqlPedido = "
    SELECT p.*, c.nombre_cliente, c.direccion_sede, c.ciudad, c.telefono1
    FROM pedidos p
    JOIN clientes c ON c.id = p.cliente_id
    WHERE p.id = $id_pedido
";
$resultPedido = mysqli_query($conexion, $sqlPedido);
$pedido = mysqli_fetch_assoc($resultPedido);

// Detalle con producto
$sqlDetalle = "
    SELECT dp.*, pr.cod_producto, pr.descripcion
    FROM detalle_pedidos dp
    JOIN producto pr ON pr.id_producto = dp.producto_id
    WHERE dp.pedido_id = $id_pedido
";
$resultDetalle = mysqli_query($conexion, $sqlDetalle);

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',10);

// Borde general alrededor de toda la info
$x0 = $pdf->GetX();
$y0 = $pdf->GetY();
$w0 = 195;
$h0 = 100; // luego podemos ajustar dinámicamente si quieres
$pdf->Rect($x0-2, $y0-2, $w0, $h0);

// Información del pedido y cliente
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Pedido: '.$pedido['numero_pedido'],0,1);
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,6,'Fecha Pedido: '.$pedido['fecha_pedido'].'   Plazo pago: '.$pedido['plazo_pago_dias'].' dias',0,1);
$pdf->Cell(0,6,'Cliente: '.$pedido['nombre_cliente'],0,1);
$pdf->Cell(0,6,'Direccion: '.$pedido['direccion_sede'].' - '.$pedido['ciudad'],0,1);
$pdf->Cell(0,6,'Telefono: '.$pedido['telefono1'],0,1);

$pdf->Ln(5);

// Tabla de productos
$pdf->SetFont('Arial','B',10);
$widths = [20,80,30,30,30];
$aligns = ['C','L','R','R','R'];
$pdf->Row(['Cant','Descripcion','P.U.','Dto.','Total'], $widths, ['C','C','C','C','C']);

$pdf->SetFont('Arial','',10);
$total = 0;
while($row = mysqli_fetch_assoc($resultDetalle)){
    $pdf->Row([
        $row['cantidad'],
        $row['cod_producto'].' - '.$row['descripcion'],
        number_format($row['precio'],0,',','.'),
        number_format(0,0,',','.'), // dto si lo manejas
        number_format($row['total'],0,',','.')
    ], $widths, $aligns);
    $total += $row['total'];
}

// Total
$pdf->SetFont('Arial','B',10);
$pdf->Row(['','TOTAL','','',number_format($total,0,',','.')], $widths, $aligns);

// Observaciones
$pdf->Ln(5);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(40,6,'OBSERVACIONES:',0,1);
$pdf->SetFont('Arial','',10);
$pdf->MultiCell(0,6,$pedido['observaciones'] ?? '');

$pdf->Output();
?>
