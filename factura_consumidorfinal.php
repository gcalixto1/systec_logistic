<?php
// === Obtener nombre del departamento ===
$sql2 = "SELECT valor FROM departamentos WHERE codigo = '" . $emisor['direccion']['departamento'] . "'";
$resultado2 = $conexion->query($sql2);
$row2 = $resultado2->fetch_assoc();

$sql3 = "SELECT valor FROM municipios WHERE codigo = '" . $emisor['direccion']['municipio'] . "'";
$resultado3 = $conexion->query($sql3);
$row3 = $resultado3->fetch_assoc();

// === Obtener nombre del departamento ===
$sql4 = "SELECT valor FROM departamentos WHERE codigo = '" . $receptor['direccion']['departamento'] . "'";
$resultado4 = $conexion->query($sql4);
$row4 = $resultado4->fetch_assoc();

$sql5 = "SELECT valor FROM municipios WHERE codigo = '" . $receptor['direccion']['municipio'] . "'";
$resultado5 = $conexion->query($sql5);
$row5 = $resultado5->fetch_assoc();

$sql6 = "SELECT valor FROM documentos WHERE codigo = '" . $receptor['tipoDocumento'] . "'";
$resultado6 = $conexion->query($sql6);
$row6 = $resultado6->fetch_assoc();

$sql7 = "SELECT usuario.nombre,factura.numerofactura FROM usuario INNER JOIN factura ON factura.idusuario =
usuario.idusuario WHERE factura.id = $id";
$resultado7 = $conexion->query($sql7);
$row7 = $resultado7->fetch_assoc();

// === Crear PDF ===
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

// === Encabezado ===
$pdf->Image($archivologo, 12, 12, 40, 20); // (x, y, width, height)
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, utf8_decode('DOCUMENTO TRIBUTARIO ELECTRÓNICO'), 0, 1, 'C');
$pdf->Cell(0, 6, utf8_decode('FACTURA'), 0, 1, 'C');

// Código QR
$pdf->Image($archivoQR, 85, 27, 40, 40);

// === Datos Generales ===
$pdf->SetY(30);
$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(35, 6, utf8_decode('Código de Generación: '), 0, 0);
$pdf->Cell(225, 6, utf8_decode('Modelo de Fcaturación: '), 0, 0, 'C');
$pdf->Ln(5);
$pdf->SetFont('Times', '', size: 9);
$pdf->Cell(35, 6, utf8_decode($ident['codigoGeneracion']), 0, 0);
$pdf->Cell(220, 6, utf8_decode('Modelo Facturación previo '), 0, 0, 'C');
$pdf->Ln(5);
$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(35, 6, utf8_decode('Número de Control: '), 0, 0);
$pdf->Cell(220, 6, utf8_decode('Tipo de Transmisión: '), 0, 0, 'C');
$pdf->Ln(5);
$pdf->SetFont('Times', '', 9);
$pdf->Cell(35, 6, utf8_decode($ident['numeroControl']), 0, 0);
$pdf->Cell(210, 6, utf8_decode('Transmisión normal'), 0, 0, 'C');
$pdf->Ln(5);
$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(35, 6, utf8_decode('Sello de Recepción:'), 0, 0);
$pdf->Cell(234, 6, utf8_decode('Fecha y Hora de Generación: '), 0, 0, 'C');
$pdf->Ln(5);
$pdf->SetFont('Times', '', 9);
$pdf->Cell(35, 6, utf8_decode($row['selloRecibido']), 0, 0);
$pdf->Cell(210, 6, utf8_decode($ident['fecEmi'] . ' ' . $ident['horEmi']), 0, 1, 'C');

$pdf->Ln(5);

$actividad = $emisor['descActividad'] ?? '';
$codActividad = $emisor['descActividad']['codActividad'] ?? '';
$actividadR = $receptor['descActividad'] ?? '';
$codActividadR = $receptor['descActividad']['codActividad'] ?? '';

// --- Texto EMISOR ---
$emisorText = "Nombre: {$emisor['nombre']}\n";
$emisorText .= "NIT: {$emisor['nit']}\n";
$emisorText .= "NRC: {$emisor['nrc']}\n";
$emisorText .= "Actividad económica: $actividad\n";
$emisorText .= "Dirección: " . ($emisor['direccion']['complemento'] ?? '') . ', ' . utf8_decode($row2['valor']) . ', ' .
    utf8_decode($row3['valor']) . "\n";
$emisorText .= "Número de teléfono: " . ($emisor['telefono'] ?? '') . "\n";
$emisorText .= "Correo electrónico: " . ($emisor['correo'] ?? '') . "\n";
$emisorText .= "Tipo de establecimiento: Casa Matriz";

