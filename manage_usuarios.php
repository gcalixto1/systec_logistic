<?php
include ('conexionfin.php');

$id = isset($_GET['idusuario']) ? $_GET['idusuario'] : '';
$meta = array();

if (!empty($id)) {
    $id = intval($id);
    $query = $conexion->query("SELECT * FROM usuario WHERE idusuario = $id");
    if ($query) {
        $usuario = $query->fetch_assoc();
        if ($usuario) {
            $meta = $usuario;
        }
    }
}
?>
<div class="container-fluid">
    <div class="card">
        <form class="form form-material" method="post" action="#" name="saveusuario" id="saveusuario">
            <div id="save"></div>
            <div class="form-body">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group has-feedback">
                            <input type="hidden" name="id" value="<?php echo isset($_GET['idusuario']) ? $_GET['idusuario'] : ''; ?>">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="name">Nombre Completo</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" value="<?php echo isset($meta['nombre']) ? htmlspecialchars($meta['nombre']) : ''; ?>" required>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="name">Correo</label>
                            <input type="text" name="correo" id="correo" class="form-control" value="<?php echo isset($meta['correo']) ? htmlspecialchars($meta['correo']) : ''; ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="control-label">Rol de Acceso:</label>
                            <select name="rol" id="rol" class='form-control' aria-required="true">
                                <option value="">SELECCIONE</option>
                                <?php
                                require_once ("includes/class.php");
                                $listarN = new Action();
                                $listarN = $listarN->Listarnivel();
                                for ($i = 0; $i < sizeof($listarN); $i++) {
                                    $selected = '';
                                    if (isset($meta['rol']) && $meta['rol'] == $listarN[$i]['idrol']) {
                                        $selected = 'selected';
                                    }
                                    ?>
                                    <option value="<?php echo htmlspecialchars($listarN[$i]['idrol']); ?>" <?php echo $selected; ?>>
                                        <?php echo $listarN[$i]['rol']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="name">Usuario</label>
                            <input type="text" name="usuario" id="usuario" class="form-control" value="<?php echo isset($meta['usuario']) ? htmlspecialchars($meta['usuario']) : ''; ?>" required>
                        </div>
                        <?php if (!empty($id)): ?>
                            <div class="form-group col-md-12">
                                <label for="change_password">¿Desea cambiar la clave?</label><br>
                                <input type="radio" name="change_password" value="si" id="change_password_si"> Si<br>
                                <input type="radio" name="change_password" value="no" id="change_password_no" checked> No<br>
                            </div>
                            <div class="form-group col-md-12" id="clave_container" style="display: none;">
                                <label for="name">Clave de Acceso</label>
                                <input type="password" name="clave" id="clave" class="form-control" value="">
                            </div>
                        <?php else: ?>
                            <div class="form-group col-md-12">
                                <label for="name">Clave de Acceso</label>
                                <input type="password" name="clave" id="clave" class="form-control" value="<?php echo isset($meta['clave']); ?>" required>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('input[name="change_password"]').change(function() {
            if ($(this).val() == "si") {
                $('#clave_container').show();
            } else {
                $('#clave_container').hide();
                $('#clave').val('');
            }
        });
    });
    $('#saveusuario').submit(function (e) {

        e.preventDefault();
        var isValid = true;
        $('#saveusuario input[required]').each(function () {
            if ($(this).val().trim() === '') {
                isValid = false;
                Swal.fire({
                    title: 'Error!',
                    text: 'Todos los campos son obligatorios. Por favor, complete el campo vacio',
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
                return false;
            }
        });
        if (isValid) {
            start_load();
            $.ajax({
                url: 'ajax.php?action=save_usuarios',
                method: 'POST',
                data: $(this).serialize(),
                success: function (resp) {
                    if (resp == 1) {
                        Swal.fire({
                            title: 'Éxito!',
                            text: 'El registro se guardó con éxito.',
                            icon: 'success',
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    }
                }
            });
        }
    });
</script>
