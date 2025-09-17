<?php
include ('conexionfin.php');

$meta = array();
$query = $conexion->query("SELECT * FROM apertura_caja");
if ($query) {
    $usuario = $query->fetch_assoc();
    if ($usuario) {
        $meta = $usuario;
    }
}
$fechaActual = getdate();

// Extraer el año, mes y día de la fecha actual
$año = $fechaActual['year']; // Solo toma los últimos dos dígitos del año
$mes = str_pad($fechaActual['mon'], 2, '0', STR_PAD_LEFT); // Asegura que el mes tenga dos dígitos (agrega un cero si es necesario)
$dia = str_pad($fechaActual['mday'], 2, '0', STR_PAD_LEFT) - 1; // Asegura que el día tenga dos dígitos (agrega un cero si es necesario)

// Generar el código de correlativo
$correlativo = "AC{$mes}{$año}{$dia}";
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
                            <input type="hidden" name="id"
                                value="">
                        </div>
                    <div class="form-group col-md-6">
                            <label for="name">Numero de Apertura</label>
                            <input type="text" name="num_apertura" id="num_apertura" class="form-control"
                                value="<?php echo $correlativo; ?>"
                                required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="name">Efectivo que inicia</label>
                            <input type="text" name="saldo_inicial" id="saldo_inicial" class="form-control"
                                value="<?php echo isset($meta['saldo_inicial']) ? htmlspecialchars($meta['saldo_inicial']) : ''; ?>"
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

        start_load();

        $.ajax({

            url: 'ajax.php?action=save_apertura_caja',

            method: 'POST',

            data: $(this).serialize(),

            success: function (resp) {

                if (resp == 1) {

                    Swal.fire({

                        title: 'Exito!',

                        text: 'El registro se guardo con Exito',

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