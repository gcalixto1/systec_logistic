<?php
include('conexionfin.php');

require_once("includes/class.php");
$pro = new Action();

$id = isset($_GET['id_producto']) ? $_GET['id_producto'] : '';

$meta = array();

if (!empty($id)) {
    $query = $conexion->query("SELECT * FROM producto WHERE id_producto = '$id'");
    if ($query) {
        $producto = $query->fetch_assoc();
        if ($producto) {
            $meta = $producto;
        }
    }
}

$actividades = $pro->Listaretiquetas();
$tipos = $pro->Listartipos();
$familias = $pro->ListarCategorias();
$umbs = $pro->Listarumbs();
$relaciones = $pro->Listarrelaciones();
?>

<div class="container-fluid">
    <div class="card">
        <form class="form form-material" method="post" action="#" enctype="multipart/form-data" name="saveproductos" id="saveproductos">
            <div id="save"></div>
            <div class="form-body">
                <div class="card-body">
                    <div class="row">

                        <!-- Hidden -->
                        <input type="hidden" name="id_producto" value="<?php echo isset($meta['id_producto']) ? htmlspecialchars($meta['id_producto']) : '' ?>">

                        <!-- Código del producto -->
                        <div class="form-group col-md-3">
                            <label for="cod_producto">Código Producto</label>
                            <input type="text" name="cod_producto" id="cod_producto" class="form-control" 
                                value="<?php echo isset($meta['cod_producto']) ? htmlspecialchars($meta['cod_producto']) : ''; ?>" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="cod_producto">STR  ID</label>
                            <input type="text" name="str_id" id="str_id" class="form-control" 
                                value="<?php echo isset($meta['str_id']) ? htmlspecialchars($meta['str_id']) : ''; ?>" required>
                        </div>

                        <!-- Descripción -->
                        <div class="form-group col-md-6">
                            <label for="descripcion">Descripción</label>
                            <input type="text" name="descripcion" id="descripcion" class="form-control"
                                value="<?php echo isset($meta['descripcion']) ? htmlspecialchars($meta['descripcion']) : ''; ?>" required>
                        </div>

                        <!-- Etiqueta -->
                        <div class="form-group col-md-4">
                            <label for="etiqueta">Seleccionar Etiqueta</label>
                            <select name="etiqueta" id="etiqueta" class="form-control" required>
                                <option value="">-- SELECCIONE --</option>
                                <?php foreach ($actividades as $act): ?>
                                <option value="<?php echo $act['etiqueta']; ?>"
                                    <?php echo (isset($meta['etiqueta']) && $meta['etiqueta'] == $act['etiqueta']) ? 'selected' : ''; ?>>
                                    <?php echo $act['id'] . ' - ' . $act['etiqueta']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Tipo -->
                        <div class="form-group col-md-4">
                            <label for="tipo">Seleccionar Tipo</label>
                            <select name="tipo" id="tipo" class="form-control" required>
                                <option value="">-- SELECCIONE --</option>
                                <?php foreach ($tipos as $act): ?>
                                <option value="<?php echo $act['tipo']; ?>"
                                    <?php echo (isset($meta['tipo']) && $meta['tipo'] == $act['tipo']) ? 'selected' : ''; ?>>
                                    <?php echo $act['id'] . ' - ' . $act['tipo']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Familia -->
                        <div class="form-group col-md-4">
                            <label for="familia">Seleccionar Familia</label>
                            <select name="familia" id="familia" class="form-control" required>
                                <option value="">-- SELECCIONE --</option>
                                <?php foreach ($familias as $act): ?>
                                <option value="<?php echo $act['categoria_des']; ?>"
                                    <?php echo (isset($meta['familia']) && $meta['familia'] == $act['categoria_des']) ? 'selected' : ''; ?>>
                                    <?php echo $act['categoria_id'] . ' - ' . $act['categoria_des']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Calibre -->
                        <div class="form-group col-md-4">
                            <label for="calibre">Calibre</label>
                            <input type="text" name="calibre" id="calibre" class="form-control"
                                value="<?php echo isset($meta['calibre']) ? htmlspecialchars($meta['calibre']) : ''; ?>">
                        </div>

                        <!-- Unidad embalaje mínima -->
                        <div class="form-group col-md-4">
                            <label for="und_embalaje_minima">Und. Embalaje Mínima</label>
                            <input type="number" name="und_embalaje_minima" id="und_embalaje_minima" class="form-control"
                                value="<?php echo isset($meta['und_embalaje_minima']) ? $meta['und_embalaje_minima'] : ''; ?>">
                        </div>

                        <!-- Peso KG -->
                        <div class="form-group col-md-4">
                            <label for="peso_kg">Peso KG</label>
                            <input type="text" name="peso_kg" id="peso_kg" class="form-control"
                                value="<?php echo isset($meta['peso_kg']) ? $meta['peso_kg'] : ''; ?>">
                        </div>

                        <!-- Peso por Paca/Caja -->
                        <div class="form-group col-md-4">
                            <label for="peso_kg_paca_caja">Peso KG por Paca/Caja</label>
                            <input type="text" name="peso_kg_paca_caja" id="peso_kg_paca_caja" class="form-control"
                                value="<?php echo isset($meta['peso_kg_paca_caja']) ? $meta['peso_kg_paca_caja'] : ''; ?>">
                        </div>

                        <!-- UMB -->
                        <div class="form-group col-md-4">
                            <label for="umb">Seleccionar UMB</label>
                            <select name="umb" id="umb" class="form-control" required>
                                <option value="">-- SELECCIONE --</option>
                                <?php foreach ($umbs as $act): ?>
                                <option value="<?php echo $act['umb']; ?>"
                                    <?php echo (isset($meta['umb']) && $meta['umb'] == $act['umb']) ? 'selected' : ''; ?>>
                                    <?php echo $act['id'] . ' - ' . $act['umb']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Micraje -->
                        <div class="form-group col-md-4">
                            <label for="micraje">Micraje</label>
                            <input type="number" name="micraje" id="micraje" class="form-control"
                                value="<?php echo isset($meta['micraje']) ? htmlspecialchars($meta['micraje']) : ''; ?>">
                        </div>

                        <!-- Referencia 1 -->
                        <div class="form-group col-md-4">
                            <label for="ref_1">Referencia 1</label>
                            <input type="number" name="ref_1" id="ref_1" class="form-control"
                                value="<?php echo isset($meta['ref_1']) ? htmlspecialchars($meta['ref_1']) : ''; ?>">
                        </div>

                        <!-- Referencia 2 -->
                        <div class="form-group col-md-4">
                            <label for="ref_2">Referencia 2</label>
                            <input type="number" name="ref_2" id="ref_2" class="form-control"
                                value="<?php echo isset($meta['ref_2']) ? htmlspecialchars($meta['ref_2']) : ''; ?>">
                        </div>

                        <!-- Ref Tubo -->
                        <div class="form-group col-md-4">
                            <label for="ref_tubo">Referencia de tubo</label>
                            <input type="text" name="ref_tubo" id="ref_tubo" class="form-control"
                                value="<?php echo isset($meta['ref_tubo']) ? $meta['ref_tubo'] : ''; ?>">
                        </div>

                        <!-- Peso Tubo -->
                        <div class="form-group col-md-4">
                            <label for="peso_tubo">Peso de tubo</label>
                            <input type="text" name="peso_tubo" id="peso_tubo" class="form-control"
                                value="<?php echo isset($meta['peso_tubo']) ? $meta['peso_tubo'] : ''; ?>">
                        </div>

                        <!-- Relaciones -->
                        <div class="form-group col-md-4">
                            <label for="relacion">Seleccionar Relacion</label>
                            <select name="relacion" id="relacion" class="form-control" required>
                                <option value="">-- SELECCIONE --</option>
                                <?php foreach ($relaciones as $act): ?>
                                <option value="<?php echo $act['relacion']; ?>"
                                    <?php echo (isset($meta['relacion']) && $meta['relacion'] == $act['relacion']) ? 'selected' : ''; ?>>
                                    <?php echo $act['id'] . ' - ' . $act['relacion']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Tiempo de producción por paca -->
                        <div class="form-group col-md-4">
                            <label for="tiempo_produccion_paca">Tiempo de producción/Paca</label>
                            <input type="text" name="tiempo_produccion_paca" id="tiempo_produccion_paca" class="form-control"
                                value="<?php echo isset($meta['tiempo_produccion_paca']) ? $meta['tiempo_produccion_paca'] : ''; ?>">
                        </div>

                        <!-- Stock mínimo -->
                        <div class="form-group col-md-4">
                            <label for="stock_minimo">Stock Mínimo</label>
                            <input type="number" name="stock_minimo" id="stock_minimo" class="form-control"
                                value="<?php echo isset($meta['stock_minimo']) ? $meta['stock_minimo'] : ''; ?>">
                        </div>

                        <!-- Precio sin remision -->
                        <div class="form-group col-md-4">
                            <label for="precio_lista_5">Precio remisonado(lista 5)</label>
                            <input type="number" step="0.01" name="precio_lista_5" id="precio_lista_5" class="form-control"
                                value="<?php echo isset($meta['precio_lista_5']) ? $meta['precio_lista_5'] : ''; ?>">
                        </div>

                        <!-- Precio remision -->
                        <div class="form-group col-md-4">
                            <label for="precio_remision_lista_5">Precio facturado (lista 5)</label>
                            <input type="number" step="0.01" name="precio_remision_lista_5" id="precio_remision_lista_5" class="form-control"
                                value="<?php echo isset($meta['precio_remision_lista_5']) ? $meta['precio_remision_lista_5'] : ''; ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="lead_time">Lead Time (Meses)</label>
                            <input type="text" step="0.01" name="lead_time" id="lead_time" class="form-control"
                                value="<?php echo isset($meta['lead_time']) ? $meta['lead_time'] : ''; ?>">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$('#saveproductos').submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        url: 'ajax.php?action=save_productos',
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(resp) {
            if (resp == 1) {
                Swal.fire({
                    title: 'Guardado!',
                    text: 'Producto guardado correctamente.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: 'No se pudo guardar el producto.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        }
    });
});

// $(document).ready(function () {
//     $("#precio_lista_5").on("input", function () {
//         let precio = parseFloat($(this).val()) || 0;
//         let precioRemision = precio * 1.19;

//         // Redondear al múltiplo de 100 más cercano
//         let precioAprox = Math.round(precioRemision / 100) * 100;

//         $("#precio_remision_lista_5").val(precioAprox);
//     });
// });
</script>
