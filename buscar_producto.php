<?php
include('conexionfin.php'); // Incluir el archivo de conexión a la base de datos
echo "<script src='assets/DataTables/datatables.min.js'></script>";
// Manejo de la búsqueda del producto
if (isset($_GET['codproducto'])) {
    $codigo_producto = $_GET['codproducto'];

    if ($codigo_producto != "") {
        // Realizar la búsqueda del producto en la base de datos
        $qry = $conexion->query("SELECT * FROM producto INNER JOIN categoria ON categoria.categoria_id = producto.categoria WHERE codBarra = '$codigo_producto'");

        if ($qry->num_rows > 0) {
            // Mostrar el resultado de la búsqueda
            echo "<h2>Resultados de la búsqueda para el Producto con Código: $codigo_producto</h2>";
            echo "<table class='table table-responsive' id='borrower-list'>";
            echo "<colgroup><col width='5%'><col width='30%'><col width='30%'><col width='15%'><col width='35%'><col width='30%'></colgroup>";
            echo "<thead>
                <tr>
                  <th class='text-center'>#</th>
                  <th class='text-center'>Codigo de Barra</th>
                  <th class='text-center'>Nombre Productos/Servicio</th>
                  <th class='text-center'>Categoria</th>
                  <th class='text-center'>Stock</th>
                  <th class='text-center'>Accion</th>
                </tr>
            </thead>";
            echo "<tbody>";

            $i = 1;
            while ($row = $qry->fetch_assoc()) {
                echo "<tr>";
                echo "<td style='font-size: 15px;' class=''>$i</td>";
                echo "<td style='font-size: 15px;' class=''>" . $row['codBarra'] . "</td>";
                echo "<td style='font-size: 15px;' class=''>" . $row['descripcion'] . "</td>";
                echo "<td style='font-size: 15px;' class=''>" . $row['categoria_des'] . "</td>";
                echo "<td style='font-size: 15px;' class=''>" . $row['existencia'] . "</td>";
                echo "<td style='white-space: nowrap;'>";
                echo "<button class='btn btn-info edit_borrower' type='button' data-id='" . $row['codBarra'] . "'><i class='fas fa-eye'></i> Detalles</button>";
                echo "</td>";
                echo "</tr>";
                $i++;
            }

            echo "</tbody></table>";
        } else {
            // Mostrar mensaje si no se encuentra el producto
            echo "<p>No se encontraron resultados para el producto con código: $codigo_producto</p>";
        }
        echo "<script>
    $(document).ready(function(){
        // Agregar Stock al Producto
        $('#borrower-list').on('click', '.edit_borrower', function () {
            uni_modal_documentos('Movimientos del Producto', 'detalle_kardex.php?codBarra=' + $(this).attr('data-id'));
        });
    });
    </script>";
    } else {
        // Realizar la búsqueda del producto en la base de datos
        $qry = $conexion->query("SELECT * FROM producto INNER JOIN categoria ON categoria.categoria_id = producto.categoria");

        if ($qry->num_rows > 0) {
            // Mostrar el resultado de la búsqueda
            echo "<h2>Resultados de la búsqueda de todos los Productos</h2>";
            echo "<table class='table table-responsive' id='borrower-list'>";
            echo "<colgroup><col width='5%'><col width='30%'><col width='30%'><col width='15%'><col width='35%'><col width='30%'></colgroup>";
            echo "<thead>
                    <tr>
                      <th class='text-center'>#</th>
                      <th class='text-center'>Codigo de Barra</th>
                      <th class='text-center'>Nombre Productos/Servicio</th>
                      <th class='text-center'>Categoria</th>
                      <th class='text-center'>Stock</th>
                      <th class='text-center'>Accion</th>
                    </tr>
                </thead>";
            echo "<tbody>";

            $i = 1;
            while ($row = $qry->fetch_assoc()) {
                echo "<tr>";
                echo "<td style='font-size: 15px;' class=''>$i</td>";
                echo "<td style='font-size: 15px;' class=''>" . $row['codBarra'] . "</td>";
                echo "<td style='font-size: 15px;' class=''>" . $row['descripcion'] . "</td>";
                echo "<td style='font-size: 15px;' class=''>" . $row['categoria_des'] . "</td>";
                echo "<td style='font-size: 15px;' class=''>" . $row['existencia'] . "</td>";
                echo "<td style='white-space: nowrap;'>";
                echo "<button class='btn btn-info edit_borrower' type='button' data-id='" . $row['codBarra'] . "'><i class='fas fa-eye'></i> Detalles</button>";
                echo "</td>";
                echo "</tr>";
                $i++;
            }

            echo "</tbody></table>";
        } else {
        }
        echo "<script>
        $(document).ready(function(){
            // Agregar Stock al Producto
            $('#borrower-list').on('click', '.edit_borrower', function () {
                uni_modal_documentos('Movimientos del Producto', 'detalle_kardex.php?codBarra=' + $(this).attr('data-id'));
            });
        });
        </script>";
    }
}
