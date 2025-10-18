<?php include("conexionfin.php"); ?>

<div class="container-fluid mt-4">
  <div class="card shadow">
    <div class="card-header text-white d-flex justify-content-between align-items-center ">
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
            <th>STR ID</th>
            <th>CÓD INTERNO</th>
            <th>DESCRIPCIÓN</th>
            <th>FAMILIA</th>
            <th>MICRAJE</th>
            <th>RELACIÓN</th>
            <th>CALIBRE</th>
            <th>UND EMBALAJE</th>
            <th>STOCK UNID/KG</th>
            <th>STOCK CAJA</th>
            <th>LOTE</th>
            <th>COSTO UNIT</th>
            <th>COSTO TOTAL</th>
            <th>ALMACÉN</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>

<script>
  function renderStock(data){
      if(parseFloat(data) <= 0) return '<span class="text-danger fw-bold">' + data + '</span>';
      return data;
  }

  var tabla = $('#tablaInventario').DataTable({
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
    columns: [
      { data: "str_id" },
      { data: "cod_producto" },
      { data: "descripcion" },
      { data: "familia" },
      { data: "micraje" },
      { data: "relacion" },
      { data: "calibre" },
      { data: "und_embalaje_minima" },
      { data: "stock_unidades_kg", render: renderStock },
      { data: "stock_caja", render: renderStock },
      { data: "lote" },
      { data: "costo_unitario" },
      { data: "costo_total" },
      { data: "nombre_almacen" }
    ],
    order: [[2, 'asc']],
    language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" }
  });

  $('#filtroAlmacen').on('change', function() {
    tabla.ajax.reload();
  });

</script>
s