<?php
include("conexionfin.php");

// Guardar meta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendedor_id = $_POST['vendedor_id'];
    $mes = $_POST['mes'];
    $anio = $_POST['anio'];
    $meta = $_POST['meta'];

    $sql = "INSERT INTO metas_vendedor (vendedor_id, mes, anio, meta_ventas) 
            VALUES ('$vendedor_id', '$mes', '$anio', '$meta')";
    mysqli_query($conexion, $sql);
}

// Obtener vendedores
$vendedores = mysqli_query($conexion, "SELECT idusuario, nombre FROM usuario");

// Consultar metas con lo abonado y el total de pedidos
$metas = mysqli_query($conexion, "
    SELECT 
        m.id,
        u.nombre AS vendedor,
        m.mes,
        m.anio,
        m.meta_ventas,

        -- Facturado: solo lo abonado en cartera_abono
        COALESCE(SUM(CASE 
                        WHEN MONTH(p.fecha_pedido) = m.mes 
                          AND YEAR(p.fecha_pedido) = m.anio
                        THEN ca.monto_abono 
                        ELSE 0 
                     END),0) AS venta_facturada,

        -- Total: el total de los pedidos en detalle_pedidos
        COALESCE(SUM(CASE 
                        WHEN MONTH(p.fecha_pedido) = m.mes 
                          AND YEAR(p.fecha_pedido) = m.anio
                        THEN dp.total 
                        ELSE 0 
                     END),0) AS venta_total

    FROM metas_vendedor m
    INNER JOIN usuario u ON u.idusuario = m.vendedor_id
    LEFT JOIN pedidos p ON p.usuario_id = u.idusuario
    LEFT JOIN detalle_pedidos dp ON dp.pedido_id = p.id
    LEFT JOIN cartera_abono ca ON ca.id_pedido = p.id
    GROUP BY m.id, u.nombre, m.mes, m.anio, m.meta_ventas
    ORDER BY m.anio DESC, m.mes DESC
");

// Meses en español
$meses = [
    1 => "Enero",
    2 => "Febrero",
    3 => "Marzo",
    4 => "Abril",
    5 => "Mayo",
    6 => "Junio",
    7 => "Julio",
    8 => "Agosto",
    9 => "Septiembre",
    10 => "Octubre",
    11 => "Noviembre",
    12 => "Diciembre"
];
?>

<div class="container-fluid">
    <div class="card-header">
        <h4 class="card-title text-black"><i class="fa fa-box"></i> Asignar Meta a Vendedor</h4>
    </div>

    <form method="POST" class="row g-3 mb-4">
        <div class="col-md-4">
            <label>Vendedor</label>
            <select name="vendedor_id" class="form-control" required>
                <option value="">Seleccione...</option>
                <?php while ($v = mysqli_fetch_assoc($vendedores)): ?>
                    <option value="<?= $v['idusuario'] ?>"><?= $v['nombre'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label>Mes</label>
            <select name="mes" class="form-control" required>
                <?php foreach ($meses as $num => $nombre): ?>
                    <option value="<?= $num ?>"><?= $nombre ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label>Año</label>
            <input type="number" name="anio" class="form-control" value="<?= date('Y') ?>" required>
        </div>
        <div class="col-md-2">
            <label>Meta ($)</label>
            <input type="number" step="0.01" name="meta" class="form-control" required>
        </div>
        <div class="col-md-2 align-self-end">
            <button class="btn btn-primary w-100">Guardar</button>
        </div>
    </form>

    <hr>

    <!-- Tabs -->
    <ul class="nav nav-tabs" id="metaTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tabla-tab" data-bs-toggle="tab" data-bs-target="#tabla" type="button" role="tab">Tabla</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="grafico-tab" data-bs-toggle="tab" data-bs-target="#graficoTab" type="button" role="tab">Gráfico</button>
        </li>
    </ul>

    <div class="tab-content mt-3">
        <!-- Tabla -->
        <div class="tab-pane fade show active" id="tabla" role="tabpanel">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Vendedor</th>
                        <th>Mes</th>
                        <th>Año</th>
                        <th>Meta</th>
                        <th>Facturado</th>
                        <th>Total</th>
                        <th>% Facturación</th>
                        <th>% Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $labels = [];
                    $metaData = [];
                    $totalData = [];

                    mysqli_data_seek($metas, 0); // resetear el puntero por si ya usaste $metas arriba
                    while ($m = mysqli_fetch_assoc($metas)):
                        $porc_fact = $m['meta_ventas'] > 0 ? ($m['venta_facturada'] / $m['meta_ventas']) * 100 : 0;
                        $porc_total = $m['meta_ventas'] > 0 ? ($m['venta_total'] / $m['meta_ventas']) * 100 : 0;

                        $labels[] = $m['vendedor'] . " " . $meses[$m['mes']] . "/" . $m['anio'];
                        $metaData[] = $m['meta_ventas'];
                        $totalData[] = $m['venta_total'];
                    ?>
                        <tr>
                            <td><?= $m['vendedor'] ?></td>
                            <td><?= $meses[$m['mes']] ?></td>
                            <td><?= $m['anio'] ?></td>
                            <td>$<?= number_format($m['meta_ventas'], 2) ?></td>
                            <td>$<?= number_format($m['venta_facturada'], 2) ?></td>
                            <td>$<?= number_format($m['venta_total'], 2) ?></td>
                            <td><?= number_format($porc_fact, 2) ?>%</td>
                            <td><?= number_format($porc_total, 2) ?>%</td>
                            <td>
                                <button
                                    class="btn btn-sm btn-warning editarMetaBtn"
                                    data-id="<?= $m['id'] ?>"
                                    data-meta="<?= $m['meta_ventas'] ?>">
                                    Editar
                                </button>
                                <button
                                    class="btn btn-sm btn-danger eliminarMetaBtn"
                                    data-id="<?= $m['id'] ?>">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal para Editar Meta -->
        <div class="modal fade" id="editarMetaModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="editar_meta.php" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Meta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_meta" id="id_meta">
                        <div class="mb-3">
                            <label for="nueva_meta" class="form-label">Nueva Meta ($)</label>
                            <input type="number" step="0.01" name="nueva_meta" id="nueva_meta" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal para Eliminar Meta -->
        <div class="modal fade" id="eliminarMetaModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="eliminar_meta.php" class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Eliminar Meta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_meta" id="id_meta_eliminar">
                        <p>¿Estás seguro que deseas eliminar esta meta asignada?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>


        <script>
            document.querySelectorAll(".editarMetaBtn").forEach(btn => {
                btn.addEventListener("click", function() {
                    const id = this.getAttribute("data-id");
                    const meta = this.getAttribute("data-meta");
                    document.getElementById("id_meta").value = id;
                    document.getElementById("nueva_meta").value = meta;
                    new bootstrap.Modal(document.getElementById("editarMetaModal")).show();
                });
            });
        </script>


        <!-- Gráfico -->
        <div class="tab-pane fade" id="graficoTab" role="tabpanel">
            <canvas id="grafico" height="120"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const ctx = document.getElementById('grafico').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                    label: 'Meta',
                    data: <?= json_encode($metaData) ?>,
                    backgroundColor: 'rgba(0, 119, 36, 0.6)'
                },
                {
                    label: 'Total Vendido',
                    data: <?= json_encode($totalData) ?>,
                    backgroundColor: 'rgba(0, 110, 255, 0.6)'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    document.querySelectorAll(".eliminarMetaBtn").forEach(btn => {
    btn.addEventListener("click", function() {
        const id = this.getAttribute("data-id");
        document.getElementById("id_meta_eliminar").value = id;
        new bootstrap.Modal(document.getElementById("eliminarMetaModal")).show();
    });
});
</script>