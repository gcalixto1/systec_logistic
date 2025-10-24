<?php
include("conexionfin.php");

// === OBTENER CONSECUTIVO ===
$consecutivo = "oc";
$query = $conexion->query("SELECT * FROM consecutivos WHERE codigo_consecutivo='$consecutivo' LIMIT 1");
$row = $query->fetch_assoc();
$numeroOC = $row ? str_pad($row['valor'], 6, '0', STR_PAD_LEFT) : '';
?>
<style>
    .spinner-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.7);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .spinner {
        border: 6px solid #ccc;
        border-top: 6px solid #007bff;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>
<div id="spinner"
    style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background-color:rgba(255, 255, 255, 0.77);z-index:9999;text-align:center;padding-top:200px;font-size:24px;">
    <div class="spinner-border text-primary" role="status">
        <span class="sr-only">Cargando...</span>
    </div>
    <p>Procesando Orden de Compra...</p>
</div>
<div class="container-fluid">
    <h3 class="text-center mb-4">🧾 Generar Orden de Compra</h3>

    <form id="formOC">

        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label><b>No. Orden</b></label>
                        <input type="text" class="form-control" name="numero_oc" value="OC-<?php echo $numeroOC; ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label><b>Fecha</b></label>
                        <input type="date" class="form-control" name="fecha_oc" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="col-md-4">
                        <label><b>Proveedor</b></label>
                        <select class="form-control" id="proveedor" name="proveedor_id"></select>
                    </div>
                    <div class="col-md-12">
                        <label><b>Observaciones</b></label>
                        <input type="text" class="form-control" name="observacion" placeholder="Escribe alguna nota para el proveedor...">
                    </div>
                </div>
            </div>
        </div>

        <!-- AGREGAR PRODUCTO -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Agregar Producto</h5>
                <div class="row  align-items-end" id="form-producto">
                    <div class="col-md-4">
                        <label>Producto</label>
                        <select class="form-control" id="producto" name="producto_id"></select>
                        <input type="hidden" id="descproducto" name="descproducto">
                        <input type="hidden" id="idPR" name="idPR">
                    </div>
                    <div class="col-md-2">
                        <label>Unds/Kg</label>
                        <input type="number" class="form-control" id="cantidad" placeholder="0">
                    </div>
                    <div class="col-md-2">
                        <label>Caja/Paca/Bob</label>
                        <input type="number" class="form-control" id="caja" placeholder="0">
                    </div>
                    <div class="col-md-2">
                        <label>Costo Unit.</label>
                        <input type="number" class="form-control" id="precio" placeholder="0.00" step="0.01">
                    </div>
                    <div class="col-md-2">
                        <label>Subtotal</label>
                        <input type="text" class="form-control" id="subtotal" readonly>
                    </div>
                    <div class="col-md-2" hidden>
                        <label>IVA</label>
                        <input type="checkbox" class="form-check-input" id="checkIVAProducto" checked> Aplicar IVA
                    </div>

                </div>
                <br>
                <br>
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary w-100" id="btnAgregar">➕ Agregar</button>
                </div>
            </div>
        </div>

        <!-- DETALLE -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Detalle de la Orden</h5>
                <div class="table-responsive">
                    <table class="table table-bordered" id="tablaDetalle">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Unds/Kg</th>
                                <th>Caja/Paca/Bob</th>
                                <th>Costo Unit.</th>
                                <th>Subtotal</th>
                                <th>IVA</th>
                                <th>Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TOTALES -->
        <div class="card shadow-sm">
            <div class="card-body text-end">
                <p><b>Subtotal:</b> <span id="totalSubtotal">0.00</span></p>
                <p><b>IVA:</b> <span id="totalIVA">0.00</span></p>
                <h5><b>Total:</b> <span id="totalFinal">0.00</span></h5>
                <button type="submit" class="btn btn-success btn-lg mt-3">💾 Registrar Orden</button>
            </div>
        </div>
    </form>
</div>

<script>
    let detalle = [];
    // jQuery.noConflict();
    function showSpinner() {
        $('#spinner').show();
    }

    function hideSpinner() {
        $('#spinner').hide();
    }

    // ================== SELECT2 PROVEEDOR ==================
    $('#proveedor').select2({
        placeholder: "Buscar proveedor...",
        ajax: {
            url: 'buscar_proveedores.php',
            dataType: 'json',
            delay: 250,
            data: params => ({
                q: params.term
            }),
            processResults: function(data) {
                // tus archivos ya devuelven { results: [...] }
                return data;
            }
        },
        minimumInputLength: 1
    });
    // ================== SELECT2 PRODUCTO ==================
    $('#producto').select2({
        placeholder: "Buscar producto por código o descripción...",
        ajax: {
            url: 'buscar_productoC.php',
            dataType: 'json',
            delay: 250,
            data: params => ({
                q: params.term
            }),
            processResults: function(data) {
                return data; // ya vienen como { results: [...] }
            }
        },
        minimumInputLength: 1
    });

    $('#producto').on('select2:select', function(e) {
        let data = e.params.data;
        $('#descproducto').val(data.text);
        $('#idPR').val(data.id);
        $('#precio').val(parseFloat(data.precio || 0).toFixed(2));
        unidadesPorCaja = parseFloat(data.unidades) || 1;
        $('#caja').val(1);
        $('#cantidad').val(unidadesPorCaja);
        $('#caja').val(1);
        calcularSubtotal();
    });

    $('#cantidad, #precio').on('keyup change', calcularSubtotal);

    function calcularSubtotal() {
        let cantidad = parseFloat($('#caja').val()) || 0;
        let precio = parseFloat($('#precio').val()) || 0;
        $('#subtotal').val((cantidad * precio).toFixed(2));
    }

    // ================== AGREGAR PRODUCTO ==================
    $('#btnAgregar').on('click', () => {
        let producto = $('#idPR').val();
        let productod = $('#descproducto').val();
        let cantidad = parseFloat($('#cantidad').val());
        let caja = parseFloat($('#caja').val());
        let precio = parseFloat($('#precio').val());
        let subtotal = caja * precio;
        let aplicaIVA = $('#checkIVAProducto').is(':checked');
        let iva = aplicaIVA ? subtotal * 0.19 : 0; // 19% IVA (Colombia)
        let total = subtotal + iva;

        if (!producto || cantidad <= 0) {
            Swal.fire("Completa los campos del producto");
            return;
        }

        detalle.push({
            producto,
            productod,
            cantidad,
            caja,
            precio,
            subtotal,
            iva,
            total
        });
        renderTabla();
        actualizarTotales();

        $('#producto').val(null).trigger('change');
        $('#cantidad,#caja,#precio,#subtotal').val('');
    });

    function renderTabla() {
        let tbody = $('#tablaDetalle tbody');
        tbody.empty();
        detalle.forEach((f, i) => {
            tbody.append(`<tr>
            <td>${f.productod}</td>
            <td>${f.cantidad}</td>
            <td>${f.caja}</td>
            <td>${f.precio.toFixed(2)}</td>
            <td>${f.subtotal.toFixed(2)}</td>
            <td>${f.iva.toFixed(2)}</td>
            <td>${f.total.toFixed(2)}</td>
            <td><button class="btn btn-danger btn-sm eliminar" data-i="${i}">🗑️</button></td>
        </tr>`);
        });
    }
    $(document).on('click', '.eliminar', function() {
        let i = $(this).data('i');
        detalle.splice(i, 1);
        renderTabla();
        actualizarTotales();
    });

    function actualizarTotales() {
        let subtotal = detalle.reduce((a, f) => a + f.subtotal, 0);
        let iva = detalle.reduce((a, f) => a + f.iva, 0);
        let total = detalle.reduce((a, f) => a + f.total, 0);
        $('#totalSubtotal').text(subtotal.toFixed(2));
        $('#totalIVA').text(iva.toFixed(2));
        $('#totalFinal').text(total.toFixed(2));
    }

    // ================== GUARDAR ORDEN ==================
    $('#formOC').on('submit', function(e) {
        e.preventDefault();
        showSpinner();
        if (detalle.length == 0) {
            Swal.fire("Agregue al menos un producto");
            return;
        }
        let formData = new FormData(this);
        formData.append('detalle', JSON.stringify(detalle));

        $.ajax({
            url: 'ajax.php?action=save_orden_compra',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(resp) {
                if (resp.success) {
                    hideSpinner();
                    Swal.fire({
                        icon: 'success',
                        title: '✅ Orden guardada',
                        text: `Número: ${resp.numero_oc}`
                    }).then(() => window.open('doc_orden_compra.php?id=' + resp.id_oc, '_blank'));
                } else {
                    Swal.fire('Error', resp.message, 'error');
                }
            }
        });
    });

    $('#cantidad').on('change keyup', function() {
        let cantidadTotal = parseFloat($(this).val()) || 0;
        let cajas = cantidadTotal / unidadesPorCaja; // calculo inverso
        $('#caja').val(cajas.toFixed(2));

        let precio = parseFloat($('#precio').val()) || 0;
        $('#subtotal').val((cajas * precio).toFixed(2));
    });

    // Cuando cambia caja, actualiza cantidad (por si el usuario cambia cajas directamente)
    $('#caja').on('change keyup', function() {
        let cajas = parseFloat($(this).val()) || 0;
        let cantidadTotal = cajas * unidadesPorCaja; // normal
        $('#cantidad').val(cantidadTotal);

        let precio = parseFloat($('#precio').val()) || 0;
        $('#subtotal').val((cajas * precio).toFixed(2));
    });
</script>