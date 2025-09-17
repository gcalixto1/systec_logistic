<div class="container-fluid">
    <div class="card">
        <form class="form form-material" method="post" action="#" name="saveapertura" id="saveapertura">
            <div id="save">
            </div>
            <div class="form-body">
                <div class="card-body">
                    <div class="row">
                    <div class="form-group has-feedback">
                            <input type="hidden" name="id"
                                value="">
                        </div>
                    <div class="form-group col-md-12">
                            <label for="name">Categoria</label>
                            <input type="text" name="categoria_des" id="categoria_des" class="form-control" placeholder="Ingrese una categoria"
                                required>
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
        var categoriaDes = $('#categoria_des').val().trim();
        
        // Validar si el campo está vacío
        if (categoriaDes === "") {
            Swal.fire({
                title: 'Error!',
                text: 'El campo de categoría no puede estar vacío.',
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
            return; // No enviar el formulario si está vacío
        }

        start_load();
        $.ajax({
            url: 'ajax.php?action=save_categorias',
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