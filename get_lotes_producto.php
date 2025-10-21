<?php
include("conexionfin.php");

if(isset($_POST['id_producto']) && isset($_POST['almacen_id'])){
  $id_producto = $_POST['id_producto'];
  $almacen_id = $_POST['almacen_id'];

  $sql = "SELECT lote,
                 SUM(CASE WHEN tipo_movimiento LIKE '%ENTRADA%' THEN cantidad ELSE -cantidad END) AS cantidad,unidades
          FROM movimientos_inventario
          WHERE id_producto = ? AND almacen_id = ?
          GROUP BY lote
          HAVING SUM(CASE WHEN tipo_movimiento LIKE '%ENTRADA%' THEN cantidad ELSE -cantidad END) > 0
          ORDER BY lote ASC";
  $stmt = $conexion->prepare($sql);
  $stmt->bind_param("ii", $id_producto, $almacen_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if($result->num_rows > 0){
    echo "<option value=''>Seleccione Lote</option>";
    while($row = $result->fetch_assoc()){
      echo "<option value='{$row['lote']}'>{$row['lote']} ({$row['unidades']} u) / ({$row['cantidad']} cjs)</option>";
    }
  } else {
    echo "<option value=''>Sin lotes disponibles</option>";
  }
}
?>
