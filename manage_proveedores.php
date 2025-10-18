<?php
include('conexionfin.php');

$id = isset($_GET['id']) ? $_GET['id'] : '';

$meta = array();

if (!empty($id)) {
    $id = intval($id);
    $query = $conexion->query("SELECT * FROM proveedores WHERE id = $id");
    if ($query) {
        $proveedor = $query->fetch_assoc();
        if ($proveedor) {
            $meta = $proveedor;
        }
    }
}

require_once("includes/class.php");
$pro = new Action();

// Listas de catálogos
$documentos = $pro->ListarDocumentos();
?>
<div class="container-fluid">
    <div class="card">
        <form class="form form-material" method="post" action="#" name="saveproveedor" id="saveproveedor">
            <div id="save"></div>
            <div class="form-body">
                <div class="card-body">
                    <div class="row">
                        <input type="hidden" name="id"
                            value="<?php echo $id ?>">

                        <div class="form-group col-md-6">
                            <label for="tipo_id">Tipo ID</label>
                            <select name="tipo_id" id="tipo_id" class="form-control" required>
                                <option value="">-- SELECCIONE --</option>
                                <option value="NIT" <?php echo (isset($meta['tipo_id']) && $meta['tipo_id'] == 'NIT') ? 'selected' : ''; ?>>NIT</option>
                                <option value="PASAPORTE" <?php echo (isset($meta['tipo_id']) && $meta['tipo_id'] == 'PASAPORTE') ? 'selected' : ''; ?>>PASAPORTE</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="documento">Documento</label>
                            <input type="text" name="documento" id="documento" class="form-control"
                                value="<?php echo isset($meta['documento']) ? htmlspecialchars($meta['documento']) : ''; ?>" required>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="proveedor">Nombre del Proveedor</label>
                            <input type="text" name="nombre_proveedor" id="nombre_proveedor" class="form-control"
                                value="<?php echo isset($meta['nombre_proveedor']) ? htmlspecialchars($meta['nombre_proveedor']) : ''; ?>" required>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="direccion">Dirección</label>
                            <input type="text" name="direccion" id="direccion" class="form-control"
                                value="<?php echo isset($meta['direccion']) ? htmlspecialchars($meta['direccion']) : ''; ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="plazo">Plazo (días)</label>
                            <input type="number" name="plazo" id="plazo" class="form-control"
                                value="<?php echo isset($meta['plazo']) ? htmlspecialchars($meta['plazo']) : '0'; ?>" min="0">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="celular">Celular</label>
                            <input type="text" name="celular" id="celular" class="form-control"
                                value="<?php echo isset($meta['celular']) ? htmlspecialchars($meta['celular']) : ''; ?>">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" name="email" id="email" class="form-control"
                                value="<?php echo isset($meta['email']) ? htmlspecialchars($meta['email']) : ''; ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="cupo_credito">Cupo de Crédito</label>
                            <input type="number" name="cupo_credito" id="cupo_credito" class="form-control" step="0.01"
                                value="<?php echo isset($meta['cupo_credito']) ? htmlspecialchars($meta['cupo_credito']) : '0.00'; ?>">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="nombre_contacto">Nombre de Contacto</label>
                            <input type="text" name="nombre_contacto" id="nombre_contacto" class="form-control"
                                value="<?php echo isset($meta['nombre_contacto']) ? htmlspecialchars($meta['nombre_contacto']) : ''; ?>">
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
                text: 'Todos los campos obligatorios deben estar completos.',
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
                        text: 'El registro se guardó correctamente.',
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
</script>
