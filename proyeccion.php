<?php
include("conexionfin.php"); // conexión a la BD

// --- Obtener porcentaje de incremento ---
$incremento = 0.05; // default 5%
$res_inc = $conexion->query("SELECT * FROM proyecciones_ventas ORDER BY id DESC LIMIT 1");
$proy_actual = null;
if($res_inc && $res_inc->num_rows > 0){
    $proy_actual = $res_inc->fetch_assoc();
    $incremento = $proy_actual['porcentaje_incremento']/100;
}

// --- Histórico ---
$sql_historico = "
SELECT 'HISTORICO' AS tipo,
       DATE_FORMAT(p.fecha_pedido, '%Y-%m') AS mes,
       SUM(dp.total) AS monto
FROM pedidos p
JOIN detalle_pedidos dp ON p.id = dp.pedido_id
WHERE p.fecha_pedido >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
  AND p.estatus IN ('FACTURADO','REMISIONADO','CANCELADO')
GROUP BY DATE_FORMAT(p.fecha_pedido, '%Y-%m')
ORDER BY mes
";

$result_historico = $conexion->query($sql_historico);
$datos_historico = [];
while($row = $result_historico->fetch_assoc()){
    $datos_historico[] = $row;
}

// --- Promedio mensual histórico ---
$promedio = count($datos_historico) > 0 ? array_sum(array_column($datos_historico,'monto')) / count($datos_historico) : 0;

// --- Proyección 12 meses desde mes actual ---
$datos_proyeccion = [];
for($i=0; $i<12; $i++){
    $mes = date('Y-m', strtotime("+$i month"));
    $monto_proy = round($promedio * pow(1+$incremento, $i),2);
    $datos_proyeccion[] = [
        'tipo'=>'PROYECCION',
        'mes'=>$mes,
        'monto'=>$monto_proy
    ];
}

// --- Combinar datos y ordenar por tipo y mes ---
$datos = array_merge($datos_historico, $datos_proyeccion);

// --- Función para traducir mes a español ---
function mes_es($fecha){
    $meses = ['01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'];
    $m = date('m', strtotime($fecha));
    $y = date('Y', strtotime($fecha));
    return $meses[$m]." ".$y;
}
?>
<style>
.historico{background-color:#d0ebff;}
.proyeccion{background-color:#ffe5b4;}
</style>

<div class="container-fluid">

 <div class="card-header">
        <h4 class="card-title text-black">📊 Proyección de Ventas (Histórico + Futuro)</h4>
    </div>
<br>
<!-- Botón para configurar porcentaje -->
 <div class="col-sm-12 col-xs-12 text-right">
            <button class="btn btn-warning btn-lg" type="button" data-bs-toggle="modal" data-bs-target="#modalEditarProy">⚙️ Configurar % Incremento</button>
        </div>

<!-- Tabs para tabla y gráfico -->
<ul class="nav nav-tabs" id="tabControl" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="tabla-tab" data-bs-toggle="tab" data-bs-target="#tabla" type="button">Tabla</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="grafico-tab" data-bs-toggle="tab" data-bs-target="#grafico" type="button">Gráfico</button>
  </li>
</ul>
<div class="tab-content mt-3">
  <div class="tab-pane fade show active" id="tabla" role="tabpanel">
      <table id="proyeccions" class="table table-striped">
          <thead>
              <tr>
                  <th>Mes</th>
                  <th>Monto ($)</th>
                  <th>Tipo</th>
              </tr>
          </thead>
          <tbody>
          <?php foreach($datos as $d): ?>
              <tr class="<?= strtolower($d['tipo']) ?>">
                  <td><?= mes_es($d['mes']) ?></td>
                  <td><?= number_format($d['monto'],2) ?></td>
                  <td><?= $d['tipo'] ?></td>
                  
              </tr>
          <?php endforeach; ?>
          </tbody>
      </table>
  </div>
  <div class="tab-pane fade" id="grafico" role="tabpanel">
      <canvas id="ventasChart" width="1000" height="400"></canvas>
  </div>
</div>

<!-- Modal editar porcentaje -->
<div class="modal fade" id="modalEditarProy" tabindex="-1">
<div class="modal-dialog">
    <form method="POST" action="proyecciones_config.php" class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">Editar % Incremento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <input type="hidden" name="id" value="<?= $proy_actual['id'] ?? '' ?>">
        <div class="mb-2">
            <label>% Incremento</label>
            <input type="number" step="0.01" class="form-control" name="porcentaje" value="<?= $proy_actual['porcentaje_incremento'] ?? 5 ?>" required>
        </div>
    </div>
    <div class="modal-footer">
        <?php if($proy_actual): ?>
            <button type="submit" name="guardar" class="btn btn-primary">💾 Guardar</button>
            <a href="proyecciones_config.php?eliminar=<?= $proy_actual['id'] ?>" class="btn btn-danger">🗑️ Eliminar</a>
        <?php else: ?>
            <button type="submit" name="guardar" class="btn btn-success">💾 Guardar Nuevo</button>
        <?php endif; ?>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    </div>
    </form>
</div>
</div>

</div>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    $('#proyeccions').DataTable({
        pageLength: 12,
        order: [[2,'asc'],[0,'asc']] // ordena primero por tipo y luego por mes
    });
});

const ctx = document.getElementById('ventasChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_map('mes_es', array_column($datos,'mes'))) ?>,
        datasets: [{
            label: 'Monto de Ventas',
            data: <?= json_encode(array_column($datos,'monto')) ?>,
            borderColor: 'blue',
            backgroundColor: 'rgba(0,0,255,0.2)',
            fill: true,
            tension: 0.2,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            title: { display: true, text: 'Proyección de Ventas (Empresa)' }
        },
        scales: { y: { beginAtZero: true } }
    }
});
</script>

