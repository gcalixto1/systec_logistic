<?php include('conexionfin.php'); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<style>
.op-item {
    background: #ffffff;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    cursor: grab;
    transition: background 0.2s ease;
}
.op-item:hover {
    background: #f1f1f1;
}
.drag-handle {
    cursor: grab;
    color: #6c757d;
    margin-right: 8px;
    font-size: 25px;    
}
.sortable-placeholder {
    background-color: #e3f2fd;
    border: 2px dashed #0d6efd;
    height: 60px;
    border-radius: 10px;
    margin-bottom: 10px;
}
</style>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>📋 Órdenes de Producción</h4>
        <select id="maquina" class="form-control w-auto">
            <option value="">Todas las máquinas</option>
            <?php
            $result = $conexion->query("SELECT id, maquina FROM lista_maquina ORDER BY maquina ASC");
            while($row = $result->fetch_assoc()){
                echo "<option value='{$row['id']}'>{$row['maquina']}</option>";
            }
            ?>
        </select>
    </div>

    <div id="spinner" class="text-center mt-4" style="display:none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden"></span>
        </div>
        <p class="mt-2">Cargando órdenes...</p>
    </div>

    <div id="contenedor-op"></div>
</div>

<script>
$(document).ready(function(){

    function activarDragDrop() {
        $("#contenedor-op").sortable({
            items: "> .op-item",
            placeholder: "sortable-placeholder",
            handle: ".drag-handle",
            axis: "y",
            tolerance: "pointer",
            helper: "clone",
            cursor: "move",
            start: function(e, ui) {
                ui.placeholder.height(ui.item.outerHeight());
            },
            update: function(e, ui) {
                let order = $("#contenedor-op").sortable('toArray', { attribute: 'data-op-id' });

                $.ajax({
                    url: 'actualizar_prioridad.php',
                    method: 'POST',
                    data: { orden: order },
                    success: function(resp) {
                        console.log("Orden actualizado correctamente:", resp);
                    },
                    error: function(err) {
                        console.error("Error al actualizar el orden:", err);
                    }
                });
            }
        }).disableSelection();
    }

    function cargarOrdenes(maquina = '') {
        $('#spinner').show();
        $('#contenedor-op').hide();

        $.ajax({
            url: 'listar_op_data.php',
            method: 'GET',
            data: { maquina: maquina },
            success: function(data) {
                $('#spinner').hide();
                $('#contenedor-op').html(data).fadeIn();
                activarDragDrop();
            },
            error: function() {
                $('#spinner').hide();
                $('#contenedor-op').html('<div class="alert alert-danger">Error al cargar las órdenes.</div>').fadeIn();
            }
        });
    }

    cargarOrdenes();

    $('#maquina').on('change', function(){
        cargarOrdenes($(this).val());
    });

});
</script>
