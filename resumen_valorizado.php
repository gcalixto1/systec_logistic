<?php include("conexionfin.php"); 
// --- Consultar la vista ---
$sql = "SELECT * FROM vista_inventario_valorizado";
$result = $conexion->query($sql);
?>
<div class="container-fluid">
    <h2 class="text-center mb-4">📦 MRP - Plan de Requerimiento de materiales</h2>
<div class="col-md-10">
    <table id="tablaInventario" class="table table-responsive" style="width:100%">
         <thead class="table-dark">
        <tr>
            <th>Str_id</th>
            <th>Código Int</th>
            <th>Descripción</th>
            <th>Familia</th>
            <th>Calibre</th>
            <th>Und Embalaje</th>
            <th>Stock Total (Unds/Kg)</th>
            <th>Stock Total (Cajas/Pacas/Bob)</th>
            <th>Inv. Tránsito (Unds/kg)</th>
            <th>Inv. Tránsito (Cajas)</th>
            <th>Total Inv. Unds/Kg</th>
            <th>Total Inv. Cajas/Paca/Bob</th>
            <th>Promedio Mensual</th>
            <th>Promedio Mensual (Cajas/Paca/Bob)</th>
            <th>Meses Cobertura</th>
            <th>Punto Reorden</th>
            <th>Lead Time</th>
            <th>Stock Mínimo</th>
            <th>Alerta</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['str_id']}</td>";
                echo "<td>{$row['cod_producto']}</td>";
                echo "<td>{$row['descripcion']}</td>";
                echo "<td>{$row['familia']}</td>";
                echo "<td>{$row['calibre']}</td>";
                echo "<td>{$row['factor_conversion']}</td>";
                echo "<td>{$row['stock_total_unidades']}</td>";
                echo "<td>{$row['stock_total_cajas']}</td>";
                echo "<td>{$row['Total_Transito_Unidades']}</td>";
                echo "<td>{$row['Inv_en_Transito_oc_caja_paca_bob']}</td>";
                echo "<td>{$row['Total_inven_stock_unidades']}</td>";
                echo "<td>{$row['Total_inven_stock_caja_paca']}</td>";
                echo "<td>{$row['Promedio_Consumo_Mensual']}</td>";
                echo "<td>{$row['Promedio_Consumo_Mensual_Cajas']}</td>";
                echo "<td>{$row['Meses_Cobertura_Transito']}</td>";
                echo "<td>{$row['Punto_reorden']}</td>";
                echo "<td>{$row['lead_time']}</td>";
                echo "<td>{$row['stock_minimo']}</td>";

                // Mostrar alerta visual
                $alerta = trim($row['Alerta_Compra']);
                if ($alerta === 'COMPRAR') {
                    echo "<td><span class='comprar'>COMPRAR</span></td>";
                } else {
                    echo "<td></td>";
                }

                echo "</tr>";
            }
        }
        ?>
        </tbody>
    </table>
</div>
</div>

<script>
    $('#tablaInventario').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });
</script>