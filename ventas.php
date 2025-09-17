<?php
include('conexionfin.php');

$id = isset($_GET['idfactura']) ? $_GET['idfactura'] : '';
$meta = array();
if (!empty($id)) {
    $id = intval($id);
    $query = $conexion->query("SELECT * FROM factura inner join cliente on cliente.idcliente = factura.idcliente WHERE id = $id");
    if ($query) {
        $cliente = $query->fetch_assoc();
        if ($cliente) {
            $meta = $cliente;
            // echo "<pre>";
            // print_r($meta);
            // echo "</pre>";
        }
    }
}
// if ($meta['tipofactura'] == "fcf") {
//     include 'dte.php';
// } else if ($meta['tipofactura'] == "ccf") {
//     include 'dteCCF.php';
// }

?>

<?php ?>
<style>
    .spinner-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.7);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .spinner {
        border: 6px solid #ccc;
        border-top: 6px solid #007bff;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>
<div id="spinner"
    style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background-color:rgba(255, 255, 255, 0.77);z-index:9999;text-align:center;padding-top:200px;font-size:24px;">
    <div class="spinner-border text-primary" role="status">
        <span class="sr-only">Cargando...</span>
    </div>
    <p>Procesando Comprobante...</p>
</div>
<div class="container-fluid">

    <form class="form form-material" method="post" action="#" name="pagoForm" id="pagoForm">
        <div class="form-content">
            <div class="form-body">
                <input type="hidden" name="id" id="id"
                    value="<?php echo isset($_GET['idfactura']) ? $_GET['idfactura'] : '' ?>">
                <input type=" hidden" name="montodevuelto" hidden id="montodevuelto" value="0.00">
                <input type="hidden" name="creditoinicial" id="creditoinicial" value="0.00">
                <input type="hidden" name="creditodisponible" id="creditodisponible" value="0.00">
                <input type="hidden" name="abonototal" id="abonototal" value="0.00">
                <input type="hidden" name="tipodocventa" id="tipodocventa"
                    value="<?php echo isset($meta['tipofactura']) ? $meta['tipofactura'] : '' ?>">

                <div class="row">
                    <div class="col-md-4">
                        <h6 class="mb-0 font-light">Total a Pagar</h6>
                        <h3 class="mb-0 font-medium"><label id="TextImporte"
                                name="TextImporte"><?php echo isset($meta['totalpagar']) ? htmlspecialchars($meta['totalpagar']) : ''; ?></label>
                        </h3>
                    </div>
                    <div class="col-md-4">
                        <h6 class="mb-0 font-light">Total Recibido</h6>
                        <h3 class="mb-0 font-medium"><label id="TextPagado" name="TextPagado">0.00</label></h3>
                    </div>
                    <div class="col-md-4">
                        <h6 class="mb-0 font-light">Total Cambio</h6>
                        <h3 class="mb-0 font-medium"><label id="TextCambio" name="TextCambio">0.00</label></h3>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <h6 class="mb-0 font-light">Nombre del Cliente</h6>
                        <h5 class="mb-0 font-medium"><label id="TextCliente"
                                name="TextCliente"><?php echo isset($meta['nombre']) ? htmlspecialchars($meta['nombre']) : ''; ?></label></label>
                        </h5>
                    </div>
                </div>
                <hr>
                <div id="condiciones">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group has-feedback">
                                <label class="control-label">Monto Recibido: </label>
                                <input class="form-control" type="text" name="montopagado" id="montopagado"
                                    autocomplete="off" placeholder="Monto Recibido" step="0.01" value="0.00"
                                    oninput="calcularCambio()" aria-required="true">
                            </div>
                        </div>
                    </div>
                </div>

                <div id="mediopagos"></div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group has-feedback">
                            <label class="control-label">Observaciones: </label>
                            <input type="text" class="form-control" name="observaciones" id="observaciones"
                                onkeyup="this.value=this.value.toUpperCase();"
                                placeholder="Ingrese Observaciones en Venta" autocomplete="off" aria-required="true">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function calcularCambio() {
        // Obtener los valores de los campos
        var importe = parseFloat(document.getElementById("TextImporte").textContent) || 0;
        var pagado = parseFloat(document.getElementById("montopagado").value) || 0;

        // Calcular la diferencia
        var cambio = pagado - importe;

        // Mostrar el resultado en el campo TextCambio y asegurarse de que los decimales sean siempre 2
        document.getElementById("TextCambio").textContent = cambio.toFixed(2);
        document.getElementById("TextPagado").textContent = pagado.toFixed(2);
    }

    $(document).ready(function () {
        function showSpinner() {
            $('#spinner').show();
        }

        function hideSpinner() {
            $('#spinner').hide();
        }

        $('#pagoForm').submit(function (e) {
            e.preventDefault();
            showSpinner();

            $.ajax({
                url: 'ajax.php?action=save_venta_completa',
                method: 'POST',
                data: $(this).serialize(),
                processData: true,
                contentType: 'application/x-www-form-urlencoded',
                dataType: 'json',
                success: function (resp) {
                    if (resp.success) {
                        let idfactura = resp.idfactura;
                        let tipo = resp.tipodocfac;
                        let urlDTE = tipo === 'ccf' ? 'dteCCF.php' : 'dte.php';

                        $.ajax({
                            url: urlDTE,
                            method: 'POST',
                            data: {
                                idfactura: idfactura
                            },
                            dataType: 'json',
                            success: function (respDte) {
                                hideSpinner();

                                const selloRecepcion = respDte.selloRecepcion || '';
                                const exito = selloRecepcion.trim() !== '';

                                if (exito) {
                                    Swal.fire({
                                        title: '¡Éxito!',
                                        text: 'Venta registrada y procesada correctamente.',
                                        icon: 'success',
                                        confirmButtonColor: '#28a745',
                                        confirmButtonText: 'Imprimir y enviar por correo comprobante DTE'
                                    }).then(() => {
                                        const win1 = window.open(
                                            'ticket.php?id=' +
                                            encodeURIComponent(
                                                idfactura), '_blank');
                                        const win2 = window.open(
                                            'facturaElectronica.php?id=' +
                                            encodeURIComponent(
                                                idfactura) +
                                            '&codigo=0', '_blank');

                                        if (!win1 || !win2) {
                                            alert(
                                                "Por favor habilita las ventanas emergentes (pop-ups) para este sitio."
                                            );
                                        } else {
                                            setTimeout(() => location
                                                .reload(), 500);
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error en DTE',
                                        text: respDte.detalle ||
                                            'Hacienda no devolvió un sello de recepción.',
                                        icon: 'error'
                                    });
                                }
                            },
                            error: function () {
                                hideSpinner();
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Error al enviar los datos al DTE.',
                                    icon: 'error'
                                });
                            }
                        });
                    } else {
                        hideSpinner();
                        Swal.fire({
                            title: 'Error',
                            text: resp.message || 'Error al guardar la venta.',
                            icon: 'error'
                        });
                    }
                },
                error: function () {
                    hideSpinner();
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo conectar con el servidor.',
                        icon: 'error'
                    });
                }
            });
        });


    });
</script>