<?php
include_once "includes/header.php";
?>
<link href="css/dashboar.css" rel="stylesheet">
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Panel de Administración</h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-7569 py-2">
                <div class="card-block">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xl font-weight-bold text-gray text-uppercase mb-1">
                                Ventas de Hoy</div>
                            <div style="font-size:34px;" class="h5 mb-0 text-white-800">
                                <?php echo '$' . number_format($data['total_dia'], 2, '.', ','); ?>
                            </div><br>
                            <div style="font-size:16px;" class="h5 mb-0 text-default-800">Cantidad de ventas :
                                <?php echo $data['ventas']; ?>
                            </div>
                            <p>
                            <div style="font-size:16px;" class="h5 mb-0 text-default-800">Total en el mes : <b>
                                    <?php echo '$' . number_format($data['total_mes'], 2, '.', ','); ?>
                                </b></div>
                        </div>
                        <div class="col-auto">
                            <img width="75" height="75" src="img/icons8-carrito-de-compras-100.png"
                                alt="shopping-cart" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-7569 py-2">
                <div class="card-block">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xl font-weight-bold text-gray text-uppercase mb-1">
                                Productos Registrados</div>
                            <div style="font-size:34px;" class="h5 mb-0 text-white-800">
                                <?php echo $data['productos']; ?>
                            </div><br>
                            <div style="font-size:16px;" class="h5 mb-0 text-default-800">productos con stock bajo:
                                <b>
                                    <?php echo $data['producto_minimo']; ?>
                                </b>
                            </div>
                            <p>
                            <div style="font-size:16px;color:white;" class="h5 mb-0 text-white-800">.
                                </b></div>
                        </div>
                        <div class="col-auto">
                            <img width="75" height="75" src="img/icons8-caja-100.png" alt="shopping-cart" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-756 py-2">
                <div class="card-block">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xl font-weight-bold text-gray text-uppercase mb-1">
                                Clientes</div>
                            <div style="font-size:34px;" class="h5 mb-0 text-white-800">
                                <?php echo $data['clientes']; ?>
                            </div><br>
                            <div style="font-size:16px;" class="h5 mb-0 text-default-800">proveedores :
                                <?php echo $data['proveedores']; ?>
                            </div>
                            <p>
                            <div style="font-size:16px;" class="h5 mb-0 text-default-800">Usuarios: <b>
                                    <?php echo $data['usuarios']; ?>
                                </b></div>
                        </div>
                        <div class="col-auto">
                            <img width="75" height="75" src="img/icons8-usuario-100.png" alt="stable" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow mb-3">
                <div class="card-header py-2">
                    <h6 class="m-0 font-weight-bold text-primary">Ventas por mes</h6>
                </div>
                <div class="card-body">
                    <canvas id="ventasPorMesChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow mb-3">
                <div class="card-header py-2">
                    <h6 class="m-0 font-weight-bold text-primary">Ventas del año <?php echo date("Y"); ?></h6>
                </div>
                <div class="card-body">
                    <canvas id="bar-chart4" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow mb-3">
                <div class="card-header py-2">
                    <h6 class="m-0 font-weight-bold text-primary">Los 5 productos más vendidos</h6>
                </div>
                <div class="card-body">
                    <canvas id="productosMasVendidosChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<script>
var totalVentasMesData = [<?php echo $data['total_mes']; ?>];
var productNames = <?php echo json_encode($product_names); ?>;
var productQuantities = <?php echo json_encode($product_quantities); ?>;
var ventasSemanales = <?php echo json_encode($ventas_semanales); ?>;

// Generar etiquetas y datos dinámicos para las semanas
var etiquetasSemanas = ventasSemanales.map(item => 'Semana ' + item.semana);
var datosVentas = ventasSemanales.map(item => item.ventas);

// Inicialización del gráfico de ventas por semana
var ctx1 = document.getElementById('ventasPorMesChart').getContext('2d');

var totalVentasMesChart = new Chart(ctx1, {
    type: 'line', // Puedes cambiar a 'bar' para un gráfico de barras
    data: {
        labels: etiquetasSemanas, // Etiquetas dinámicas: Semana 1, Semana 2, etc.
        datasets: [{
            label: 'Ventas por Semana',
            data: datosVentas, // Datos dinámicos de ventas
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1,
            fill: true // Opcional: llena el área debajo de la línea
        }]
    },
    options: {
        responsive: true,
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Semanas del Mes'
                }
            },
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Cantidad de Ventas ($)'
                }
            }
        }
    }
});


// Ordenar los nombres de los productos alfabéticamente
productNames.sort();

// Obtener el contexto del gráfico
var ctx2 = document.getElementById('productosMasVendidosChart').getContext('2d');

// Crear el gráfico de productos más vendidos
var productosMasVendidosChart = new Chart(ctx2, {
    type: 'line',
    data: {
        // Etiquetas de los productos ordenadas
        labels: productNames,
        datasets: [{
            // Nombre del conjunto de datos
            label: 'Productos mas vendidos',
            // Cantidades de productos vendidos
            data: productQuantities,
            // Colores para las porciones del gráfico
            backgroundColor: [
                '#FF5399',
                '#FFC300',
                '#DAF7A6',
                '#C70039',
                '#85144b',
                '#001f3f',
                '#FF4136',
                '#3D9970',
                '#FFC875',
                '#4D7512'
            ]
        }]
    },
    options: {
        // Hacer el gráfico responsive
        responsive: true
    }
});


new Chart(document.getElementById("bar-chart4"), {
    type: 'bar',
    data: {
        labels: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
        datasets: [{
            label: "Monto Mensual",
            backgroundColor: ["#ff7676", "#3e95cd", "#808080", "#F38630", "#7B82EC", "#8EE1BC",
                "#D3E37D", "#E8AC9E", "#2E64FE", "#E399DA", "#F7BE81", "#FA5858"
            ],
            data: [<?php

                if ($venmes[0]['totalmes'] == 0) {
                    echo 0;
                } else {

                    $meses = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0);
                    foreach ($venmes as $row) {
                        $mes = $row['mes'];
                        $meses[$mes] = $row['totalmes'];
                    }
                    foreach ($meses as $mes) {
                        echo "{$mes},";
                    }
                } ?>]
        }]
    },
    options: {
        legend: {
            display: false
        },
        title: {
            display: true,
            text: 'Suma de Monto Mensual'
        }
    }
});
</script>