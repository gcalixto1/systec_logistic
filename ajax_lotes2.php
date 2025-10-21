<?php
include("conexionfin.php");

$id_interno = $_POST['id_interno'] ?? '';
$almacen = $_POST['almacen'] ?? '';

if (empty($id_interno) || empty($almacen)) {
  echo "<option value=''>Seleccione el lote</option>";
  exit;
}

$sql = "
SELECT i.lote, i.stock_caja_paca_bobinas AS stock
FROM inventario i
INNER JOIN producto p ON p.id_producto = i.producto_id
WHERE p.str_id = '$id_interno' AND i.almacen_id = '$almacen'
";

$res = $conexion->query($sql);

if ($res && $res->num_rows > 0) {
  echo "<option value=''>Seleccione el lote</option>";
  while ($row = $res->fetch_assoc()) {
    echo "<option value='{$row['lote']}'>{$row['lote']} (Stock: {$row['stock']})</option>";
  }
} else {
  echo "<option value=''>Sin lotes disponibles</option>";
}
?>
