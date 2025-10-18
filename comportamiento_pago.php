<?php
include("conexionfin.php");

// Exportar a Excel si se presiona el botón
if(isset($_POST['exportar_excel'])){
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=clientes_mayor_deuda.xls");
    echo "Cliente\tTotal Deuda\tTotal Abonado\tPendiente\t% Pagado\n";

    $sql = "
        SELECT 
            c.nombre_cliente,
            COALESCE(SUM(dp.total),0) AS total_deuda,
            COALESCE(SUM(a.monto_abono),0) AS total_abonado,
            COALESCE(SUM(dp.total),0) - COALESCE(SUM(a.monto_abono),0) AS pendiente
        FROM clientes c
        LEFT JOIN pedidos p ON p.cliente_id = c.id AND p.estatus <> 'CANCELADO'
        LEFT JOIN detalle_pedidos dp ON dp.pedido_id = p.id
        LEFT JOIN cartera_abono a ON a.id_pedido = p.id
        GROUP BY c.id, c.nombre_cliente
        HAVING pendiente > 0
        ORDER BY pendiente DESC
    ";

    $result = mysqli_query($conexion, $sql);
    while($row = mysqli_fetch_assoc($result)){
        $porcentaje = $row['total_deuda'] > 0 ? ($row['total_abonado']/$row['total_deuda'])*100 : 0;
        echo "{$row['nombre_cliente']}\t{$row['total_deuda']}\t{$row['total_abonado']}\t{$row['pendiente']}\t".number_format($porcentaje,2)."%\n";
    }
    exit();
}
?>

<div class="container-fluid">

 <div class="card-header">
        <h4 class="card-title text-black"><i class="fa fa-box"></i> Comportamiento de pago</h4>
    </div>
    <br>
    <br>
    <table id="tablaDeudas" class="display">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Total Deuda</th>
                <th>Total Abonado</th>
                <th>Pendiente</th>
                <th>% Pagado</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "
                SELECT 
                    c.nombre_cliente,
                    COALESCE(SUM(dp.total),0) AS total_deuda,
                    COALESCE(SUM(a.monto_abono),0) AS total_abonado,
                    COALESCE(SUM(dp.total),0) - COALESCE(SUM(a.monto_abono),0) AS pendiente
                FROM clientes c
                LEFT JOIN pedidos p ON p.cliente_id = c.id AND p.estatus <> 'CANCELADO'
                LEFT JOIN detalle_pedidos dp ON dp.pedido_id = p.id
                LEFT JOIN cartera_abono a ON a.id_pedido = p.id
                GROUP BY c.id, c.nombre_cliente
                HAVING pendiente > 0
                ORDER BY pendiente DESC
            ";
            $result = mysqli_query($conexion, $sql);
            while($row = mysqli_fetch_assoc($result)){
                $porcentaje = $row['total_deuda'] > 0 ? ($row['total_abonado']/$row['total_deuda'])*100 : 0;
                echo "<tr>";
                echo "<td>".htmlspecialchars($row['nombre_cliente'])."</td>";
                echo "<td>$".number_format($row['total_deuda'],2)."</td>";
                echo "<td>$".number_format($row['total_abonado'],2)."</td>";
                echo "<td>$".number_format($row['pendiente'],2)."</td>";
                echo "<td>".number_format($porcentaje,2)."%</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
    $('#tablaDeudas').DataTable({
        "order": [[3, "desc"]] // Ordenar por Pendiente de mayor a menor
    });
</script>
