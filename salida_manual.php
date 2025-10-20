<?php include("conexionfin.php"); ?>

<div class="container-fluid">
    <h4 class="mb-4">Salida Manual de Productos</h4>

    <div class="card p-3 mb-3">
        <div class="row mb-3">
            <div class="col-md-4">
                <label>Fecha</label>
                <input type="date" id="fecha" class="form-control" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-4">
                <label>Almacén</label>
                <select id="almacen_id" class="form-control">
                    <option value="">-- Seleccione almacén --</option>
                    <?php
                    $res = $conexion->query("SELECT id, nombre FROM almacenes ORDER BY nombre");
                    while ($a = $res->fetch_assoc()) {
                        echo "<option value='{$a['id']}'>{$a['nombre']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-4">
                <label>Observación</label>
                <input type="text" id="observacion" class="form-control" placeholder="Ej: Ajuste, salida manual...">
            </div>
        </div>
    </div>

    <!-- Tabla de productos -->
    <div class="card p-3">
        <h6>Detalle de Productos</h6>
        <table class="table table-bordered" id="tablaProductos">
            <thead class="table-light">
                <tr>
                    <th>Producto</th>
                    <th>UMB</th>
                    <th>Unidad Embalaje</th>
                    <th>Cantidad</th>
                    <th>Lote</th>
                    <th>Costo Unitario</th>
                    <th>Costo Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <div class="col-md-4">
            <button id="btnAdd" class="btn btn-primary btn-sm">➕ Agregar Producto</button>
        </div>
        <br>
        <div class="col-md-4">
            <button id="btnGuardar" class="btn btn-success">💾 Guardar Salida Manual</button>
        </div>
    </div>
</div>

<script>

    // Agregar fila vacía
    $('#btnAdd').click(function() {
        let fila = `<tr>
            <td>
                <select class="form-control form-control-sm producto">
                    <option value="">-- Seleccione --</option>
                    <?php
                    $resProd = $conexion->query("SELECT id_producto, descripcion, umb, und_embalaje_minima FROM producto ORDER BY descripcion");
                    while ($p = $resProd->fetch_assoc()) {
                        echo "<option value='{$p['id_producto']}' data-umb='{$p['umb']}' data-emb='{$p['und_embalaje_minima']}'>{$p['descripcion']}</option>";
                    }
                    ?>
                </select>
            </td>
            <td class="umb"></td>
            <td class="emb"></td>
            <td><input type="number" min="0" class="form-control form-control-sm cantidad" value="0"></td>
            <td>
                <select class="form-control form-control-sm lote">
                    <option value="">-- Seleccione lote --</option>
                </select>
            </td>
            <td><input type="number" min="0" step="0.01" class="form-control form-control-sm costo_unit" value="0.00"></td>
            <td class="total">0.00</td>
            <td><button class="btn btn-danger btn-sm btnDel">🗑️</button></td>
        </tr>`;
        $('#tablaProductos tbody').append(fila);
    });

    // Eliminar fila
    $(document).on('click', '.btnDel', function() {
        $(this).closest('tr').remove();
    });

    // Actualizar UMB y embalaje + cargar lotes disponibles
    $(document).on('change', '.producto', function() {
        let tr = $(this).closest('tr');
        let productoId = $(this).val();
        let umb = $('option:selected', this).data('umb') || '';
        let emb = $('option:selected', this).data('emb') || '';
        tr.find('.umb').text(umb);
        tr.find('.emb').text(emb);

        let almacen_id = $('#almacen_id').val();
        if (!productoId || !almacen_id) return;

        // Cargar lotes desde movimientos_inventario
        $.ajax({
            url: 'ajax.php?action=get_lotes_producto',
            type: 'POST',
            dataType: 'json',
            data: { producto_id: productoId, almacen_id: almacen_id },
            success: function(res) {
                let selectLote = tr.find('.lote');
                selectLote.html('<option value="">-- Seleccione lote --</option>');
                if(res.success){
                    res.lotes.forEach(l => {
                        selectLote.append(`<option value="${l.lote}">${l.lote} (Disponible: ${l.cantidad})</option>`);
                    });
                }
            }
        });
    });

    // Actualizar total
    $(document).on('input', '.cantidad, .costo_unit', function() {
        let tr = $(this).closest('tr');
        let cantidad = parseFloat(tr.find('.cantidad').val()) || 0;
        let costo = parseFloat(tr.find('.costo_unit').val()) || 0;
        tr.find('.total').text((cantidad * costo).toFixed(2));
    });

    // Guardar salida manual
    $('#btnGuardar').click(function() {
        let fecha = $('#fecha').val();
        let observacion = $('#observacion').val();
        let almacen_id = $('#almacen_id').val();
        let detalle = [];

        $('#tablaProductos tbody tr').each(function() {
            let producto = $(this).find('.producto').val();
            let cantidad = $(this).find('.cantidad').val();
            let lote = $(this).find('.lote').val();
            let costo = $(this).find('.costo_unit').val();

            if (producto && cantidad > 0) {
                detalle.push({ producto, cantidad, lote, costo });
            }
        });

        if (!almacen_id) { alert("Seleccione un almacén"); return; }
        if (detalle.length == 0) { alert("Agregue al menos un producto"); return; }

        $.ajax({
            url: 'ajax.php?action=save_salida_manual',
            type: 'POST',
            dataType: 'json',
            data: { fecha, observacion, almacen_id, detalle: JSON.stringify(detalle) },
            success: function(res) {
                alert(res.message);
                if (res.success) location.reload();
            }
        });
    });

</script>
