<?php
include('conexionfin.php');

$id = 1;
$meta = array();
if (!empty($id)) {
    $id = intval($id);
    $query = $conexion->query("SELECT * FROM configuracion WHERE id = $id");
    if ($query) {
        $configuracion = $query->fetch_assoc();
        if ($configuracion) {
            $meta = $configuracion;
        }
    }
}

require_once("includes/class.php");
$pro = new Action();

$departamentoSeleccionado = isset($_POST['departamento']) ? $_POST['departamento'] : (isset($meta['dato6']) ? $meta['dato6'] : '');
$municipioSeleccionado = isset($_POST['municipio']) ? $_POST['municipio'] : (isset($meta['dato7']) ? $meta['dato7'] : '');

$departamentos = $pro->ListarDepartamentos();
$municipios = $departamentoSeleccionado ? $pro->ListarMunicipios($departamentoSeleccionado) : [];
$documentos = $pro->ListarDocumentos();

$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$actividades = $pro->ListarActividades($busqueda);

$giroSeleccionado = isset($meta['giro']) ? $meta['giro'] : '';

?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title text-black"><i class="fa fa-pen"></i> Configuración (Perfil General de la Empresa)
            </h4>
        </div>
        <form id="configuracion">
            <div id="error"></div>
            <div class="form-body">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group has-feedback">
                                <label>NIT:</label>
                                <input type="text" name="txtDni"
                                    value="<?php echo isset($meta['dni']) ? htmlspecialchars($meta['dni']) : ''; ?>"
                                    id="txtDni" placeholder="Dni de la Empresa" required class="form-control">
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="nrc">N.R.C.</label>
                            <input type="text" name="dato1" id="dato1" class="form-control"
                                value="<?php echo isset($meta['dato1']) ? htmlspecialchars($meta['dato1']) : ''; ?>"
                                required>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group has-feedback">
                                <label>Nombre/Razon Social:</label>
                                <input type="text" name="txtNombre" class="form-control"
                                    value="<?php echo isset($meta['nombre']) ? htmlspecialchars($meta['nombre']) : ''; ?>"
                                    id="txtNombre" placeholder="Nombre de la Empresa" required class="form-control">

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group has-feedback">
                                <label>Nombre Comercial:</label>
                                <input type="text" name="txtRSocial" class="form-control"
                                    value="<?php echo isset($meta['razon_social']) ? htmlspecialchars($meta['razon_social']) : ''; ?>"
                                    id="txtRSocial" placeholder="Razon Social de la Empresa">

                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="actividad">Seleccionar Actividad</label>
                            <select name="giro" id="giro" class="form-control" required>
                                <option value="">-- SELECCIONE --</option>
                                <?php foreach ($actividades as $act): ?>
                                <option value="<?php echo $act['codigo']; ?>"
                                    <?php echo (isset($meta['giro']) && $meta['giro'] == $act['codigo']) ? 'selected' : ''; ?>>
                                    <?php echo $act['codigo'] . ' - ' . $act['descripcion']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="nrc">Resolucion por MH:</label>
                            <input type="text" name="dato2" id="dato2" class="form-control"
                                value="<?php echo isset($meta['dato2']) ? htmlspecialchars($meta['dato2']) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="nrc">Correlativo desde:</label>
                            <input type="text" name="dato3" id="dato3" class="form-control"
                                value="<?php echo isset($meta['dato3']) ? htmlspecialchars($meta['dato3']) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="nrc">Correlativo Hasta:</label>
                            <input type="text" name="dato4" id="dato4" class="form-control"
                                value="<?php echo isset($meta['dato4']) ? htmlspecialchars($meta['dato4']) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="nrc">Fecha de Resolucion:</label>
                            <input type="text" name="dato5" id="dato5" class="form-control"
                                value="<?php echo isset($meta['dato5']) ? htmlspecialchars($meta['dato5']) : ''; ?>"
                                required>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group has-feedback">
                                <label>Teléfono:</label>
                                <input type="text" name="txtTelEmpresa" class="form-control"
                                    value="<?php echo isset($meta['telefono']) ? htmlspecialchars($meta['telefono']) : ''; ?>"
                                    id="txtTelEmpresa" placeholder="teléfono de la Empresa" required>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group has-feedback">
                                <label>Correo Electrónico:</label>
                                <input type="email" name="txtEmailEmpresa" class="form-control"
                                    value="<?php echo isset($meta['email']) ? htmlspecialchars($meta['email']) : ''; ?>"
                                    id="txtEmailEmpresa" placeholder="Correo de la Empresa" required>

                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="departamento">Departamento</label>
                            <select name="dato6" id="dato6" class="form-control" required>
                                <option value="">-- SELECCIONE --</option>
                                <?php foreach ($departamentos as $dep): ?>
                                <option value="<?php echo $dep['codigo']; ?>"
                                    <?php echo ($dep['codigo'] == $departamentoSeleccionado) ? 'selected' : ''; ?>>
                                    <?php echo $dep['valor']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="municipio">Municipio</label>
                            <select name="dato7" id="dato7" class="form-control" required>
                                <option value="">-- SELECCIONE MUNICIPIO --</option>
                                <?php foreach ($municipios as $mun): ?>
                                <option value="<?php echo $mun['codigo']; ?>"
                                    <?php echo ($mun['codigo'] == $municipioSeleccionado) ? 'selected' : ''; ?>>
                                    <?php echo $mun['valor']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group has-feedback">
                                <label>Dirección:</label>
                                <input type="text" name="txtDirEmpresa" class="form-control"
                                    value="<?php echo isset($meta['direccion']) ? htmlspecialchars($meta['direccion']) : ''; ?>"
                                    id="txtDirEmpresa" placeholder="Dirreción de la Empresa" required>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group has-feedback">
                                <label>Impuesto (%):</label>
                                <input type="text" name="txtIgv" class="form-control"
                                    value="<?php echo isset($meta['igv']) ? htmlspecialchars($meta['igv']) : ''; ?>"
                                    id="txtIgv" placeholder="IGV de la Empresa" required>

                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group has-feedback">
                                <label>Moneda:</label>
                                <input type="text" name="moneda" class="form-control"
                                    value="<?php echo isset($meta['moneda']) ? htmlspecialchars($meta['moneda']) : ''; ?>"
                                    id="moneda" placeholder="$" required>

                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="control-label">Impresora</label>
                            <select name="impresion" id="impresion" class="form-control" required aria-required="true">
                                <option value="">-- SELECCIONE --</option>
                                <option value="58mm" <?php echo ($meta['impresion'] == "58mm") ? "selected" : ""; ?>>
                                    Impresion 58mm</option>
                                <option value="80mm" <?php echo ($meta['impresion'] == "80mm") ? "selected" : ""; ?>>
                                    Impresion 80mm</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="control-label">Ambiente</label>
                            <select name="dato8" id="dato8" class="form-control" required aria-required="true">
                                <option value="">-- SELECCIONE --</option>
                                <option value="00" <?php echo ($meta['dato8'] == "00") ? "selected" : ""; ?>>
                                    Ambiente de Pruebas</option>
                                <option value="01" <?php echo ($meta['dato8'] == "01") ? "selected" : ""; ?>>
                                    Ambiente de producción</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label class="control-label">Clave Privada</label>
                            <input type="text" name="clavePRIV" id="clavePRIV" class="form-control"
                                value="<?php echo isset($meta['clavePRIV']) ? htmlspecialchars($meta['clavePRIV']) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group col-md-3">
                            <label class="control-label">Clave API</label>
                            <input type="text" name="claveAPI" id="claveAPI" class="form-control"
                                value="<?php echo isset($meta['claveAPI']) ? htmlspecialchars($meta['claveAPI']) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group col-md-3">
                            <label class="form-label">Servidor SMTP</label>
                            <input type="text" name="smtp_host" class="form-control" value="<?= $meta['smtp_host'] ?>"
                                required>
                        </div>
                        <div class="form-group col-md-3">
                            <label class="form-label">Contraseña SMTP</label>
                            <input type="password" name="smtp_pass" class="form-control"
                                value="<?= $meta['smtp_pass'] ?>" required>
                        </div>

                        <div class="form-group col-md-3">
                            <label class="form-label">Seguridad</label>
                            <select name="smtp_secure" class="form-control" required>
                                <option value="tls" <?= $meta['smtp_secure'] == 'tls' ? 'selected' : '' ?>>TLS
                                </option>
                                <option value="ssl" <?= $meta['smtp_secure'] == 'ssl' ? 'selected' : '' ?>>SSL
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label class="form-label">Puerto</label>
                            <input type="number" name="smtp_port" class="form-control" value="<?= $meta['smtp_port'] ?>"
                                required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-preview thumbnail" data-trigger="fileinput"
                                style="width: 210px; height: 110px;">
                                <?php
                                    $logoPrincipal = file_exists("img/logo.png") ? "img/logo.png" : "img/ninguna.png";
                                    echo "<img id='logoPreview' src='$logoPrincipal' class='img-rounded' border='0' width='210' height='110' title='Logo' data-rel='tooltip'>";
                                    ?>
                            </div>
                            <div>
                                <label for="imagen" class="btn btn-success">
                                    <span class="fileinput-exists"><i class="fa fa-paint-brush"></i> Logo
                                        Principal</span>
                                </label>
                                <input type="file" size="10" accept=".png" data-original-title="Subir Logo Principal"
                                    data-rel="tooltip" placeholder="Suba su Logo Principal" name="imagen" id="imagen"
                                    style="display: none;" onchange="previewImage(event, 'logoPreview', 'imagen')" />
                                <small>
                                    <p>Para Subir el Logo Principal debe tener en cuenta:
                                        <br> * La Imagen debe ser extension.png<br> * La imagen no debe ser mayor de
                                        200 KB
                                    </p>
                                </small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-preview thumbnail" data-trigger="fileinput"
                                    style="width: 210px; height: 110px;">
                                    <?php
                                    $logoPrincipal2 = file_exists("img/fondo.jpg") ? "img/fondo.jpg" : "img/ninguna.png";
                                    echo "<img id='logoPreview2' src='$logoPrincipal2' class='img-rounded' border='0' width='210' height='110' title='Logo' data-rel='tooltip'>";
                                    ?>
                                </div>
                                <div>
                                    <label for="imagen2" class="btn btn-warning">
                                        <span class="fileinput-exists"><i class="fa fa-paint-brush"></i> Fondo
                                            Principal</span>
                                    </label>
                                    <input type="file" size="10" accept=".png"
                                        data-original-title="Subir Logo Principal" data-rel="tooltip"
                                        placeholder="Suba su Logo Principal" name="imagen2" id="imagen2"
                                        style="display: none;"
                                        onchange="previewImage2(event, 'logoPreview2', 'imagen2')" />
                                    <small>
                                        <p>Para Subir el fondo Principal debe tener en cuenta:
                                            <br> * La Imagen debe ser extension.png<br> * La imagen no debe ser mayor de
                                            200 KB
                                        </p>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div>
                        <button type="submit" class="btn btn-primary btn-lg" style="color:white;"><span
                                class="fa fa-save"></span> Actualizar Datos</button>
                        <button class="btn btn-dark btn-lg" onclick="window.location.reload();" type="reset"><span
                                class="fa fa-window-close"></span>
                            Cancelar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
