<?php include("conexionfin.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Traslado de Producto entre Almacenes</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="card shadow">
    <div class="card-header bg-gradient" style="background: linear-gradient(90deg,#38b2ac,#4299e1); color: white;">
      <h5 class="mb-0">🔄 Transferencia de Producto entre Almacenes</h5>
    </div>
    <div class="card-body">

      <form id="formTraslado">
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label fw-bold">Producto (ID Interno)</label>
            <input type="text" id="id_interno" name="id_interno" class="form-control" placeholder="Ej: ADH2440YD40" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold">Cantidad a Transferir</label>
            <input type="number" step="0.01" id="cantidad" name="cantidad" class="form-control" placeholder="Ej: 50" required>
            <small id="max_cant" class="text-muted"></small>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label fw-bold">Número de Lote</label>
            <select id="lote" name="lote" class="form-select" required>
              <option value="">Seleccione el lote</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold">Almacén Destino</label>
            <select id="almacen_destino" name="almacen_destino" class="form-select" required>
              <option value="">Seleccione un almacén</option>
              <?php
                $res = $conexion->query("SELECT id, nombre FROM almacenes ORDER BY nombre");
                while($row = $res->fetch_assoc()){
                  echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
                }
              ?>
            </select>
          </div>
        </div>

        <div class="row mb-4">
          <div class="col-md-6">
            <label class="form-label fw-bold">Almacén Origen</label>
            <select id="almacen_origen" name="almacen_origen" class="form-select" required>
              <option value="">Seleccione un almacén</option>
              <?php
                $res = $conexion->query("SELECT id, nombre FROM almacenes ORDER BY nombre");
                while($row = $res->fetch_assoc()){
                  echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
                }
              ?>
            </select>
          </div>
        </div>

        <div class="d-flex justify-content-end">
          <button type="button" class="btn btn-secondary me-2" onclick="window.history.back()">Cancelar</button>
          <button type="submit" class="btn btn-success">Transferir</button>
        </div>
      </form>

      <div id="msg" class="mt-3"></div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  // cargar lotes dinámicos según producto y almacén origen
  $("#id_interno, #almacen_origen").on("change", function() {
    const id_interno = $("#id_interno").val().trim();
    const almacen = $("#almacen_origen").val();
    if (id_interno && almacen) {
      $.post("ajax_lotes2.php", { id_interno, almacen }, function(data) {
        $("#lote").html(data);
      });
    }
  });

  // enviar formulario traslado
  $("#formTraslado").on("submit", function(e) {
    e.preventDefault();
    $.post("guardar_traslado.php", $(this).serialize(), function(resp) {
      $("#msg").html(resp);
      $("#formTraslado")[0].reset();
    });
  });
});
</script>
</body>
</html>
