<?php
include("conexionfin.php");

// --- Filtros opcionales ---
$filtro_producto = $_GET['producto'] ?? '';
$filtro_fecha_desde = $_GET['desde'] ?? '';
$filtro_fecha_hasta = $_GET['hasta'] ?? '';

$where = [];
if ($filtro_producto != '') {
    $filtro_producto_esc = $conexion->real_escape_string($filtro_producto);
    $where[] = "p.descripcion LIKE '%$filtro_producto_esc%'";
}
if ($filtro_fecha_desde != '' && $filtro_fecha_hasta != '') {
    $filtro_fecha_desde_esc = $conexion->real_escape_string($filtro_fecha_desde);
    $filtro_fecha_hasta_esc = $conexion->real_escape_string($filtro_fecha_hasta);
    $where[] = "m.fecha_movimiento BETWEEN '$filtro_fecha_desde_esc' AND '$filtro_fecha_hasta_esc'";
}

$condicion = $where ? "WHERE " . implode(" AND ", $where) : "";

// --- Consulta principal ---
$sql = "
SELECT 
    m.id_movimiento,
    p.str_id,
    p.cod_producto,
    p.descripcion AS producto,
    m.lote,
    m.tipo_movimiento,
    I.stock_unidades_kg,
    I.stock_caja_paca_bobinas,
    m.umb,
    m.cliente_proveedor,
    m.almacen_id,
    m.num_documento,
    m.costo_unitario,
    m.costo_total,
    m.fecha_movimiento,
    u.nombre AS usuario
FROM movimientos_inventario m
INNER JOIN producto p ON m.id_producto = p.id_producto
INNER JOIN inventario I ON I.producto_id = p.id_producto
LEFT JOIN usuario u ON m.usuario_id = u.idusuario
$condicion
ORDER BY m.fecha_movimiento DESC
";

// --- Ejecutar consulta y verificar errores ---
$result = $conexion->query($sql);
if (!$result) {
    die("Error en la consulta SQL: " . $conexion->error);
}
?>

<style>
    h2 { text-align: center; color: #333; margin-bottom: 10px; }
    form.filtros { text-align: center; margin-bottom: 20px; }
    input, select { padding: 6px; margin: 4px; border: 1px solid #ccc; border-radius: 5px; }
    button { padding: 7px 14px; border: none; border-radius: 5px; background-color: #007bff; color: white; cursor: pointer; }
    button:hover { background-color: #0056b3; }
    table { width: 100%; border-collapse: collapse; background: white; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
    th { background-color: #333; color: white; }
    tr:nth-child(even) { background-color: #f2f2f2; }
    .entrada { color: green; font-weight: bold; }
    .salida { color: red; font-weight: bold; }
    .ajuste { color: orange; font-weight: bold; }
</style>
<div class="container-fluid">
<h2>📦 Historial de Movimientos de Inventario</h2>

<table id="tablamovimientos">
    <thead>
        <tr>
            <th>ID</th>
            <th>STR ID</th>
            <th>Código</th>
            <th>Descripción</th>
            <th>Lote</th>
            <th>Tipo Movimiento</th>
            <th>Stock Unidades</th>
            <th>Stock Caja/Paca/KG</th>
            <th>Costo Unitario</th>
            <th>Costo Total</th>
            <th>Fecha</th>
            <th>Usuario</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $tipo = strtolower($row['tipo_movimiento']);
                $clase = $tipo == 'entrada' ? 'entrada' : ($tipo == 'salida' ? 'salida' : 'ajuste');

                echo "<tr>
                    <td>{$row['id_movimiento']}</td>
                    <td>{$row['str_id']}</td>
                    <td>{$row['cod_producto']}</td>
                    <td>{$row['producto']}</td>
                    <td>{$row['lote']}</td>
                    <td class='{$clase}'>{$row['tipo_movimiento']}</td>
                    <td>{$row['stock_unidades_kg']}</td>
                    <td>{$row['stock_caja_paca_bobinas']}</td>
                    <td>{$row['costo_unitario']}</td>
                    <td>{$row['costo_total']}</td>
                    <td>{$row['fecha_movimiento']}</td>
                    <td>{$row['usuario']}</td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='14'>No hay movimientos registrados</td></tr>";
        }
        ?>
    </tbody>
</table>
</div>
<script>

  $('#tablamovimientos').DataTable({
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
    }
  });

</script>