function previewImage(event, previewId, inputId) {
    console.log("Función llamada");
    var input = event.target;
    var reader = new FileReader();
    reader.onload = function() {
        console.log("Imagen cargada");
        var preview = document.getElementById(previewId);
        var img = new Image();
        img.onload = function() {
            var canvas = document.createElement('canvas');
            var ctx = canvas.getContext('2d');
            canvas.width = 210;
            canvas.height = 110;
            ctx.drawImage(img, 0, 0, 210, 110);
            preview.src = canvas.toDataURL('image/png');
        };
        img.src = reader.result;
    }
    reader.onerror = function(event) {
        console.error("Error al cargar la imagen:", event.target.error);
    }
    if (input.files && input.files[0]) {
        reader.readAsDataURL(input.files[0]);
    } else {
        console.error("No se seleccionó ningún archivo.");
    }
}

function previewImage2(event, previewId, inputId) {
    console.log("Función llamada");
    var input = event.target;
    var reader = new FileReader();
    reader.onload = function() {
        console.log("Imagen cargada");
        var preview = document.getElementById(previewId);
        var img = new Image();
        img.onload = function() {
            var canvas = document.createElement('canvas');
            var ctx = canvas.getContext('2d');
            canvas.width = 210;
            canvas.height = 110;
            ctx.drawImage(img, 0, 0, 210, 110);
            preview.src = canvas.toDataURL('image/png');
        };
        img.src = reader.result;
    }
    reader.onerror = function(event) {
        console.error("Error al cargar la imagen:", event.target.error);
    }
    if (input.files && input.files[0]) {
        reader.readAsDataURL(input.files[0]);
    } else {
        console.error("No se seleccionó ningún archivo.");
    }
}
$(document).ready(function() {
    $('#configuracion').submit(function(e) {
        e.preventDefault(); // Evitar el envío del formulario por defecto

        // Deshabilitar el botón de enviar y mostrar el mensaje "Actualizando..."
        $('#configuracion button[type="submit"]').attr('disabled', true).html('Actualizando...');

        // Eliminar cualquier mensaje de error anterior
        if ($(this).find('.alert-danger').length > 0) {
            $(this).find('.alert-danger').remove();
        }

        // Realizar la petición AJAX
        $.ajax({
            url: 'ajax.php?action=save_configuracion',
            method: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            cache: false,
            error: function(err) {
                console.log(err);
                // Habilitar el botón de enviar y restaurar su texto original
                $('#configuracion button[type="submit"]').removeAttr('disabled').html(
                    'Entrar al sistema');
            },
            success: function(resp) {
                if (resp == 1) {
                    // Mostrar mensaje de éxito y recargar la página
                    Swal.fire({
                        title: 'Éxito!',
                        text: "El registro se guardó con éxito",
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
});
$(document).ready(function() {
    $('#dato6').change(function() {
        var idDepartamento = $(this).val();
        if (idDepartamento !== '') {
            $.ajax({
                url: 'cargar_municipios.php?id_departamento=' + idDepartamento,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    var municipioSelect = $('#dato7');
                    municipioSelect.empty();
                    municipioSelect.append(
                        '<option value="">-- SELECCIONE MUNICIPIO --</option>');
                    $.each(response, function(index, municipio) {
                        municipioSelect.append($('<option>', {
                            value: municipio.codigo,
                            text: municipio.valor
                        }));
                    });
                }
            });
        }
    });

    <?php if (!empty($departamentoSeleccionado) && !empty($municipioSeleccionado)): ?>
    $.ajax({
        url: 'ajax_municipios.php?id_departamento=' + '<?php echo $departamentoSeleccionado; ?>',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            var municipioSelect = $('#dato7');
            municipioSelect.empty();
            municipioSelect.append('<option value="">-- SELECCIONE MUNICIPIO --</option>');
            $.each(response, function(index, municipio) {
                var selected = municipio.codigo ===
                    '<?php echo $municipioSeleccionado; ?>' ? 'selected' : '';
                municipioSelect.append($('<option>', {
                    value: municipio.codigo,
                    text: municipio.valor,
                    selected: selected
                }));
            });
        }
    });
    <?php endif; ?>
});


$('#giro').select2({
    placeholder: "-- SELECCIONE ACTIVIDAD --",
    minimumInputLength: 2,
    ajax: {
        url: 'buscar_actividades.php',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                term: params.term // término de búsqueda
            };
        },
        processResults: function(data) {
            return {
                results: data.results
            };
        },
        cache: true
    },
    width: '100%'
});
</script>