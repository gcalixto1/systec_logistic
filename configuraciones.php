<?php include('conexionfin.php'); ?>
<style>
.myButton {
   font-size: 20px;
   font-weight: bold;
   background: #3d3d3d;
   color: #ffffff;
   border: 2px solid #424242;
   border-radius: 10px;
   padding: 10px 15px;
   letter-spacing: 1px;
   margin-top: 20px;
}

.myButton:hover {
   background: #4f4f4f;
   color: #ffffff;
}
</style>


<div class="container-fluid">
<div class="card-header">
        <h4 class="card-title text-black"><i class="fa fa-address-card"></i> Configuraciones Generales</h4>
    </div>
    <br/>
    <button class="myButton" id="new_categoria"><img width="50" height="50" src="https://img.icons8.com/ios/50/ffffff/open-box.png" alt="open-box"/> Agregar Categorias de productos</button>
    <button class="myButton" id="new_rol"><img width="50" height="50" src="https://img.icons8.com/ios/50/ffffff/user.png" alt="open-box"/> Agregar Roles de usuario</button>
    <button class="myButton" id="new_Respaldo"><img width="50" height="50" src="https://img.icons8.com/ios/50/ffffff/database.png" alt="open-box"/> Crear Respaldo de Base de datos</button>
</div>
<script>
   $('#new_categoria').click(function () {
        uni_modal("Gestion de Categoria para Productos", "manage_categorias.php")
    })
</script>