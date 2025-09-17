<?php
include('conexionfin.php'); // Incluir el archivo de conexión a la base de datos
?>
<div class="container-fluid">
    <div class="card-header">
        <h4 class="card-title text-black"><i class="fa fa-box"></i> Movimientos de Productos</h4>
    </div>
    <br>
    <div class="col-lg-12">

        <label for="codproducto">Codigo de Producto</label>
        <div class="input-group col-md-6">
            <span class="input-group-text" id="basic-addon1"><i class="fa fa-barcode fa-lg"></i></span>
            <input type="text" id="codproducto" placeholder="Escribe el código" class="form-control">
            <button type="button" id="buscar_producto" class="btn btn-dark"><i class="fa fa-search"></i> Mostrar
                Producto</button> <!-- Botón de búsqueda -->
        </div>
        <br />

        <div id="resultado_busqueda">
            <!-- Aquí se mostrarán los resultados de la búsqueda -->
        </div>
    </div>
</div>

<!-- JavaScript con jQuery para la búsqueda AJAX -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        // Función para manejar la búsqueda de producto
        $('#buscar_producto').click(function() {
            var codigo_producto = $('#codproducto').val();
            $.ajax({
                type: 'GET',
                url: 'buscar_producto.php', // Archivo PHP para manejar la búsqueda del producto
                data: {
                    codproducto: codigo_producto
                },
                success: function(data) {
                    $('#resultado_busqueda').html(
                        data); // Actualizar la tabla de resultados con los datos recibidos
                }
            });
        });

        // Manejar la búsqueda cuando se presione "Enter" en el campo de entrada
        $('#codproducto').keypress(function(event) {
            if (event.which === 13) { // 13 es el código de tecla para "Enter"
                $('#buscar_producto').click(); // Simular clic en el botón de búsqueda
            }
        });
    });
</script>