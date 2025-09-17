<?php
include('conexionfin.php');

$id = isset($_GET['idcliente']) ? $_GET['idcliente'] : '';
$meta = array();
if (!empty($id)) {
    $id = intval($id);
    $query = $conexion->query("SELECT * FROM cliente LEFT JOIN cliente_direccion ON cliente_direccion.cliente_dni = cliente.dni  WHERE idcliente = $id");
    if ($query) {
        $cliente = $query->fetch_assoc();
        if ($cliente) {
            $meta = $cliente;
        }
    }
}

require_once("includes/class.php");
$pro = new Action();

$departamentoSeleccionado = isset($_POST['departamento']) ? $_POST['departamento'] : (isset($meta['departamento']) ? $meta['departamento'] : '');
$municipioSeleccionado = isset($_POST['municipio']) ? $_POST['municipio'] : (isset($meta['municipio']) ? $meta['municipio'] : '');

$departamentos = $pro->ListarDepartamentos();
$municipios = $departamentoSeleccionado ? $pro->ListarMunicipios($departamentoSeleccionado) : [];
$documentos = $pro->ListarDocumentos();

$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$actividades = $pro->ListarActividades($busqueda);
?>
<div class="container-fluid">
    <div class="card">
        <form class="form form-material" method="post" action="#" name="saveclientes" id="saveclientes">
            <div id="save"></div>
            <div class="form-body">
                <div class="card-body">
                    <div class="row">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">

                        <div class="form-group col-md-6">
                            <label for="tipoControbuyente">Tipo Cliente</label>
                            <select name="tipoControbuyente" id="tipoControbuyente" class="form-control" required>
                                <option value="0">SELECCIONE</option>
                                <option value="1"
                                    <?php echo (isset($meta['tipoControbuyente']) && $meta['tipoControbuyente'] == 1) ? 'selected' : ''; ?>>
                                    PERSONA NATURAL</option>
                                <option value="2"
                                    <?php echo (isset($meta['tipoControbuyente']) && $meta['tipoControbuyente'] == 2) ? 'selected' : ''; ?>>
                                    PERSONA JURIDICA</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="actividad">Seleccionar Actividad</label>
                            <select name="dato3" id="dato3" class="form-control select2" required>
                                <option value="">-- SELECCIONE --</option>
                                <?php foreach ($actividades as $act): ?>
                                <option value="<?php echo $act['codigo']; ?>"
                                    <?php echo (isset($meta['dato3']) && $meta['dato3'] == $act['codigo']) ? 'selected' : ''; ?>>
                                    <?php echo $act['codigo'] . ' - ' . $act['descripcion']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="nrc">N.R.C.</label>
                            <input type="text" name="dato1" id="dato1" class="form-control"
                                value="<?php echo isset($meta['dato1']) ? htmlspecialchars($meta['dato1']) : ''; ?>"
                                required>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="nombre">Nombre / Razón Social</label>
                            <input type="text" name="nombre" id="nombre" class="form-control"
                                value="<?php echo isset($meta['nombre']) ? htmlspecialchars($meta['nombre']) : ''; ?>"
                                required>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="nombreComercial">Nombre Comercial</label>
                            <input type="text" name="dato2" id="dato2" class="form-control"
                                value="<?php echo isset($meta['dato2']) ? htmlspecialchars($meta['dato2']) : ''; ?>"
                                required>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="tipoDoc">Tipo Documento</label>
                            <select name="tipoDoc" id="tipoDoc" class="form-control" required>
                                <option value="">-- SELECCIONE --</option>
                                <?php foreach ($documentos as $doc): ?>
                                <option value="<?php echo $doc['codigo']; ?>"
                                    <?php echo (isset($meta['tipoDocumento']) && $meta['tipoDocumento'] == $doc['codigo']) ? 'selected' : ''; ?>>
                                    <?php echo $doc['valor']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="dni">Número de Documento</label>
                            <input type="text" name="dni" id="dni" class="form-control"
                                value="<?php echo isset($meta['dni']) ? htmlspecialchars($meta['dni']) : ''; ?>"
                                required>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="telefono">Teléfono</label>
                            <input type="text" name="telefono" id="telefono" maxlength="12" class="form-control"
                                value="<?php echo isset($meta['telefono']) ? htmlspecialchars($meta['telefono']) : ''; ?>"
                                required>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="correo">Correo</label>
                            <input type="email" name="correo" id="correo" class="form-control"
                                value="<?php echo isset($meta['correo']) ? htmlspecialchars($meta['correo']) : ''; ?>"
                                required>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="departamento">Departamento</label>
                            <select name="departamento" id="departamento" class="form-control" required>
                                <option value="">-- SELECCIONE --</option>
                                <?php foreach ($departamentos as $dep): ?>
                                <option value="<?php echo $dep['codigo']; ?>"
                                    <?php echo ($dep['codigo'] == $departamentoSeleccionado) ? 'selected' : ''; ?>>
                                    <?php echo $dep['valor']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="municipio">Municipio</label>
                            <select name="municipio" id="municipio" class="form-control" required>
                                <option value="">-- SELECCIONE MUNICIPIO --</option>
                                <?php foreach ($municipios as $mun): ?>
                                <option value="<?php echo $mun['codigo']; ?>"
                                    <?php echo ($mun['codigo'] == $municipioSeleccionado) ? 'selected' : ''; ?>>
                                    <?php echo $mun['valor']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="direccion">Dirección</label>
                            <input type="text" name="direccion" id="direccion" class="form-control"
                                value="<?php echo isset($meta['complemento']) ? htmlspecialchars($meta['complemento']) : ''; ?>"
                                required>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$('#saveclientes').submit(function(e) {
    e.preventDefault();
    var isValid = true;

    if (isValid) {
        start_load();
        $.ajax({
            url: 'ajax.php?action=save_clientes',
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
                    });
                }
            }
        });
    }
});


$(document).ready(function() {
    $('#departamento').change(function() {
        var idDepartamento = $(this).val();
        if (idDepartamento !== '') {
            $.ajax({
                url: 'cargar_municipios.php?id_departamento=' + idDepartamento,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    var municipioSelect = $('#municipio');
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
            var municipioSelect = $('#municipio');
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

$(document).ready(function() {
    $('.select2').select2({
        width: '100%',
        height: '150%',
        placeholder: "-- Seleccionar una actividad --",
        allowClear: true
    });
});
</script>