<?php 
include('conexionfin.php'); 
?>

<style>
.card-pedido {
    border: 1px solid #ccc;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
    background: #f8f9fa;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.card-pedido h5 {
    background: #002c5aff;
    color: white;
    padding: 8px;
    border-radius: 8px;
    font-size: 14px;
}

.form-control-sm,
.form-select-sm {
    font-size: 13px;
}

.label {
    font-weight: 600;
    font-size: 13px;
}

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
    <p>Procesando Orden de Produccion...</p>
</div>
<div class="container-fluid">
    <div class="card-header">
        <h4 class="card-title text-black">
            <i class="fa fa-box"></i> MÓDULO DE PRODUCCIÓN HANDYPLAST
        </h4>
    </div>

    <div class="mt-3">
        <button class="btn btn-warning btn-lg" type="button" id="select_pedido">
            <i class="fa fa-search"></i> SELECCIONAR O.P
        </button>
        <button type="button" class="btn btn-info  btn-lg" id="btn_select_maquina">
            <i class="fa fa-cogs"></i> Seleccionar Máquina
        </button>
        <button class="btn btn-success btn-lg" type="button" id="guardar_op">
            <i class="fa fa-save"></i> Programar O.P
        </button>
        <div id="alerta-maquina" class="alert alert-success mt-3 text-center fw-bold" style="display:none;">
        Máquina asignada a esta Orden: <span id="nombre-maquina-alerta"></span>
    </div>
    </div>
    
    <div id="formularios-container" class="mt-4">
        <p class="text-muted text-center">Seleccione una o varias órdenes de pedidos para comenzar</p>
    </div>
    
</div>
<div class="row mt-3">
    <div class="col-md-3">
        <label hidden class="label">Código Máquina</label>
        <input type="text" hidden id="maquina_cod" class="form-control form-control-sm" readonly>
    </div>
    <div class="col-md-5">
        <label hidden class="label">Nombre de Máquina</label>
        <input type="text" hidden id="maquina_nombre" class="form-control form-control-sm" readonly>
    </div>
    <div class="col-md-2">
        <label hidden class="label">ID Máquina</label>
        <input type="text" hidden id="maquina_id" class="form-control form-control-sm" readonly>
    </div>
</div>

<script>
function showSpinner() {
    $('#spinner').show();
}

function hideSpinner() {
    $('#spinner').hide();
}

