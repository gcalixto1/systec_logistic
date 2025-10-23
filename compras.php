<?php include("conexionfin.php"); ?>
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
    <p>Procesando Ingreso de Orden de Compra...</p>
</div>
<div class="container-fluid">
    <h4 class="mb-4">Ingreso de Órdenes de Compra</h4>

    <!-- Selección de OC -->
    <div class="mb-3">
        <label class="form-label">Seleccionar Orden de Compra</label>
        <select id="ordenSelect" class="form-control">
            <option value="">-- Seleccione una orden --</option>
            <?php
            $res = $conexion->query("SELECT id_oc, numero_oc FROM orden_compra where estado<>'COMPLETA' ORDER BY id_oc DESC");
            while ($r = $res->fetch_assoc()) {
                echo "<option value='{$r['id_oc']}'>{$r['numero_oc']}</option>";
            }
            ?>
        </select>
    </div>

    <!-- Datos de la orden -->
    <div id="datosOrden" class="card p-3 mb-3" style="display:none;">
        <div class="row mb-2">
            <div class="col-md-4">
                <label>Proveedor</label>
                <input type="text" id="proveedor" class="form-control" readonly>
            </div>
            <div class="col-md-4">
                <label>Fecha</label>
                <input type="text" id="fecha" class="form-control" readonly>
            </div>
            <div class="col-md-4">
                <label>Estado</label>
                <select id="estado" class="form-control">
                    <option value="PENDIENTE">Pendiente</option>
                    <option value="PARCIAL">Parcial</option>
                    <option value="COMPLETA">Completa</option>
                </select>
            </div>
        </div>

        <h6>Detalle de productos</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="detalleOrden">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Cant. OC</th>
                        <th>Cant. Pendiente</th>
                        <th>Cant. a Ingresar</th>
                        <th>Almacén</th>
                        <th>Lote</th>
                        <th>Costo Unitario</th>
                        <th>Costo Total</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="col-md-4">
            <button id="btnGuardar" class="btn btn-success mt-3">Guardar Ingreso</button>
        </div>
    </div>
</div>

