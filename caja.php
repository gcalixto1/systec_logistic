<?php
include('conexionfin.php');

// Obtener la caja actualmente abierta
$query = $conexion->query("SELECT * FROM apertura_caja WHERE estado = 'A' LIMIT 1");
$meta = $query && $query->num_rows > 0 ? $query->fetch_assoc() : null;
$hayCajaAbierta = !is_null($meta);
?>
<div class="container-fluid">
    <div class="card-header">
        <h4 class="card-title text-black"><i class="fa fa-money-bill-wave"></i> Gestión de Caja</h4>
    </div>
    <br>
    <div class="col-lg-12">
        <?php if (isset($_SESSION['login_rol']) && $_SESSION['login_rol'] == 1): ?>
        <div class="text-right">
            <?php if ($hayCajaAbierta): ?>
            <button class="btn btn-dark btn-lg" disabled type="button" id="apertura">
                <i class="fa fa-cash-register"></i> Apertura de Caja
            </button>
            <?php else: ?>
            <button class="btn btn-success btn-lg" type="button" id="apertura">
                <i class="fa fa-cash-register"></i> Apertura de Caja
            </button>
            <?php endif; ?>
        </div>
        <br />
        <?php endif; ?>

        <input hidden name="idcajaA" id="idcajaA" class="form-control"
            value="<?php echo isset($meta['idcaja']) ? htmlspecialchars($meta['idcaja']) : ''; ?>">

        <table class="table table-responsive" id="borrower-list">
            <colgroup>
                <col width="5%">
                <col width="10%">
                <col width="15%">
                <col width="10%">
                <col width="10%">
                <col width="10%">
                <col width="15%">
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Número de Apertura</th>
                    <th class="text-center">Fecha de Apertura</th>
                    <th class="text-center">Saldo Inicial</th>
                    <th class="text-center">Ingresos/Ventas y otros</th>
                    <th class="text-center">Egresos/Gasto</th>
                    <th class="text-center">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                $qry = $conexion->query("SELECT * FROM apertura_caja ORDER BY idcaja DESC");
                while ($row = $qry->fetch_assoc()):
                    $estadoCaja = $row['estado'];
                    $idcaja = $row['idcaja'];
                    $num_apertura = $row['num_apertura'];
                    ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($num_apertura); ?></td>
                    <td><?php echo htmlspecialchars($row['fch_hora_apertura']); ?></td>
                    <td><?php echo htmlspecialchars($row['saldo_inicial']); ?></td>
                    <td><?php echo htmlspecialchars($row['saldo_venta_total']); ?></td>
                    <td><?php echo htmlspecialchars($row['gasto']); ?></td>
                    <td style="white-space: nowrap;">
                        <center>
                            <?php
                                // Mostrar botones según si es la caja abierta o está cerrada
                                if ($estadoCaja === 'A' && isset($meta['idcaja']) && $meta['idcaja'] == $idcaja) {
                                    echo '
                            <button class="btn btn-primary btn-ms edit_borrower" type="button"
                                data-id="' . $idcaja . '" title="Ver Detalle">
                                <i class="fa fa-eye"></i>
                            </button>
                            <button class="btn btn-danger btn-ms cierre" type="button"
                                data-id="' . $idcaja . '" title="Cerrar Caja">
                                <i class="fa fa-lock"></i>
                            </button>';
                                } else {
                                    echo '
                            <button class="btn btn-success btn-ms edit_reportez" type="button"
                                data-id="' . $num_apertura . '" title="Imprimir Reporte">
                                <i class="fa fa-print"></i>
                            </button>';
                                }
                                ?>
                        </center>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
var id_caja = document.getElementById("idcajaA").value;
$('#borrower-list').dataTable();

$('#apertura').click(function() {
    uni_modal("Apertura de Caja", "apertura_caja.php");
});

$('#borrower-list').on('click', '.cierre', function() {
    uni_modal("Cierre de Caja", "cierre_caja.php?idcaja=" + $(this).attr('data-id'));
});

$('#borrower-list').on('click', '.edit_borrower', function() {
    uni_modal_documentos("Detalle de caja", "detalle_caja.php?idcaja=" + $(this).attr('data-id'));
});

$('#borrower-list').on('click', '.edit_reportez', function() {
    uni_modal_documentos("Reporte", "ver_reporte.php?num_apertura=" + $(this).attr('data-id'));
});
</script>