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
        <form class="form form-material" method="post" action="#" name="savecliente" id="savecliente">
            <div id="save"></div>
            <div class="form-body">
                <div class="card-body">
                    <div class="row">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">

                        <!-- Tipo de Cliente -->
                        <div class="form-group col-md-6">
                            <label for="tipo_contribuyente">Tipo Cliente</label>
                            <select name="tipo_contribuyente" id="tipo_contribuyente" class="form-control" required>
                                <option value="">SELECCIONE</option>
                                <option value="1" <?= (isset($meta['tipo_contribuyente']) && $meta['tipo_contribuyente'] == 1) ? 'selected' : ''; ?>>PERSONA NATURAL</option>
                                <option value="2" <?= (isset($meta['tipo_contribuyente']) && $meta['tipo_contribuyente'] == 2) ? 'selected' : ''; ?>>PERSONA JURÍDICA</option>
                            </select>
                        </div>

                        <!-- Nombre / Razón Social -->
                        <div class="form-group col-md-6">
                            <label for="nombre">Nombre / Razón Social</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required
                                value="<?php echo isset($meta['nombre']) ? htmlspecialchars($meta['nombre']) : ''; ?>">
                        </div>

                        <!-- Nombre Comercial -->
                        <div class="form-group col-md-6">
                            <label for="nombre_comercial">Nombre Comercial</label>
                            <input type="text" name="nombre_comercial" id="nombre_comercial" class="form-control"
                                value="<?php echo isset($meta['nombre_comercial']) ? htmlspecialchars($meta['nombre_comercial']) : ''; ?>">
                        </div>

                        <!-- Tipo Documento -->
                        <div class="form-group col-md-6">
                            <label for="tipo_documento">Tipo Documento</label>
                            <select name="tipo_documento" id="tipo_documento" class="form-control" required>
                                <option value="">-- SELECCIONE --</option>
                                <?php foreach ($documentos as $doc): ?>
                                <option value="<?php echo $doc['codigo']; ?>"
                                    <?php echo (isset($meta['tipo_documento']) && $meta['tipo_documento'] == $doc['codigo']) ? 'selected' : ''; ?>>
                                    <?php echo $doc['valor']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- DNI / NIT -->
                        <div class="form-group col-md-6">
                            <label for="dni">Número de Documento</label>
                            <input type="text" name="dni" id="dni" class="form-control" required
                                value="<?php echo isset($meta['dni']) ? htmlspecialchars($meta['dni']) : ''; ?>">
                        </div>

                        <!-- Teléfono 1 -->
                        <div class="form-group col-md-6">
                            <label for="telefono1">Teléfono 1</label>
                            <input type="text" name="telefono1" id="telefono1" class="form-control"
                                value="<?php echo isset($meta['telefono1']) ? htmlspecialchars($meta['telefono1']) : ''; ?>">
                        </div>

                        <!-- Teléfono 2 -->
                        <div class="form-group col-md-6">
                            <label for="telefono2">Teléfono 2</label>
                            <input type="text" name="telefono2" id="telefono2" class="form-control"
                                value="<?php echo isset($meta['telefono2']) ? htmlspecialchars($meta['telefono2']) : ''; ?>">
                        </div>

                        <!-- Correo -->
                        <div class="form-group col-md-6">
                            <label for="correo">Correo</label>
                            <input type="email" name="correo" id="correo" class="form-control"
                                value="<?php echo isset($meta['correo']) ? htmlspecialchars($meta['correo']) : ''; ?>">
                        </div>

                        <!-- Estatus -->
                        <div class="form-group col-md-6">
                            <label for="estatus">Estatus</label>
                            <select name="estatus" id="estatus" class="form-control">
                                <option value="ACTIVO" <?= (isset($meta['estatus']) && $meta['estatus'] == 'ACTIVO') ? 'selected' : ''; ?>>ACTIVO</option>
                                <option value="INACTIVO" <?= (isset($meta['estatus']) && $meta['estatus'] == 'INACTIVO') ? 'selected' : ''; ?>>INACTIVO</option>
                            </select>
                        </div>

                        <!-- Asesor -->
                        <div class="form-group col-md-6">
                            <label for="asesor">Asesor Handy Plast</label>
                            <input type="text" name="asesor" id="asesor" class="form-control"
                                value="<?php echo isset($meta['asesor']) ? htmlspecialchars($meta['asesor']) : ''; ?>">
                        </div>

                        <!-- Plazo de Pago -->
                        <div class="form-group col-md-6">
                            <label for="plazo_pago_dias">Plazo de Pago (días)</label>
                            <input type="number" name="plazo_pago_dias" id="plazo_pago_dias" class="form-control"
                                value="<?php echo isset($meta['plazo_pago_dias']) ? (int)$meta['plazo_pago_dias'] : 0; ?>">
                        </div>

                        <!-- Listas de precio -->
                        <div class="form-group col-md-6">
                            <label for="listas_precio_habilitadas">Listas de Precio (1 a 5)</label>
                            <input type="number" name="listas_precio_habilitadas" id="listas_precio_habilitadas" class="form-control"
                                min="1" max="5"
                                value="<?php echo isset($meta['listas_precio_habilitadas']) ? (int)$meta['listas_precio_habilitadas'] : 1; ?>">
                        </div>

                        <!-- Tiene Sedes -->
                        <div class="form-group col-md-6">
                            <label for="tiene_sedes">¿Tiene Sedes?</label>
                            <select name="tiene_sedes" id="tiene_sedes" class="form-control">
                                <option value="0" <?= (isset($meta['tiene_sedes']) && $meta['tiene_sedes'] == 0) ? 'selected' : ''; ?>>NO</option>
                                <option value="1" <?= (isset($meta['tiene_sedes']) && $meta['tiene_sedes'] == 1) ? 'selected' : ''; ?>>SÍ</option>
                            </select>
                        </div>

                        <!-- Dirección general si no hay sedes -->
                        <div class="form-group col-md-12">
                            <label for="direccion_principal">Dirección Principal (si no tiene sedes)</label>
                            <input type="text" name="direccion_principal" id="direccion_principal" class="form-control"
                                value="<?php echo isset($meta['direccion_principal']) ? htmlspecialchars($meta['direccion_principal']) : ''; ?>">
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
$('#savecliente').submit(function(e) {
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
                    }).then(() => location.reload());
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