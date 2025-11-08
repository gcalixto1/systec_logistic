<?php
include("conexionfin.php"); // Conexión a la base de datos

// Verificar si se incluye "No conforme"
$incluir_no_conforme = isset($_GET['incluir_no_conforme']) && $_GET['incluir_no_conforme'] == '1';

// Construir condición dinámica
$filtro_almacen = $incluir_no_conforme ? "1=1" : "a.nombre != 'No conforme'";

// Consulta para obtener inventario valorizado agrupado por producto
$sql = "
SELECT 
    p.id_producto,
    p.str_id,
    p.cod_producto,
    p.descripcion,
    p.familia,
    p.calibre,
    p.und_embalaje_minima,
    COALESCE(i.stock_unidades_kg, 0) AS stock_unidades_kg,
    COALESCE(i.stock_caja_paca_bobinas, 0) AS stock_caja_paca_bobinas,
    COALESCE(i.costo_unitario, 0) AS costo_unitario,
    (COALESCE(i.stock_unidades_kg, 0) * COALESCE(i.costo_unitario, 0)) AS costo_total,
    p.precio_lista_5,
    (COALESCE(i.stock_unidades_kg, 0) * COALESCE(p.precio_lista_5, 0)) AS precio_total,
    i.lote,
    i.almacen_id,
    a.nombre AS nombre_almacen
FROM inventario i
INNER JOIN producto p ON p.id_producto = i.producto_id
LEFT JOIN almacenes a ON a.id = i.almacen_id
WHERE $filtro_almacen
ORDER BY p.descripcion
";

$result = $conexion->query($sql);
?>

<style>
  h2 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
  }

  .filtro {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    margin-bottom: 15px;
    font-weight: bold;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  th,
  td {
    padding: 8px;
    text-align: center;
  }

  th {
    background-color: #333;
    color: #fff;
  }

  .totales {
    font-weight: bold;
    font-size: 1.1em;
    background-color: #e9ecef;
  }

  .text-end {
    text-align: right;
  }
</style>

<div class="container-fluid">
  <h2>📦 Inventario Valorizado por Producto</h2>

  <div class="filtro">
    <label for="checkNoConforme">
      <input type="checkbox" id="checkNoConforme" <?php echo $incluir_no_conforme ? 'checked' : ''; ?>>
      Incluir Almacén “No conforme”
    </label>
  </div>

  <div id="tabla-container">
    <table id="tablaInventario" class="display">
      <thead>
        <tr>
          <th>STR ID</th>
          <th>Código</th>
          <th>Descripción</th>
          <th>Unidad Embalaje</th>
          <th>Stock Und/Kg</th>
          <th>Stock Caja/Paca/Bob</th>
          <th>Costo Unitario</th>
          <th>Valor Inventario</th>
          <th>Precio Lista</th>
          <th>Valor Lista</th>
          <th>Almacén</th>
          <th>Lote</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $totalCosto = 0;
        $totalLista = 0;

        if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            $valorInventario = $row['stock_unidades_kg'] * $row['costo_unitario'];
            $valorLista = $row['stock_unidades_kg'] * $row['precio_lista_5'];
            $totalCosto += $valorInventario;
            $totalLista += $valorLista;

            echo "<tr>
                            <td>{$row['str_id']}</td>
                            <td>{$row['cod_producto']}</td>
                            <td>{$row['descripcion']}</td>
                            <td>{$row['und_embalaje_minima']}</td>
                            <td class='text-end'>" . number_format($row['stock_unidades_kg'], 2) . "</td>
                            <td class='text-end'>" . number_format($row['stock_caja_paca_bobinas'], 2) . "</td>
                            <td class='text-end'>" . number_format($row['costo_unitario'], 2) . "</td>
                            <td class='text-end'>" . number_format($valorInventario, 2) . "</td>
                            <td class='text-end'>" . number_format($row['precio_lista_5'], 2) . "</td>
                            <td class='text-end'>" . number_format($valorLista, 2) . "</td>
                            <td>{$row['nombre_almacen']}</td>
                            <td>{$row['lote']}</td>
                        </tr>";
          }
        } else {
          echo "<tr><td colspan='11'>No hay registros de inventario</td></tr>";
        }
        ?>
      </tbody>
      <tfoot>
        <tr class="totales">
          <td colspan="6" class="text-end">Totales:</td>
          <td class="text-end"><?php echo number_format($totalCosto, 2); ?></td>
          <td></td>
          <td class="text-end"><?php echo number_format($totalLista, 2); ?></td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

<script>
  let tabla = $('#tablaInventario').DataTable({
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
    },
    pageLength: 25,
    order: [
      [1, 'asc']
    ],
    responsive: true
  });

  // Cuando se cambia el checkbox, recargar el contenido vía AJAX
  $('#checkNoConforme').on('change', function() {
    const incluir = $(this).is(':checked') ? 1 : 0;

    $.ajax({
      url: "index.php?page=inventario_valorizado",
      type: "GET",
      data: {
        incluir_no_conforme: incluir
      },
      success: function(data) {
        // Extraer solo el contenido de #tabla-container del HTML recibido
        const nuevaTabla = $(data).find('#tabla-container').html();
        $('#tabla-container').html(nuevaTabla);

        // Reaplicar DataTables
        $('#tablaInventario').DataTable({
          language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
          },
          pageLength: 25,
          order: [
            [1, 'asc']
          ],
          responsive: true
        });
      },
      error: function() {
        alert('Error al actualizar el inventario.');
      }
    });
  });
</script>