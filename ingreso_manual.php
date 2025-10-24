    <?php include("conexionfin.php"); ?>

    <div class="container-fluid">
        <h4 class="mb-4">Ingreso Manual de Compras</h4>

        <div class="card p-3 mb-3">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Proveedor</label>
                    <select id="proveedor" class="form-control">
                        <option value="">-- Seleccione proveedor --</option>
                        <?php
                        $res = $conexion->query("SELECT id, nombre_proveedor FROM proveedores ORDER BY nombre_proveedor");
                        while ($r = $res->fetch_assoc()) {
                            echo "<option value='{$r['id']}'>{$r['nombre_proveedor']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Fecha</label>
                    <input type="date" id="fecha" class="form-control" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-3">
                    <label>Almacén</label>
                    <select id="almacen_id" class="form-control">
                        <option value="">-- Seleccione almacen --</option>
                        <?php
                        $res = $conexion->query("SELECT id, nombre FROM almacenes ORDER BY nombre");
                        while ($a = $res->fetch_assoc()) {
                            echo "<option value='{$a['id']}'>{$a['nombre']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Observación</label>
                    <input type="text" id="observacion" class="form-control" placeholder="Ej: Ajuste, ingreso manual...">
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
                        <th>Unid. x Caja/paca/kg</th>
                        <th>Cajas/Paca/Bob</th>
                        <th>Unds/Kg</th>
                        <th>Lote</th>
                        <th>Costo Unitario</th>
                        <th>Costo Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <div class="col-md-4 mb-2">
                <button id="btnAdd" class="btn btn-primary btn-sm">➕ Agregar Producto</button>
            </div>
            <div class="col-md-4">
                <button id="btnGuardar" class="btn btn-success">💾 Guardar Ingreso Manual</button>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            // ➕ Agregar fila
            $('#btnAdd').click(function() {
                let fila = `<tr>
                <td>
                    <select class="form-control form-control-sm producto">
                        <option value="">-- Seleccione --</option>
                        <?php
                        $resProd = $conexion->query("
                            SELECT id_producto, descripcion, umb,
                                CASE WHEN relacion = 'KG' THEN peso_kg_paca_caja
                                        ELSE und_embalaje_minima END AS und_embalaje_minima
                            FROM producto ORDER BY descripcion
                        ");
                        while ($p = $resProd->fetch_assoc()) {
                            echo "<option value='{$p['id_producto']}' 
                                        data-umb='{$p['umb']}' 
                                        data-emb='{$p['und_embalaje_minima']}'>{$p['descripcion']}</option>";
                        }
                        ?>
                    </select>
                </td>
                <td class="umb"></td>
                <td class="emb text-center"></td>
                <td><input type="number" min="0" class="form-control form-control-sm cajas" value="0"></td>
                <td><input type="number" min="0" class="form-control form-control-sm unidades" value="0"></td>
                <td><input type="text" class="form-control form-control-sm lote"></td>
                <td><input type="number" min="0" step="0.01" class="form-control form-control-sm costo_unit" value="0.00"></td>
                <td class="total text-end">0.00</td>
                <td><button class="btn btn-danger btn-sm btnDel">🗑️</button></td>
            </tr>`;
                $('#tablaProductos tbody').append(fila);
            });

            // 🗑️ Eliminar fila
            $(document).on('click', '.btnDel', function() {
                $(this).closest('tr').remove();
            });

            // 🧾 Actualizar UMB y embalaje
            $(document).on('change', '.producto', function() {
                let umb = $('option:selected', this).data('umb') || '';
                let emb = parseFloat($('option:selected', this).data('emb')) || 0;
                let tr = $(this).closest('tr');
                tr.find('.umb').text(umb);
                tr.find('.emb').text(emb);
                tr.find('.cajas, .unidades').val(0);
            });

            // 🔁 Calcular unidades ↔ cajas
            $(document).on('input', '.cajas', function() {
                let tr = $(this).closest('tr');
                let cajas = parseFloat($(this).val()) || 0;
                let emb = parseFloat(tr.find('.emb').text()) || 1;
                tr.find('.unidades').val((cajas * emb).toFixed(0)).trigger('input');
            });

            $(document).on('input', '.unidades', function() {
                let tr = $(this).closest('tr');
                let unidades = parseFloat($(this).val()) || 0;
                let emb = parseFloat(tr.find('.emb').text()) || 1;
                tr.find('.cajas').val((unidades / emb).toFixed(2));
            });

            // 💰 Calcular total por fila
            $(document).on('input', '.unidades, .costo_unit', function() {
                let tr = $(this).closest('tr');
                let unidades = parseFloat(tr.find('.unidades').val()) || 0;
                let cajas = parseFloat(tr.find('.cajas').val()) || 0;
                let costo = parseFloat(tr.find('.costo_unit').val()) || 0;
                tr.find('.total').text((cajas * costo).toFixed(2));
            });

            // 💾 Guardar ingreso manual
            $('#btnGuardar').click(function() {
                let proveedor = $('#proveedor').val();
                let fecha = $('#fecha').val();
                let observacion = $('#observacion').val();
                let almacen_id = $('#almacen_id').val();
                let detalle = [];

                $('#tablaProductos tbody tr').each(function() {
                    let producto = $(this).find('.producto').val();
                    let unidades = parseFloat($(this).find('.unidades').val()) || 0;
                    let cajas = parseFloat($(this).find('.cajas').val()) || 0;
                    let lote = $(this).find('.lote').val();
                    let costo = parseFloat($(this).find('.costo_unit').val()) || 0;

                    if (producto && unidades > 0) {
                        detalle.push({
                            producto,
                            unidades,
                            cajas,
                            lote,
                            costo
                        });
                    }
                });

                if (!almacen_id) {
                    alert("Seleccione un almacén");
                    return;
                }
                if (detalle.length == 0) {
                    alert("Agregue al menos un producto");
                    return;
                }

                $.ajax({
                    url: 'ajax.php?action=save_ingreso_manual',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        proveedor,
                        fecha,
                        observacion,
                        almacen_id,
                        detalle: JSON.stringify(detalle)
                    },
                    success: function(res) {
                        Swal.fire({
                            title: 'Éxito!',
                            text: res.message,
                            icon: 'success',
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'OK'
                        }).then(() => location.reload());
                    }
                });
            });
        });
    </script>