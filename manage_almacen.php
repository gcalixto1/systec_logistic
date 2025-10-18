<?php
include('conexionfin.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$meta = array();

if ($id > 0) {
    $query = $conexion->query("SELECT * FROM almacenes WHERE id = $id");
    if ($query && $query->num_rows > 0) {
        $meta = $query->fetch_assoc();
    }
}
?>
<div class="container-fluid">
    <div class="card">
        <form class="form form-material" method="post" action="#" name="saveapertura" id="saveapertura">
            <div id="save">
            </div>
            <div class="form-body">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group has-feedback">
                             <input type="hidden" name="id" value="<?php echo $id; ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="name">codigo de lmacen</label>
                            <input type="text" name="codigo" id="codigo" class="form-control"
                                placeholder="codigo de almacen"  value="<?php echo isset($meta['codigo']) ? htmlspecialchars($meta['codigo']) : ''; ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="name">Nombre de lmacen</label>
                            <input type="text" name="nombre" id="nombre" class="form-control"
                                placeholder="Ingrese nombre de almacen"  value="<?php echo isset($meta['nombre']) ? htmlspecialchars($meta['nombre']) : ''; ?>" required>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $('#saveapertura').submit(function(e) {
        e.preventDefault();

        // Obtener el valor del campo categoria_des
        var presentacion = $('#codigo').val().trim();
        var presentacion1 = $('#nombre').val().trim();

        // Validar si el campo está vacío
        if (presentacion === "" && presentacion1 === "") {
            Swal.fire({
                title: 'Error!',
                text: 'Los campos no pueden estar vacíos.',
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
            return; // No enviar el formulario si está vacío
        }

        start_load();
        $.ajax({
            url: 'ajax.php?action=save_almacen',
            method: 'POST',
            data: $(this).serialize(),
            success: function(resp) {
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
    });
</script>