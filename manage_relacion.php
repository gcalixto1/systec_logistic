<?php
include('conexionfin.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$meta = array();

if ($id > 0) {
    $query = $conexion->query("SELECT * FROM relaciones WHERE id = $id");
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
                    <div class="form-group col-md-12">
                            <label for="name">Relacion</label>
                            <input type="text" name="relacion" id="relacion" class="form-control" placeholder="Ingrese una relacion"
                              value="<?php echo isset($meta['relacion']) ? htmlspecialchars($meta['relacion']) : ''; ?>" required>  
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $('#saveapertura').submit(function (e) {
        e.preventDefault();
        
        // Obtener el valor del campo categoria_des
        var categoriaDes = $('#relacion').val().trim();
        
        // Validar si el campo está vacío
        if (categoriaDes === "") {
            Swal.fire({
                title: 'Error!',
                text: 'El campo de Etiqueta no puede estar vacío.',
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
            return; // No enviar el formulario si está vacío
        }

        start_load();
        $.ajax({
            url: 'ajax.php?action=save_relaciones',
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
    });
</script>