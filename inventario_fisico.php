<?php
include("conexionfin.php");

$actualizado = false;

// --- Actualización del inventario ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_inventario'])) {
    foreach ($_POST['id_inventario'] as $index => $id_inventario) {
        $nueva_unidades = trim($_POST['nueva_unidades'][$index]);
        $nueva_cajas = trim($_POST['nueva_cajas'][$index]);

        if ($nueva_unidades !== '' || $nueva_cajas !== '') {
            $sql_update = "
                UPDATE inventario 
                SET 
                    stock_unidades_kg = IF('$nueva_unidades'='', stock_unidades_kg, '$nueva_unidades'),
                    stock_caja_paca_bobinas = IF('$nueva_cajas'='', stock_caja_paca_bobinas, '$nueva_cajas'),
                    fecha_actualizacion = NOW()
                WHERE id_inventario = $id_inventario
            ";
            $conexion->query($sql_update);
            $actualizado = true;
        }
    }
}

// --- Consulta de inventario con productos ---
$sql = "
SELECT 
    i.id_inventario,
    p.str_id,
    p.cod_producto,
    p.descripcion,
    i.lote,
    i.stock_unidades_kg,
    i.stock_caja_paca_bobinas,
    p.und_embalaje_minima
FROM inventario i
INNER JOIN producto p ON i.producto_id = p.id_producto
WHERE i.stock_unidades_kg > 0 OR i.stock_caja_paca_bobinas > 0
ORDER BY p.cod_producto, i.lote
";
$result = $conexion->query($sql);
?>

<style>
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
    th { background-color: #333; color: #fff; }
    input[type="number"] { width: 80px; text-align: right; }
    .btn { padding: 8px 16px; margin-top: 10px; cursor: pointer; border: none; border-radius: 5px; }
    .btn-actualizar { background-color: #28a745; color: #fff; }
    .btn-imprimir { background-color: #007bff; color: #fff; margin-left: 10px; }
    h2 { text-align: center; color: #333; }
</style>

<script>
function syncCantidad(index) {
    const undEmbalaje = parseFloat(document.getElementById('und_embalaje_' + index).value) || 1;
    const unidadesInput = document.getElementById('nueva_unidades_' + index);
    const cajasInput = document.getElementById('nueva_cajas_' + index);

    unidadesInput.addEventListener('input', () => {
        const unidades = parseFloat(unidadesInput.value) || 0;
        cajasInput.value = unidades > 0 ? (unidades / undEmbalaje).toFixed(2) : '';
    });

    cajasInput.addEventListener('input', () => {
        const cajas = parseFloat(cajasInput.value) || 0;
        unidadesInput.value = cajas > 0 ? (cajas * undEmbalaje).toFixed(2) : '';
    });
}

function imprimirInventario() {
    window.open('imprimir_inventario.php', '_blank');
}
</script>
</head>

<body>
<h2>📦 Inventario Físico por Lote</h2>

<form method="POST" action="">
    <table>
        <thead>
            <tr>
                <th>STR ID</th>
                <th>Código</th>
                <th>Descripción</th>
                <th>Lote</th>
                <th>Stock (Unidades/Kg)</th>
                <th>Stock (Cajas/Pacas)</th>
                <th>Cant. Física (Unidades)</th>
                <th>Cant. Física (Cajas)</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($result->num_rows > 0) {
            $index = 0;
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['str_id']}</td>
                    <td>{$row['cod_producto']}</td>
                    <td>{$row['descripcion']}</td>
                    <td>{$row['lote']}</td>
                    <td>{$row['stock_unidades_kg']}</td>
                    <td>{$row['stock_caja_paca_bobinas']}</td>
                    <td><input type='number' step='0.01' name='nueva_unidades[]' id='nueva_unidades_{$index}'></td>
                    <td><input type='number' step='0.01' name='nueva_cajas[]' id='nueva_cajas_{$index}'></td>
                    <input type='hidden' name='id_inventario[]' value='{$row['id_inventario']}'>
                    <input type='hidden' id='und_embalaje_{$index}' value='{$row['und_embalaje_minima']}'>
                    <script>syncCantidad({$index});</script>
                </tr>";
                $index++;
            }
        } else {
            echo "<tr><td colspan='8'>No hay inventario registrado</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <button type="submit" class="btn btn-actualizar">💾 Actualizar Inventario</button>
    <button type="button" class="btn btn-imprimir" onclick="imprimirInventario()">🖨️ Imprimir Inventario</button>
</form>

<?php if ($actualizado): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Inventario actualizado',
    text: 'Los cambios se guardaron correctamente.',
    confirmButtonColor: '#28a745'
});
</script>
<?php endif; ?>