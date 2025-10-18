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
        <h4 class="card-title text-black"><i class="fa fa-box"></i> Resumen de Pedidos</h4>
    </div>
    <br>
    <div class="col-lg-12">
        <br />
        <table class="table table-responsive" id="borrower-list">
            <colgroup>
                <col width="5%">
                <col width="10%">
                <col width="15%">
                <col width="10%">
                <col width="10%">
                <col width="10%">
                <col width="10%">
                <col width="10%">
                <col width="10%">
                <col width="10%">
                <col width="10%">
                <col width="10%">
            </colgroup>
            <thead class="table-dark">
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Pedido</th>
                    <th class="text-center">Fecha Pedido</th>
                    <th class="text-center">Cliente</th>
                    <th class="text-center">Sede</th>
                    <th class="text-center">Ciudad</th>
                    <th class="text-center">Código Producto</th>
                    <th class="text-center">Descripción</th>
                    <th class="text-center">Ref 1</th>
                    <th class="text-center">Ref 2</th>
                    <th class="text-center">Relación</th>
                    <th class="text-center">Calibre</th>
                    <th class="text-center">Und Embalaje</th>
                    <th class="text-center">UMB</th>
                    <th class="text-center">Estatus</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                $qry = $conexion->query("
                    SELECT 
                        p.numero_pedido,
                        p.fecha_pedido,
                        c.nombre_cliente,
                        c.nombre_sede,
                        c.ciudad,
                        pr.cod_producto,
                        pr.descripcion,
                        pr.ref_1,
                        pr.ref_2,
                        pr.relacion,
                        pr.calibre,
                        pr.und_embalaje_minima,
                        pr.umb,
                        p.estatus
                    FROM pedidos p
                    INNER JOIN clientes c ON c.id = p.cliente_id
                    INNER JOIN detalle_pedidos dp ON dp.pedido_id = p.id
                    INNER JOIN producto pr ON pr.id_producto = dp.producto_id
                    WHERE p.usuario_id = ".$_SESSION['login_idusuario']."
                    ORDER BY p.numero_pedido, pr.cod_producto
                ");

                while ($row = $qry->fetch_assoc()):
                ?>
                    <tr>
                        <td style="font-size:12px;"><?php echo $i++ ?></td>
                        <td style="font-size:12px;"><?php echo $row['numero_pedido'] ?></td>
                        <td style="font-size:12px;"><?php echo $row['fecha_pedido'] ?></td>
                        <td style="font-size:12px;"><?php echo $row['nombre_cliente'] ?></td>
                        <td style="font-size:12px;"><?php echo $row['nombre_sede'] ?></td>
                        <td style="font-size:12px;"><?php echo $row['ciudad'] ?></td>
                        <td style="font-size:12px;"><?php echo $row['cod_producto'] ?></td>
                        <td style="font-size:12px;"><?php echo $row['descripcion'] ?></td>
                        <td style="font-size:12px;"><?php echo $row['ref_1'] ?></td>
                        <td style="font-size:12px;"><?php echo $row['ref_2'] ?></td>
                        <td style="font-size:12px;"><?php echo $row['relacion'] ?></td>
                        <td style="font-size:12px;"><?php echo $row['calibre'] ?></td>
                        <td style="font-size:12px;"><?php echo $row['und_embalaje_minima'] ?></td>
                        <td style="font-size:12px;"><?php echo $row['umb'] ?></td>
                        <td style="font-size:12px;"><?php echo $row['estatus'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $('#borrower-list').dataTable({
        "order": [[1, "asc"]],
        "pageLength": 25
    });
</script>