// 🔹 Agregar formulario de producto + selector de bobina
function addProductoForm(producto) {
    // Aseguramos que vengan los IDs correctos del JSON
    const pedidoID = producto.pedido_id || producto.id_pedido || '';
    const productoID = producto.producto_id || producto.id_producto || '';

    const formHtml = `
    <div class="card-pedido" data-pedido-id="${pedidoID}" data-producto-id="${productoID}">
        <h5>O.P #${producto.numero_pedido || ''} | ${producto.descripcion || ''} | ${producto.cod_producto || ''}</h5>
        <div class="row g-2">
            <div class="col-md-3">
                <label class="label">Cliente</label>
                <input type="text" class="form-control form-control-sm" value="${producto.nombre_cliente || ''}" readonly>
            </div>
            <div class="col-md-2">
                <label class="label">Ciudad</label>
                <input type="text" class="form-control form-control-sm" value="${producto.ciudad || ''}" readonly>
            </div>
            <div class="col-md-1">
                <label class="label">Calibre</label>
                <input type="text" class="form-control form-control-sm" value="${producto.calibre || ''}" readonly>
            </div>
            <div class="col-md-2">
                <label class="label">Referencia</label>
                <input type="text" class="form-control form-control-sm" 
                    value="${(producto.ref_1 || '') + 'x' + (producto.ref_2 || '')+ ' ' +(producto.etiqueta || '')+ '-' +(producto.relacion || '')}" 
                    readonly>
            </div>
            <div class="col-md-2">
                <label class="label">Cantidad Pedido</label>
                <input type="text" class="form-control form-control-sm" 
                    value="${(producto.cantidad || '') + '  Paca'+''}" readonly>
                <input type="text" class="form-control form-control-sm" 
                    value="${(producto.cantidad * (producto.und_embalaje_minima || 0)) + '  unds/kg'+''}" readonly>
            </div>
            <div class="col-md-2">
                <label class="label">Peso GR</label>
                <input type="text" class="form-control form-control-sm" value="${producto.peso_gr || ''}" readonly>
            </div>
        </div>

        <!-- 🔹 Selección de Bobina + Lote -->
        <div class="row g-2 mt-3">
            <div class="col-md-3">
                <label class="label">Seleccionar Bobina</label>
                <select class="form-control form-select-sm select-bobina">
                    <option value="">-- Seleccione una bobina --</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="label">Tipo de Bobina</label>
                <input type="text" class="form-control form-control-sm tipo-bobina" readonly>
            </div>
            <div class="col-md-1">
                <label class="label">Ref. Tubo</label>
                <input type="text" class="form-control form-control-sm ref-tubo" value="${producto.ref_tubo || ''}" readonly>
            </div>

            <div class="col-md-2">
                <label class="label">Inventario Bobina (kg)</label>
                <input type="text" class="form-control form-control-sm inventario-bobina" readonly>
            </div>

            <div class="col-md-2">
                <label class="label">Lote</label>
                <select class="form-control form-select-sm select-lote">
                    <option value="">-- Seleccione un lote --</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="label">Peso a Utilizar (kg)</label>
                <input type="number" class="form-control form-control-sm peso-utilizar" 
                    value="${(producto.cantidad * (producto.peso_gr || 0) / 1000).toFixed(2)}" min="0" step="0.01" readonly>
            </div>
            
            <div class="col-md-2">
                <label class="label">Cantidad</label>
                <input type="text" class="form-control form-control-sm cantidad-bobina" 
                    value="${(producto.cantidad || '') + '  Paca'+''}" readonly>
            </div>
            <div class="col-md-2">
                <label class="label">Bobina</label>
                <input type="text" class="form-control form-control-sm ancho-bobina" 
                    style="background:#f8f9fa;" 
                    value="${(producto.ref_1 || '') + 'cm  Cant.: 0'}" readonly>
                    <input type="text" class="form-control form-control-sm bobinacan" 
                    style="background:#f8f9fa;" readonly>
            </div>
        </div>
    </div>`;

    $('#formularios-container').append(formHtml);

    // 🔹 Cargar bobinas al desplegable
    loadBobinas($('.select-bobina').last());
}


// 🔹 Cargar bobinas desde el inventario
function loadBobinas(selectElement) {
    $.ajax({
        url: 'get_bobinas.php',
        method: 'GET',
        dataType: 'json',
        success: function(bobinas) {
            if (bobinas.length > 0) {
                bobinas.forEach(b => {
                    selectElement.append(`<option value="${b.id_producto}" 
                        data-tipo="${b.tipo}"
                        data-cantidad="${b.stock_unidades_kg}"
                        data-peso="${b.peso_kg}"
                        data-cod_producto="${b.cod_producto}">
                        ${b.descripcion} (${b.cod_producto})
                    </option>`);
                });
            } else {
                selectElement.append('<option disabled>No hay bobinas disponibles</option>');
            }
        }
    });
}

