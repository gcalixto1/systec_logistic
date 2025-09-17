<?php
include 'conexionfin.php';

$query = "SELECT factura.id, factura.fechafactura, factura.numerofactura, consecutivos.descripcionconse, 
                respuestadte.codigoGeneracion, cliente.nombre, cliente.dni, factura.totalpagar, medio_pago.medio_pago
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

<!-- Buscador -->
<input type="text" id="buscarFactura" class="form-control mb-2" placeholder="Buscar por nombre, código o total">

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
                <th>METODO DE PAGO</th>
                <th>ACCION</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $resultado->fetch_assoc()) { ?>
                <tr>
                    <td><?= date('d/m/Y H:i', strtotime($row['fechafactura'])) ?></td>
                    <td><?= htmlspecialchars($row['descripcionconse']) ?></td>
                    <td><?= htmlspecialchars($row['codigoGeneracion']) ?></td>
                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                    <td><?= htmlspecialchars("$ " . $row['totalpagar']) ?></td>
                    <td><?= htmlspecialchars($row['medio_pago']) ?></td>
                    <td>
                        <button type="button" class="btn btn-primary btn-agregar-documento"
                            data-codigo="<?= $row['codigoGeneracion'] ?>"
                            data-fecha="<?= date('d/m/Y H:i', strtotime($row['fechafactura'])) ?>"
                            data-monto="<?= $row['totalpagar'] ?>" data-dismiss="modal">
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
            const monto = $(this).data('monto');
            const fecha = $(this).data('fecha');

            const fila = `
                <tr>
                    <td>Comprobante de Credito Fiscal</td>
                    <td>Electronico</td>
                    <td>${codigo}</td>
                    <td>${fecha}</td>
                    <td><button type="button" class="btn btn-danger btn-sm eliminar-fila"><i class="fas fa-trash"></i></button></td>
                </tr>
            `;

            $('#tablaDocumentosRelacionados tbody').append(fila);

            let montoActual = parseFloat($('#monto').val()) || 0;
            let nuevoMonto = parseFloat(monto) || 0;
            $('#monto').val((montoActual + nuevoMonto).toFixed(2));
        });

        // Eliminar fila
        $(document).on('click', '.eliminar-fila', function () {
            $(this).closest('tr').remove();
        });
    });
</script>