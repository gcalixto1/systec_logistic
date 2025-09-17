<div class="container-fluid">
    <div class="card">
        <form class="form form-material" method="post" action="#" name="saveapertura" id="saveapertura">
            <div id="save">
            </div>
            <div class="form-body">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group has-feedback">
                            <input type="hidden" name="id" value="">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="name">Presentacion de Producto</label>
                            <input type="text" name="presentacion" id="presentacion" class="form-control"
                                placeholder="Ingrese la presentacion del producto" required>
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
        var presentacion = $('#presentacion').val().trim();

        // Validar si el campo está vacío
        if (presentacion === "") {
            Swal.fire({
                title: 'Error!',
                text: 'El campo de presentacion no puede estar vacío.',
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
            return; // No enviar el formulario si está vacío
        }

        start_load();
        $.ajax({
            url: 'ajax.php?action=save_presentacion',
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