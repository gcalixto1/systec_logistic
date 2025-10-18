<?php include('conexionfin.php'); 
$query = "
SELECT 
    p.id,
    p.numero_pedido,
    p.fecha_pedido,
    DATE_ADD(p.fecha_pedido, INTERVAL p.plazo_pago_dias DAY) AS fecha_vencimiento,
    DATEDIFF(DATE_ADD(p.fecha_pedido, INTERVAL p.plazo_pago_dias DAY), CURDATE()) AS dias_restantes,
    c.nombre_cliente,
    IFNULL(det.monto_deuda, 0) AS monto_deuda,
    IFNULL(abo.total_abonado, 0) AS total_abonado,
    (IFNULL(det.monto_deuda, 0) - IFNULL(abo.total_abonado, 0)) AS monto_pendiente,
    ROUND(
        (IFNULL(abo.total_abonado, 0) / NULLIF(det.monto_deuda, 0)) * 100, 
        2
    ) AS porcentaje_pago
FROM pedidos p
INNER JOIN clientes c ON c.id = p.cliente_id

-- Subconsulta para monto de deuda (detalle)
LEFT JOIN (
    SELECT 
        pedido_id,
        SUM(cantidad * precio) AS monto_deuda
    FROM detalle_pedidos
    GROUP BY pedido_id
) det ON det.pedido_id = p.id

-- Subconsulta para monto abonado
LEFT JOIN (
    SELECT 
        id_pedido,
        SUM(monto_abono) AS total_abonado
    FROM cartera_abono
    GROUP BY id_pedido
) abo ON abo.id_pedido = p.id

WHERE p.estatus = 'PENDIENTE'
ORDER BY p.fecha_pedido DESC
";

$resultado = $conexion->query($query);
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
        <h4 class="card-title text-black"><i class="fa fa-box"></i> Resumen de Cartera</h4>
    </div>
    <br>
    <div class="col-lg-12">

        <br />

<table class="table table-bordered text-center" id="borrower-list">
    <thead class="table-dark">
        <tr>
            <th>Pedido</th>
            <th>Cliente</th>
            <th>Fecha Pedido</th>
            <th>Fecha Vencimiento</th>
            <th>Días Restantes</th>
            <th>Monto Deuda</th>
            <th>Total Abonado</th>
            <th>Pendiente</th>
            <th>% Pago</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $resultado->fetch_assoc()) { 
            $estado = '';
            $color = '';
            $dias = $row['dias_restantes'];

            if ($dias > 5) {
                $estado = "EN TIEMPO";
                $color = "bg-success text-white"; // Verde
            } elseif ($dias >= -15 && $dias <= 5) {
                $estado = "POR VENCER / RECIÉN VENCIDO";
                $color = "bg-warning"; // Amarillo
            } else {
                $estado = "VENCIDO";
                $color = "bg-danger text-white"; // Rojo
            }
        ?>
        <tr class="<?= $color ?>">
            <td><?= htmlspecialchars($row['numero_pedido']) ?></td>
            <td><?= htmlspecialchars($row['nombre_cliente']) ?></td>
            <td><?= date('d/m/Y', strtotime($row['fecha_pedido'])) ?></td>
            <td><?= date('d/m/Y', strtotime($row['fecha_vencimiento'])) ?></td>
            <td><?= $dias ?> días</td>
            <td>$<?= number_format($row['monto_deuda'], 2) ?></td>
            <td>$<?= number_format($row['total_abonado'], 2) ?></td>
            <td>$<?= number_format($row['monto_pendiente'], 2) ?></td>
            <td><?= $row['porcentaje_pago'] ?>%</td>
            <td><strong><?= $estado ?></strong></td>
        </tr>
        <?php } ?>
    </tbody>
</table>



    </div>
</div>

<script>
    $('#borrower-list').dataTable()
    $('#new_categoria').click(function() {
        uni_modal("Gestion de Presentacion de Productos", "manage_presentacion.php")
    })
    $('#borrower-list').on('click', '.edit_borrower', function() {
        uni_modal("Modificar Categoria", "manage_categorias.php?categoria_id=" + $(this).attr('data-id'))
    })
    $('#borrower-list').on('click', '.delete_borrower', function() {
        _conf("Esta seguro que quiere eliminar este proveedor?", "delete_borrower", [$(this).attr('data-id')])
    })

    function delete_borrower($id) {
        start_load()
        $.ajax({
            url: 'ajax.php?action=delete_pedido',
            method: 'POST',
            data: {
                idpedido: $id
            },
            success: function(resp) {
                if (resp == 1) {
                    Swal.fire({
                        title: '<img width="65" height="65" src="https://img.icons8.com/external-bearicons-gradient-bearicons/64/external-trash-can-graphic-design-bearicons-gradient-bearicons.png" alt="external-trash-can-graphic-design-bearicons-gradient-bearicons"/>',
                        text: "El registro fue eliminado",
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    })
                }
            }
        })
    }
</script>