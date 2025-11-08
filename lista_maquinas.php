<?php
include('conexionfin.php');

// Consultar todas las máquinas disponibles
$sql = "SELECT id, cod_mac, maquina FROM lista_maquina ORDER BY maquina ASC";
$result = $conexion->query($sql);
?>

<style>
.btn-maquina {
    width: 200px;
    height: 80px;
    margin: 10px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 10px;
    box-shadow: 0 3px 6px rgba(0,0,0,0.2);
    transition: 0.3s;
}
.btn-maquina:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 10px rgba(0,0,0,0.3);
}
.maquina-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 15px;
}
</style>

<div class="container mt-4">
    <h4 class="text-center mb-4">
        <i class="fa fa-cogs"></i> Seleccionar Línea de Máquina
    </h4>

    <div class="maquina-container">
        <?php while ($row = $result->fetch_assoc()): ?>
            <button 
                class="btn btn-warning btn-maquina"
                data-id="<?= $row['id'] ?>"
                data-cod="<?= $row['cod_mac'] ?>"
                data-nombre="<?= htmlspecialchars($row['maquina']) ?>"
            >
                <?= strtoupper($row['maquina']) ?><br>
                <small><?= $row['cod_mac'] ?></small>
            </button>
        <?php endwhile; ?>
    </div>
</div>

<script>
$(document).on('click', '.btn-maquina', function() {
    const maquinaId = $(this).data('id');
    const nombre = $(this).data('nombre');
    const codigo = $(this).data('cod');

    // Enviar los datos a la ventana principal (formulario de producción)
    if (window.parent && window.parent.document) {
        const parentDoc = window.parent.document;
        $(parentDoc).find('#maquina_id').val(maquinaId);
        $(parentDoc).find('#maquina_nombre').val(nombre);
        $(parentDoc).find('#maquina_cod').val(codigo);

        // 🔹 Ejecuta la función en el padre si existe
        if (typeof window.parent.mostrarAlertaMaquina === 'function') {
            window.parent.mostrarAlertaMaquina();
        }
    }

    // Cierra la modal (usa la función de tu sistema)
    if (window.parent && typeof window.parent.closeModal === 'function') {
        window.parent.closeModal();
    } else {
        if (window.parent.$('.modal.show').length) {
            window.parent.$('.modal.show').modal('hide');
        }
    }
});
</script>

