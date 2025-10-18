<?php include('conexionfin.php'); ?>
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
        <h4 class="card-title text-black"><i class="fa fa-box"></i> Mis Pedidos Relizados</h4>
    </div>
    <br>
    <div class="col-lg-12">

        <br />

        <table class="table table-responsive" id="borrower-list">
            <colgroup>
                <col width="10%">
                <col width="15%">
                <col width="35%">
                <col width="10%">
                <col width="20%">
                <col width="20%">
                <col width="20%">
                <col width="20%">
                <col width="20%">
                <col width="20%">
            </colgroup>
            <thead class="table-dark">
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">NIT</th>
                    <th class="text-center">Nombre del cliente</th>
                    <th class="text-center">Numero de Pedido</th>
                    <th class="text-center">Fecha de emision</th>
                    <th class="text-center">Plazo de pago</th>
                    <th class="text-center">Fecha de Vencimiento</th>
                    <th class="text-center">SubTotal</th>
                    <th class="text-center">IVA</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Accion</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                $qry = $conexion->query("SELECT 
                                                c.nit,
                                                p.id,
                                                c.nombre_cliente,
                                                p.numero_pedido,
                                                p.fecha_pedido,
                                                p.plazo_pago_dias,
                                                DATE_ADD(p.fecha_pedido, INTERVAL p.plazo_pago_dias DAY) AS fecha_vencimiento,
                                                SUM(dp.subtotal) AS total_subtotal,
                                                SUM(dp.iva) AS total_iva,
                                                SUM(dp.total) AS total_total
                                            FROM pedidos p
                                            INNER JOIN clientes c ON c.id = p.cliente_id
                                            INNER JOIN detalle_pedidos dp ON dp.pedido_id = p.id
                                            WHERE p.usuario_id = $_SESSION[login_idusuario]
                                            GROUP BY 
                                                c.nit,
                                                c.nombre_cliente,
                                                p.numero_pedido,
                                                p.fecha_pedido,
                                                p.plazo_pago_dias;
                                            ");
                while ($row = $qry->fetch_assoc()):
                ?>
                    <tr>
                        <td style="font-size: 12px;" class="">
                            <?php echo $i++ ?>
                        </td>
                        <td style="font-size: 12px;" class="">
                            <?php echo $row['nit'] ?>
                        </td>
                        <td style="font-size: 12px;" class="">
                            <?php echo $row['nombre_cliente'] ?>
                        </td>
                        <td style="font-size: 12px;" class="">
                            <?php echo $row['numero_pedido'] ?>
                        </td>
                        <td style="font-size: 12px;" class="">
                            <?php echo $row['fecha_pedido'] ?>
                        </td>
                        <td style="font-size: 12px;" class="">
                            <?php echo $row['plazo_pago_dias'] ?>
                        </td>
                        <td style="font-size: 12px;" class="">
                            <?php echo $row['fecha_vencimiento'] ?>
                        </td>
                        <td style="font-size: 12px;" class="">
                            <?php echo $row['total_subtotal'] ?>
                        </td>
                        <td style="font-size: 12px;" class="">
                            <?php echo $row['total_iva'] ?>
                        </td>
                        <td style="font-size: 12px;" class="">
                            <?php echo $row['total_total'] ?>
                        </td>
                        <td style="white-space: nowrap;">
                            <button class="btn btn-danger btn-sm delete_borrower" type="button"
                                data-id="<?php echo $row['id'] ?>">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>
</div>

<script>
    $('#borrower-list').dataTable()
    
    $('#borrower-list').on('click', '.delete_borrower', function() {
        _conf("Esta seguro que quiere eliminar este pedido?", "delete_borrower", [$(this).attr('data-id')])
    })

    function delete_borrower($id) {
        start_load()
       $.ajax({
    url: 'delete_pedido.php',
    method: 'POST',
    data: { idpedido: $id },
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