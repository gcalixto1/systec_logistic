<?php
include('conexionfin.php');

$codigo = isset($_GET['codigo']) ? $_GET['codigo'] : '';
$meta = array();

if (!empty($codigo)) {
    $codigo = $conexion->real_escape_string($codigo);

    $query = $conexion->query("SELECT factura.id, factura.fechafactura, factura.numerofactura, consecutivos.descripcionconse, 
                respuestadte.codigoGeneracion, cliente.nombre, cliente.dni,factura.totalpagar, medio_pago.medio_pago,respuestadte.jsondte
          FROM factura
          INNER JOIN cliente ON cliente.idcliente = factura.idcliente
          INNER JOIN consecutivos ON consecutivos.codigo_consecutivo = factura.tipofactura
          INNER JOIN respuestadte ON respuestadte.id_factura = factura.id
          INNER JOIN medio_pago ON medio_pago.codigo = factura.forma_pago
          WHERE respuestadte.codigoGeneracion = '$codigo'");

    if ($query && $query->num_rows > 0) {
        $resultado = $query->fetch_assoc();
        $meta = $resultado;
    } else {
        echo "No se encontró información para el código: $codigo";
    }
} else {
    echo "Código de generación no proporcionado.";
}

$json = $resultado['jsondte'];

// Verifica que el JSON sea válido
$data = json_decode($json, true);
if (!$data || json_last_error() !== JSON_ERROR_NONE) {
    die("Error al decodificar el JSON");
}

$ident = $data['identificacion'];
$emisor = $data['emisor'];
$receptor = $data['receptor'];
$cuerpo = $data['cuerpoDocumento'];
$resumen = $data['resumen'];
$documentosRelacionados = $data['documentoRelacionado'] ?? [];

require_once("includes/class.php");
$pro = new Action();
$documentos = $pro->ListarDocumentos();
?>
<div class="container-fluid">
    <div class="card">
        <div id="spinner"
            style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background-color:rgba(255, 255, 255, 0.77);z-index:9999;text-align:center;padding-top:200px;font-size:24px;">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
            <p>Procesando invalidación...</p>
        </div>
        <form class="form form-material" method="post" action="#" name="saveInvalidacion" id="saveInvalidacion">
            <div id="save"></div>
            <div class="form-body">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group has-feedback">
                            <input type="hidden" name="id"
                                value="<?php echo isset($_GET['codigo']) ? $_GET['codigo'] : ''; ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="name">Tipo DTE</label>
                            <input type="text" name="descripcionconse" id="descripcionconse" class="form-control"
                                value="<?php echo isset($meta['descripcionconse']) ? htmlspecialchars($meta['descripcionconse']) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="name">Número de Documento</label>
                            <input type="text" name="numeroControl" id="numeroControl" class="form-control"
                                value="<?php echo isset($ident['numeroControl']) ? htmlspecialchars($ident['numeroControl']) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group col-md-12">
                            <label class="control-label">Código Generación</label>
                            <input type="text" name="codigoGeneracion" id="codigoGeneracion" class="form-control"
                                value="<?php echo isset($ident['codigoGeneracion']) ? htmlspecialchars($ident['codigoGeneracion']) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="name">Nombre Cliente</label>
                            <input type="text" name="nombre" id="nombre" class="form-control"
                                value="<?php echo isset($meta['nombre']) ? htmlspecialchars($meta['nombre']) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="name">Total de la Operación</label>
                            <input type="text" name="totalpagar" id="totalpagar" class="form-control"
                                value="<?php echo isset($meta['totalpagar']) ? htmlspecialchars($meta['totalpagar']) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="tipoControbuyente">Causa de Invalidación</label>
                            <select name="tipoInvalidacion" id="tipoInvalidacion" class="form-control" required>
                                <option value="0"></option>
                                <option value="1"> Error en la información del Documento Tributario Electrónico a
                                    invalidar </option>
                                <option value="2"> Recindir de la operación realizada</option>
                                <option value="3"> Otro</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="name">Motivo</label>
                            <input type="text" name="Motivo" id="Motivo" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="name">Persona que anula la operación</label>
                            <input type="text" name="solicitante" id="solicitante" class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="tipoDoc">Tipo Documento</label>
                            <select name="tipoDoc1" id="tipoDoc1" class="form-control" required>
                                <option value=""></option>
                                <?php foreach ($documentos as $doc): ?>
                                    <option value="<?php echo $doc['codigo']; ?>"
                                        <?php echo (isset($meta['tipoDocumento']) && $meta['tipoDocumento'] == $doc['codigo']) ? 'selected' : ''; ?>>
                                        <?php echo $doc['valor']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class=" form-group col-md-3">
                                    <label for="name">Documento</label>
                                    <input type="text" name="documento1" id="documento1" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="name">Persona que solicita invalidación de la operación</label>
                            <input type="text" name="responsable" id="responsable" class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="tipoDoc">Tipo Documento</label>
                            <select name="tipoDoc2" id="tipoDoc2" class="form-control" required>
                                <option value=""></option>
                                <?php foreach ($documentos as $doc): ?>
                                    <option value="<?php echo $doc['codigo']; ?>"
                                    <?php echo (isset($meta['tipoDocumento']) && $meta['tipoDocumento'] == $doc['codigo']) ? 'selected' : ''; ?>>
                                        <?php echo $doc['valor']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="name">Documento</label>
                            <input type="text" name="documento2" id="documento2" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        function showSpinner() {
            $('#spinner').show();
        }

        function hideSpinner() {
            $('#spinner').hide();
        }

        $('#saveInvalidacion').submit(function (e) {
            e.preventDefault();

            var isValid = true;
            $('#saveInvalidacion input[required], #saveInvalidacion select[required]').each(function () {
                if ($(this).val().trim() === '') {
                    isValid = false;
                    Swal.fire({
                        title: 'Error!',
                        text: 'Todos los campos son obligatorios.',
                        icon: 'error',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
            });

            if (isValid) {
                showSpinner();

                var formData = $(this).serialize();

                $.ajax({
                    url: 'ajax.php?action=saveInvalidacion',
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (resp) {
                        if (resp.success) {
                            // Segunda llamada: enviar a dteAN.php
                            $.ajax({
                                url: 'dteAN.php',
                                method: 'POST',
                                data: formData,
                                dataType: 'json',
                                success: function (respDte) {
                                    hideSpinner();
                                    if (respDte.success) {
                                        Swal.fire({
                                            title: 'Éxito!',
                                            text: 'Invalidación guardada y procesada correctamente.',
                                            icon: 'success',
                                            confirmButtonColor: '#28a745',
                                            confirmButtonText: 'OK'
                                        }).then(() => location.reload());
                                    } else {
                                        Swal.fire({
                                            title: 'Error en DTE',
                                            text: respDte.message ||
                                                'Ocurrió un error al procesar el DTE.',
                                            icon: 'error'
                                        });
                                    }
                                },
                                error: function () {
                                    hideSpinner();
                                    Swal.fire({
                                        title: 'Error',
                                        text: 'Error al enviar la invalidación al DTE.',
                                        icon: 'error'
                                    });
                                }
                            });
                        } else {
                            hideSpinner();
                            Swal.fire({
                                title: 'Error',
                                text: resp.message || 'Error al guardar invalidación.',
                                icon: 'error'
                            });
                        }
                    },
                    error: function () {
                        hideSpinner();
                        Swal.fire({
                            title: 'Error',
                            text: 'No se pudo conectar con el servidor.',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });
</script>