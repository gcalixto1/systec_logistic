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
$documento = $_GET['dni'] ?? '';
require_once("includes/class.php");
$pro = new Action();

$departamentoSeleccionado = isset($_POST['departamento']) ? $_POST['departamento'] : (isset($meta['departamento']) ? $meta['departamento'] : '');
$municipioSeleccionado = isset($_POST['municipio']) ? $_POST['municipio'] : (isset($meta['municipio']) ? $meta['municipio'] : '');

$departamentos = $pro->ListarDepartamentos();
$municipios = $departamentoSeleccionado ? $pro->ListarMunicipios($departamentoSeleccionado) : [];
$documentos = $pro->ListarDocumentos();

$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$actividades = $pro->ListarActividades($busqueda);

$codigoGeneracion = strtoupper(vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4)));
?>

<div class="container-fluid">
    <div class="card-header">
        <h4 class="card-title text-black"><i class="fa fa-box"></i> CREAR NOTA DE CREDITO</h4>
    </div>
    <br>
    <div class="card">
        <form class="form form-material" method="post" action="#" name="savecliente" id="savecliente">
            <div id="save"></div>
            <div class="form-body">
                <div class="card-body">
                    <div class="row">
                        <input type="text" hidden name="codigoGeneracion" value="<?php echo $codigoGeneracion; ?>">
                        <div class="form-group col-md-3">
                            <label for="dni">Número de Documento</label>
                            <div class="input-group">
                                <input type="text" name="dni" id="dni" class="form-control"
                                    value="<?php echo isset($meta['dni']) ? htmlspecialchars($meta['dni']) : ''; ?>"
                                    required>
                                <div class="input-group-append">
                                    <button class="btn btn-info" type="button" id="buscar_dni"><i
                                            class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-9"></div>
                        <div class="form-group col-md-3">
                            <label for="nombre">Nombre / Razón Social</label>
                            <input type="text" name="nombre" id="nombre" class="form-control"
                                value="<?php echo isset($meta['nombre']) ? htmlspecialchars($meta['nombre']) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="nrc">N.R.C.</label>
                            <input type="text" name="dato1" id="dato1" class="form-control"
                                value="<?php echo isset($meta['dato1']) ? htmlspecialchars($meta['dato1']) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="nombreComercial">Nombre Comercial</label>
                            <input type="text" name="dato2" id="dato2" class="form-control"
                                value="<?php echo isset($meta['dato2']) ? htmlspecialchars($meta['dato2']) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group col-md-3">
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
                        <div class="form-group col-md-3">
                            <label for="correo">Correo</label>
                            <input type="email" name="correo" id="correo" class="form-control"
                                value="<?php echo isset($meta['correo']) ? htmlspecialchars($meta['correo']) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="telefono">Teléfono</label>
                            <input type="text" name="telefono" id="telefono" maxlength="12" class="form-control"
                                value="<?php echo isset($meta['telefono']) ? htmlspecialchars($meta['telefono']) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group col-md-3">
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

                        <div class="form-group col-md-3">
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

                        <div class="form-group col-md-6">
                            <label for="direccion">Dirección</label>
                            <input type="text" name="direccion" id="direccion" class="form-control"
                                value="<?php echo isset($meta['complemento']) ? htmlspecialchars($meta['complemento']) : ''; ?>"
                                required>
                        </div>

                        <div class="form-group col-md-6"></div>
                        <div class="form-group col-md-12"></div>

                        <div class="form-group col-md-6">
                            <button class="btn btn-primary btn-md" type="button" data-toggle="modal"
                                data-target="#modalFacturas" data-dni="<?= $documento ?>">
                                <i class="fas fa-plus"></i> Documento Relacionado
                            </button>
                        </div>
                        <div class="col-md-12">
                            <table id="tablaDocumentosRelacionados" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Tipo Documento</th>
                                        <th>Tipo Generación</th>
                                        <th>Código Generación</th>
                                        <th>Fecha Generación</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Se agregan dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="descripcion">Observaciones</label>
                            <input type="text" name="descripcion" id="descripcion" class="form-control"
                                value="<?php echo isset($meta['descripcion']) ? htmlspecialchars($meta['descripcion']) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="monto">Monto</label>
                            <input type="text" name="monto" id="monto" class="form-control"
                                value="<?php echo isset($meta['monto']) ? htmlspecialchars($meta['monto']) : ''; ?>"
                                required>
                        </div>
                        <div class="form-group col-md-6"></div>
                        <div class="form-group col-md-6">
                            <button class="btn btn-success btn-md" type="submit" id="btnGuardarNotaCredito">
                                <i class="fas fa-save"></i> Guardar Nota de Crédito
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="modal fade" id="modalFacturas" tabindex="-1" role="dialog" aria-labelledby="modalFacturasLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalFacturasLabel">Documentos Relacionados</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="contenidoModalFacturas">
                    <div class="text-center">
                        <i class="fa fa-spinner fa-spin fa-2x"></i> Cargando datos...
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<script>
$('#savecliente').submit(function(e) {
    e.preventDefault();

    // Mostrar spinner
    function showSpinner() {
        $('#spinner').show();
    }

    // Ocultar spinner
    function hideSpinner() {
        $('#spinner').hide();
    }

    // Validación simple (puedes agregar más campos si es necesario)
    const monto = $('#monto').val().trim();
    const descripcion = $('#descripcion').val().trim();
    if (!monto || !descripcion) {
        Swal.fire({
            title: 'Campos obligatorios',
            text: 'Debes completar todos los campos requeridos.',
            icon: 'warning'
        });
        return;
    }

    showSpinner();

    // Recopilar documentos relacionados
    const documentos = [];
    $('#tablaDocumentosRelacionados tbody tr').each(function() {
        const codigo = $(this).find('td').eq(2).text().trim();
        const fecha = $(this).find('td').eq(3).text().trim();
        documentos.push({
            codigo,
            fecha
        });
    });

    // Preparar FormData
    const formElement = document.getElementById('savecliente');
    const formData = new FormData(formElement);
    formData.append('documentos', JSON.stringify(documentos));

    // Clonamos formData para la segunda petición (DTE)
    const formData2 = new FormData(formElement);
    formData2.append('documentos', JSON.stringify(documentos));

    // Enviar primera petición (guardar en BD)
    $.ajax({
        url: 'ajax.php?action=saveNotasCredito',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(resp) {
            if (resp.success) {
                // Enviar segunda petición (procesar DTE)
                $.ajax({
                    url: 'dteNC.php',
                    method: 'POST',
                    data: formData2,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(respDte) {
                        hideSpinner();
                        if (respDte.success) {
                            const codigo = respDte.codigo_generacion || '';
                            Swal.fire({
                                title: '¡Éxito!',
                                text: 'NOTA DE CRÉDITO guardada y procesada correctamente.',
                                icon: 'success',
                                confirmButtonText: 'Ver Comprobante'
                            }).then(() => {
                                window.open(
                                    `facturaElectronica.php?codigo=${encodeURIComponent(codigo)}&tipo=NC`,
                                    '_blank'
                                );
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error en DTE',
                                text: respDte.message ||
                                    'Ocurrió un error al procesar el DTE.',
                                icon: 'error'
                            });
                        }
                    },
                    error: function() {
                        hideSpinner();
                        Swal.fire({
                            title: 'Error',
                            text: 'No se pudo procesar el DTE.',
                            icon: 'error'
                        });
                    }
                });
            } else {
                hideSpinner();
                Swal.fire({
                    title: 'Error',
                    text: resp.message || 'Error al guardar la nota de crédito.',
                    icon: 'error'
                });
            }
        },
        error: function() {
            hideSpinner();
            Swal.fire({
                title: 'Error',
                text: 'No se pudo conectar con el servidor.',
                icon: 'error'
            });
        }
    });
});



