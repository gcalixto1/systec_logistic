<?php
require('conexionfin.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado - Etiqueta de Producción</title>
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
        .busqueda {
            text-align: center;
            margin-bottom: 15px;
        }
        .busqueda input[type="text"] {
            padding: 8px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .busqueda button {
            background-color: #007bff;
            color: white;
            padding: 8px 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .busqueda button:hover {
            background-color: #0056b3;
        }
    </style>

    <!-- DataTables 2.x CSS y JS (sin jQuery) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/v/bs5/dt-2.1.1/datatables.min.css">
    <script src="https://cdn.datatables.net/v/bs5/dt-2.1.1/datatables.min.js"></script>
</head>
<body>

<h2>Listado - Etiqueta de Producción</h2>

<div class="tabla-contenedor">
    <table id="tablaEtiquetas">
        <thead>
            <tr>
                <th>ID</th>
                <th>Código Etiqueta (Campo 1)</th>
                <th>Cliente (Campo 2)</th>
                <th>Referencia (Campo 3)</th>
                <th>Ciudad (Campo 4)</th>
                <th>Pedido</th>
                <th>Usuario</th>
                <th>Fecha Creación</th>
                <th>PDF</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // Consulta principal
        $query = "SELECT 
            pr.id_producto,
            CONCAT(
                LPAD(dp.id, 2, '0'), ' - ', 
                'L', pr.calibre,
                pr.str_id,
                pr.ref_1,
                pr.ref_2,
                pr.relacion,
                DATE_FORMAT(pr.fecha_creacion, '%d%m%y')
            ) AS Campo_1,
            UPPER(c.nombre_cliente) AS Campo_2,
            CONCAT(
                pr.ref_1, 'x', pr.ref_2, ' - ',
                pr.str_id, '-', pr.relacion
            ) AS Campo_3,
            UPPER(c.ciudad) AS Campo_4,
            p.numero_pedido,
            u.nombre AS usuario,
            pr.fecha_creacion
        FROM detalle_pedidos dp
        INNER JOIN pedidos p ON p.id = dp.pedido_id
        INNER JOIN producto pr ON pr.id_producto = dp.producto_id
        INNER JOIN clientes c ON c.id = p.cliente_id
        INNER JOIN usuario u ON u.idusuario = p.usuario_id
        ORDER BY p.fecha_pedido DESC";

        $result = $conexion->query($query);
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>'.htmlspecialchars($row['id_producto']).'</td>';
            echo '<td>'.htmlspecialchars($row['Campo_1']).'</td>';
            echo '<td>'.htmlspecialchars($row['Campo_2']).'</td>';
            echo '<td>'.htmlspecialchars($row['Campo_3']).'</td>';
            echo '<td>'.htmlspecialchars($row['Campo_4']).'</td>';
            echo '<td>'.htmlspecialchars($row['numero_pedido']).'</td>';
            echo '<td>'.htmlspecialchars($row['usuario']).'</td>';
            echo '<td>'.htmlspecialchars($row['fecha_creacion']).'</td>';
            echo '<td>
                <form method="POST" action="generar_pdf_etiqueta.php" target="_blank">
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
    new DataTable('#tablaEtiquetas', {
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
