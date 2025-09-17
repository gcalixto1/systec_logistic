<div class="container-fluid">
    <div class="card">
        <form class="form form-material" method="post" action="#" name="movimientos_caja" id="movimientos_caja">
            <div id="save"></div>
            <div class="form-body">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group has-feedback">
                            <input type="hidden" name="id" value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="control-label">Tipo de Transacción</label>
                            <select name="transaccion" id="transaccion" class="form-control" required aria-required="true">
                                <option value="ENTRADA">ENTRADA</option>
                                <option value="SALIDA">SALIDA</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6" id="ingreso-group">
                            <label for="ingreso">Ingreso</label>
                            <input type="number" name="ingreso" id="ingreso" class="form-control" placeholder="Monto de ingreso">
                        </div>
                        <div class="form-group col-md-6" id="egreso-group" style="display: none;">
                            <label for="egreso">Egreso</label>
                            <input type="number" name="egreso" id="egreso" class="form-control" placeholder="Monto de egreso">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="comentario">Comentario</label>
                            <textarea name="comentario" id="comentario" class="form-control" placeholder="Añadir comentario" required></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
        </form>
    </div>
</div>

<script>
    function toggleFields() {
        const transaccion = $('#transaccion').val();
        if (transaccion === 'ENTRADA') {
            $('#ingreso-group').show();
            $('#egreso-group').hide();
            $('#egreso').val('0.00'); // Valor automático de egreso
        } else if (transaccion === 'SALIDA') {
            $('#ingreso-group').hide();
            $('#egreso-group').show();
            $('#ingreso').val('0.00'); // Valor automático de ingreso
        }
    }

    $(document).ready(function () {
        $('#transaccion').change(toggleFields);
        toggleFields(); // Inicializa la visibilidad según el valor por defecto
    });

    $('#movimientos_caja').submit(function (e) {
        e.preventDefault();
        start_load();
        $.ajax({
            url: 'ajax.php?action=movimientos_caja',
            method: 'POST',
            data: $(this).serialize(),
            success: function (resp) {
                if (resp == 1) {
                    Swal.fire({
                        title: 'Éxito!',
                        text: 'El registro se guardó con éxito',
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
