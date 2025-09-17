<?php include 'conexionfin.php';
require_once("includes/class.php");
$pro = new Action();
$documentos = $pro->ListarDocumentos();
?>

<form class="form form-material" method="post" action="#" name="saveSujetoExcluido" id="saveSujetoExcluido">
    <div class="container-fluid">
        <div id="spinner"
            style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background-color:rgba(255, 255, 255, 0.77);z-index:9999;text-align:center;padding-top:200px;font-size:24px;">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
            <p>Procesando Contingencia</p>
        </div>
        <div class="card-header">
            <h4 class="card-title text-black"><i class="fa fa-file-alt"></i> Contingencias</h4>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label>Fecha Inicio Contingencia:</label>
                <input type="date" class="form-control" name="fechaIni" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="form-group col-md-6">
                <label>Hora Inicio Contingencia:</label>
                <input type="time" class="form-control" name="horaIni" value="00:00:59">
            </div>
            <div class="form-group col-md-6">
                <label>Fecha Fin Contingencia:</label>
                <input type="date" class="form-control" name="fechaFin" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="form-group col-md-6">
                <label>Hora Fin Contingencia:</label>
                <input type="time" class="form-control" name="horaFin" value="23:59:59">
            </div>
            <div class="form-group col-md-6">
                <label for="direccion">Nombre del Responsable</label>
                <input type="text" name="responsable" id="responsable" class="form-control"
                    value="<?php echo isset($meta['responsable']) ? htmlspecialchars($meta['responsable']) : ''; ?>"
                    required>
            </div>
            <div class="form-group col-md-3">
                <label for="tipoDoc">Tipo Documento</label>
                <select name="tipoDoc" id="tipoDoc" class="form-control" required>
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
            <div class="form-group col-md-3">
                <label>Tipo de Contingencia :</label>
                <select name="tcontingencia" id="tcontingencia" class="form-control select2">
                    <option value="">-- SELECCIONE --</option>
                    <?php
                    require_once("includes/class.php");
                    $pago = new Action();
                    $pago = $pago->ListarCtaContingencias();
                    foreach ($pago as $p) {
                        echo "<option value='{$p['codigo']}'>{$p['valores']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group col-md-3"></div>
            <div class="form-group col-md-3">
                <label for="codigo">Codigo Generacion Contingencia</label>
                <input name="codigo" id="codigo" class="form-control" rows="3" required />
            </div>
            <div class="form-group col-md-3">
                <label for="tipoF">Tipo de Documento de Contingencia</label>
                <input name="tipoF" id="tipoF" class="form-control" rows="3" required />
            </div>

            <div class="form-group col-md-6">
                <button class="btn btn-primary btn-md" type="button" data-toggle="modal" data-target="#modalFacturas"
                    data-dni="<?= $documento ?>">
                    <i class="fas fa-plus"></i> Seleccionar documento para contingencia
                </button>
            </div>
            <div class="form-group col-md-6"></div>
            <div class="form-group col-md-12"></div>
            <div class="form-group col-md-12"></div>

            <div class="form-group col-md-12 mt-3">
                <button type="submit" class="btn btn-success"><i class="fa fa-archive"></i> Transmitir
                    Factura</button>
            </div>
        </div>
    </div>
</form>

<div class="modal fade" id="modalFacturas" tabindex="-1" role="dialog" aria-labelledby="modalFacturasLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFacturasLabel">Documentos Relacionados</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="contenidoModalFacturas">
                <div class="text-center">
                    <i class="fa fa-spinner fa-spin fa-2x"></i> Cargando datos...
                </div>
            </div>
        </div>
    </div>
</div>


<script>
$('#modalFacturas').on('show.bs.modal', function(event) {
    const button = $(event.relatedTarget);

    $('#contenidoModalFacturas').html(
        '<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i> Cargando...</div>');

    $.ajax({
        url: 'lista_ventas_contingencias.php',
        method: 'GET',
        data: {
            documento: '00000000'
        },
        success: function(data) {
            $('#contenidoModalFacturas').html(data);
        },
        error: function() {
            $('#contenidoModalFacturas').html(
                '<div class="alert alert-danger">Error al cargar los datos.</div>');
        }
    });
});

$('#saveSujetoExcluido').submit(function(e) {
    e.preventDefault();
    start_load();

    let formElement = document.getElementById('saveSujetoExcluido');
    let formData = new FormData(formElement);

    $.ajax({
        url: 'ajax.php?action=save_contingencia',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(resp) {
            if (resp.success) {
                $.ajax({
                    url: 'dteCON.php',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(respDte) {
                        hideSpinner();
                        if (respDte.success) {
                            Swal.fire({
                                title: 'Éxito!',
                                text: 'Evento de Contingencia guardado y procesado correctamente.',
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                confirmButtonText: 'OK'
                            }).then(() => {

                            });
                        } else {
                            Swal.fire({
                                title: 'Error en DTE',
                                text: respDte.message ||
                                    'Ocurrió un error al procesar el DTE.',
                                icon: 'error'
                            });
                        }
                    },
                    error: function() {
                        hideSpinner();
                        Swal.fire({
                            title: 'Error',
                            text: 'Error al enviar el sujeto excluido al DTE.',
                            icon: 'error'
                        });
                    }
                });
            } else {
                hideSpinner();
                Swal.fire({
                    title: 'Error',
                    text: resp.message || 'Error al guardar sujeto excluido.',
                    icon: 'error'
                });
            }
        },
        error: function() {
            hideSpinner();
            Swal.fire({
                title: 'Error',
                text: 'No se pudo conectar con el servidor.',
                icon: 'error'
            });
        }
    });
});
</script>