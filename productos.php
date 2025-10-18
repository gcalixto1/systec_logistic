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
         <table class="table table-responsive" id="borrower-list">
    <thead class="table-dark">
        <tr>
            <th class="text-center">STR ID</th>
            <th class="text-center">CÓDIGO INTERNO</th>
            <th class="text-center">DESCRIPCIÓN</th>
            <th class="text-center">FAMILIA</th>
            <th class="text-center">MICRAJE</th>
            <th class="text-center">REF 1</th>
            <th class="text-center">REF 2</th>
            <th class="text-center">RELACIÓN</th>
            <th class="text-center">CALIBRE</th>
            <th class="text-center">UNIDAD DE EMBALAJE</th>
            <th class="text-center">PESO POR UNIDAD KG</th>
            <th class="text-center">PESO POR PACA/CAJA</th>
             <th class="text-center">PRECIO FACTURADO L5</th>
            <th class="text-center">PRECIO REMISIONADO L5</th>
           
            <th class="text-center">Opciones</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $qry = $conexion->query("SELECT * FROM producto");
        while ($row = $qry->fetch_assoc()):
        ?>
            <tr>
                <td style="font-size: 12px;"><?php echo $row['str_id'] ?></td>
                <td style="font-size: 12px;"><?php echo $row['cod_producto'] ?></td>
                <td style="font-size: 12px;"><?php echo $row['descripcion'] ?></td>
                <td style="font-size: 12px;"><?php echo $row['familia'] ?></td>
                <td style="font-size: 12px;"><?php echo $row['micraje'] ?></td>
                <td style="font-size: 12px;"><?php echo $row['ref_1'] ?></td>
                <td style="font-size: 12px;"><?php echo $row['ref_2'] ?></td>
                <td style="font-size: 12px;"><?php echo $row['relacion'] ?></td>
                <td style="font-size: 12px;"><?php echo $row['calibre'] ?></td>
                <td style="font-size: 12px;"><?php echo $row['und_embalaje_minima'] ?></td>
                <td style="font-size: 12px;"><?php echo $row['peso_kg'] ?></td>
                <td style="font-size: 12px;"><?php echo $row['peso_kg_paca_caja'] ?></td>
                <td style="font-size: 12px;"><?php echo $row['precio_remision_lista_5'] ?></td>
                <td style="font-size: 12px;"><?php echo $row['precio_lista_5'] ?></td>
                <td style="white-space: nowrap;">
                    <button class="btn btn-success btn-sm edit_borrower" type="button"
                        data-id="<?php echo $row['id_producto'] ?>">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-sm delete_borrower" type="button"
                        data-id="<?php echo $row['id_producto'] ?>">
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
    uni_modal("Modificar Productos", "manage_productos.php?id_producto=" + $(this).attr('data-id'))
})
$('#borrower-list').on('click', '.add_borrower', function() {
    uni_modal("Agregar Stock al Productos", "agregar_producto.php?id_producto=" + $(this).attr('data-id'))
})
$('#borrower-list').on('click', '.delete_borrower', function() {
    _conf("Esta seguro que quiere eliminar este producto?", "delete_borrower", [$(this).attr('data-id')])
})

function delete_borrower($id_producto) {
    start_load()
    $.ajax({
        url: 'ajax.php?action=delete_producto',
        method: 'POST',
        data: {
            id_producto: $id_producto
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