<?php
include('conexionfin.php');

$sql = "
SELECT
    p.fecha_pedido,
    p.id AS id_pedido,
    p.numero_pedido,
    c.nombre_cliente,
    c.ciudad,
    pr.cod_producto,
    pr.tipo,
    pr.calibre,
    pr.ref_1,
    pr.ref_2,
    pr.relacion,
    dp.cantidad,
    CASE 
        WHEN pr.relacion = 'KG' THEN dp.cantidad * pr.peso_kg_paca_caja
        ELSE dp.cantidad * pr.und_embalaje_minima
    END AS unidades_por_caja,
    CASE 
        WHEN pr.relacion = 'KG' THEN dp.cantidad * pr.peso_kg_paca_caja * pr.peso_kg
        ELSE dp.cantidad * pr.und_embalaje_minima * pr.peso_kg
    END AS peso_total,
    u.nombre,
    pr.descripcion,
    c.id
FROM pedidos p
INNER JOIN detalle_pedidos dp ON dp.pedido_id = p.id
INNER JOIN producto pr ON pr.id_producto = dp.producto_id
INNER JOIN clientes c ON c.id = p.cliente_id
INNER JOIN usuario u ON u.idusuario = p.usuario_id
WHERE (pr.tipo = 'Stretch' OR pr.familia LIKE '%Stretch%')
  AND p.estatus <> 'PROGRAMADO'
  AND pr.activo = 1
ORDER BY p.fecha_pedido DESC
";

$res = $conexion->query($sql);
?>

<div class="container-fluid">
    <div class="mb-3">
        <label><b>Seleccionar columnas a mostrar:</b></label><br>
        <div id="columnSelector" class="d-flex flex-wrap gap-2">
            <label><input type="checkbox" class="col-toggle" data-col="1" checked> Fecha</label>
            <label><input type="checkbox" class="col-toggle" data-col="2" checked> Pedido</label>
            <label><input type="checkbox" class="col-toggle" data-col="3" checked> Cliente</label>
            <label><input type="checkbox" class="col-toggle" data-col="4" checked> Ciudad</label>
            <label><input type="checkbox" class="col-toggle" data-col="5" checked> Cod Producto</label>
            <label><input type="checkbox" class="col-toggle" data-col="6" checked> Tipo</label>
            <label><input type="checkbox" class="col-toggle" data-col="7" checked> Calibre</label>
            <label><input type="checkbox" class="col-toggle" data-col="8" checked> Ref 1</label>
            <label><input type="checkbox" class="col-toggle" data-col="9" checked> Ref 2</label>
            <label><input type="checkbox" class="col-toggle" data-col="10" checked> Relación</label>
            <label><input type="checkbox" class="col-toggle" data-col="11" checked> Cantidad</label>
            <label><input type="checkbox" class="col-toggle" data-col="12" checked> Unds Caja</label>
            <label><input type="checkbox" class="col-toggle" data-col="13" checked> Peso Total</label>
            <label><input type="checkbox" class="col-toggle" data-col="14" checked> Asesor</label>
            <label><input type="checkbox" class="col-toggle" data-col="15" checked> Producto</label>
            <label><input type="checkbox" class="col-toggle" data-col="16" checked> Cod Cliente</label>
        </div>
    </div>

    <form id="form-select-pedidos">
        <table class="table table-bordered table-responsive" id="tabla-pedidos">
            <thead class="table-dark">
                <tr>
                    <th><input type="checkbox" id="select_all"></th>
                    <th>Fecha</th>
                    <th>Pedido</th>
                    <th>Cliente</th>
                    <th>Ciudad</th>
                    <th>Cod Producto</th>
                    <th>Tipo</th>
                    <th>Calibre</th>
                    <th>Ref 1</th>
                    <th>Ref 2</th>
                    <th>Relación</th>
                    <th>Cantidad</th>
                    <th>Unds Caja</th>
                    <th>Peso Total</th>
                    <th>Asesor</th>
                    <th>Producto</th>
                    <th>Cod Cliente</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $res->fetch_assoc()): ?>
                <tr>
                    <td class="text-center">
                         <input type="checkbox" class="pedido-check"
                        value="<?php echo $row['id_pedido']; ?>"
                        data-cod-producto="<?php echo $row['cod_producto']; ?>">
                    </td>
                    <td><?php echo $row['fecha_pedido']; ?></td>
                    <td><?php echo $row['numero_pedido']; ?></td>
                    <td><?php echo $row['nombre_cliente']; ?></td>
                    <td><?php echo $row['ciudad']; ?></td>
                    <td><?php echo $row['cod_producto']; ?></td>
                    <td><?php echo $row['tipo']; ?></td>
                    <td><?php echo $row['calibre']; ?></td>
                    <td><?php echo $row['ref_1']; ?></td>
                    <td><?php echo $row['ref_2']; ?></td>
                    <td><?php echo $row['relacion']; ?></td>
                    <td><?php echo $row['cantidad']; ?></td>
                    <td><?php echo $row['unidades_por_caja']; ?></td>
                    <td><?php echo $row['peso_total']; ?></td>
                    <td><?php echo $row['nombre']; ?></td>
                    <td><?php echo $row['descripcion']; ?></td>
                    <td><?php echo $row['id']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="text-end mt-2">
            <button type="button" class="btn btn-success" id="cargarSeleccion">
                <i class="fa fa-upload"></i> Cargar pedidos seleccionados
            </button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    const table = $('#tabla-pedidos').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        pageLength: 5,
        columnDefs: [{ orderable: false, targets: 0 }],
        language: {
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros por página",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            paginate: {
                first: "Primero", last: "Último",
                next: "Siguiente", previous: "Anterior"
            },
            zeroRecords: "No se encontraron registros coincidentes",
            infoEmpty: "Mostrando 0 a 0 de 0 registros",
            infoFiltered: "(filtrado de _MAX_ registros totales)"
        }
    });

    // 🔹 Seleccionar / deseleccionar todo
    $('#select_all').on('change', function() {
        $('.pedido-check').prop('checked', $(this).is(':checked'));
    });

    // 🔹 Mostrar / ocultar columnas según selección
    $('.col-toggle').on('change', function() {
        const colIndex = parseInt($(this).data('col'));
        const visible = $(this).is(':checked');
        table.column(colIndex).visible(visible);
    });

    // 🔹 Enviar pedidos seleccionados al formulario principal
    $('#cargarSeleccion').click(function() {
        let pedidos = [];
        $('.pedido-check:checked').each(function() {
        pedidos.push({
            pedido_id: $(this).val(),
            cod_producto: $(this).data('cod-producto')
        });
    });

        if (pedidos.length === 0) {
            alert('Seleccione al menos un pedido.');
            return;
        }

        if (window.parent && window.parent.fillFormsFromPedidos) {
            window.parent.fillFormsFromPedidos(pedidos);
            window.parent.$('#uni_modal').modal('hide');
        }
    });
});
</script>
