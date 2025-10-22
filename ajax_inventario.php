<?php
include("conexionfin.php");
header('Content-Type: application/json; charset=utf-8');

// --- Filtro por almacén (opcional) ---
$almacen = isset($_GET['almacen']) && $_GET['almacen'] !== '' ? intval($_GET['almacen']) : null;
$condAlmacen = $almacen ? "WHERE i.almacen_id = $almacen" : "";

// --- Consulta principal ---
$sql = "
SELECT 
    p.id_producto,
    p.str_id,
    p.cod_producto,
    p.descripcion,
    p.familia,
    p.micraje,
    p.relacion,
    p.calibre,
    p.und_embalaje_minima,
    i.lote,
    i.almacen_id,
    a.nombre AS nombre_almacen,
    -- Stock actual en unidades/kg
    COALESCE(SUM(i.stock_unidades_kg), 0) AS stock_unidades_kg,
    -- Stock actual en cajas/pacas
    COALESCE(SUM(i.stock_caja_paca_bobinas), 0) AS stock_caja_paca_bobinas,
    -- Promedio del costo unitario
    COALESCE(AVG(i.costo_unitario), 0) AS costo_unitario,
    -- Valor total valorizado
    COALESCE(SUM(i.stock_unidades_kg * i.costo_unitario), 0) AS costo_total
FROM inventario i
INNER JOIN producto p ON i.producto_id = p.id_producto
LEFT JOIN almacenes a ON a.id = i.almacen_id
$condAlmacen
GROUP BY p.id_producto, i.lote, i.almacen_id
ORDER BY p.descripcion, i.lote
";

// --- Ejecutar y manejar errores ---
$res = mysqli_query($conexion, $sql);
if (!$res) {
    echo json_encode([
        'error' => true,
        'message' => 'Error en la consulta SQL: ' . mysqli_error($conexion)
    ]);
    exit;
}

// --- Convertir resultados a array ---
$data = [];
while ($row = mysqli_fetch_assoc($res)) {
    $data[] = [
        'id_producto' => $row['id_producto'],
        'str_id' => $row['str_id'],
        'cod_producto' => $row['cod_producto'],
        'descripcion' => $row['descripcion'],
        'familia' => $row['familia'],
        'micraje' => $row['micraje'],
        'relacion' => $row['relacion'],
        'calibre' => $row['calibre'],
        'und_embalaje_minima' => $row['und_embalaje_minima'],
        'lote' => $row['lote'],
        'almacen_id' => $row['almacen_id'],
        'nombre_almacen' => $row['nombre_almacen'],
        'stock_unidades_kg' => (float)$row['stock_unidades_kg'],
        'stock_caja_paca_bobinas' => (float)$row['stock_caja_paca_bobinas'],
        'costo_unitario' => (float)$row['costo_unitario'],
        'costo_total' => (float)$row['costo_total']
    ];
}

// --- Devolver respuesta JSON ---
echo json_encode(['data' => $data], JSON_UNESCAPED_UNICODE);
?>
