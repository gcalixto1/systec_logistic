<?php
include("conexionfin.php");

// --- Variables generales ---
$mensaje = '';
$preview = [];
$errores = [];
$insertEnabled = false;

// --- Detectar si se envió previsualizar abonos ---
$active_tab_masiva = isset($_POST['previsualizar_abonos']);

// --- Guardar abono individual ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_abono'])) {
    $id_pedido = (int) ($_POST['id_pedido'] ?? 0);
    $monto_abono = (float) ($_POST['monto_abono'] ?? 0);
    $banco = mysqli_real_escape_string($conexion, $_POST['banco'] ?? '');
    $forma_pago = mysqli_real_escape_string($conexion, $_POST['forma_pago'] ?? '');
    $referencia = mysqli_real_escape_string($conexion, $_POST['referencia'] ?? '');

    if ($monto_abono <= 0) {
        $mensaje = 'error|El monto del abono debe ser mayor a 0';
    } else {
        $sqlPedido = "
            SELECT det.monto_deuda - IFNULL(abo.total_abonado,0) AS saldo, det.monto_deuda
            FROM (
                SELECT pedido_id, SUM(cantidad * precio) AS monto_deuda
                FROM detalle_pedidos
                GROUP BY pedido_id
            ) det
            LEFT JOIN (
                SELECT id_pedido, SUM(monto_abono) AS total_abonado
                FROM cartera_abono
                GROUP BY id_pedido
            ) abo ON abo.id_pedido = det.pedido_id
            WHERE det.pedido_id = '$id_pedido'
        ";
        $pedido = mysqli_fetch_assoc(mysqli_query($conexion, $sqlPedido));
        $monto_deuda = (float) ($pedido['monto_deuda'] ?? 0);
        $saldo_anterior = (float) ($pedido['saldo'] ?? 0);
        $monto_pendiente = max($saldo_anterior - $monto_abono, 0);

        $idusuario = $_SESSION['login_idusuario'] ?? 1;

        $sql = "INSERT INTO cartera_abono (id_pedido, monto_deuda, monto_abono, monto_pendiente, banco, forma_pago, referencia, usuario_id) 
                VALUES ('$id_pedido', '$monto_deuda', '$monto_abono', '$monto_pendiente', '$banco', '$forma_pago', '$referencia', '$idusuario')";
        mysqli_query($conexion, $sql);

        if ($monto_pendiente <= 0) {
            mysqli_query($conexion, "UPDATE pedidos SET estatus = 'CANCELADO' WHERE id = '$id_pedido'");
        }

        $mensaje = 'success|Abono registrado correctamente';
    }

    list($icon, $text) = explode('|', $mensaje, 2);
    echo "<script>
        Swal.fire({
            icon: ".json_encode($icon).",
            title: ".json_encode($icon === 'success' ? '¡Éxito!' : 'Error').",
            text: ".json_encode($text).",
            confirmButtonText: 'Aceptar'
        }).then(()=>{ window.location.href = 'index.php?page=historial_abonos'; });
    </script>";
    exit();
}

