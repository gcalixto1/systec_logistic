<?php
include('conexionfin.php');

// Consulta de facturas pendientes
$sqlFacturas = "SELECT factura.id, factura.fechafactura, factura.numerofactura, consecutivos.descripcionconse, consecutivos.codigo_consecutivo,
                cliente.nombre, cliente.dni, factura.totalpagar, medio_pago.codigo AS medio_pago,medio_pago.medio_pago as forma_pago
          FROM factura
          INNER JOIN cliente ON cliente.idcliente = factura.idcliente
          INNER JOIN consecutivos ON consecutivos.codigo_consecutivo = factura.tipofactura
          INNER JOIN medio_pago ON medio_pago.codigo = factura.forma_pago
          WHERE factura.estado = 'Pendiente'
          ORDER BY factura.fechafactura DESC";

$resFacturas = $conexion->query($sqlFacturas);
?>
<!-- Buscador -->
<input type="text" id="buscarFactura" class="form-control mb-2" placeholder="Buscar por nombre, código o total">
<table id="tablaVentas" class="table table-bordered">
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
        <?php while ($factura = $resFacturas->fetch_assoc()): ?>
            <?php
            $idfactura = $factura['id'];

            // Consulta detalle + producto
            $sqlDetalle = "SELECT d.cod_producto, d.precioventa, d.cantidad, p.descripcion
                           FROM detallefactura d
                           INNER JOIN producto p ON d.cod_producto = p.codproducto
                           WHERE d.idfactura = $idfactura";
            $resDetalle = $conexion->query($sqlDetalle);

            $detalle = [];
            while ($row = $resDetalle->fetch_assoc()) {
                $detalle[] = [
                    'id' => $row['cod_producto'],
                    'descripcion' => $row['descripcion'],
                    'precio' => floatval($row['precioventa']),
                    'cantidad' => intval($row['cantidad']),
                ];
            }

            $detalle_json = htmlspecialchars(json_encode($detalle), ENT_QUOTES, 'UTF-8');
            ?>
            <tr>
                <td><?= date('d/m/Y H:i', strtotime($factura['fechafactura'])) ?></td>
                <td><?= htmlspecialchars($factura['descripcionconse']) ?></td>
                <td><?= $factura['numerofactura'] ?></td>
                <td><?= $factura['nombre'] ?></td>
                <td>$ <?= number_format($factura['totalpagar'], 2) ?></td>
                <td><?= htmlspecialchars($factura['forma_pago']) ?></td>
                <td>
                    <button type="button" class="btn btn-primary seleccionar-factura"
                        data-cliente="<?= htmlspecialchars($factura['nombre'], ENT_QUOTES) ?>"
                        data-prefix="<?= $factura['codigo_consecutivo'] ?>" data-pago="<?= $factura['medio_pago'] ?>"
                        data-idfactura="<?= $factura['id'] ?>" data-detalle='<?= $detalle_json ?>' data-dismiss="modal">
                        <i class="fa fa-hand-pointer"></i>
                    </button>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<script>
    $(document).ready(function () {
        // Filtro en tiempo real
        $("#buscarFactura").on("keyup", function () {
            const valor = $(this).val().toLowerCase();
            $("#tablaVentas tbody tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(valor) > -1)
            });
        });
    });
</script>