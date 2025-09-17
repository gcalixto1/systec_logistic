<?php
// Iniciar el buffer de salida
ob_start();
include "conexionfin.php";

if (empty($_REQUEST['id'])) {
    echo "No es posible generar la factura.";
} else {
    $id_factura = $_GET['id'];
    $consulta = mysqli_query($conexion, "SELECT * FROM configuracion");
    $resultado = mysqli_fetch_assoc($consulta);
    $ventas = mysqli_query($conexion, "SELECT * FROM factura WHERE id = $id_factura");
    $result_venta = mysqli_fetch_assoc($ventas);
    $idcliente = $result_venta['idcliente'];
    $clientes = mysqli_query($conexion, "SELECT * FROM cliente WHERE idcliente = $idcliente");
    $result_cliente = mysqli_fetch_assoc($clientes);
    $productos = mysqli_query($conexion, "SELECT d.idfactura, d.cod_producto, d.cantidad, p.codBarra, p.descripcion, p.precio 
                                          FROM detallefactura d 
                                          INNER JOIN producto p ON d.idfactura = $id_factura 
                                          WHERE d.cod_producto = p.codproducto");
    require_once 'factura/fpdf/fpdf.php';
    $pdf = new FPDF('P', 'mm', array(58, 150));
    $pdf->AddPage();
    $pdf->SetMargins(1, 0, 0);
    $pdf->SetTitle("rpt_ticket_venta");
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(37, 5, utf8_decode($resultado['nombre']), 0, 1, 'C');
    $pdf->Ln(2);

    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(20, 5, "Ruc: ", 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(35, 5, $resultado['dni'], 0, 1, 'L');
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(20, 5, utf8_decode("Teléfono: "), 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(35, 5, $resultado['telefono'], 0, 1, 'L');
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(20, 5, utf8_decode("Dirección: "), 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(35, 5, utf8_decode($resultado['direccion']), 0, 1, 'L');
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(20, 5, "Ticket: ", 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(20, 5, $id_factura, 0, 1, 'L');
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(20, 5, "Fecha: ", 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(35, 5, $result_venta['fechafactura'], 0, 1, 'L');
    $pdf->Ln(2);

    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(56, 5, "Datos del cliente", 0, 1, 'L');
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(30, 5, "Nombre", 0, 0, 'L');
    $pdf->Cell(26, 5, utf8_decode("Teléfono"), 0, 1, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(30, 5, utf8_decode($result_cliente['nombre']), 0, 0, 'L');
    $pdf->Cell(26, 5, utf8_decode($result_cliente['telefono']), 0, 1, 'L');
    $pdf->Ln(2);

    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(56, 5, "Detalle de Productos", 0, 1, 'L');
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(30, 5, 'Nombre', 0, 0, 'L');
    $pdf->Cell(8, 5, 'Cant', 0, 0, 'L');
    $pdf->Cell(8, 5, 'Precio', 0, 0, 'L');
    $pdf->Cell(10, 5, 'Total', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 7);
    while ($row = mysqli_fetch_assoc($productos)) {
        $pdf->MultiCell(30, 5, utf8_decode($row['descripcion']), 0, 'L');
        $pdf->SetXY($pdf->GetX() + 30, $pdf->GetY() - 5); // Ajustar la posición X para las siguientes celdas en la misma línea
        $pdf->Cell(8, 5, $row['cantidad'], 0, 0, 'L');
        $pdf->Cell(8, 5, number_format($row['precio'], 2, '.', ','), 0, 0, 'L');
        $importe = number_format($row['cantidad'] * $row['precio'], 2, '.', ',');
        $pdf->Cell(10, 5, $importe, 0, 1, 'L');
    }
    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(56, 5, 'Total: ' . number_format($result_venta['totalpagar'], 2, '.', ','), 0, 1, 'R');
    $pdf->Ln(2);
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(56, 5, utf8_decode("Gracias por su preferencia"), 0, 1, 'C');
    $pdf->Output("compra.pdf", "I");
}

// Limpiar el buffer de salida antes de enviar el PDF
ob_clean();

// Salida del PDF
$pdf->Output('I', 'ticket.pdf');

// Terminar el buffer de salida
ob_end_flush();
