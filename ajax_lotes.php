<?php
include("conexionfin.php");

$id_producto = intval($_GET['id_producto']);
$almacen_id  = intval($_GET['almacen_id']);

// Obtener factor de conversión (cuántas unidades tiene una caja)
$consultaFactor = $conexion->query("SELECT und_embalaje_minima FROM producto WHERE id_producto = $id_producto");
$factor = 1;
if($consultaFactor->num_rows > 0){
    $factor = floatval($consultaFactor->fetch_assoc()['und_embalaje_minima']);
}

$sql = "
    SELECT 
        lote, 
        SUM(CASE WHEN tipo_movimiento LIKE '%ENTRADA%' THEN cantidad ELSE -cantidad END) AS cantidad
    FROM movimientos_inventario
    WHERE id_producto = $id_producto
      AND almacen_id = $almacen_id
    GROUP BY lote
    HAVING SUM(CASE WHEN tipo_movimiento LIKE '%ENTRADA%' THEN cantidad ELSE -cantidad END) > 0
    ORDER BY lote ASC
";

$result = $conexion->query($sql);

if ($result->num_rows > 0) {
    echo '<table class="table table-bordered table-striped align-middle mt-3">';
    echo '<thead class="table-primary">
            <tr>
              <th>Lote</th>
              <th>Cant. (Unidades)</th>
              <th>Cant. (Cajas)</th>
              <th>Salida (Unidades)</th>
              <th>Salida (Cajas)</th>
            </tr>
          </thead><tbody>';

    while ($row = $result->fetch_assoc()) {
        $unidades = floatval($row['cantidad']);
        $cajas = $unidades / $factor;

        echo '<tr>
                <td>'.htmlspecialchars($row['lote']).'</td>
                <td class="text-end">'.number_format($unidades, 2).'</td>
                <td class="text-end">'.number_format($cajas, 2).'</td>
                <td><input type="number" step="0.01" class="form-control form-control-sm salida_unidades" data-factor="'.$factor.'"></td>
                <td><input type="number" step="0.01" class="form-control form-control-sm salida_cajas" data-factor="'.$factor.'"></td>
              </tr>';
    }

    echo '</tbody></table>';
} else {
    echo '<div class="alert alert-warning mt-3">No hay lotes disponibles para este producto en el almacén seleccionado.</div>';
}
?>

<script>
// Sincronizar cálculos entre cajas y unidades
$('.salida_unidades').on('input', function(){
    let factor = parseFloat($(this).data('factor'));
    let unidades = parseFloat($(this).val()) || 0;
    let cajas = unidades / factor;
    $(this).closest('tr').find('.salida_cajas').val(cajas.toFixed(2));
});

$('.salida_cajas').on('input', function(){
    let factor = parseFloat($(this).data('factor'));
    let cajas = parseFloat($(this).val()) || 0;
    let unidades = cajas * factor;
    $(this).closest('tr').find('.salida_unidades').val(unidades.toFixed(2));
});
</script>
