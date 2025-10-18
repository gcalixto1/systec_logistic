<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Crear Pedido</title>
<style>
.table td, .table th { vertical-align: middle; }
</style>
</head>
<body class="bg-light">

<div class="container-fluid">
    <h2 class="mb-4 text-center">📝 Crear Pedido</h2>

    <form id="formPedido">

        <!-- INFO PEDIDO -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-12">
                        <label>Cliente</label>
                        <select class="form-control" id="cliente" name="cliente_id"></select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label>Sede</label>
                        <input type="text" class="form-control" id="nombre_sede" name="nombre_sede">
                    </div>
                    <div class="col-12 col-md-3">
                        <label>Canal de Ventas</label>
                        <select class="form-control" id="canal" name="canal_venta">
                            <option value="">Seleccione...</option>
                            <option value="correo electronico">Correo Electronico</option>
                            <option value="llamada telefonica">Llamada Telefonica</option>
                            <option value="punto fisico">Punto Fisico</option>
                            <option value="whatsApp">WhatsApp</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label>Plazo de Pago (días)</label>
                        <input type="number" class="form-control" id="plazo" name="plazo_pago_dias" placeholder="30">
                    </div>
                    <div class="col-12 col-md-3">
                        <label>Tipo de Transporte</label>
                        <select class="form-control" id="transporte" name="tipo_transporte">
                            <option value="">Seleccione...</option>
                            <option value="cliente recoge">Cliente recoge</option>
                            <option value="transporte propio">Transporte Propio</option>
                            <option value="transporte tercero">Transporte Tercero</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <!-- AGREGAR PRODUCTO -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Agregar Producto</h5>
                <div class="row g-3 align-items-end" id="form-producto">
                    <div class="col-12 col-md-12">
                        <label>Producto</label>
                        <select class="form-control" id="producto" name="producto_id"></select>
                        <input type="hidden" id="descproducto" name="descproducto">
                        <input type="hidden" id="idPR" name="idPR">
                    </div>
                    <div class="col-6 col-md-2"><label>Tipo</label><input type="text" class="form-control" id="tipo" placeholder="0"></div>
                    <div class="col-6 col-md-2"><label>Calibre</label><input type="text" class="form-control" id="calibre" placeholder="0"></div>
                    <div class="col-6 col-md-2"><label>Referencia 1</label><input type="number" class="form-control" id="ref_1" placeholder="0"></div>
                    <div class="col-6 col-md-2"><label>Referencia 2</label><input type="number" class="form-control" id="ref_2" placeholder="0"></div>
                    <div class="col-6 col-md-2"><label>Relación</label><input type="text" class="form-control" id="relacion" placeholder="0"></div>
                    <div class="col-6 col-md-2"><label>Cantidad (Unidades)</label><input type="text" class="form-control" id="cantidad" placeholder="0"></div>
                    <div class="col-6 col-md-2"><label>Caja / Paca</label><input type="number" class="form-control" id="caja" placeholder="0"></div>
                    <div class="col-6 col-md-2">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="checkIVAProducto" checked>
                            <label class="form-check-label">Marcado Facturado / Sin Marcar Remisionado</label>
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <label>Lista de Precios</label>
                        <select class="form-control" id="listaPrecio">
                            <option value="5">Lista 5</option>
                            <option value="4">Lista 4</option>
                            <option value="3">Lista 3</option>
                            <option value="2">Lista 2</option>
                            <option value="1">Lista 1</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-2"><label>Precio Unit.</label><input type="number" class="form-control" id="precio" placeholder="0.00" step="0.01"></div>
                    <div class="col-6 col-md-2"><label>Subtotal</label><input type="text" class="form-control" id="subtotal" readonly></div>
                    <div class="col-6 col-md-2"><button type="button" class="btn btn-primary w-100" id="btnAgregar">➕</button></div>
                </div>
            </div>
        </div>
   <!-- ENVÍO PARA MUESTRA -->
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="envioMuestra">
            <label class="form-check-label" for="envioMuestra">
                Envío para Muestra
            </label>
        </div>
        <!-- DETALLE -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Detalle del Pedido</h5>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center" id="tablaDetalle">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unit.</th>
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
            <div class="card-body">
                <div class="row justify-content-end">
                    <div class="col-12 col-md-4">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-end"><strong>Subtotal:</strong></td>
                                    <td class="text-end" id="totalSubtotal">0.00</td>
                                </tr>
                                <tr>
                                    <td class="text-end"><strong>IVA:</strong></td>
                                    <td class="text-end" id="totalIVA">0.00</td>
                                </tr>
                                <tr class="border-top">
                                    <td class="text-end"><strong>Total:</strong></td>
                                    <td class="text-end h5" id="totalFinal">0.00</td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="submit" class="btn btn-success w-100 mt-3">💾 Guardar Pedido</button>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>
