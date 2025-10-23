<?php
include("conexionfin.php");

// Obtener lista de almacenes
$almacenes = $conexion->query("SELECT id, nombre FROM almacenes ORDER BY nombre ASC");
?>

<div class="container-fluid">
  <div class="card shadow">
    <div class="card-header bg-defaults text-white">
      <h5 >Salida Manual de Producto</h5>
    </div>
    <div class="card-body">

      <!-- 🔍 Buscador -->
      <div class="mb-3 position-relative">
        <label class="form-label fw-bold">Buscar producto:</label>
        <input type="text" id="buscador" class="form-control" placeholder="Escribe nombre o código...">
        <div id="resultados" class="list-group position-absolute w-50" style="z-index:1000;"></div>
      </div>

      <!-- Datos del producto seleccionado -->
      <div id="formProducto" style="display:none;">
        <div class="row g-3 mb-3">
          <div class="col-md-2">
            <label class="form-label fw-bold">Cajas / Paca / Bobina:</label>
            <input type="number" id="cajas" class="form-control" min="0" step="any">
          </div>
          <div class="col-md-2">
            <label class="form-label fw-bold">Cantidad (unidades / KG):</label>
            <input type="number" id="cantidad" class="form-control" min="0" step="any">
          </div>
          <div class="col-md-2">
            <label class="form-label fw-bold">Almacén:</label>
            <select id="almacen" class="form-control">
              <option value="">Seleccione</option>
              <?php
              mysqli_data_seek($almacenes, 0);
              while($a = $almacenes->fetch_assoc()){
                echo "<option value='{$a['id']}'>{$a['nombre']}</option>";
              }
              ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label fw-bold">Lote:</label>
            <select id="lote" class="form-control">
              <option value="">Seleccione un almacén primero</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label fw-bold">Precio Unitario:</label>
            <input type="number" id="costo" class="form-control" min="0" step="any">
          </div>
          <div class="col-md-2">
            <label class="form-label fw-bold">Precio Total:</label>
            <input type="text" id="total" class="form-control" readonly>
          </div>
        </div>

        <div class="text-end mb-3">
          <button class="btn btn-primary" id="agregarProducto">➕ Agregar producto</button>
        </div>
      </div>

      <!-- Observación -->
      <div class="mb-3">
        <label class="form-label fw-bold">Observación:</label>
        <input type="text" id="observacion" class="form-control" placeholder="Observación">
      </div>

      <!-- 🧾 Tabla -->
      <table class="table table-bordered text-center align-middle" id="tablaProductos">
        <thead class="table-light">
          <tr>
            <th>ID Producto</th>
            <th>ID Interno</th>
            <th>Descripción</th>
            <th>Cajas</th>
            <th>Cantidad</th>
            <th>Unidades</th>
            <th>UMB</th>
            <th>Almacén</th>
            <th>Lote</th>
            <th>Costo Unitario</th>
            <th>Costo Total</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>

      <div class="text-end">
        <button class="btn btn-success" id="guardarSalida">Guardar Salida</button>
      </div>

    </div>
  </div>
</div>

<script>
let productoSeleccionado = null;
let unidadesPorCaja = 1;

// 🔎 Buscar producto mientras escribes
$('#buscador').on('keyup', function(){
  let query = $(this).val().trim();
  if(query.length < 1){
    $('#resultados').fadeOut();
    return;
  }

  $.ajax({
    url: 'buscar_producto.php', 
    method: 'POST',
    data: {query: query},
    success: function(data){
      $('#resultados').html(data).fadeIn();
    }
  });
});

// 📦 Al seleccionar un producto del buscador
$(document).on('click', '.item-producto', function(){
  productoSeleccionado = {
    id: $(this).data('id'),
    interno: $(this).data('interno'),
    descripcion: $(this).data('descripcion'),
    umb: $(this).data('umb'),
    embalaje: $(this).data('embalaje'),
    unidades: parseFloat($(this).data('embalaje')) || 1
  };

  unidadesPorCaja = productoSeleccionado.unidades;
  $('#formProducto').slideDown();
  $('#resultados').fadeOut();
  $('#buscador').val(productoSeleccionado.descripcion);

  $('#cajas').val(1);
  $('#cantidad').val(unidadesPorCaja);
  recalcularTotal();
});

// 🏭 Cargar lotes cuando cambia almacén
$('#almacen').on('change', function(){
  let almacen_id = $(this).val();
  let id_producto = productoSeleccionado?.id;

  if(!almacen_id || !id_producto){
    $('#lote').html('<option value="">Seleccione un almacén primero</option>');
    return;
  }

  $.ajax({
    url: 'get_lotes_producto.php',
    method: 'POST',
    data: {id_producto: id_producto, almacen_id: almacen_id},
    success: function(data){
      $('#lote').html(data);
    }
  });
});

// 🔄 Recalcular cantidad y total al cambiar cajas o cantidad
$('#cajas').on('keyup change', function(){
  let cajas = parseFloat($(this).val()) || 0;
  let cantidad = cajas * unidadesPorCaja;
  $('#cantidad').val(cantidad.toFixed(2));
  recalcularTotal();
});

