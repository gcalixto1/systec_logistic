<?php
include('conexionfin.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$meta = array();

if ($id > 0) {
    $query = $conexion->query("SELECT * FROM clientes WHERE id = $id");
    if ($query && $query->num_rows > 0) {
        $meta = $query->fetch_assoc();
    }
}
?>

<div class="container-fluid">
    <div class="card">
        <form class="form form-material" method="post" action="#" name="savecliente" id="savecliente">
            <div id="save"></div>
            <div class="form-body">
                <div class="card-body">
                    <div class="row">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">

                        <!-- NIT -->
                        <div class="form-group col-md-6">
                            <label for="nit">NIT</label>
                            <input type="text" name="nit" id="nit" class="form-control" required
                                value="<?php echo isset($meta['nit']) ? htmlspecialchars($meta['nit']) : ''; ?>">
                        </div>

                        <!-- Nombre Cliente -->
                        <div class="form-group col-md-6">
                            <label for="nombre_cliente">Nombre del Cliente</label>
                            <input type="text" name="nombre_cliente" id="nombre_cliente" class="form-control" required
                                value="<?php echo isset($meta['nombre_cliente']) ? htmlspecialchars($meta['nombre_cliente']) : ''; ?>">
                        </div>

                        <!-- Nombre Comercial -->
                        <div class="form-group col-md-6">
                            <label for="nombre_comercial">Nombre Comercial</label>
                            <input type="text" name="nombre_comercial" id="nombre_comercial" class="form-control"
                                value="<?php echo isset($meta['nombre_comercial']) ? htmlspecialchars($meta['nombre_comercial']) : ''; ?>">
                        </div>

                        <!-- Tiene Sedes -->
                        <div class="form-group col-md-3">
                            <label for="tiene_sedes">¿Tiene Sedes?</label>
                            <select name="tiene_sedes" id="tiene_sedes" class="form-control">
                                <option value="NO" <?= (isset($meta['tiene_sedes']) && $meta['tiene_sedes'] == 'NO') ? 'selected' : ''; ?>>NO</option>
                                <option value="SI" <?= (isset($meta['tiene_sedes']) && $meta['tiene_sedes'] == 'SI') ? 'selected' : ''; ?>>SÍ</option>
                            </select>
                        </div>

                        <!-- Estatus Cliente -->
                        <div class="form-group col-md-3">
                            <label for="estatus_cliente">Estatus</label>
                            <select name="estatus_cliente" id="estatus_cliente" class="form-control">
                                <option value="ACTIVO" <?= (isset($meta['estatus_cliente']) && $meta['estatus_cliente'] == 'ACTIVO') ? 'selected' : ''; ?>>ACTIVO</option>
                                <option value="INACTIVO" <?= (isset($meta['estatus_cliente']) && $meta['estatus_cliente'] == 'INACTIVO') ? 'selected' : ''; ?>>INACTIVO</option>
                            </select>
                        </div>

                        <!-- Asesor -->
                        <div class="form-group col-md-6">
                            <label for="asesor_handy_plast">Asesor Handy Plast</label>
                            <input type="text" name="asesor_handy_plast" id="asesor_handy_plast" class="form-control"
                                value="<?php echo isset($meta['asesor_handy_plast']) ? htmlspecialchars($meta['asesor_handy_plast']) : ''; ?>">
                        </div>

                        <!-- Plazo Pago -->
                        <div class="form-group col-md-3">
                            <label for="plazos_pago_dias">Plazo de Pago (días)</label>
                            <input type="number" name="plazos_pago_dias" id="plazos_pago_dias" class="form-control"
                                value="<?php echo isset($meta['plazos_pago_dias']) ? (int)$meta['plazos_pago_dias'] : 0; ?>">
                        </div>

                        <!-- Lista de Precios -->
                        <div class="form-group col-md-3">
                            <label for="listas_precio_habilitadas">Listas de Precio (1 a 5)</label>
                            <input type="number" name="listas_precio_habilitadas" id="listas_precio_habilitadas" class="form-control"
                                min="1" max="5"
                                value="<?php echo isset($meta['listas_precio_habilitadas']) ? (int)$meta['listas_precio_habilitadas'] : 1; ?>">
                        </div>

                        <!-- Nombre Sede -->
                        <div class="form-group col-md-6">
                            <label for="nombre_sede">Nombre de la Sede</label>
                            <input type="text" name="nombre_sede" id="nombre_sede" class="form-control"
                                value="<?php echo isset($meta['nombre_sede']) ? htmlspecialchars($meta['nombre_sede']) : ''; ?>">
                        </div>

                        <!-- Dirección Sede -->
                        <div class="form-group col-md-6">
                            <label for="direccion_sede">Dirección de la Sede</label>
                            <input type="text" name="direccion_sede" id="direccion_sede" class="form-control"
                                value="<?php echo isset($meta['direccion_sede']) ? htmlspecialchars($meta['direccion_sede']) : ''; ?>">
                        </div>

                        <!-- Ciudad -->
                        <div class="form-group col-md-6">
                            <label for="ciudad">Ciudad</label>
                            <input type="text" name="ciudad" id="ciudad" class="form-control"
                                value="<?php echo isset($meta['ciudad']) ? htmlspecialchars($meta['ciudad']) : ''; ?>">
                        </div>

                        <!-- Departamento -->
                        <div class="form-group col-md-6">
                            <label for="departamento">Departamento</label>
                            <input type="text" name="departamento" id="departamento" class="form-control"
                                value="<?php echo isset($meta['departamento']) ? htmlspecialchars($meta['departamento']) : ''; ?>">
                        </div>

                        <!-- Teléfono 1 -->
                        <div class="form-group col-md-3">
                            <label for="telefono1">Teléfono 1</label>
                            <input type="text" name="telefono1" id="telefono1" class="form-control"
                                value="<?php echo isset($meta['telefono1']) ? htmlspecialchars($meta['telefono1']) : ''; ?>">
                        </div>

                        <!-- Teléfono 2 -->
                        <div class="form-group col-md-3">
                            <label for="telefono2">Teléfono 2</label>
                            <input type="text" name="telefono2" id="telefono2" class="form-control"
                                value="<?php echo isset($meta['telefono2']) ? htmlspecialchars($meta['telefono2']) : ''; ?>">
                        </div>

                        <!-- Correo -->
                        <div class="form-group col-md-6">
                            <label for="correo_electronico">Correo Electrónico</label>
                            <input type="email" name="correo_electronico" id="correo_electronico" class="form-control"
                                value="<?php echo isset($meta['correo_electronico']) ? htmlspecialchars($meta['correo_electronico']) : ''; ?>">
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
});
</script>
