<?php
include('conexionfin.php');

$maquina = isset($_GET['maquina']) ? $conexion->real_escape_string($_GET['maquina']) : '';

$filtro = "";
if ($maquina != '') {
    $filtro = "WHERE op.maquina_id = '$maquina'";
}

$sql = "
SELECT op.id, op.numero_op, op.estado, op.cantidad_programada, op.usuario_programo, 
       op.maquina_id, op.prioridad, m.maquina, c.nombre_cliente,c.ciudad,c.nombre_sede,c.departamento
FROM orden_produccion op
INNER JOIN pedidos p ON p.id = op.pedido_id
INNER JOIN clientes c ON c.id = p.cliente_id
LEFT JOIN lista_maquina m ON m.id = op.maquina_id
$filtro
ORDER BY op.prioridad ASC, op.id DESC
";

$res = $conexion->query($sql);

if ($res->num_rows === 0) {
    echo "<div class='alert alert-warning'>No se encontraron órdenes.</div>";
    exit;
}

while ($row = $res->fetch_assoc()) {
    echo "
    <div class='op-item' data-op-id='{$row['id']}'>
        <div class='d-flex justify-content-between align-items-center' >
            <div>
                <span class='drag-handle' style='font-size: 18px;'>⠿
                <p><strong>Numero de Orden: </strong>{$row['numero_op']}</p>
                <p class='mb-1'><b>Cliente:</b> {$row['nombre_cliente']}</p>
                <p class='mb-1'><b>Ciudad:</b> {$row['ciudad']}  <small class='text-white'>_______</small>   <b> Departamento:</b> {$row['departamento']}</small></p>
                <p class='mb-1'></p>
                <p class='mb-0'><small>Programado por: {$row['usuario_programo']}</small><small class='text-white'>_______</small>   <small>Máquina: {$row['maquina']}</small></p></span>
            </div>
            <div class='text-end'>
                <h4><span class='text-white badge bg-".($row['estado'] == 'PROGRAMADA' ? 'warning' : 'info')."'>{$row['estado']}</spans><br>
                <small>Cant.: {$row['cantidad_programada']}</small></h4>
            </div>
        </div>
    </div>
    ";
}
?>
