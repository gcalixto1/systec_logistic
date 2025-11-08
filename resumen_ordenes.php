<?php
include('conexionfin.php');
?>
<div class="container-fluids">
    <h4 class="text-center mb-4 fw-bold text-primary">📋 Órdenes de Producción - Handyplast</h4>

    <!-- 🔹 FILTROS -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Máquina</label>
                    <select id="filtro_maquina" class="form-control form-select-sm">
                        <option value="">Todas</option>
                        <?php
                        $maquinas = $conexion->query("SELECT id, cod_mac, maquina FROM lista_maquina ORDER BY maquina ASC");
                        while ($m = $maquinas->fetch_assoc()) {
                            echo "<option value='{$m['id']}'>{$m['maquina']} ({$m['cod_mac']})</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Fecha desde</label>
                    <input type="date" id="filtro_desde" class="form-control form-control-sm">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Fecha hasta</label>
                    <input type="date" id="filtro_hasta" class="form-control form-control-sm">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select id="filtro_estado" class="form-control form-select-sm">
                        <option value="">Todos</option>
                        <option value="PROGRAMADA">Programada</option>
                        <option value="EN PROCESO">En proceso</option>
                        <option value="FINALIZADA">Finalizada</option>
                    </select>
                </div>

                <div class="col-md-1 text-center">
                    <button id="btn_filtrar" class="btn btn-sm btn-primary w-100">Filtrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 🔹 TABLA DE ÓRDENES -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle" id="tabla_op">
                    <thead class="table-primary">
                        <tr class="text-center">
                            <th># O.P</th>
                            <th>Pedido</th>
                            <th>Producto</th>
                            <th>Máquina</th>
                            <th>Cant. Programada</th>
                            <th>Peso Total (kg)</th>
                            <th>Estado</th>
                            <th>Usuario</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody id="body_op">
                        <tr><td colspan="9" class="text-center text-muted">Cargando datos...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    // Cargar O.P al iniciar
    cargarOrdenes();

    // Filtro por botón
    $('#btn_filtrar').click(function(){
        cargarOrdenes();
    });

    function cargarOrdenes(){
        const filtros = {
            maquina: $('#filtro_maquina').val(),
            desde: $('#filtro_desde').val(),
            hasta: $('#filtro_hasta').val(),
            estado: $('#filtro_estado').val()
        };

        $('#body_op').html(`<tr><td colspan="9" class="text-center text-muted">Cargando datos...</td></tr>`);

        $.ajax({
            url: 'listar_ordenes_data.php',
            type: 'POST',
            data: filtros,
            dataType: 'json',
            success: function(res){
                if(res.length === 0){
                    $('#body_op').html(`<tr><td colspan="9" class="text-center text-muted">No hay resultados para los filtros seleccionados</td></tr>`);
                    return;
                }

                let html = '';
                res.forEach(op => {
                    html += `
                        <tr class="text-center">
                            <td class="fw-bold text-primary">${op.numero_op}</td>
                            <td>${op.pedido_id}</td>
                            <td>${op.producto}</td>
                            <td>${op.maquina}</td>
                            <td>${op.cantidad_programada}</td>
                            <td>${op.peso_total_kg}</td>
                            <td><span class="badge ${estadoColor(op.estado)}">${op.estado}</span></td>
                            <td>${op.usuario_programo}</td>
                            <td>${op.fecha_creacion}</td>
                        </tr>
                    `;
                });
                $('#body_op').html(html);
            },
            error: function(err){
                console.error(err);
                $('#body_op').html(`<tr><td colspan="9" class="text-center text-danger">Error al cargar las órdenes.</td></tr>`);
            }
        });
    }

    function estadoColor(estado){
        switch(estado){
            case 'PROGRAMADA': return 'bg-info text-dark';
            case 'EN PROCESO': return 'bg-warning text-dark';
            case 'FINALIZADA': return 'bg-success';
            default: return 'bg-secondary';
        }
    }
});
</script>
</body>
</html>