// --- Pedidos pendientes (solo saldo positivo) ---
$pendientes = mysqli_query($conexion, "
    SELECT 
        p.id, 
        p.numero_pedido, 
        c.nombre_cliente AS cliente, 
        det.monto_deuda,
        (det.monto_deuda - IFNULL(abo.total_abonado,0)) AS pendiente
    FROM pedidos p
    INNER JOIN clientes c ON c.id = p.cliente_id
    INNER JOIN (
        SELECT pedido_id, SUM(cantidad * precio) AS monto_deuda
        FROM detalle_pedidos
        GROUP BY pedido_id
    ) det ON det.pedido_id = p.id
    LEFT JOIN (
        SELECT id_pedido, SUM(monto_abono) AS total_abonado
        FROM cartera_abono
        GROUP BY id_pedido
    ) abo ON abo.id_pedido = p.id
    WHERE p.estatus <> 'CANCELADO'
    HAVING pendiente > 0
");

// --- Todos los abonos ---
$abonos = mysqli_query($conexion, "
    SELECT a.*, p.numero_pedido, c.nombre_cliente AS cliente, p.fecha_pedido, u.nombre
    FROM cartera_abono a
    INNER JOIN pedidos p ON p.id = a.id_pedido
    INNER JOIN clientes c ON c.id = p.cliente_id
    INNER JOIN usuario u ON u.idusuario = a.usuario_id
    ORDER BY a.id DESC
");
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<div class="container-fluid">
    <h3>Gestión de Abonos</h3>

    <!-- Tabs -->
    <ul class="nav nav-tabs" id="abonoTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link <?= !$active_tab_masiva ? 'active' : '' ?>" id="individual-tab" data-bs-toggle="tab" href="#individual" role="tab">Abono Individual</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $active_tab_masiva ? 'active' : '' ?>" id="masiva-tab" data-bs-toggle="tab" href="#masiva" role="tab">Carga Masiva</a>
        </li>
    </ul>

    <div class="tab-content mt-3">
        <!-- Tab Individual -->
        <div class="tab-pane fade <?= !$active_tab_masiva ? 'show active' : '' ?>" id="individual" role="tabpanel">
            <form method="POST" class="row g-3 mb-4">
                <div class="col-md-4">
                    <label>Pedido</label>
                    <select name="id_pedido" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <?php while($p = mysqli_fetch_assoc($pendientes)): ?>
                            <option value="<?= $p['id'] ?>">
                                <?= $p['numero_pedido'] ?> - <?= $p['cliente'] ?> (Pendiente: $<?= number_format($p['pendiente'],2) ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Monto Abono</label>
                    <input type="number" step="0.01" name="monto_abono" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label>Banco</label>
                    <input type="text" name="banco" class="form-control">
                </div>
                <div class="col-md-2">
                    <label>Forma Pago</label>
                    <select name="forma_pago" class="form-control">
                        <option>Efectivo</option>
                        <option>Transferencia</option>
                        <option>Cheque</option>
                        <option>Tarjeta</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Referencia</label>
                    <input type="text" name="referencia" class="form-control">
                </div>
                <div class="col-md-1 align-self-end">
                    <button class="btn btn-success w-100" name="guardar_abono">Abonar</button>
                </div>
            </form>

            <h3>Historial de Abonos</h3>
            <table class="table table-bordered table-striped" id="tabla-abonos">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha Pedido</th>
                        <th>Asesor</th>
                        <th>Pedido</th>
                        <th>Cliente</th>
                        <th>Monto Deuda</th>
                        <th>Abono</th>
                        <th>Pendiente</th>
                        <th>Banco</th>
                        <th>Forma Pago</th>
                        <th>Referencia</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($a = mysqli_fetch_assoc($abonos)): ?>
                    <tr>
                        <td><?= $a['fecha_abono'] ?></td>
                        <td><?= $a['nombre'] ?></td>
                        <td><?= $a['numero_pedido'] ?></td>
                        <td><?= $a['cliente'] ?></td>
                        <td>$<?= number_format($a['monto_deuda'],2) ?></td>
                        <td>$<?= number_format($a['monto_abono'],2) ?></td>
                        <td>$<?= number_format($a['monto_pendiente'],2) ?></td>
                        <td><?= htmlspecialchars($a['banco']) ?></td>
                        <td><?= htmlspecialchars($a['forma_pago']) ?></td>
                        <td><?= htmlspecialchars($a['referencia']) ?></td>
                        <td>
                            <button class="btn btn-danger btn-sm eliminar-abono" data-id="<?= $a['id'] ?>">
                                <i class="fa fa-trash"></i> Eliminar
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Tab Masiva -->
        <div class="tab-pane fade <?= $active_tab_masiva ? 'show active' : '' ?>" id="masiva" role="tabpanel">
            <div class="mb-3">
                <a href="descargar_plantilla_abonos.php" class="btn btn-info">Descargar Plantilla Excel</a>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <label>Subir archivo Excel/CSV</label>
                <input type="file" name="archivo_excel" accept=".csv,.txt,.xls,.xlsx" required>
                <button type="submit" name="previsualizar_abonos" class="btn btn-primary mt-2">Previsualizar Abonos</button>
            </form>
        </div>
    </div>
</div>

<script>
// --- Eliminar abono ---
document.querySelectorAll('.eliminar-abono').forEach(btn => {
    btn.addEventListener('click', e => {
        e.preventDefault();
        const id = btn.dataset.id;
        Swal.fire({
            title: '¿Eliminar este abono?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('ajax.php?action=delete_abono', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + id
                })
                .then(res => res.text())
                .then(resp => {
                    if (resp.trim() === '1') {
                        Swal.fire('Eliminado', 'El abono fue eliminado correctamente', 'success')
                        .then(()=> location.reload());
                    } else {
                        Swal.fire('Error', 'No se pudo eliminar el abono', 'error');
                    }
                });
            }
        });
    });
});
</script>
