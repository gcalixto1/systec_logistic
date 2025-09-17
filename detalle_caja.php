<?php
include('conexionfin.php');

$id = isset($_GET['idcaja']) ? intval($_GET['idcaja']) : 0;
if ($id > 0) {
    // Variables para almacenar los resultados
    $SALDO_INICIALES = 0.00;
    $INGRESOS_TOTALES = 0.00;
    $INGRESOS_TOTALES2 = 0.00;
    $SALIDAS = 0.00;
    $TARJETAS = 0.00;
    $NOMBREREPONSABLE = '';
    $USUARIOREPONSABLE = '';
    $CAJAACTIVA = '';
    $NUMEROCAJA = '';
    $FECHAAPERTURA = '';
    $FECHACIERRE = '';
    $OBSERVACIONES = '';
    $ESTADO = '';

    // Obtener los datos de la apertura de caja y el responsable
    $stmt = $conexion->prepare("SELECT a.saldo_inicial,
                                       a.caja,
                                       a.num_apertura,
                                       a.fch_hora_apertura,
                                       a.fch_hora_cierre,
                                       a.notas,
                                       u.nombre,
                                       u.usuario,
                                       a.estado,
                                       IFNULL(a.saldo_venta_total,0.00),
                                       IFNULL(a.gasto,0.00),
                                       IFNULL(a.saldo_tarjeta,0.00)
                                FROM apertura_caja a
                                INNER JOIN usuario u ON u.usuario = a.usuario
                                WHERE a.idcaja = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($SALDO_INICIALES, $CAJAACTIVA, $NUMEROCAJA, $FECHAAPERTURA, $FECHACIERRE, $OBSERVACIONES, $NOMBREREPONSABLE, $USUARIOREPONSABLE, $ESTADO, $INGRESOS_TOTALES, $SALIDAS, $TARJETAS);
    $stmt->fetch();
    $stmt->close();

    // Verifica si hay resultados y muestra la tabla
?>

<div class="container-fluid">
    <div class="card">
        <form class="form form-material" method="post" action="#" name="savecliente" id="savecliente">
            <div id="save"></div>
            <div class="form-body">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group has-feedback">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                        </div>
                        <h3><b> Estado de Caja : <?php
                                                        if ($ESTADO == "A") {
                                                            echo '<span class="badge" style ="color:white; background:#2F7C03;"><i class="fas fa-check" style ="color:white; background:#2F7C03;"></i> CAJA ABIERTA</span>'; // Check verde
                                                        } else {
                                                            echo '<span class="badge" style ="color:white; background:#900C3F;"><i class="fas fa-times" style ="color:white; background:#900C3F;"></i> CAJA CERRADA</span>'; // X roja
                                                        }
                                                        ?></b></h3>
                        <table class="table success">
                            <colgroup>
                                <col width="20%">
                                <col width="80%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th class="info">Responsable</th>
                                    <td><b><?php echo htmlspecialchars($NOMBREREPONSABLE); ?></b>
                                        (<?php echo htmlspecialchars($USUARIOREPONSABLE); ?>)</td>
                                </tr>
                                <tr>
                                    <th class="info">Nombre Caja</th>
                                    <td><?php echo htmlspecialchars($CAJAACTIVA); ?></td>
                                </tr>
                                <tr>
                                    <th class="info">Numero apertura</th>
                                    <td><?php echo htmlspecialchars($NUMEROCAJA); ?></td>
                                </tr>
                                <tr>
                                    <th class="info">Fecha de Apertura</th>
                                    <td><?php echo date('d/m/Y H:i:s', strtotime($FECHAAPERTURA)); ?></td>
                                </tr>
                                <tr>
                                    <th class="info">Fecha de Cierre</th>
                                    <td><?php echo date('d/m/Y H:i:s', strtotime($FECHACIERRE)); ?></td>
                                </tr>
                                <tr>
                                    <th class="info">Saldo Inicial</th>
                                    <td><?php echo '$' . htmlspecialchars($SALDO_INICIALES); ?></td>
                                </tr>
                                <tr>
                                    <th class="info">Ingresos/Ventas y otros</th>
                                    <td><?php echo '$' . htmlspecialchars($INGRESOS_TOTALES); ?></td>
                                </tr>
                                <tr>
                                    <th class="info">Egresos/Gasto</th>
                                    <td><?php echo '$' . htmlspecialchars($SALIDAS); ?></td>
                                </tr>
                                <tr>
                                    <th class="info">Ventas con Tarjeta</th>
                                    <td><?php echo '$' . htmlspecialchars($TARJETAS); ?></td>
                                </tr>
                                <tr>
                                    <th class="info">Observacion</th>
                                    <td><?php echo htmlspecialchars($OBSERVACIONES); ?></td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php
} else {
    echo "ID de caja no válido.";
}
?>