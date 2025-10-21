<?php
include("conexionfin.php");

if(isset($_POST['query'])){
  $q = $_POST['query'];
  $stmt = $conexion->prepare("SELECT id_producto, str_id, descripcion, und_embalaje_minima, umb FROM producto WHERE descripcion LIKE ? OR id_producto LIKE ? LIMIT 10");
  $like = "%".$q."%";
  $stmt->bind_param("ss", $like, $like);
  $stmt->execute();
  $result = $stmt->get_result();

  if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
      echo "<a href='#' class='list-group-item list-group-item-action item-producto'
            data-id='{$row['id_producto']}'
            data-interno='{$row['str_id']}'
            data-descripcion='{$row['descripcion']}'
            data-umb='{$row['umb']}'
            data-embalaje='{$row['und_embalaje_minima']}'>
            {$row['str_id']} - {$row['descripcion']}
            </a>";
    }
  } else {
    echo "<p class='list-group-item text-muted'>No se encontraron resultados</p>";
  }
}
?>
