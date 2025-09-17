<?php
include 'conexionfin.php';

$query = "SELECT 
            factura.id, 
            factura.fechafactura, 
            factura.numerofactura, 
            consecutivos.descripcionconse, 
            respuestadte.codigoGeneracion, 
            cliente.nombre, 
            cliente.dni, 
            factura.totalpagar, 
            medio_pago.medio_pago,
            CASE 
                WHEN EXISTS (
                    SELECT 1 FROM invalidaciones 
                    WHERE invalidaciones.codigoGeneracion = respuestadte.codigoGeneracion
                ) THEN 'Invalidación realizada'
                ELSE 'Procesada en MH'
            END AS estado
        FROM factura
        INNER JOIN cliente ON cliente.idcliente = factura.idcliente
        INNER JOIN consecutivos ON consecutivos.codigo_consecutivo = factura.tipofactura
        INNER JOIN respuestadte ON respuestadte.id_factura = factura.id
        INNER JOIN medio_pago ON medio_pago.codigo = factura.forma_pago
        WHERE respuestadte.estado = 'PROCESADO'
        ORDER BY factura.fechafactura DESC";

$resultado = $conexion->query($query);
if (!$resultado) {
    die("Error en la consulta: " . $conexion->error);
}
?>
<style>
    .fila-invalidada {
        background-color: #f8d7da !important;
        /* rojo suave */
    }
</style>
<div class="container-fluid">
    <div class="card-header">
        <h4 class="card-title text-black"><i class="fa fa-clock"></i> Historial de ventas</h4>
    </div>
    <br>
    <!-- Buscador -->
    <h6><b>Consultar Venta</b></h6>
    <input type="text" id="buscarFactura" class="form-control mb-2" placeholder="Buscar por nombre, código o total">
    <br>
    <!-- Tabla -->
    <div class="table-responsive">
        <table id="tablaVentas" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>FECHA FACTURA</th>
                    <th>TIPO COMPROBANTE</th>
                    <th>CODIGO COMPROBANTE</th>
                    <th>CLIENTE</th>
                    <th>TOTAL FACTURA</th>
                    <th>ESTADO</th>
                    <th>ACCION</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $resultado->fetch_assoc()) {
                    $claseFila = ($row['estado'] == 'Invalidación realizada') ? 'fila-invalidada' : '';
                    ?>
                    <tr class="<?= $claseFila ?>">
                        <td><?= date('d/m/Y H:i', strtotime($row['fechafactura'])) ?></td>
                        <td><?= htmlspecialchars($row['descripcionconse']) ?></td>
                        <td><?= htmlspecialchars($row['codigoGeneracion']) ?></td>
                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                        <td><?= htmlspecialchars("$ " . $row['totalpagar']) ?></td>
                        <td><?= htmlspecialchars($row['estado']) ?></td>
                        <td>
                            <button type="button" class="btn btn-warning btn-agregar-documento edit_invalidar"
                                data-codigo="<?= $row['codigoGeneracion'] ?>" <?= $row['estado'] == 'Invalidación realizada' ? 'disabled' : '' ?>>
                                <i class="fa fa-hand-pointer"> </i> Invalidar
                            </button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <nav>
            <ul id="paginacion" class="pagination justify-content-center mt-3"></ul>
        </nav>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // Filtro en tiempo real
        $("#buscarFactura").on("keyup", function () {
            const valor = $(this).val().toLowerCase();
            $("#tablaVentas tbody tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(valor) > -1)
            });
        });

        // Botón Invalidar
        $('#tablaVentas').on('click', '.edit_invalidar', function () {
            const codigo = $(this).attr('data-codigo');
            uni_modal("Transmitir Invalidación de DTE", "invalidarDTE.php?codigo=" + codigo);
        });
    });
</script>