// 🔹 Cuando se seleccione una bobina, llenar los campos + cargar lotes
$(document).on('change', '.select-bobina', function() {
    const option = $(this).find('option:selected');
    const tipo = option.data('cod_producto') || '';
    const cantidad = option.data('cantidad') || '';
    const peso_unitario = parseFloat(option.data('peso')) || 0;
    const peso_total = cantidad * peso_unitario;

    const parent = $(this).closest('.card-pedido');
    parent.find('.tipo-bobina').val(tipo);
    parent.find('.inventario-bobina').val(peso_total);

    // 🔸 Cargar lotes correspondientes
    const bobina_id = option.val();
    const selectLote = parent.find('.select-lote');
    selectLote.empty().append('<option value="">Cargando lotes...</option>');

    if (bobina_id) {
        $.ajax({
            url: 'get_lotes_por_bobina.php',
            method: 'POST',
            data: {
                bobina_id
            },
            dataType: 'json',
            success: function(lotes) {
                selectLote.empty().append('<option value="">-- Seleccione un lote --</option>');
                if (lotes.length > 0) {
                    lotes.forEach(l => {
                        selectLote.append(`<option value="${l.lote}">${l.lote}</option>`);
                    });
                } else {
                    selectLote.append('<option disabled>No hay lotes disponibles</option>');
                }
            },
            error: function() {
                selectLote.empty().append('<option disabled>Error al cargar lotes</option>');
            }
        });
    } else {
        selectLote.empty().append('<option value="">-- Seleccione un lote --</option>');
    }
});

// 🔹 Cuando se seleccione un lote, calcular automáticamente cuántas bobinas se necesitan
$(document).on('change', '.select-lote', function() {
    const parent = $(this).closest('.card-pedido');
    const selectBobina = parent.find('.select-bobina option:selected');

    const pesoUtilizar = parseFloat(parent.find('.peso-utilizar').val()) || 0;
    const cantidad = parseFloat(selectBobina.data('cantidad')) || 0;
    const pesoUnitario = parseFloat(selectBobina.data('peso')) || 0;

    const pesoDisponible = cantidad * pesoUnitario;

    // Calcular cuántas bobinas completas se necesitan
    let bobinasNecesarias = 0;
    if (pesoUnitario > 0) {
        bobinasNecesarias = pesoUtilizar / pesoUnitario;
    }

    // Redondear siempre hacia arriba
    bobinasNecesarias = Math.ceil(bobinasNecesarias);

    // Actualizar campo “Bobina”
    const ancho = (parent.find('.ancho-bobina').val().split('cm')[0] || '').trim();
    const campoBobina = parent.find('.ancho-bobina');
    const campoBobina2 = parent.find('.bobinacan');

    campoBobina.val(`${ancho}cm  Cant.: ${bobinasNecesarias}`);
    campoBobina2.val(`${bobinasNecesarias}`);

    // 🎨 Colorear según disponibilidad
    if (pesoDisponible >= pesoUtilizar) {
        campoBobina.css('background', '#d4edda'); // Verde claro
        campoBobina.css('color', '#155724');
    } else {
        campoBobina.css('background', '#f8d7da'); // Rojo claro
        campoBobina.css('color', '#721c24');
    }

    // Actualizar inventario visible
    parent.find('.inventario-bobina').val(pesoDisponible.toFixed(2));
});

// 🔹 Abrir modal
$('#select_pedido').click(function() {
    uni_modal_documentos("Seleccionar Pedidos Stretch", "manage_produccion.php");
});

// 🔹 Llenar formularios desde modal (varios pedidos)
function fillFormsFromPedidos(seleccionados) {
    if (seleccionados.length === 0) {
        alert('Seleccione al menos un producto del pedido.');
        return;
    }

    $('#formularios-container').empty();

    let requests = seleccionados.map(item =>
        $.ajax({
            url: 'get_pedido_stretch.php',
            method: 'POST',
            data: {
                pedido_id: item.pedido_id,
                cod_producto: item.cod_producto
            },
            dataType: 'json'
        })
    );

    Promise.all(requests).then(results => {
        let totalProductos = 0;
        results.forEach(res => {
            if (Array.isArray(res) && res.length > 0) {
                res.forEach(prod => addProductoForm(prod));
                totalProductos += res.length;
            }
        });
        if (totalProductos === 0) {
            $('#formularios-container').html(
                '<p class="text-center text-danger">No se encontraron productos Stretch seleccionados.</p>');
        }
    }).catch(err => {
        console.error(err);
        alert('Error al cargar los productos.');
    });
}

