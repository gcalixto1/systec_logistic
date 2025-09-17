<?php include("conexionfin.php"); ?>
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
        <h4 class="card-title text-black"><i class="fa fa-user"></i> Gestion de Usuarios</h4>
    </div>
    <br>
    <div class="col-lg-12">

        <div class="col-sm-12 col-xs-12 text-right">
            <button class="btn btn-success btn-lg" type="button" id="new_usuario"><i class="fa fa-plus"></i> Nuevo
                Usuario</button>
        </div>
        <br />
        <table class="table table-responsive" id="borrower-list">
            <colgroup>
                <col width="5%">
                <col width="45%">
                <col width="45%">
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Nombre Completo</th>
                    <th class="text-center">Correo</th>
                    <th class="text-center">Usuario</th>
                    <th class="text-center">Rol</th>
                    <th class="text-center">Accion</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                $qry = $conexion->query("SELECT *,rol.rol as perfil FROM usuario INNER JOIN rol ON rol.idrol = usuario.rol");
                while ($row = $qry->fetch_assoc()):
                ?>
                    <tr>
                        <td style="font-size: 12px;" class="">
                            <?php echo $i++ ?>
                        </td>
                        <td style="font-size: 12px;" class="">
                            <?php echo $row['nombre'] ?>
                        </td>
                        <td style="font-size: 12px;" class="">
                            <?php echo $row['correo'] ?>
                        </td>
                        <td style="font-size: 12px;" class="">
                            <?php echo $row['usuario'] ?>
                        </td>
                        <td style="font-size: 12px;" class="">
                            <?php echo $row['perfil'] ?>
                        </td>

                        <td style="white-space: nowrap;">
                            <button class="btn btn-success btn-sm edit_borrower" type="button"
                                data-id="<?php echo $row['idusuario'] ?>">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm delete_borrower" type="button"
                                data-id="<?php echo $row['idusuario'] ?>">
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
    $('#new_usuario').click(function() {
        uni_modal("Gestion de usuarios", "manage_usuarios.php")
    })
    $('#borrower-list').on('click', '.edit_borrower', function() {
        uni_modal("Modificar usuario", "manage_usuarios.php?idusuario=" + $(this).attr('data-id'))
    })
    $('#borrower-list').on('click', '.delete_borrower', function() {
        _conf("Esta seguro que quiere eliminar este usuario?", "delete_borrower", [$(this).attr('data-id')])
    })

    function delete_borrower($idusuario) {
        start_load()
        $.ajax({
            url: 'ajax.php?action=eliminar_usuario',
            method: 'POST',
            data: {
                idusuario: $idusuario
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