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
        <h4 class="card-title text-black"><i class="fa fa-address-card"></i> Gestion de Clientes</h4>
    </div>
    <br>
    <div class="col-lg-12">

        <div class="col-sm-12 col-xs-12 text-right">
            <button class="btn btn-success btn-lg" type="button" id="new_cliente"><i class="fa fa-plus"></i> Nuevo
                Cliente</button>
        </div>
        <br />
        <table class="table table-responsive" id="borrower-list">
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
                    <th class="text-center">Nombre Completo</th>
                    <th class="text-center">Documento</th>
                    <th class="text-center">Telefono</th>
                    <th class="text-center">Correo</th>
                    <th class="text-center">Accion</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                $qry = $conexion->query("SELECT * FROM cliente");
                while ($row = $qry->fetch_assoc()):
                    ?>
                <tr>
                    <td>
                        <?php echo $i++ ?>
                    </td>
                    <td class="">
                        <?php echo $row['nombre'] ?>
                    </td>
                    <td class="">
                        <?php echo $row['dni'] ?>
                    </td>
                    <td class="">
                        <?php echo $row['telefono1'] ?>
                    </td>
                    <td class="">
                        <?php echo $row['correo'] ?>
                    </td>

                    <td style="white-space: nowrap;">
                        <button class="btn btn-success btn-sm edit_borrower" type="button"
                            data-id="<?php echo $row['idcliente'] ?>">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm delete_borrower" type="button"
                            data-id="<?php echo $row['idcliente'] ?>">
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
$('#new_cliente').click(function() {
    uni_modal("Gestion de Clientes", "manage_clientes.php")
})
$('#borrower-list').on('click', '.edit_borrower', function() {
    uni_modal("Modificar Cliente", "manage_clientes.php?idcliente=" + $(this).attr('data-id'))
})
$('#borrower-list').on('click', '.delete_borrower', function() {
    _conf("Esta seguro que quiere eliminar este Cliente?", "delete_borrower", [$(this).attr('data-id')])
})

function delete_borrower($idcliente) {
    start_load()
    $.ajax({
        url: 'ajax.php?action=delete_cliente',
        method: 'POST',
        data: {
            idcliente: $idcliente
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