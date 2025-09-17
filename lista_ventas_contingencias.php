<?php
include 'conexionfin.php';

$query = "SELECT *
          FROM lista_contingencia_dte
          INNER JOIN factura ON factura.id = lista_contingencia_dte.idfactura
          INNER JOIN consecutivos ON consecutivos.codigo_consecutivo = factura.tipofactura
          WHERE lista_contingencia_dte.codigoGeneracion nOT IN (
              SELECT codigoGeneracion
              FROM respuestadte
              WHERE estado = 'PROCESADO' OR estado = 'RECIBIDO'
          )";

$resultado = $conexion->query($query);
if (!$resultado) {
    die("Error en la consulta: " . $conexion->error);
}
?>

<!-- Buscador -->
<input type="text" id="buscarFactura" class="form-control mb-2" placeholder="Buscar por nombre, código o total">

<!-- Tabla -->
<div class="table-responsive">
    <table id="tablaVentas" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>FECHA CONTINGENCIA</th>
                <th>TIPO COMPROBANTE</th>
                <th>CODIGO COMPROBANTE</th>
                <th>ACCION</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $resultado->fetch_assoc()) { ?>
                <tr>
                    <td><?= date('d/m/Y H:i', strtotime($row['fechafactura'])) ?></td>
                    <td><?= htmlspecialchars($row['descripcionconse']) ?></td>
                    <td><?= htmlspecialchars($row['codigoGeneracion']) ?></td>
                    <td>
                        <button type="button" class="btn btn-primary btn-agregar-documento"
                            data-codigo="<?= $row['codigoGeneracion'] ?>" data-monto="<?= $row['descripcionconse'] ?>"
                            data-dismiss="modal">
                            <i class="fa fa-hand-pointer"></i>
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

        // Botón seleccionar factura
        $(document).on('click', '.btn-agregar-documento', function () {
            const codigo = $(this).data('codigo');
            const tipoF = $(this).data('monto');

            $('#codigo').val(codigo);
            $('#tipoF').val(tipoF);
        });
    });
</script>