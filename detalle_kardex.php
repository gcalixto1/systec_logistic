<?php
include('conexionfin.php');

if (isset($_GET['codBarra'])) {
    $codproducto = $_GET['codBarra'];

    $qry = $conexion->query("SELECT kp.fecha_trans,kp.descripcion,kp.movimiento,kp.entradas,kp.salidas,kp.devolucion,kp.stock_actual 
                                    FROM kardex_producto kp 
                                    LEFT JOIN producto p ON p.codproducto = kp.producto or p.codBarra = kp.producto 
                                    WHERE p.codBarra ='$codproducto'");

    echo <<<EOT
<style>
    .border-entrada { border-left: 5px solid #065000 !important; }
    .border-salida { border-left: 5px solid #D5A604 !important; }
    .border-devolucion { border-left: 5px solid #7C51B4 !important; }
    .legend {
        display: flex;
        justify-content: center;
        margin-bottom: 10px;
        gap: 10px;
    }
    .legend-item {
        padding: 5px 15px;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        font-weight: bold;
        color: white;
    }
    .legend-entrada { background-color: #065000; }
    .legend-salida { background-color: #D5A604; color: black; }
    .legend-devolucion { background-color: #7C51B4; }
    .legend-all { background-color: #444; }
</style>

<div class='legend'>
    <button onclick='aplicarFiltros("entrada")' class='legend-item legend-entrada'><i class='fa fa-plus-square'></i> Entrada</button>
    <button onclick='aplicarFiltros("salida")' class='legend-item legend-salida'><i class='fa fa-minus-square'></i> Salida</button>
    <button onclick='aplicarFiltros("devolucion")' class='legend-item legend-devolucion'><i class='fa fa-retweet'></i> Devolución</button>
    <button onclick='aplicarFiltros("todos")' class='legend-item legend-all'>Mostrar Todos</button>
</div>

<div class='row mb-3'>
    <div class='col-md-3'>
        <label>Desde:</label>
        <input type='date' id='desde' class='form-control'>
    </div>
    <div class='col-md-3'>
        <label>Hasta:</label>
        <input type='date' id='hasta' class='form-control'>
    </div>
</div>

<table class='table table-bordered table-striped' id='tabla-kardex'>
    <thead>
        <tr>
            <th>#</th>
            <th>Fecha Movimiento</th>
            <th>Descripción</th>
            <th>Movimiento</th>
            <th>Stock Actual</th>
        </tr>
    </thead>
    <tbody>
EOT;

    $contador = 1;
    while ($row = $qry->fetch_assoc()) {
        $tipo = '';
        $clase_color = '';

        if ($row['entradas'] != 0) {
            $tipo = 'entrada';
            $clase_color = 'border-entrada';
        } elseif ($row['salidas'] != 0) {
            $tipo = 'salida';
            $clase_color = 'border-salida';
        } elseif ($row['devolucion'] != 0) {
            $tipo = 'devolucion';
            $clase_color = 'border-devolucion';
        }

        $fecha = date('Y-m-d', strtotime($row['fecha_trans'])); // para filtros

        echo "<tr class='$clase_color' data-tipo='$tipo' data-fecha='$fecha'>";
        echo "<td>{$contador}</td>";
        echo "<td>" . date('d/m/Y', strtotime($row['fecha_trans'])) . "</td>";
        echo "<td>{$row['descripcion']}</td>";
        echo "<td>";
        echo ($row['entradas'] != 0 ? "Entrada: {$row['entradas']} <i class='fa fa-plus-square'></i><br>" : '');
        echo ($row['salidas'] != 0 ? "Salida: {$row['salidas']} <i class='fa fa-minus-square'></i><br>" : '');
        echo ($row['devolucion'] != 0 ? "Devolución: {$row['devolucion']} <i class='fa fa-retweet'></i>" : '');
        echo "</td>";
        echo "<td>{$row['stock_actual']}</td>";
        echo "</tr>";
        $contador++;
    }

    echo "</tbody></table>";
    ?>
<!-- Scripts necesarios -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
let tabla;

$(document).ready(function() {
    tabla = $('#tabla-kardex').DataTable({
        pageLength: 10
    });

    $('#desde, #hasta').on('change', function() {
        aplicarFiltros(tipoSeleccionado);
    });
});

let tipoSeleccionado = 'todos';

function aplicarFiltros(tipo) {
    tipoSeleccionado = tipo;

    const desde = $('#desde').val();
    const hasta = $('#hasta').val();

    tabla.rows().every(function() {
        const fila = $(this.node());
        const tipoFila = fila.data('tipo');
        const fechaFila = fila.data('fecha');

        const mostrarPorTipo = (tipo === 'todos' || tipo === tipoFila);
        const mostrarPorFecha =
            (!desde || fechaFila >= desde) &&
            (!hasta || fechaFila <= hasta);

        if (mostrarPorTipo && mostrarPorFecha) {
            fila.show();
        } else {
            fila.hide();
        }
    });

    tabla.draw();
}
</script>
<?php
} else {
    echo "<p>No se encontraron resultados para el producto con código: $codproducto</p>";
}
?>