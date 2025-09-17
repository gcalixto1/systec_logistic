<?php
include('conexionfin.php');

$meta = array();
$id = isset($_GET['idcaja']) ? $_GET['idcaja'] : '';
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
                                       a.estado
                                FROM apertura_caja a
                                INNER JOIN usuario u ON u.usuario = a.usuario
                                WHERE a.idcaja = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($SALDO_INICIALES, $CAJAACTIVA, $NUMEROCAJA, $FECHAAPERTURA, $FECHACIERRE, $OBSERVACIONES, $NOMBREREPONSABLE, $USUARIOREPONSABLE, $ESTADO);
    $stmt->fetch();
    $stmt->close();

    // Calcular los ingresos totales del día
    $sql = "SELECT IFNULL(SUM(ingreso), 0.00) FROM movimientos_de_caja WHERE DATE(fecha) = CURDATE()";
    $result = $conexion->query($sql);
    $INGRESOS_TOTALES2 = $result->fetch_row()[0];

    // Calcular los ingresos totales de las facturas del día
    $sql = "SELECT IFNULL(SUM(totalpagar), 0.00) FROM factura WHERE forma_pago = '01' AND estado <> 'Pendiente' AND DATE(fechafactura) = CURDATE()";
    $result = $conexion->query($sql);
    $INGRESOS_TOTALES = $result->fetch_row()[0];

    // Calcular las salidas del día
    $sql = "SELECT IFNULL(SUM(egreso), 0.00) FROM movimientos_de_caja WHERE DATE(fecha) = CURDATE()";
    $result = $conexion->query($sql);
    $SALIDAS = $result->fetch_row()[0];

    // Calcular el total pagado con tarjeta del día
    $sql = "SELECT IFNULL(SUM(totalpagar), 0.00) FROM factura WHERE forma_pago = '05' AND estado <> 'Pendiente' AND DATE(fechafactura) = CURDATE()";
    $result = $conexion->query($sql);
    $TARJETAS = $result->fetch_row()[0];

    // Calcular los totales
    $TOTAL = number_format($INGRESOS_TOTALES + $INGRESOS_TOTALES2 + $TARJETAS ?? 0, 2);
    $TOTAL_COMPLETO = number_format($TOTAL + $SALDO_INICIALES - $SALIDAS ?? 0, 2);

    // Asignar valores a la variable meta para usar en la interfaz
    $meta = [
        'SALDO_INICIALES' => $SALDO_INICIALES,
        'INGRESOS_TOTALES' => $INGRESOS_TOTALES,
        'INGRESOS_TOTALES2' => $INGRESOS_TOTALES2,
        'SALIDAS' => $SALIDAS,
        'TARJETAS' => $TARJETAS,
        'TOTAL' => $TOTAL,
        'TOTAL_COMPLETO' => $TOTAL_COMPLETO,
        'OBSERVACIONES' => $OBSERVACIONES
    ];
} else {
    echo "ID de caja no válido.";
}
?>

<style>
.encabezado {
    font-size: 24px;
    font-weight: bold;
    color: #048aa1;
}

.encabezadototales {
    font-size: 24px;
    font-weight: bold;
    color: #004799;
}

.campos {
    font-size: 16px;
    font-weight: bold;
}

.text_green {
    color: darkgreen;
    font-weight: bold;
}

.text_red {
    color: darkred;
    font-weight: bold;
}

.text_total {
    font-size: 35px;
    font-weight: bold;
    text-align: center;
}

.text_total2 {
    font-size: 20px;
    font-weight: bold;
    margin-left: 275px;
}
</style>

<div class="container-fluid">
    <div class="card">
        <form class="form form-material" method="post" action="#" name="savecierre" id="savecierre">
            <div id="save">
            </div>
            <div class="form-body">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <input type="hidden" name="id"
                                value="<?php echo isset($_GET['idcaja']) ? htmlspecialchars($_GET['idcaja']) : '' ?>">
                            <label class="encabezadototales">Ventas del Dia:
                                <?php echo '$' . (isset($meta['TOTAL']) ? htmlspecialchars($meta['TOTAL']) : ''); ?></label>
                        </div>
                        <!-- <div class="form-group col-md-6">
                    <label class="encabezadototales">Ganacias:  <?php echo isset($meta['MAX_num_apertura']) ? htmlspecialchars($meta['MAX_num_apertura']) : ''; ?></label>
                    </div> -->
                    </div>
                    <label class="encabezado"><i class='fa fa-calculator'></i> Dinero en Caja </label>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="name" class="campos">Fondo de caja</label>
                            <input type="text" name="saldo_inicial" id="saldo_inicial" class="form-control text_green"
                                value="<?php echo isset($meta['SALDO_INICIALES']) ? htmlspecialchars($meta['SALDO_INICIALES']) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="name" class="campos">Ventas en efectivo</label>
                            <input type="text" name="saldo_ventas_total" id="saldo_ventas_total"
                                class="form-control text_green"
                                value="<?php echo isset($meta['INGRESOS_TOTALES']) ? htmlspecialchars($meta['INGRESOS_TOTALES']) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="name" class="campos">Ventas Con Tranferencia</label>
                            <input type="text" name="saldo_tarjeta" id="saldo_tarjeta" class="form-control text_green"
                                value="<?php echo isset($meta['TARJETAS']) ? htmlspecialchars($meta['TARJETAS']) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group col-md-6">
                            <!-- <label for="name" class="campos">Ventas al Credito</label>
                        <input type="text" name="saldo_inicial" id="saldo_inicial" class="form-control text_green" 
                            value="<?php echo isset($meta['saldo_inicial']) ? htmlspecialchars($meta['saldo_inicial']) : ''; ?>"
                            required> -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="name" class="campos">Entradas</label>
                            <input type="text" name="entradas" id="entradas" class="form-control text_green"
                                value="<?php echo isset($meta['INGRESOS_TOTALES2']) ? htmlspecialchars($meta['INGRESOS_TOTALES2']) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="name" class="campos">Salidas</label>
                            <input type="text" name="gasto" id="gasto" class="form-control text_red"
                                value="<?php echo isset($meta['SALIDAS']) ? htmlspecialchars($meta['SALIDAS']) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="name" class="campos text_total2">Total en Caja</label>
                            <input type="text" name="total_completo" id="total_completo" class="form-control text_total"
                                value="<?php echo isset($meta['TOTAL_COMPLETO']) ? htmlspecialchars($meta['TOTAL_COMPLETO']) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="name" class="campos text_total2">Observaciones/Notas</label>
                            <input type="text" name="notas" id="notas" class="form-control"
                                value="<?php echo isset($meta['OBSERVACIONES']) ? htmlspecialchars($meta['OBSERVACIONES']) : ''; ?>"
                                required>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>

<script>
$('#savecierre').submit(function(e) {
    e.preventDefault();
    start_load();
    $.ajax({
        url: 'ajax.php?action=save_cierre_caja',
        method: 'POST',
        data: $(this).serialize(),
        success: function(resp) {
            if (resp == 1) {
                Swal.fire({
                    title: 'Exito!',
                    text: 'El Cierre se Proceso con Exito',
                    icon: 'success',
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            }
        }
    });
});
</script>