<script>
// VARIABLES
let detalle = [];
let precios = { facturado:{}, remi:{} };
let unidadesPorCaja = 1;
let ivaGlobal = null; 

// ================== SELECT2 CLIENTES ==================
$('#cliente').select2({
    placeholder: "Buscar cliente por código, NIT o nombre...",
    allowClear: true,
    ajax: {
        url: 'buscar_cliente_por_dni.php',
        dataType: 'json',
        delay: 250,
        data: params => ({ q: params.term }),
        processResults: data => data
    },
    minimumInputLength: 1
});
$('#cliente').on('select2:select', e => {
    let data = e.params.data;
    $('#nombre_sede').val(data.nombre_sede || "");
    $('#plazo').val(data.plazo_pago || "");
});
$('#cliente').on('select2:clear', () => { $('#nombre_sede').val(""); $('#plazo').val(""); });

// ================== SELECT2 PRODUCTOS ==================
$('#producto').select2({
    placeholder: "Buscar producto por código o descripción...",
    allowClear: true,
    ajax: {
        url: 'buscar_productoC.php',
        dataType: 'json',
        delay: 250,
        data: params => ({ q: params.term }),
        processResults: data => data
    },
    minimumInputLength: 1
});
$('#producto').on('select2:select', function(e){
    let data = e.params.data;
    unidadesPorCaja = parseFloat(data.unidades) || 1;
    $('#caja').val(1);
    $('#cantidad').val(unidadesPorCaja);
    $('#calibre').val(data.calibre||0);
    $('#ref_1').val(data.ref1||0);
    $('#ref_2').val(data.ref2||0);
    $('#tipo').val(data.tipo||0);
    $('#descproducto').val(data.text);
    $('#idPR').val(data.id);
    $('#relacion').val(data.relacion||0);
    let base = parseFloat(data.precio)||0;
    let baseRemi = parseFloat(data.precioRemi)||base;
    precios.facturado[5]=base; precios.remi[5]=baseRemi;
    for(let i=4;i>=1;i--){
        precios.facturado[i]=precios.facturado[i+1]-precios.facturado[i+1]*0.03;
        precios.remi[i]=precios.remi[i+1]-precios.remi[i+1]*0.03;
    }
    $('#listaPrecio').val(5);
    actualizarPrecio();
});

// ================== FUNCIONES DE PRECIOS ==================
function actualizarPrecio(){
    if($('#envioMuestra').is(':checked')){
        $('#precio,#subtotal').val('0.00');
        return;
    }
    let lista = parseInt($('#listaPrecio').val());
    let precioFinal = $('#checkIVAProducto').is(':checked') ? precios.remi[lista] : precios.facturado[lista];
    $('#precio').val(precioFinal.toFixed(2));
    calcularSubtotal();
}
function calcularSubtotal(){
    if($('#envioMuestra').is(':checked')){
        $('#subtotal').val('0.00');
        $('#cantidad').val($('#cantidad').val()); // mantener cantidad real
        return;
    }
    let cajas = parseFloat($('#caja').val())||0;
    let cantidadTotal = cajas * unidadesPorCaja;
    $('#cantidad').val(cantidadTotal);
    let precio = parseFloat($('#precio').val())||0;
    $('#subtotal').val((cajas*precio).toFixed(2));
}
$('#listaPrecio,#checkIVAProducto,#precio').on('change keyup', actualizarPrecio);
$('#caja').on('change keyup', calcularSubtotal);
// Cuando cambia cantidad, recalcula cajas y subtotal
$('#cantidad').on('change keyup', function(){
    let cantidadTotal = parseFloat($(this).val()) || 0;
    let cajas = cantidadTotal / unidadesPorCaja; // calculo inverso
    $('#caja').val(cajas.toFixed(2));

    let precio = parseFloat($('#precio').val())||0;
    $('#subtotal').val((cajas * precio).toFixed(2));
});

// Cuando cambia caja, actualiza cantidad (por si el usuario cambia cajas directamente)
$('#caja').on('change keyup', function(){
    let cajas = parseFloat($(this).val())||0;
    let cantidadTotal = cajas * unidadesPorCaja; // normal
    $('#cantidad').val(cantidadTotal);

    let precio = parseFloat($('#precio').val())||0;
    $('#subtotal').val((cajas * precio).toFixed(2));
});