<script>
    function showSpinner() {
        $('#spinner').show();
    }

    function hideSpinner() {
        $('#spinner').hide();
    }
    $(document).ready(function() {

        // === Cargar datos al seleccionar orden ===
        $('#ordenSelect').change(function() {
            let id_oc = $(this).val();
            if (id_oc == "") {
                $('#datosOrden').hide();
                return;
            }

            $.ajax({
                url: 'ajax.php?action=get_orden',
                type: 'POST',
                data: {
                    id_oc: id_oc
                },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        $('#datosOrden').show();
                        $('#proveedor').val(res.proveedor);
                        $('#fecha').val(res.fecha);
                        $('#estado').val(res.estado);

                        let rows = '';
                        res.detalle.forEach((d, i) => {
                            const pendiente = d.cantidad - d.cantidad_recibida;
                            const unidadesPorCaja = parseFloat(d.und_embalaje_minima) || 1;
                            const cantidadCajas = (d.cantidad / unidadesPorCaja).toFixed(2);
                            const pendienteCajas = (pendiente / unidadesPorCaja).toFixed(2);

                            // Lote autogenerado
                            const correlativo = ('00000' + d.id_detalle).slice(-5);
                            const fecha = new Date();
                            const fechaStr = String(fecha.getDate()).padStart(2, '0') +
                                String(fecha.getMonth() + 1).padStart(2, '0') +
                                String(fecha.getFullYear()).slice(-2);
                            const loteAuto = correlativo + fechaStr;

                            // Opciones de almacén
                            let almacenes = '';
                            res.almacenes.forEach(a => {
                                almacenes += `<option value="${a.id_almacen}">${a.nombre}</option>`;
                            });

                            rows += `<tr>
                                <td>${d.producto}</td>
                                <td>${d.descripcion}</td>

                                <!-- Cantidad OC -->
                                <td>
                                    ${d.cantidad.toFixed(2)} <br><small class="text-muted">(${cantidadCajas} cjs)</small>
                                </td>

                                <!-- Cantidad Pendiente -->
                                <td>
                                    ${pendiente.toFixed(2)}
                                    <br><small class="text-muted">(${pendienteCajas} cjs)</small>
                                </td>

                                <!-- Cantidad a Ingresar -->
                                <td>
                                    <div class="input-group input-group">
                                        <input type="number" class="form-control cant-unidades" 
                                            min="0" max="${pendiente}" value="0" 
                                            placeholder="Unidades"
                                            data-id="${d.id_detalle}" 
                                            data-idprod="${d.producto}"
                                            data-precio="${d.precio}"
                                            data-upcaja="${unidadesPorCaja}">
                                        <span class="input-group-text">u</span>
                                        <input type="number" class="form-control cant-cajas" 
                                            min="0" step="0.01" value="0"
                                            placeholder="Cajas">
                                        <span class="input-group-text">cjs</span>
                                    </div>
                                </td>

                                <td><select class="form-control form-control almacen">${almacenes}</select></td>
                                <td><input type="text" class="form-control form-control-sm lote" value="${loteAuto}"></td>
                                <td><input type="number" class="form-control form-control-sm cant-precio"
                                    min="0" value="${parseFloat(d.precio).toFixed(2)}"></td>
                                <td class="total">0.00</td>
                            </tr>`;
                        });

                        $('#detalleOrden tbody').html(rows);

                        // === Sincronizar unidades ↔ cajas ===
                        $('#detalleOrden').on('input', '.cant-unidades', function() {
                            const row = $(this).closest('tr');
                            const unidades = parseFloat($(this).val()) || 0;
                            const upCaja = parseFloat($(this).data('upcaja')) || 1;
                            const cajas = unidades / upCaja;
                            row.find('.cant-cajas').val(cajas.toFixed(2));
                            actualizarTotal(row);
                        });

                        $('#detalleOrden').on('input', '.cant-cajas', function() {
                            const row = $(this).closest('tr');
                            const cajas = parseFloat($(this).val()) || 0;
                            const upCaja = parseFloat(row.find('.cant-unidades').data('upcaja')) || 1;
                            const unidades = cajas * upCaja;
                            row.find('.cant-unidades').val(unidades.toFixed(2));
                            actualizarTotal(row);
                        });

                        $('#detalleOrden').on('input', '.cant-precio', function() {
                            const row = $(this).closest('tr');
                            actualizarTotal(row);
                        });

                        function actualizarTotal(row) {
                            const unidades = parseFloat(row.find('.cant-cajas').val()) || 0;
                            const precio = parseFloat(row.find('.cant-precio').val()) || 0;
                            const total = unidades * precio;
                            row.find('.total').text(total.toFixed(2));
                        }

                    } else {
                        Swal.fire('Aviso', res.message, 'warning');
                    }
                }
            });
        });

        // === Guardar ingreso ===
        $('#btnGuardar').click(function() {
            let id_oc = $('#ordenSelect').val();
            let estado = $('#estado').val();
            let detalle = [];
            showSpinner();

            $('#detalleOrden tbody tr').each(function() {
                let unidades = parseFloat($(this).find('.cant-unidades').val());
                let cajas = parseFloat($(this).find('.cant-cajas').val());
                if (unidades > 0) {
                    detalle.push({
                        id_detalle: $(this).find('.cant-unidades').data('id'),
                        id_producto: $(this).find('.cant-unidades').data('idprod'),
                        cantidad: unidades,
                        caja: cajas,
                        precio: $(this).find('.cant-precio').val(),
                        almacen_id: $(this).find('.almacen').val(),
                        lote: $(this).find('.lote').val()
                    });
                }
            });

            if (detalle.length == 0) {
                Swal.fire('Aviso', 'Debe ingresar al menos una cantidad.', 'warning');
                return;
            }

            $.ajax({
                url: 'ajax.php?action=save_ingreso',
                type: 'POST',
                data: {
                    id_oc: id_oc,
                    estado: estado,
                    detalle: JSON.stringify(detalle)
                },
                dataType: 'json',
                success: function(res) {

                    Swal.fire('Resultado', res.message, res.success ? 'success' : 'error');
                    if (res.success)
                        hideSpinner();
                    location.reload();
                }
            });
        });

    });
</script>