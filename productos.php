<?php include('conexionfin.php'); ?>
<style>
.boton_add {
    margin-top: -4%;
    margin-left: 75%;
    width: 25%;
}

.boton_add2 {
    margin-top: -4%;
    margin-left: 75%;
    width: 25%;
}
</style>
<div class="container-fluid">
    <div class="card-header">
        <h4 class="card-title text-black"><i class="fa fa-box"></i> Gestion de Productos</h4>
    </div>
    <br>
    <div class="col-lg-12">

        <div class="col-sm-12 col-xs-12 text-right">
            <button class="btn btn-success btn-lg" type="button" id="new_producto"><i class="fa fa-plus"></i> Nuevo
                Producto</button>
        </div>
        <br />
        <table class="table table-bordered table-responsive" id="borrower-list">
            <colgroup>
                <col width="5%">
                <col width="30%">
                <col width="30%">
                <col width="15%">
                <col width="35%">
                <col width="30%">
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Nombre Productos/Servicios</th>
                    <th class="text-center">Categoria</th>
                    <th class="text-center">Precio Venta</th>
                    <th class="text-center">Stock</th>
                    <th class="text-center">Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                $qry = $conexion->query("SELECT * FROM producto INNER JOIN categoria ON categoria.categoria_id = producto.categoria");
                while ($row = $qry->fetch_assoc()):
                    ?>
                <tr>
                    <td style="font-size: 12px;" class="">
                        <?php echo $i++ ?>
                    </td>
                    <td style="font-size: 12px;" class="">
                        <?php echo $row['descripcion'] ?>
                    </td>
                    <td style="font-size: 12px;" class="">
                        <?php echo $row['categoria_des'] ?>
                    </td>
                    <td style="font-size: 12px;" class="">
                        <?php echo $row['precio'] ?>
                    </td>
                    <td style="font-size: 12px;" class="">
                        <?php echo $row['existencia'] ?>
                    </td>

                    <td style="white-space: nowrap;">
                        <button class="btn btn-primary btn-sm add_borrower" type="button"
                            data-id="<?php echo $row['codproducto'] ?>">
                            <i class="fas fa-audio-description"></i>
                        </button>
                        <button class="btn btn-success btn-sm edit_borrower" type="button"
                            data-id="<?php echo $row['codproducto'] ?>">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm delete_borrower" type="button"
                            data-id="<?php echo $row['codproducto'] ?>">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>

                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>
</div>

<script>
$('#borrower-list').dataTable()
$('#new_producto').click(function() {
    uni_modal("Gestion de Productos", "manage_productos.php")
})
$('#new_categoria').click(function() {
    uni_modal("Gestion de Categoria para Productos", "manage_categorias.php")
})
$('#borrower-list').on('click', '.edit_borrower', function() {
    uni_modal("Modificar Productos", "manage_productos.php?codproducto=" + $(this).attr('data-id'))
})
$('#borrower-list').on('click', '.add_borrower', function() {
    uni_modal("Agregar Stock al Productos", "agregar_producto.php?codproducto=" + $(this).attr('data-id'))
})
$('#borrower-list').on('click', '.delete_borrower', function() {
    _conf("Esta seguro que quiere eliminar este producto?", "delete_borrower", [$(this).attr('data-id')])
})

function delete_borrower($codproducto) {
    start_load()
    $.ajax({
        url: 'ajax.php?action=delete_producto',
        method: 'POST',
        data: {
            codproducto: $codproducto
        },
        success: function(resp) {
            if (resp == 1) {
                Swal.fire({
                    title: '<img width="65" height="65" src="https://img.icons8.com/external-bearicons-gradient-bearicons/64/external-trash-can-graphic-design-bearicons-gradient-bearicons.png" alt="external-trash-can-graphic-design-bearicons-gradient-bearicons"/>',
                    text: "El registro fue eliminado",
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                })
            }
        }
    })
}
</script>