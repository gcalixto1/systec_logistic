<?php
include('conexionfin.php');

$id = isset($_GET['idproveedor']) ? $_GET['idproveedor'] : '';

$meta = array();

if (!empty($id)) {

    $id = intval($id);

    $query = $conexion->query("SELECT * FROM proveedor WHERE idproveedor = $id");

    if ($query) {

        $proveedor = $query->fetch_assoc();

        if ($proveedor) {

            $meta = $proveedor;
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
?>
<div class="container-fluid">
    <div class="card">
        <form class="form form-material" method="post" action="#" name="saveproveedor" id="saveproveedor">
            <div id="save">
            </div>
            <div class="form-body">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group has-feedback">
                            <input type="hidden" name="id"
                                value="<?php echo isset($_GET['idproveedor']) ? $_GET['idproveedor'] : '' ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="name">Tipo Contribuyente</label>
                            <select name="tipoControbuyente" id="tipoControbuyente" class="form-control" required
                                aria-required="true">
                                <option value="0"
                                    <?php echo isset($meta['tipoControbuyente']) && $meta['tipoControbuyente'] == 0 ? 'selected' : ''; ?>>
                                    SELECCIONE
                                </option>
                                <option value="1"
                                    <?php echo isset($meta['tipoControbuyente']) && $meta['tipoControbuyente'] == 1 ? 'selected' : ''; ?>>
                                    PERSONA NATURAL
                                </option>
                                <option value="2"
                                    <?php echo isset($meta['tipoControbuyente']) && $meta['tipoControbuyente'] == 2 ? 'selected' : ''; ?>>
                                    PERSONA JURIDICA
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-md-8"></div>
                        <div class="form-group col-md-6">
                            <label for="name">Nombre Completo</label>
                            <input type="text" name="proveedor" id="proveedor" class="form-control"
                                value="<?php echo isset($meta['proveedor']) ? htmlspecialchars($meta['proveedor']) : ''; ?>"
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
                        <div class="form-group col-md-3">
                            <label for="name">Documento</label>
                            <input type="text" name="dni" id="dni" class="form-control"
                                value="<?php echo isset($meta['documento']) ? htmlspecialchars($meta['documento']) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="name">Telefono</label>
                            <input type="text" name="telefono" id="telefono" class="form-control"
                                value="<?php echo isset($meta['telefono']) ? htmlspecialchars($meta['telefono']) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="name">Correo Electronico</label>
                            <input type="text" name="correo" id="correo" class="form-control"
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
$('#saveproveedor').submit(function(e) {
    e.preventDefault();
    var isValid = true;
    $('#saveproveedor input[required]').each(function() {
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
            url: 'ajax.php?action=save_proveedores',
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
</script>