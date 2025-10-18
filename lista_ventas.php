<?php
include 'conexionfin.php';

// Consulta pedidos con detalle
$query = "SELECT 
    pr.str_id,
    pr.cod_producto,
    pr.descripcion,
    c.nombre_cliente,
    p.numero_pedido,
    u.nombre AS usuario,
    p.fecha_pedido,
    dp.cantidad,
    dp.precio,
    dp.total,
    pr.relacion,
    c.ciudad,
    c.nombre_sede,
    pr.ref_2,
    pr.calibre,
    pr.umb,
    pr.und_embalaje_minima,
    pr.peso_kg_paca_caja
FROM detalle_pedidos dp
INNER JOIN pedidos p ON p.id = dp.pedido_id
INNER JOIN producto pr ON pr.id_producto = dp.producto_id
INNER JOIN clientes c ON c.id = p.cliente_id
INNER JOIN usuario u ON u.idusuario = p.usuario_id
ORDER BY p.fecha_pedido DESC;";

$resultado = $conexion->query($query);
if (!$resultado) {
    die("Error en la consulta: " . $conexion->error);
}

$datos = [];
while ($row = $resultado->fetch_assoc()) {
    $datos[] = $row;
}
?>
<div class="container-fluids">
    <div class="card-header">
        <h4 class="card-title text-black"><i class="fa fa-box"></i> Resumen de Ventas General</h4>
    </div>
    <br>

    <!-- Tabs -->
    <ul class="nav nav-tabs" id="tabsVentas" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tabTabla" data-bs-toggle="tab" data-bs-target="#contenidoTabla" type="button" role="tab">Tabla</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tabGraficos" data-bs-toggle="tab" data-bs-target="#contenidoGraficos" type="button" role="tab">Gráficos</button>
        </li>
    </ul>

    <div class="tab-content mt-3">
        <!-- TABLA -->
        <div class="tab-pane fade show active" id="contenidoTabla" role="tabpanel">
            <!-- Filtros -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <select id="filtroTipo" class="form-control">
                        <option value="">Buscar en todo</option>
                        <option value="cliente">Cliente</option>
                        <option value="usuario">Asesor</option>
                        <option value="pedido">N° Pedido</option>
                    </select>
                </div>
                <div class="col-md-9">
                    <input type="text" id="buscarPedido" class="form-control" placeholder="Escribe para buscar...">
                </div>
            </div>

            <!-- Tabla -->
            <div class="table-responsive">
                <table id="tablaPedidos" class="table table-bordered table-responsive">
                    <thead class="table-dark">
                        <tr>
                            <th>FECHA PEDIDO</th>
                            <th>ASESOR</th>
                            <th>N° PEDIDO</th>
                            <th>CLIENTE</th>
                            <th>SEDE</th>
                            <th>CIUDAD</th>
                            <th>USUARIO</th>
                            <th>CÓDIGO PRODUCTO</th>
                            <th>DESCRIPCIÓN</th>
                            <th>CANT/PACA</th>
                            <th>CANT. UNIDADES</th>
                            <th>PRECIO</th>
                            <th>TOTAL</th>
                            <th>RELACIÓN</th>
                            <th>CALIBRE</th>
                            <th>UMB</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datos as $row) { ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($row['fecha_pedido'])) ?></td>
                                <td><?= htmlspecialchars($row['usuario']) ?></td>
                                <td class="col-pedido"><?= htmlspecialchars($row['numero_pedido']) ?></td>
                                <td class="col-cliente"><?= htmlspecialchars($row['nombre_cliente']) ?></td>
                                <td><?= htmlspecialchars($row['nombre_sede']) ?></td>
                                <td><?= htmlspecialchars($row['ciudad']) ?></td>
                                <td class="col-usuario"><?= htmlspecialchars($row['usuario']) ?></td>
                                <td><?= htmlspecialchars($row['cod_producto']) ?></td>
                                <td><?= htmlspecialchars($row['descripcion']) ?></td>
                                <td><?= htmlspecialchars($row['cantidad']) ?></td>
                                <td><?= htmlspecialchars($row['und_embalaje_minima'] * $row['cantidad']) ?></td>
                                <td><?= htmlspecialchars(number_format($row['precio'], 2)) ?></td>
                                <td class="col-total"><?= htmlspecialchars(number_format($row['total'], 2, '.', '')) ?></td>
                                <td><?= htmlspecialchars($row['relacion']) ?></td>
                                <td><?= htmlspecialchars($row['calibre']) ?></td>
                                <td><?= htmlspecialchars($row['umb']) ?></td>
                                <!-- <td>
                                <button type="button" class="btn btn-primary btn-agregar-documento"
                                    data-pedido="<?= $row['numero_pedido'] ?>"
                                    data-cliente="<?= $row['nombre_cliente'] ?>"
                                    data-producto="<?= $row['descripcion'] ?>"
                                    data-total="<?= $row['total'] ?>">
                                    <i class="fa fa-hand-pointer"></i>
                                </button>
                            </td> -->
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <!-- Controles de paginación -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Mostrar
                        <select id="registrosPorPagina">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="50">50</option>
                        </select>
                        registros
                    </div>
                    <ul id="paginacion" class="pagination mb-0"></ul>
                </div>
            </div>
        </div>

        <!-- GRAFICOS -->
        <div class="tab-pane fade" id="contenidoGraficos" role="tabpanel">
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Ventas por Asesor (Barras)</h5>
                            <div style="max-width: 400px; margin: auto;">
                                <canvas id="graficoVentas"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Ventas por Asesor (Porcentaje)</h5>
                            <div style="max-width: 300px; margin: auto;">
                                <canvas id="graficoPie"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        const filas = $("#tablaPedidos tbody tr");
        let paginaActual = 1;
        let registrosPorPagina = parseInt($("#registrosPorPagina").val());

        // PAGINACION FUNCIONAL
        function mostrarPagina(pagina) {
            paginaActual = pagina;
            let inicio = (pagina - 1) * registrosPorPagina;
            let fin = inicio + registrosPorPagina;

            filas.hide();
            let visibles = filas.filter(":visibleBase");
            visibles.slice(inicio, fin).show();

            generarPaginacion(visibles.length);
        }

        filas.each(function() {
            $(this).attr("data-visible", "1");
        });

        function generarPaginacion(totalRegistros) {
            let totalPaginas = Math.ceil(totalRegistros / registrosPorPagina);
            let paginacion = $("#paginacion");
            paginacion.empty();
            for (let i = 1; i <= totalPaginas; i++) {
                let li = $("<li class='page-item " + (i === paginaActual ? "active" : "") + "'><a href='#' class='page-link'>" + i + "</a></li>");
                li.on("click", function(e) {
                    e.preventDefault();
                    mostrarPagina(i);
                });
                paginacion.append(li);
            }
        }

        $("#buscarPedido, #filtroTipo").on("keyup change", function() {
            const valor = $("#buscarPedido").val().toLowerCase();
            const tipo = $("#filtroTipo").val();

            filas.each(function() {
                let texto = "";
                if (tipo === "cliente") texto = $(this).find(".col-cliente").text().toLowerCase();
                else if (tipo === "usuario") texto = $(this).find(".col-usuario").text().toLowerCase();
                else if (tipo === "pedido") texto = $(this).find(".col-pedido").text().toLowerCase();
                else texto = $(this).text().toLowerCase();

                if (texto.indexOf(valor) > -1) $(this).attr("data-visible", "1");
                else $(this).attr("data-visible", "0");
            });

            mostrarPagina(1);
        });

        $("#registrosPorPagina").on("change", function() {
            registrosPorPagina = parseInt($(this).val());
            mostrarPagina(1);
        });

        $.expr[':'].visibleBase = function(elem) {
            return $(elem).attr("data-visible") === "1";
        };

        // GRAFICOS CON TODOS LOS DATOS
        const ctxBar = document.getElementById('graficoVentas').getContext('2d');
        const ctxPie = document.getElementById('graficoPie').getContext('2d');

        let graficoBar = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Ventas ($)',
                    data: [],
                    backgroundColor: 'rgba(54, 162, 235, 0.6)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        let graficoPie = new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        function actualizarGraficos() {
            let ventas = {};
            filas.each(function() {
                const usuario = $(this).find(".col-usuario").text().trim();
                const total = parseFloat($(this).find(".col-total").text()) || 0;
                if (!ventas[usuario]) ventas[usuario] = 0;
                ventas[usuario] += total;
            });

            graficoBar.data.labels = Object.keys(ventas);
            graficoBar.data.datasets[0].data = Object.values(ventas);
            graficoBar.update();

            graficoPie.data.labels = Object.keys(ventas);
            graficoPie.data.datasets[0].data = Object.values(ventas);
            graficoPie.update();
        }

        // Inicial
        mostrarPagina(1);

        // Actualizar gráficos solo al mostrar la pestaña "Graficos"
        var tabs = document.querySelectorAll('#tabsVentas button');
        tabs.forEach(tab => {
            tab.addEventListener('shown.bs.tab', function(e) {
                if (e.target.id === 'tabGraficos') actualizarGraficos();
            });
        });
    });
</script>