// --- Texto RECEPTOR ---
$receptorText = "Nombre: {$receptor['nombre']}\n";
$receptorText .= "Tipo de documento de identificación: " . utf8_decode($row6['valor']) . "\n";
$receptorText .= "Número de Documento de Identificación: " . ($receptor['numDocumento'] ?? '') . "\n";
$receptorText .= "Actividad económica: $actividadR ($codActividadR)\n";
$receptorText .= "Dirección: " . ($receptor['direccion']['complemento'] ?? '') . ', ' . utf8_decode($row4['valor']) . ',
' . utf8_decode($row5['valor']) . "\n";
$receptorText .= "Correo electrónico: " . ($receptor['correo'] ?? '') . "\n";
$receptorText .= "Teléfono: " . ($receptor['telefono'] ?? '');

// Posiciones y medidas
$startY = 70;
$emisorX = 10;
$receptorX = 110;
$width = 90;

// Dibujar título y calcular altura EMISOR
$pdf->SetXY($emisorX, $startY);
$pdf->SetFont('Arial', 'UB', 14);
$pdf->Cell($width, 7, 'Emisor', 0, 2, 'C');
$pdf->SetFont('Times', '', 11);
$pdf->SetX($emisorX);
$startYContent = $pdf->GetY();
$pdf->MultiCell($width, 4, utf8_decode($emisorText), 0, 'L');
$endY = $pdf->GetY();
$boxHeight = $endY - $startY;

// Dibujar rectángulo redondeado EMISOR
$pdf->RoundedRect($emisorX, $startY, $width, $boxHeight + 3, 3);

// Dibujar título y contenido RECEPTOR
$pdf->SetXY($receptorX, $startY);
$pdf->SetFont('Arial', 'UB', 14);
$pdf->Cell($width, 7, 'Receptor', 0, 2, 'C');
$pdf->SetFont('Times', '', 11);
$pdf->SetX($receptorX);
$pdf->MultiCell($width, 4, utf8_decode($receptorText), 0, 'L');

// Dibujar rectángulo redondeado RECEPTOR (igual altura que EMISOR)
$pdf->RoundedRect($receptorX, $startY, $width, $boxHeight + 3, 3);

$pdf->Ln(5);
// Detalle de productos
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(10, 6, 'N', 1, 0, 'C');
$pdf->Cell(55, 6, 'Descripcion', 1, 0, 'C');
$pdf->Cell(15, 6, 'Cantidad', 1, 0, 'C');
$pdf->Cell(10, 6, 'Unidad', 1, 0, 'C');
$pdf->Cell(20, 6, 'Precio Uni', 1, 0, 'C');
$pdf->Cell(20, 6, 'Descuento', 1, 0, 'C');
$pdf->Cell(20, 6, 'No Sujetas', 1, 0, 'C');
$pdf->Cell(20, 6, 'Exentas', 1, 0, 'C');
$pdf->Cell(20, 6, 'Gravadas', 1, 1, 'C');

$pdf->SetFont('Arial', '', 8);
$contador = 1;

$pdf->SetFont('Arial', '', 8);
$contador = 1;

foreach ($data['cuerpoDocumento'] as $item) {
    $descripcion = utf8_decode($item['descripcion'] ?? '');

    // --- Guardar posición inicial
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // --- Anchos de las columnas
    $w = [10, 55, 15, 10, 20, 20, 20, 20, 20];

    // --- Altura base
    $h = 5;

    // --- Calcular altura de la descripción con MultiCell
    $pdf->SetXY($x + $w[0], $y); // movernos a columna descripción
    $pdf->MultiCell($w[1], $h, $descripcion, 0, 'L');
    $heightDescripcion = $pdf->GetY() - $y; // altura usada por la descripcion

    // --- Calcular la altura máxima de la fila
    $rowHeight = max($h, $heightDescripcion);

    // --- Dibujar todas las celdas manualmente ---
    $pdf->SetXY($x, $y);
    $pdf->Cell($w[0], $rowHeight, $contador++, 1, 0, 'C');

    $pdf->SetXY($x + $w[0], $y);
    $pdf->MultiCell($w[1], $h, $descripcion, 1, 'L');

    $pdf->SetXY($x + $w[0] + $w[1], $y);
    $pdf->Cell($w[2], $rowHeight, $item['cantidad'] ?? '', 1, 0, 'C');

    $pdf->Cell($w[3], $rowHeight, $item['uniMedida'] ?? '', 1, 0, 'C');
    $pdf->Cell($w[4], $rowHeight, '$' . number_format($item['precioUni'] ?? 0, 4), 1, 0, 'R');
    $pdf->Cell($w[5], $rowHeight, '$' . number_format($item['montoDescu'] ?? 0, 3), 1, 0, 'R');
    $pdf->Cell($w[6], $rowHeight, '$' . number_format($item['ventaNoSuj'] ?? 0, 2), 1, 0, 'R');
    $pdf->Cell($w[7], $rowHeight, '$' . number_format($item['ventaExenta'] ?? 0, 2), 1, 0, 'R');
    $pdf->Cell($w[8], $rowHeight, '$' . number_format($item['ventaGravada'] ?? 0, 2), 1, 0, 'R');

    // --- Pasar a la siguiente fila ---
    $pdf->Ln($rowHeight);
}

