<?php
include 'conexionfin.php'; // conexión a la base de datos
$facturas = [];
$totalVentas = 0;

// Si se presionó el botón filtrar
if (isset($_POST['filtrar'])) {
    $fecha = $_POST['fecha'];
    $query = "SELECT nombre, totalpagar, medio_pago, fechafactura 
              FROM factura
              INNER JOIN medio_pago ON medio_pago.codigo = factura.forma_pago
              INNER JOIN cliente ON cliente.idcliente = factura.idcliente
              WHERE DATE(fechafactura) = '$fecha'";
    $resultado = $conexion->query($query);
    if ($resultado) {
        $facturas = $resultado->fetch_all(MYSQLI_ASSOC);
        foreach ($facturas as $f) {
            $totalVentas += $f['totalpagar'];
        }
    }
}
?>
<div class="container-fluid mt-4">
    <div class="card shadow">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Consultar Facturas por Fecha</h5>
        </div>
        <div class="card-body">
            <form method="POST" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="fecha" class="form-label">Seleccionar Fecha</label>
                    <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>"
                        required>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="filtrar" class="btn btn-success w-100">Filtrar</button>
                </div>

                <!-- 🔹 Total de Ventas al lado derecho -->
                <?php if (isset($_POST['filtrar'])): ?>
                    <div class="col-md-4 offset-md-2 text-end">
                        <div class="p-3 bg-light border rounded shadow-sm">
                            <h4 class="mb-0 text-success">Total Ventas de la fecha selcciona</h4>
                            <h2 class="fw-bold text-primary">$<?= number_format($totalVentas, 2) ?></h2>
                        </div>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <?php if (!empty($facturas)): ?>
        <div class="card mt-4 shadow">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0">Resultados</h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID Factura</th>
                            <th>Vendedor</th>
                            <th>Total</th>
                            <th>Forma Pago</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        foreach ($facturas as $factura): ?>
                            <tr>
                                <td><?php echo $i++ ?></td>
                                <td><?= $factura['nombre'] ?></td>
                                <td>$<?= number_format($factura['totalpagar'], 2) ?></td>
                                <td><?= $factura['medio_pago'] ?></td>
                                <td><?= $factura['fechafactura'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php elseif (isset($_POST['filtrar'])): ?>
        <div class="alert alert-warning mt-4">No se encontraron facturas para esa fecha.</div>
    <?php endif; ?>
</div>