$('#btn_select_maquina').click(function() {
    uni_modal_documentos("Seleccionar Línea de Máquina", "lista_maquinas.php");
});

// 🔹 Guardar O.P en la base de datos
$('#guardar_op').click(function() {
    const maquina_id = $('#maquina_id').val();
    const usuario = 'Admin'; // según tu sesión
    const observaciones = prompt('Ingrese observaciones para la O.P (opcional):') || '';

    if (!maquina_id) {
        Swal.fire({
            icon: 'warning',
            title: '⚠️ Falta seleccionar máquina',
            text: 'Debes seleccionar una máquina antes de guardar.',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#f0ad4e'
        });
        return;
    }

    const productos = [];

    // 🔸 Recorremos todos los formularios de producto
    $('.card-pedido').each(function() {
        const card = $(this);
        const pedido_id = card.data('pedido-id') || '';
        const producto_id = card.data('producto-id') || '';
        const peso_utilizar = parseFloat(card.find('.peso-utilizar').val()) || 0;
        const cantidad_programada = parseFloat(card.find('.bobinacan').val()) || 0;

        // ⚙️ Validar que los valores existan
        if (pedido_id && producto_id && peso_utilizar > 0 && cantidad_programada > 0) {
            productos.push({
                pedido_id,
                producto_id,
                peso_utilizar,
                cantidad_programada
            });
        } else {
            console.warn(`Producto omitido: Pedido ${producto_id} - Faltan datos.`);
        }
    });

    // ⚠️ Validar antes de enviar
    if (productos.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: '⚠️ Sin productos válidos',
            text: 'Debe seleccionar al menos un producto con datos completos (bobina, lote, cantidad).',
            confirmButtonText: 'Corregir',
            confirmButtonColor: '#f0ad4e'
        });
        return;
    }

    // ✅ Mostrar spinner solo después de validar todo
    showSpinner();

    console.log('🧾 Enviando productos:', productos);

    $.ajax({
        url: 'guardar_op.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            maquina_id,
            usuario,
            observaciones,
            productos
        }),
        dataType: 'json',
        success: function(res) {
            hideSpinner();

            if (res.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: '✅ ¡Orden de Producción guardada!',
                    text: res.message,
                    showConfirmButton: true,
                    confirmButtonText: 'Imprimir O.P',
                    confirmButtonColor: '#198754'
                }).then(() => {
                    const idPedido = res.pedido_id || (res.ordenes && res.ordenes[0]?.pedido_id);
                    if (idPedido) {
                        window.open(`imprimir_op_fpdf.php?id=${idPedido}`, '_blank');
                    }
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: '❌ Error',
                    text: res.message || 'Ocurrió un problema al guardar la orden.',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#dc3545'
                });
            }
        },
        error: function(err) {
            hideSpinner();
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: '🚨 Error en el servidor',
                text: 'No se pudo guardar la orden de producción. Intenta nuevamente.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545'
            });
        }
    });
});
$(document).on('change', '#maquina_nombre', function () {
    const nombreMaquina = $(this).val();
    if (nombreMaquina) {
        $('#nombre-maquina-alerta').text(nombreMaquina);
        $('#alerta-maquina')
            .fadeIn()
            .css('background', '#d1ecf1')
            .css('color', '#0c5460')
            .css('border', '1px solid #bee5eb');
    } else {
        $('#alerta-maquina').fadeOut();
    }
});
function mostrarAlertaMaquina() {
    const nombreMaquina = document.getElementById('maquina_nombre').value;
    const alerta = document.getElementById('alerta-maquina');
    const spanNombre = document.getElementById('nombre-maquina-alerta');

    if (nombreMaquina) {
        spanNombre.textContent = nombreMaquina;
        alerta.style.display = 'block';
        alerta.style.background = '#d1ecf1';
        alerta.style.color = '#0c5460';
        alerta.style.border = '1px solid #bee5eb';
    } else {
        alerta.style.display = 'none';
    }
}

</script>