// Totales
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(170, 5, 'SUMA DE VENTAS:', 1, 0, 'R');
$pdf->Cell(20, 5, '$' . number_format($data['resumen']['totalGravada'] ?? 0, 2), 1, 1, 'R');

// Información adicional
$pdf->Ln(3);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(120, 5, '', 0, 0);
$pdf->Cell(45, 5, 'Sumal Total de Operaciones:', 0, 0, 'R');
$pdf->Cell(25, 5, '$' . number_format($data['resumen']['montoTotalOperacion'] ?? 0, 2), 0, 1, 'R');

$pdf->Cell(120, 5, '', 0, 0);
$pdf->Cell(45, 5, 'Monto global Desc., Rebajas y otros a ventas no sujetas:', 0, 0, 'R');
$pdf->Cell(25, 5, '$' . number_format($data['resumen']['descuNoSuj'] ?? 0, 2), 0, 1, 'R');

$pdf->Cell(120, 5, '', 0, 0);
$pdf->Cell(45, 5, 'Monto global Desc., Rebajas y otros a ventas Exentas:', 0, 0, 'R');
$pdf->Cell(25, 5, '$' . number_format($data['resumen']['descuExenta'] ?? 0, 2), 0, 1, 'R');

$pdf->Cell(120, 5, '', 0, 0);
$pdf->Cell(45, 5, 'Monto global Desc., Rebajas y otros a ventas Gravadas:', 0, 0, 'R');
$pdf->Cell(25, 5, '$' . number_format($data['resumen']['descuGravada'] ?? 0, 2), 0, 1, 'R');

$pdf->Cell(120, 5, '', 0, 0);
$pdf->Cell(45, 5, 'Sub-Total:', 0, 0, 'R');
$pdf->Cell(25, 5, '$' . number_format($data['resumen']['subTotal'] ?? 0, 2), 0, 1, 'R');

$pdf->Cell(120, 5, '', 0, 0);
$pdf->Cell(45, 5, 'IVA Retenido:', 0, 0, 'R');
$pdf->Cell(25, 5, '$' . number_format(0, 2), 0, 1, 'R');

$pdf->Cell(120, 5, '', 0, 0);
$pdf->Cell(45, 5, 'Retencion de Renta:', 0, 0, 'R');
$pdf->Cell(25, 5, '$' . number_format($data['resumen']['reteRenta'] ?? 0, 2), 0, 1, 'R');

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(120, 5, '', 0, 0);
$pdf->Cell(45, 5, 'Total a pagar:', 0, 0, 'R');
$pdf->Cell(25, 5, '$' . number_format($data['resumen']['totalPagar'] ?? 0, 2), 0, 1, 'R');

// Valor en letras y condición
$pdf->Ln(4);
$pdf->SetFont('Arial', '', 7.5);
$pdf->SetFillColor(80, 80, 80);
$pdf->SetTextColor(255);
$pdf->Cell(
    130,
    6,
    utf8_decode('Valor en letras: ' . strtoupper($data['resumen']['totalLetras'] ?? 'CERO 00/100')),
    0,
    0,
    'L',
    true
);
$pdf->Cell(60, 6, 'Condicion de la operacion: Contado', 0, 1, 'R', true);
$pdf->SetTextColor(0); // Restaurar color

$pdf->Ln(4);
$pdf->SetFont('Arial', '', 10);
$pdf->SetFillColor(80, 80, 80);
$pdf->SetTextColor(255);
$pdf->Cell(190, 6, 'Apendice', 0, 0, 'L', true);
$pdf->SetTextColor(0); // Restaurar color

$pdf->Ln(10);
$pdf->SetFont('Times', 'UB', 12);
$pdf->Cell(35, 6, utf8_decode('Datos del Vendedor '), 0, 0);
$pdf->Cell(225, 6, utf8_decode('Datos del Documento '), 0, 0, 'C');


$pdf->Ln(5);
$pdf->SetFont('Times', '', 10);
$pdf->Cell(35, 6, utf8_decode('Nombre : ' . $row7['nombre']), 0, 0);
$pdf->Cell(235, 6, utf8_decode('Numero de Documento : ' . $row7['numerofactura']), 0, 1, 'C');
$pdf->Cell(46, 6, utf8_decode('Sello Ministerio de Hacienda : ' . $row['selloRecibido']), 0, 0, 'L');