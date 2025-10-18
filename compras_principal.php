<?php include("conexionfin.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Módulo de Compras</title>

    <style>
        body { background: #f9fafb; }
        .tab-content {
            background: #fff;
            border: 1px solid #dee2e6;
            border-top: none;
            padding: 20px;
            border-radius: 0 0 .375rem .375rem;
        }
    </style>
</head>
<body>
<div class="container-fluid mt-4">

    <h4 class="mb-4">📦 Gestión de Compras</h4>

    <!-- 🔹 NAV TABS -->
    <ul class="nav nav-tabs" id="comprasTabs">
        <li class="nav-item">
            <button class="nav-link active" data-target="orden_compra.php">Creación de Órdenes</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-target="compras.php">Ingreso de Compras por Orden de Compra</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-target="ingreso_manual.php">Ingreso de Manual de Compras</button>
        </li>
    </ul>

    <!-- 🔹 CONTENEDOR DE CONTENIDO -->
    <div id="tabContent" class="tab-content">
        <div class="text-center py-4 text-muted">Seleccione una pestaña para cargar el contenido...</div>
    </div>

</div>

<script>
$(document).ready(function(){

    // Cargar contenido inicial
    loadTabContent("orden_compra.php");

    // Al hacer clic en una pestaña
    $('#comprasTabs button').on('click', function(){
        $('#comprasTabs .nav-link').removeClass('active');
        $(this).addClass('active');
        let target = $(this).data('target');
        loadTabContent(target);
    });

    // Función para cargar contenido por AJAX
    function loadTabContent(page){
        $('#tabContent').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Cargando...</p></div>');
        $.ajax({
            url: page,
            type: 'GET',
            success: function(res){
                $('#tabContent').html(res);
            },
            error: function(){
                $('#tabContent').html('<div class="alert alert-danger">Error al cargar '+page+'</div>');
            }
        });
    }

});
</script>
</body>
</html>
