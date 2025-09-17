<?php
include('conexionfin.php');
$query = $conexion->query("SELECT * FROM movimientos_de_caja");
if ($query) {
    $movimiento = $query->fetch_assoc();
    if ($movimiento) {
        $meta = $movimiento;
    }
}
?>
<style>
    .boton_add {
        margin-top: -4%;
        margin-left: 75%;
        width: 25%;
    }

    .boton_add2 {
        margin-top: -4%;
        margin-left: 75%;
        width: 25%;
    }
</style>

<div class="container-fluid">
    <div class="card-header">
        <h4 class="card-title text-black"><i class="fa fa-exchange-alt"></i> Gestion de Movimientos</h4>
    </div>
    <br>
    <div class="col-lg-12">
        <div class="text-right">
            <button class="btn btn-success btn-lg" type="button" id="cierre"><i class="fa fa-plus"></i> Nuevo
                Movimiento</button>
        </div>
        <br />
        <input hidden name="idcajaA" id="idcajaA" class="form-control"
            value="<?php echo isset($meta['idcaja']) ? htmlspecialchars($meta['idcaja']) : ''; ?>">
        <table class="table table-responsive" id="borrower-list">
            <colgroup>
                <col width="3%">
                <col width="10%">
                <col width="10%">
                <col width="10%">
                <col width="35%">
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Movimiento</th>
                    <th class="text-center">Monto</th>
                    <th class="text-center">Fecha</th>
                    <th class="text-center">Comentario</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                $qry = $conexion->query("SELECT * FROM movimientos_de_caja");
                while ($row = $qry->fetch_assoc()) :
                    // Determina el tipo de movimiento y el color de la columna
                    $tipoMovimiento = '';
                    $color = '';
                    $monto = 0.00;
                    if ($row['egreso'] != 0.00 && $row['egreso'] != null) {
                        $tipoMovimiento = "Salida de Efectivo";
                        $color = 'background-color: red; color: white;';
                        $monto = $row['egreso']; // Monto de egreso
                    } elseif ($row['ingreso'] != 0.00 && $row['ingreso'] != null) {
                        $tipoMovimiento = "Entrada de Efectivo";
                        $color = 'background-color: green; color: white;';
                        $monto = $row['ingreso']; // Monto de ingreso
                    }
                ?>
                    <tr>
                        <td style="font-size: 12px; <?php echo $color; ?>">
                            <?php echo $i++ ?>
                        </td>
                        <td style="font-size: 12px;">
                            <?php echo $tipoMovimiento; ?>
                        </td>
                        <td style="font-size: 12px;">
                            <?php echo number_format($monto, 2); ?>
                            <!-- Monto mostrado -->
                        </td>
                        <td style="font-size: 12px;">
                            <?php echo $row['fecha'] ?>
                        </td>
                        <td style="font-size: 12px;">
                            <?php echo $row['comentario'] ?>
                        </td>
                    </tr>
                <?php endwhile; ?>


        </table>

    </div>
</div>

<script>
    $('#borrower-list').dataTable()
    $('#cierre').click(function() {
        uni_modal("Gestion de Movimientos", "manage_movimientos.php")
    })
    $('#borrower-list').on('click', '.edit_borrower', function() {
        uni_modal_documentos("Detalle de caja", "detalle_caja.php?idcaja=" + $(this).attr('data-id'))
    })
</script>