$('#new_NC').click(function() {
    var dni = $('#dni').val().trim();
    uni_modal_notasCredito("Listado de Ventas",
        "lista_ventas.php?documento=" + dni);
})
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

$('#buscar_dni').click(function() {
    var dni = $('#dni').val().trim();
    if (dni === '') {
        Swal.fire('Advertencia', 'Ingrese un número de documento para buscar.', 'warning');
        return;
    }

    $.ajax({
        url: 'buscar_cliente_por_dni.php',
        method: 'GET',
        data: {
            dni: dni
        },
        dataType: 'json',
        success: function(data) {
            if (data && data.success) {
                $('#nombre').val(data.nombre);
                $('#dato1').val(data.dato1);
                $('#dato2').val(data.dato2);
                $('#dato3').val(data.dato3);
                $('#correo').val(data.correo);
                $('#telefono').val(data.telefono);
                $('#departamento').val(data.departamento).trigger('change');
                $('#direccion').val(data.complemento);

                // Cargar municipios correspondientes al departamento
                $.ajax({
                    url: 'cargar_municipios.php?id_departamento=' + data.departamento,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        var municipioSelect = $('#municipio');
                        municipioSelect.empty().append(
                            '<option value="">-- SELECCIONE MUNICIPIO --</option>');
                        $.each(response, function(index, municipio) {
                            municipioSelect.append($('<option>', {
                                value: municipio.codigo,
                                text: municipio.valor,
                                selected: municipio.codigo === data
                                    .municipio
                            }));
                        });
                    }
                });
            } else {
                Swal.fire('No encontrado', 'No se encontraron datos para el documento ingresado.',
                    'info');
            }
        },
        error: function() {
            Swal.fire('Error', 'Ocurrió un error al buscar el cliente.', 'error');
        }
    });
});

$('#tablaVentas').DataTable({
    language: {
        url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
    }
});

// // Acción al hacer clic en "Seleccionar"
// $(document).on('click', '.seleccionar_factura', function() {
//     var codigo = $(this).data('codigo');
//     $('#modalFacturas').modal('hide');
// });

$('#modalFacturas').on('show.bs.modal', function(event) {
    const button = $(event.relatedTarget);
    const dni = $('#dni').val().trim();

    $('#contenidoModalFacturas').html(
        '<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i> Cargando...</div>');

    $.ajax({
        url: 'lista_ventas.php',
        method: 'GET',
        data: {
            documento: dni
        },
        success: function(data) {
            $('#contenidoModalFacturas').html(data);
        },
        error: function() {
            $('#contenidoModalFacturas').html(
                '<div class="alert alert-danger">Error al cargar los datos.</div>');
        }
    });
});
</script>