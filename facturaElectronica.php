<?php
require('factura/fpdf/fpdf.php');
require('phpqrcode/qrlib.php');
include('conexionfin.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php'; // Para PHPMailer con Composer

$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$codigo = isset($_GET['codigo']) ? $conexion->real_escape_string($_GET['codigo']) : null;
$codigoSE = isset($_GET['codigoSE']) ? $conexion->real_escape_string($_GET['codigoSE']) : null;
$codigoDebito = isset($_GET['codigodebito']) ? $conexion->real_escape_string($_GET['codigodebito']) : null;
// echo "<pre>" . print_r($codigo) . "</pre>";
if (!$id && !$codigo && !$codigoSE && !$codigoSE && !$codigoDebito) {
    die("Error: Debe proporcionar 'id', 'codigo' o 'codigoSE'.");
}

if ($id) {
    $sql = "SELECT correo_enviado, selloRecibido, jsondte 
            FROM respuestadte 
            INNER JOIN factura ON factura.id = respuestadte.id_factura 
            WHERE id_factura = $id";
} else if ($codigo) {
    $sql = "SELECT correo_enviado, selloRecibido, jsondte 
            FROM respuestadte 
            INNER JOIN notas_credito ON notas_credito.codigoGeneracion = respuestadte.codigoGeneracion 
            WHERE notas_credito.codigoGeneracion = '$codigo'";


} else if ($codigoDebito) {
    $sql = "SELECT correo_enviado, selloRecibido, jsondte 
            FROM respuestadte 
            INNER JOIN notas_debito ON notas_debito.codigoGeneracion = respuestadte.codigoGeneracion 
            WHERE notas_debito.codigoGeneracion = '$codigoDebito'";


} else if ($codigoSE) {
    $sql = "SELECT selloRecibido, jsondte 
            FROM respuestadte 
            WHERE respuestadte.codigoGeneracion = '$codigoSE'";
}

$resultado = $conexion->query($sql);
echo "<pre>" . print_r($sql) . "</pre>";
$row = $resultado->fetch_assoc();
$json = $row['jsondte'];

// Verifica que el JSON sea válido
$data = json_decode($json, true);
if (!$data || json_last_error() !== JSON_ERROR_NONE) {
    die("Error al decodificar el JSON");
}

// === Extraer campos importantes ===
$ident = $data['identificacion'];
$emisor = $data['emisor'];
$receptor = $data['receptor'] ?? $data['sujetoExcluido'] ?? null;
$cuerpo = $data['cuerpoDocumento'];
$resumen = $data['resumen'];
$documentosRelacionados = $data['documentoRelacionado'] ?? [];

// Verifica si hay documentos relacionados
if (is_array($documentosRelacionados) && count($documentosRelacionados) > 0) {
    // Por ejemplo, tomamos el primero
    $docRel = $documentosRelacionados[0];
    $tipoDocRel = $docRel['tipoDocumento'] ?? '';
    $numDocRel = $docRel['numeroDocumento'] ?? '';
    $fechaDocRel = $docRel['fechaEmision'] ?? '';
} else {
    $tipoDocRel = '';
    $numDocRel = '';
    $fechaDocRel = '';
}
// === Generar QR ===
$contenidoQR = "https://admin.factura.gob.sv/consultaPublica?ambiente={$ident['ambiente']}&codGen={$ident['codigoGeneracion']}&fechaEmi={$ident['fecEmi']}";
$archivoQR = 'qr_temp.png';
$archivologo = 'img/logo.png';
QRcode::png($contenidoQR, $archivoQR, QR_ECLEVEL_H, 4);

// PDF DE FACTURA CONSUMIDOR FINAL
if ($ident['tipoDte'] == '01') {
    include('factura_consumidorfinal.php');
} else if ($ident['tipoDte'] == '03') {
    include('factura_creditofiscal.php');
} else if ($ident['tipoDte'] == '05') {
    include('comprobante_notacredito.php');
} else if ($ident['tipoDte'] == '06') {
    include('comprobante_notadebito.php');
} else if ($ident['tipoDte'] == '14') {
    include('factura_sujetoexcluido.php');
}

$nombreArchivo = $ident['numeroControl'] . '.pdf';
ob_clean(); // Limpiar buffer
$pdf->Output('F', $nombreArchivo); // Guarda el archivo en disco
$correoEnviado = $row['correo_enviado'] ?? 0;
// ----- 2. ENVIAR EL PDF POR CORREO -----
if ($correoEnviado == 0) {
    // Tu código de envío de correo aquí
    $mail = new PHPMailer(true);

    try {
        $query = "SELECT * FROM configuracion WHERE id = 1";
        $resultado = $conexion->query($query);
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'alex.calix1992@gmail.com';
        $mail->Password = 'mvwo raxu urmn jhps'; // Contraseña de aplicación
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Remitente y receptor
        $mail->setFrom('alex.calix1992@gmail.com', utf8_decode('SYSTEC - POS'));
        $mail->addAddress($receptor['correo'], 'Cliente');

        // Adjuntar PDF
        $mail->addAttachment($nombreArchivo);
        // Adjuntar JSON desde variable
        $mail->addStringAttachment(
            $json,         // contenido del archivo
            $ident['numeroControl'] . '.json',       // nombre del archivo
            'base64',             // codificación
            'application/json'    // tipo MIME
        );

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = utf8_decode('SYSTEC - Factura Electrónica');
        $mail->Body = 'Estimado cliente,<br><br>Este correo es generado automaticamente por : <b>SYSTEC</b><br><br>Gracias por su compra.';

        $mail->send();
        $pdf->Output('I', $nombreArchivo . '.pdf');

        // Opcional: Eliminar el archivo PDF del servidor
        unlink($nombreArchivo);

        // Establecer que el correo ya ha sido enviado en esta sesión
        $save = $conexion->query("UPDATE notas_credito SET correo_enviado = 1 WHERE codigoGeneracion = '$codigo'");

    } catch (Exception $e) {
        echo "No se pudo enviar el correo: {$mail->ErrorInfo}";
    }
} else {
    // Si el correo ya fue enviado, simplemente muestra el PDF
    $pdf->Output('I', $nombreArchivo . '.pdf');

    // Opcional: Eliminar el archivo PDF del servidor
    unlink($nombreArchivo);
}

?>