// ================== AGREGAR PRODUCTO ==================
$('#btnAgregar').on('click', function(){
    let productoId = $("#idPR").val();
    let producto = $('#descproducto').val();
    let cantidad = parseFloat($('#caja').val());
    let precio = parseFloat($('#precio').val());
    let subtotal = parseFloat($('#subtotal').val());
    let aplicaIVA = $('#checkIVAProducto').is(':checked');
    let iva = aplicaIVA ? subtotal*0.19 : 0;
    let total = subtotal + iva;

    // Si envío para muestra, forzar totales en 0
    if($('#envioMuestra').is(':checked')){
        subtotal = 0; iva = 0; total = 0; precio = 0;
    }

    // ================== VALIDACIÓN DE IVA GLOBAL ==================
    if (ivaGlobal === null) {
        // Primer producto define la regla
        ivaGlobal = aplicaIVA;
    } else {
        // Si ya hay regla definida y no coincide => error
        if (ivaGlobal && !aplicaIVA) {
            Swal.fire('❌ No permitido', 'No puede agregar un producto sin IVA porque ya agregó uno con IVA.', 'error');
            return;
        }
        if (!ivaGlobal && aplicaIVA) {
            Swal.fire('❌ No permitido', 'No puede agregar un producto con IVA porque ya agregó uno sin IVA.', 'error');
            return;
        }
    }
    // =============================================================

    detalle.push({productoId,producto, cantidad, precio, subtotal, iva, total});
    renderTabla();
    $('#form-producto input, #form-producto select').val('');
    $('#producto').val(null).trigger('change');
    actualizarTotales();
});

// ================== TABLA ==================
function renderTabla(){
    let tbody = $("#tablaDetalle tbody"); 
    tbody.empty();
    detalle.forEach((f,i)=>{
        let precio = $('#envioMuestra').is(':checked') ? '0.00' : f.precio.toFixed(2);
        let sub = $('#envioMuestra').is(':checked') ? '0.00' : f.subtotal.toFixed(2);
        let iva = $('#envioMuestra').is(':checked') ? '0.00' : f.iva.toFixed(2);
        let tot = $('#envioMuestra').is(':checked') ? '0.00' : f.total.toFixed(2);

        tbody.append(`<tr>
            <td>${f.producto}</td>
            <td>${$('#envioMuestra').is(':checked') ? f.cantidad.toFixed(2) : f.cantidad.toFixed(2)}</td>
            <td>${precio}</td>
            <td>${sub}</td>
            <td>${iva}</td>
            <td>${tot}</td>
            <td><button class="btn btn-danger btn-sm eliminar" data-i="${i}">🗑️</button></td>
        </tr>`);
    });
}

// ================== ENVÍO PARA MUESTRA ==================
$('#envioMuestra').on('change', function(){
    if($(this).is(':checked')){
        // Bloquear campos de precio y cantidad
        // $('#precio,#cantidad,#caja,#checkIVAProducto,#listaPrecio').prop('disabled', true);
    } else {
        $('#precio,#cantidad,#caja,#checkIVAProducto,#listaPrecio').prop('disabled', false);
        actualizarPrecio();
    }
    renderTabla();
    actualizarTotales();
});

$(document).on('click','.eliminar',function(){
    let i = $(this).data('i'); detalle.splice(i,1);
    renderTabla(); actualizarTotales();
});

// ================== TOTALES ==================
function actualizarTotales(){
    if($('#envioMuestra').is(':checked')){
        $('#totalSubtotal').text('0.00');
        $('#totalIVA').text('0.00');
        $('#totalFinal').text('0.00');
        return;
    }
    let subtotal = detalle.reduce((a,f)=>a+f.subtotal,0);
    let iva = detalle.reduce((a,f)=>a+f.iva,0);
    let total = detalle.reduce((a,f)=>a+f.total,0);
    $('#totalSubtotal').text(subtotal.toFixed(2));
    $('#totalIVA').text(iva.toFixed(2));
    $('#totalFinal').text(total.toFixed(2));
}

// ================== ENVÍO PARA MUESTRA ==================
$('#envioMuestra').on('change', function(){
    renderTabla();
    actualizarTotales();
    // también poner campos del formulario en 0
    if($(this).is(':checked')){
        $('#precio,#subtotal').val('0.00');
    } else {
        actualizarPrecio();
    }
});

// ================== GUARDAR PEDIDO ==================
$('#formPedido').on('submit', function(e){
    e.preventDefault();

    if(detalle.length == 0){
        Swal.fire("Agregue al menos un producto");
        return;
    }

    let formData = new FormData(this);
    formData.append('detalle', JSON.stringify(detalle));

    $.ajax({
        url: 'ajax.php?action=save_pedido',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(resp){
            if(resp.success){
                Swal.fire({
                    icon: 'success',
                    title: '✅ Pedido guardado',
                    text: `Número: ${resp.numero_pedido}`,
                }).then(() => {
                    // Abrir el PDF del pedido en otra ventana
                    window.open('docpedido.php?id_pedido=' + encodeURIComponent(resp.id_pedido), '_blank');
                });
            } else {
                Swal.fire('❌ Error', resp.message, 'error');
            }
        },
        error: function(xhr){
            console.log(xhr.responseText);
            Swal.fire('Error','No se pudo guardar','error');
        }
    });
});

</script>

</body>
</html>
