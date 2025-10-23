<?php include("conexionfin.php"); ?>

<div class="container-fluid mt-4">
  <div class="card shadow">
    <div class="card-header text-white d-flex justify-content-between align-items-center">
      <h5>📦 Inventario General por Almacén y Lote</h5>
      <div class="d-flex align-items-center">
        <label class="me-2 text-white">Filtrar por almacén:</label>
        <select id="filtroAlmacen" class="form-control form-select-sm">
          <option value="">Todos</option>
          <?php
          $q = mysqli_query($conexion, "SELECT id, nombre FROM almacenes");
          while ($a = mysqli_fetch_assoc($q)) {
            echo "<option value='{$a['id']}'>{$a['nombre']}</option>";
          }
          ?>
        </select>
      </div>
    </div>

    <div class="card-body">
      <table id="tablaInventario" class="table table-striped table-bordered text-center w-100">
        <thead class="table-dark">
          <tr>
            <th>Str ID</th>
            <th>Cod Interno</th>
            <th>descripcion</th>
            <th>Familia</th>
            <th>Micraje</th>
            <th>relacion</th>
            <th>Calibre</th>
            <th>Und Embalaje</th>
            <th>Stock unidades</th>
            <th>Stock Caja/Paca/KG</th>
            <th>Lote</th>
            <th>Costo Uni</th>
            <th>costo Total</th>
            <th>Almacen</th>
            <th>Accion</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>

<!-- 🔁 Modal de Traslado -->
<div class="modal fade" id="modalTraslado" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title text-white">🔁 Traslado de Producto entre Almacenes</h5>
      </div>
      <div class="modal-body">
        <form id="formTraslado">
          <input type="hidden" id="tras_id_producto">
          <input type="hidden" id="tras_id_almacen_origen">
          <input type="hidden" id="tras_und_embalaje">

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label fw-bold">ID Interno</label>
              <input type="text" class="form-control" id="tras_str_id" readonly>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Descripción</label>
              <input type="text" class="form-control" id="tras_descripcion" readonly>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-4">
              <label class="form-label fw-bold">Lote</label>
              <select id="tras_lote" class="form-control" required></select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-bold">Cantidad (Unidades/Kg)</label>
              <input type="number" step="0.01" class="form-control" id="tras_cantidad_unidades" required>
              <small id="tras_max" class="text-muted"></small>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-bold">Cantidad (Cajas)</label>
              <input type="number" step="0.01" class="form-control" id="tras_cantidad_cajas" required>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <label class="form-label fw-bold">Almacén Origen</label>
              <input type="text" id="tras_almacen_origen_nombre" class="form-control" readonly>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Almacén Destino</label>
              <select id="tras_almacen_destino" class="form-control" required>
                <option value="">Selecciona un almacén</option>
                <?php
                $almacenes = $conexion->query("SELECT id, nombre FROM almacenes");
                while ($a = $almacenes->fetch_assoc()) {
                  echo "<option value='{$a['id']}'>{$a['nombre']}</option>";
                }
                ?>
              </select>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success" id="btnTransferir">Transferir</button>
      </div>
    </div>
  </div>
</div>

<script>
  const tabla = $('#tablaInventario').DataTable({
    ajax: {
      url: 'ajax_inventario.php',
      type: 'GET',
      data: function(d) {
        d.almacen = $('#filtroAlmacen').val();
      },
      dataSrc: function(json) {
        return json.data || [];
      }
    },
    columns: [{
        data: "str_id"
      },
      {
        data: "cod_producto"
      },
      {
        data: "descripcion"
      },
      {
        data: "familia"
      },
      {
        data: "micraje"
      },
      {
        data: "relacion"
      },
      {
        data: "calibre"
      },
      {
        data: "und_embalaje_minima"
      },
      {
        data: "stock_unidades_kg"
      },
      {
        data: "stock_caja_paca_bobinas"
      },
      {
        data: "lote"
      },
      {
        data: "costo_unitario"
      },
      {
        data: "costo_total"
      },
      {
        data: "nombre_almacen"
      },
      {
        data: null,
        render: function(data) {
          return `
            <button class="btn btn-sm btn-primary btnTrasladar"
              data-id="${data.id_producto}"
              data-str="${data.str_id}"
              data-desc="${data.descripcion}"
              data-lote="${data.lote}"
              data-almacen="${data.nombre_almacen}"
              data-almacenid="${data.almacen_id}"
              data-stock="${data.stock_unidades_kg}"
              data-und="${data.und_embalaje_minima}">
              🔁 Trasladar
            </button>`;
        }
      }
    ],
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
    }
  });

  $('#filtroAlmacen').on('change', function() {
    tabla.ajax.reload();
  });

  const modalTraslado = new bootstrap.Modal(document.getElementById('modalTraslado'));

  // --- Abrir modal de traslado ---
  $(document).on('click', '.btnTrasladar', function() {
    const d = $(this).data();
    $('#tras_id_producto').val(d.id);
    $('#tras_str_id').val(d.str);
    $('#tras_descripcion').val(d.desc);
    $('#tras_id_almacen_origen').val(d.almacenid);
    $('#tras_almacen_origen_nombre').val(d.almacen);
    $('#tras_und_embalaje').val(d.und);
    $('#tras_cantidad_unidades').val(d.und);
    $('#tras_max').text('Cant. Máx: ' + d.stock + ' unidades');
    $('#tras_cantidad_cajas').val('1');

    // Cargar lotes
    $.getJSON('ajax_lotes_producto.php', {
      producto_id: d.id,
      almacen_id: d.almacenid
    }, function(resp) {
      let opciones = '<option value="">Selecciona lote</option>';
      resp.forEach(l => {
        opciones += `<option value="${l.lote}">${l.lote} (Stock: ${l.stock} und.)</option>`;
      });
      $('#tras_lote').html(opciones);
    });

    modalTraslado.show();
  });

  // --- Sincronizar cantidades ---
  $('#tras_cantidad_unidades').on('input', function() {
    const und = parseFloat($('#tras_und_embalaje').val()) || 1;
    const unidades = parseFloat($(this).val()) || 0;
    $('#tras_cantidad_cajas').val((unidades / und).toFixed(2));
  });

  $('#tras_cantidad_cajas').on('input', function() {
    const und = parseFloat($('#tras_und_embalaje').val()) || 1;
    const cajas = parseFloat($(this).val()) || 0;
    $('#tras_cantidad_unidades').val((cajas * und).toFixed(2));
  });

  // --- Enviar traslado ---
  $('#btnTransferir').on('click', function() {
    const data = {
      producto_id: $('#tras_id_producto').val(),
      almacen_origen: $('#tras_id_almacen_origen').val(),
      almacen_destino: $('#tras_almacen_destino').val(),
      lote: $('#tras_lote').val(),
      cantidad_unidades: $('#tras_cantidad_unidades').val(),
      cantidad_cajas: $('#tras_cantidad_cajas').val()
    };

    if (!data.almacen_destino || !data.lote || !data.cantidad_unidades) {
      Swal.fire({
        title: 'Advertencia!',
        text: '⚠️ Todos los campos son obligatorios.',
        icon: 'warning',
        confirmButtonColor: '#d33',
        confirmButtonText: 'OK'
      });
      return;
    }

    $.post('ajax_traslado.php', data, function(resp) {
      if (resp.success) {
        Swal.fire({
          title: 'Éxito!',
          text: resp.message,
          icon: 'success',
          confirmButtonColor: '#28a745',
          confirmButtonText: 'OK'
        }).then((result) => {
          if (result.isConfirmed) {
            location.reload();
          }
        });
      }
    }, 'json');
  });
</script>