$('#cantidad').on('keyup change', function(){
  let cantidad = parseFloat($(this).val()) || 0;
  let cajas = cantidad / unidadesPorCaja;
  $('#cajas').val(cajas.toFixed(2));
  recalcularTotal();
});

$('#costo').on('keyup change', recalcularTotal);

function recalcularTotal(){
  let cajas = parseFloat($('#cajas').val()) || 0;
  let costo = parseFloat($('#costo').val()) || 0;
  $('#total').val((cajas * costo).toFixed(2));
}

// ➕ Agregar producto a la tabla
$('#agregarProducto').on('click', function(){
  if(!productoSeleccionado){
     Swal.fire({
                title: 'Advedtencia!',
                text: '⚠️ Primero selecciona un producto.',
                icon: 'warning',
                confirmButtonColor: 'rgba(231, 208, 0, 1)',
                confirmButtonText: 'OK'
            });
            return;
  }

  let cajas = parseFloat($('#cajas').val()) || 0;
  let cantidad = parseFloat($('#cantidad').val()) || 0;
  let almacen = $('#almacen').val();
  let lote = $('#lote').val();
  let costo = parseFloat($('#costo').val()) || 0;
  let total = (cajas * costo).toFixed(2);

  if(cajas <= 0 || !almacen || !lote || costo <= 0){
     Swal.fire({
                title: 'Advedtencia!',
                text: '⚠️ Completa todos los campos antes de agregar.',
                icon: 'warning',
                confirmButtonColor: 'rgba(231, 208, 0, 1)',
                confirmButtonText: 'OK'
            });
            return;
  }

  if($('#fila_' + productoSeleccionado.id).length){
    Swal.fire({
                title: 'Advedtencia!',
                text: '⚠️ Este producto ya está agregado.',
                icon: 'warning',
                confirmButtonColor: 'rgba(231, 208, 0, 1)',
                confirmButtonText: 'OK'
            });
            return;
  }

  let nombreAlmacen = $('#almacen option:selected').text();
  let nombreLote = $('#lote option:selected').val();

  let fila = `
    <tr id="fila_${productoSeleccionado.id}">
      <td>${productoSeleccionado.id}</td>
      <td>${productoSeleccionado.interno}</td>
      <td>${productoSeleccionado.descripcion}</td>
      <td>${cajas}</td>
      <td>${cantidad}</td>
      <td>${productoSeleccionado.embalaje}</td>
      <td>${productoSeleccionado.umb}</td>
      <td>${nombreAlmacen}</td>
      <td>${nombreLote}</td>
      <td>${costo.toFixed(2)}</td>
      <td>${total}</td>
      <td><button class="btn btn-danger btn-sm eliminar">🗑</button></td>
    </tr>
  `;

  $('#tablaProductos tbody').append(fila);
  $('#formProducto input, #formProducto select').val('');
  productoSeleccionado = null;
  $('#formProducto').slideUp();
  $('#buscador').val('');
});

// 🗑 Eliminar fila
$(document).on('click', '.eliminar', function(){
  $(this).closest('tr').remove();
});


// 💾 Guardar salida manual en movimientos_inventario
$('#guardarSalida').on('click', function(){
  let productos = [];
  let observacion = $('#observacion').val().trim() || 'Salida manual';

  $('#tablaProductos tbody tr').each(function(){
    let fila = $(this);
    productos.push({
      id_producto: fila.find('td:eq(0)').text().trim(),
      id_interno: fila.find('td:eq(1)').text().trim(),
      descripcion: fila.find('td:eq(2)').text().trim(),
      cajas: parseFloat(fila.find('td:eq(3)').text()) || 0,
      cantidad: parseFloat(fila.find('td:eq(4)').text()) || 0,
      unidades: fila.find('td:eq(5)').text().trim(),
      umb: fila.find('td:eq(6)').text().trim(),
      almacen_id: obtenerIdDesdeTexto($('#almacen option'), fila.find('td:eq(7)').text().trim()),
      lote: fila.find('td:eq(8)').text().trim(),
      costo_unitario: parseFloat(fila.find('td:eq(9)').text()) || 0,
      costo_total: parseFloat(fila.find('td:eq(10)').text()) || 0
    });
  });

  if(productos.length === 0){
    Swal.fire({
                title: 'Advedtencia!',
                text: '⚠️ Debes agregar al menos un producto para registrar la salida.',
                icon: 'warning',
                confirmButtonColor: 'rgba(231, 208, 0, 1)',
                confirmButtonText: 'OK'
            });
            return;
  }

  $.ajax({
    url: 'guardar_salida_manual.php',
    method: 'POST',
    data: {productos: JSON.stringify(productos), observacion: observacion},
    success: function(resp){
      Swal.fire({
                        title: 'Éxito!',
                        text: resp,
                        icon: 'success',
                        confirmButtonColor: '#28a745',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
    },
    error: function(){
      Swal.fire({
                title: 'Error!',
                text: '❌ Error al registrar la salida. Verifica la conexión o el servidor.',
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
            return;
    }
  });
});

// 🧩 Función auxiliar para obtener el ID real del almacén según su nombre
function obtenerIdDesdeTexto(options, textoBuscado) {
  let id = '';
  options.each(function(){
    if($(this).text().trim() === textoBuscado){
      id = $(this).val();
    }
  });
  return id;
}


</script>
