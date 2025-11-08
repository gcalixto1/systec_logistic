<?php
require('conexionfin.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Productos</title>
    <style>
        h2 {
            text-align: center;
            color: #003366;
            margin-bottom: 15px;
        }
        .tabla-contenedor {
            overflow-x: auto;
            background: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            white-space: nowrap;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #003366;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        button.pdf-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 6px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        button.pdf-btn:hover {
            background-color: #1e7e34;
        }
    </style>

    <!-- DataTables 2.x puro (sin jQuery) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/v/bs5/dt-2.1.1/datatables.min.css">
    <script src="https://cdn.datatables.net/v/bs5/dt-2.1.1/datatables.min.js"></script>
</head>
<body>

<h2>Seleccione el producto para generar PDF</h2>

<div class="tabla-contenedor">
    <table id="tablaProductos">
        <thead>
            <tr>
                <th>ID Producto</th>
                <th>Código</th>
                <th>Descripción</th>
                <th>Cliente</th>
                <th>Pedido</th>
                <th>Fecha</th>
                <th>Ciudad</th>
                <th>Generar PDF</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT 
                pr.id_producto,
                pr.cod_producto,
                pr.descripcion,
                c.nombre_cliente,
                p.numero_pedido,
                p.fecha_pedido,
                c.ciudad
            FROM detalle_pedidos dp
            INNER JOIN pedidos p ON p.id = dp.pedido_id
            INNER JOIN producto pr ON pr.id_producto = dp.producto_id
            INNER JOIN clientes c ON c.id = p.cliente_id
            ORDER BY p.fecha_pedido DESC";
            
            $result = $conexion->query($query);
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>'.htmlspecialchars($row['id_producto']).'</td>';
                echo '<td>'.htmlspecialchars($row['cod_producto']).'</td>';
                echo '<td>'.htmlspecialchars($row['descripcion']).'</td>';
                echo '<td>'.htmlspecialchars($row['nombre_cliente']).'</td>';
                echo '<td>'.htmlspecialchars($row['numero_pedido']).'</td>';
                echo '<td>'.htmlspecialchars($row['fecha_pedido']).'</td>';
                echo '<td>'.htmlspecialchars($row['ciudad']).'</td>';
                echo '<td>
                        <form method="POST" action="generar_pdf.php" target="_blank">
                            <input type="hidden" name="producto_id" value="'. $row['id_producto'] .'">
                            <button class="pdf-btn" type="submit">📄 PDF</button>
                        </form>
                      </td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<script>
    // Inicializar DataTable sin jQuery
    new DataTable('#tablaProductos', {
        responsive: true,
        language: {
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Mostrando 0 a 0 de 0 registros",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior"
            },
        },
        pageLength: 10
    });
</script>

</body>
</html>
