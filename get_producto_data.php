<?php
include('conexionfin.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cod_producto = $_POST['cod_producto'];
    $tipo = 'Bobina';
    
    // Consulta para obtener datos del producto e inventario (incluye calibre explícitamente)
    $sql = "SELECT pr.cod_producto, pr.peso_kg, pr.ref_tubo, pr.calibre, i.lote, i.stock_unidades_kg FROM producto pr LEFT JOIN inventario i ON i.producto_id = pr.id_producto WHERE pr.tipo = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('s', $tipo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Producto no encontrado']);
    }
    
    $stmt->close();
    $conexion->close();
}
?>