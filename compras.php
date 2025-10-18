<?php include("conexionfin.php"); ?>

<div class="container-fluid">
    <h4 class="mb-4">Ingreso de Órdenes de Compra</h4>

    <!-- Selección de OC -->
    <div class="mb-3">
        <label class="form-label">Seleccionar Orden de Compra</label>
        <select id="ordenSelect" class="form-control">
            <option value="">-- Seleccione una orden --</option>
            <?php
            $res = $conexion->query("SELECT id_oc, numero_oc FROM orden_compra ORDER BY id_oc DESC");
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
                        <th>ID Producto</th>
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
    
$(document).ready(function(){

    // === Cargar datos al seleccionar orden ===
    $('#ordenSelect').change(function(){
        let id_oc = $(this).val();
        if(id_oc == ""){ $('#datosOrden').hide(); return; }

        $.ajax({
            url:'ajax.php?action=get_orden',
            type:'POST',
            data:{id_oc:id_oc},
            dataType:'json',
            success:function(res){
                if(res.success){
                    $('#datosOrden').show();
                    $('#proveedor').val(res.proveedor);
                    $('#fecha').val(res.fecha);
                    $('#estado').val(res.estado);

                    let rows = '';
                    res.detalle.forEach(d=>{
                        let pendiente = d.cantidad - (d.cantidad_recibida ?? 0);
                        let correlativo = ('00000' + d.id_detalle).slice(-5);
                        let fecha = new Date();
                        let fechaStr = String(fecha.getDate()).padStart(2, '0') +
                                       String(fecha.getMonth() + 1).padStart(2, '0') +
                                       String(fecha.getFullYear()).slice(-2);
                        let loteAuto = correlativo + fechaStr;

                        let almacenes = '';
                        res.almacenes.forEach(a=>{
                            almacenes += `<option value="${a.id_almacen}">${a.nombre}</option>`;
                        });

                        rows += `<tr>
                            <td>${d.producto}</td>
                            <td>${d.descripcion}</td>
                            <td>${d.cantidad}</td>
                            <td>${pendiente}</td>
                            <td><input type="number" class="form-control form-control-sm cant-ingreso"
                                min="0" max="${pendiente}" value="0"
                                data-id="${d.id_detalle}" data-precio="${d.precio}" data-idprod="${d.producto}">
                            </td>
                            <td><select class="form-control form-control-sm almacen">${almacenes}</select></td>
                            <td><input type="text" class="form-control form-control-sm lote" value="${loteAuto}"></td>
                            <td>${parseFloat(d.precio).toFixed(2)}</td>
                            <td class="total">0.00</td>
                        </tr>`;
                    });

                    $('#detalleOrden tbody').html(rows);

                    $('.cant-ingreso').on('input', function(){
                        let cant = parseFloat($(this).val()) || 0;
                        let precio = parseFloat($(this).data('precio')) || 0;
                        let total = cant * precio;
                        $(this).closest('tr').find('.total').text(total.toFixed(2));
                    });

                } else {
                    Swal.fire('Aviso', res.message, 'warning');
                }
            }
        });
    });

    // === Guardar ingreso ===
    $('#btnGuardar').click(function(){
        let id_oc = $('#ordenSelect').val();
        let estado = $('#estado').val();
        let detalle = [];

        $('#detalleOrden tbody tr').each(function(){
            let cant = parseFloat($(this).find('.cant-ingreso').val());
            if(cant > 0){
                detalle.push({
                    id_detalle: $(this).find('.cant-ingreso').data('id'),
                    id_producto: $(this).find('.cant-ingreso').data('idprod'),
                    cantidad: cant,
                    precio: $(this).find('.cant-ingreso').data('precio'),
                    almacen_id: $(this).find('.almacen').val(),
                    lote: $(this).find('.lote').val()
                });
            }
        });

        if(detalle.length == 0){ 
            Swal.fire('Aviso', 'Debe ingresar al menos una cantidad.', 'warning');
            return; 
        }

        $.ajax({
            url:'ajax.php?action=save_ingreso',
            type:'POST',
            data:{id_oc:id_oc, estado:estado, detalle:JSON.stringify(detalle)},
            dataType:'json',
            success:function(res){
                Swal.fire('Resultado', res.message, res.success ? 'success' : 'error');
                if(res.success) setTimeout(()=>location.reload(), 1500);
            }
        });
    });

});
</script>
