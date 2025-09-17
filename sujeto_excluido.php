<?php include 'conexionfin.php';
$sqlconsecutivo = "SELECT MAX(valor) AS last_id FROM consecutivos WHERE codigo_consecutivo = 'fse'";
$resultadoconsecutivo = $conexion->query($sqlconsecutivo);
$rowconsecutivo = $resultadoconsecutivo->fetch_assoc();

$number = intval(substr($rowconsecutivo['last_id'], strlen("fse")));
$newNumber = $number + 1;
$newValor = str_pad($newNumber, 15, '0', STR_PAD_LEFT);

$codigoGeneracion = strtoupper(vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4)));
require_once("includes/class.php");
$pro = new Action();

$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$actividades = $pro->ListarProveedoresSE($busqueda);
?>
<style>
.summary-box {
    background: #f8f9fa;
    border: 1px solid #ddd;
    text-align: right;
    margin-top: 10px;
    max-width: auto;
}

.summary-box strong {
    display: inline-block;
    font-size: 16px;
    min-width: 180px;
}

.summary-box label {
    font-size: 19px;
    font-weight: normal;
}

.table-container {
    max-width: auto;
    margin: auto;
}
</style>

<form class="form form-material" method="post" action="#" name="saveSujetoExcluido" id="saveSujetoExcluido">
    <div class="container-fluid">
        <div id="spinner"
            style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background-color:rgba(255, 255, 255, 0.77);z-index:9999;text-align:center;padding-top:200px;font-size:24px;">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
            <p>Procesando Sujeto Excluido...</p>
        </div>
        <div class="card-header">
            <h4 class="card-title text-black"><i class="fa fa-file-alt"></i> Sujeto Excluido</h4>
        </div>
        <div class="row">
            <div class="form-group col-md-3">
                <label>Proveedor</label>
                <select name="proveedor" id="proveedor" class="form-control" required>
                    <option value="">-- SELECCIONE --</option>
                    <?php foreach ($actividades as $act): ?>
                    <option value="<?php echo $act['idproveedor']; ?>"
                        <?php echo (isset($meta['proveedor']) && $meta['idproveedor'] == $act['idproveedor']) ? 'selected' : ''; ?>>
                        <?php echo $act['proveedor']; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-3">
                <label>Fecha:</label>
                <input type="date" class="form-control" name="fecha" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="form-group col-md-3">
                <label>Condición de pago:</label>
                <select name="forma_pago" id="forma_pago" class="form-control select2">
                    <option value="">-- SELECCIONE --</option>
                    <?php
                    require_once("includes/class.php");
                    $pago = new Action();
                    $pago = $pago->ListarMediosPagos();
                    foreach ($pago as $p) {
                        echo "<option value='{$p['codigo']}'>{$p['medio_pago']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group col-md-3"></div>

            <div class="form-group col-md-3">
                <label>Número de Control:</label>
                <input type="text" name="numero_control" class="form-control" value="DTE-14-M001P001-<?= $newValor ?>"
                    readonly>
            </div>
            <div class="form-group col-md-3">
                <label>Código de Generación:</label>
                <input type="text" name="codigo_generacion" class="form-control" value="<?= $codigoGeneracion ?>"
                    readonly>
            </div>
            <div class="form-group col-md-6"></div>
            <div class="form-group col-md-12"></div>
            <div class="form-group col-md-12"></div>
            <div class="form-group col-md-6">
                <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#modalFacturas">
                    <i class="fas fa-plus"></i> Agregar Item
                </button>
            </div>

            <table class="table table-bordered table-striped mt-3" id="tablaItems">
                <thead class="table-light">
                    <tr>
                        <th>Detalle</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Renta Retenida</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div class="summary-box col-md-12">
                <div><strong>Sumas [ = ]</strong> $<label name="sumaTotal" id="sumaTotal">0.00</label>
                </div>
                <div><strong>Renta Retenida (10.00%) [ - ]</strong> $<label name="totalRenta"
                        id="totalRenta">0.00</label>
                </div>
                <div><strong>Total [ = ]</strong> $<label name="granTotal" id="granTotal">0.00</label>
                </div>
            </div>
            <input type="hidden" name="items" id="itemsHidden">
            <div class="form-group col-md-12 mt-3">
                <button type="submit" class="btn btn-success">Guardar Sujeto Excluido</button>
            </div>
        </div>
    </div>
</form>

<!-- Modal -->
<div class="modal fade" id="modalFacturas" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2"><label>Detalle</label><input type="text" id="detalle" class="form-control"></div>
                <div class="mb-2"><label>Cantidad</label><input type="number" id="cantidad" class="form-control"
                        step="1"></div>
                <div class="mb-2"><label>Precio Unitario</label><input type="number" id="precio" class="form-control"
                        step="0.01"></div>
                <div class="mb-2"><label>Renta Retenida (%)</label><input type="number" id="renta" class="form-control"
                        value="0" step="0.01"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="agregarItem()">Agregar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
let items = [];

function agregarItem() {
    let detalle = $('#detalle').val();
    let cantidad = parseFloat($('#cantidad').val());
    let precio = parseFloat($('#precio').val());
    let renta = parseFloat($('#renta').val());

    if (!detalle || isNaN(cantidad) || isNaN(precio)) return;

    let subtotal = cantidad * precio;
    let rentaVal = subtotal * (renta / 100);

    items.push({
        detalle,
        cantidad,
        precio,
        renta,
        subtotal
    });

    actualizarTabla();
    $('#modalFacturas').modal('hide');
    $('#detalle, #cantidad, #precio, #renta').val('');
}

function actualizarTabla() {
    let tbody = $('#tablaItems tbody');
    tbody.empty();
    let total = 0;
    let totalRenta = 0;

    items.forEach((item, i) => {
        let row = `<tr>
                <td>${item.detalle}</td>
                <td>${item.cantidad}</td>
                <td>$${item.precio.toFixed(2)}</td>
                <td>$${(item.subtotal * (item.renta / 100)).toFixed(2)}</td>
                <td>$${item.subtotal.toFixed(2)}</td>
            </tr>`;
        tbody.append(row);
        total += item.subtotal;
        totalRenta += item.subtotal * (item.renta / 100);
    });

    $('#sumaTotal').text(total.toFixed(2));
    $('#totalRenta').text(totalRenta.toFixed(2));
    $('#granTotal').text((total - totalRenta).toFixed(2));
}

function eliminarItem(index) {
    items.splice(index, 1);
    actualizarTabla();
}



$(document).ready(function() {

    function showSpinner() {
        $('#spinner').show();
    }

    function hideSpinner() {
        $('#spinner').hide();
    }

    $('#saveSujetoExcluido').submit(function(e) {
        e.preventDefault();

        var isValid = true;
        $('#saveSujetoExcluido input[required], #saveSujetoExcluido select[required]').each(function() {
            if (!$(this).val() || $(this).val().trim() === '' || $(this).val() === '0') {
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

        if (!isValid) return;

        if (items.length === 0) {
            Swal.fire({
                title: 'Error!',
                text: 'Debés agregar al menos un ítem.',
                icon: 'error',
                confirmButtonColor: '#d33'
            });
            return;
        }

        showSpinner();

        let formElement = document.getElementById('saveSujetoExcluido');
        let formData = new FormData(formElement);
        formData.append('items', JSON.stringify(items));

        $.ajax({
            url: 'ajax.php?action=save_sujetoExcluido',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(resp) {
                if (resp.success) {
                    $.ajax({
                        url: 'dteSE.php',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function(respDte) {
                            hideSpinner();
                            if (respDte.success) {
                                let codigo = respDte
                                    .codigo_generacion; // Asegurate de que dteSE.php devuelve esto
                                Swal.fire({
                                    title: 'Éxito!',
                                    text: 'Sujeto excluido guardado y procesado correctamente.',
                                    icon: 'success',
                                    confirmButtonColor: '#28a745',
                                    confirmButtonText: 'Ver Comprobante'
                                }).then(() => {
                                    window.open(
                                        'facturaElectronica.php?codigoSE=' +
                                        encodeURIComponent(codigo) +
                                        '&tipo=SE',
                                        '_blank' // Esto abre en una nueva pestaña o ventana, dependiendo del navegador
                                    );
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
});
$('#proveedor').select2({
    placeholder: "-- SELECCIONE PROVEEDOR --",
    minimumInputLength: 2,
    ajax: {
        url: 'buscar_proveedores.php',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                term: params.term // término de búsqueda
            };
        },
        processResults: function(data) {
            return {
                results: data.results
            };
        },
        cache: true
    },
    width: '100%'
});
</script>