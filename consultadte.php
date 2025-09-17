<?php include('conexionfin.php'); ?>
<style>
.boton_add {
    margin-top: -4%;
    margin-left: 75%;
    width: 25%;
}

.spinner-container {
    display: none;
    text-align: center;
    padding: 20px;
}
</style>

<div class="container-fluid">
    <div class="card-header">
        <h4 class="card-title text-black"><i class="fa fa-search"></i> Consulta de DTE - Hacienda</h4>
    </div>
    <br>
    <form id="consultaDteForm">
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Código de Generación</label>
            <div class="col-sm-6">
                <input type="text" name="codigoGeneracion" class="form-control" required>
            </div>
            <div class="col-sm-4">
                <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-search"></i> Consultar</button>
                <button type="button" class="btn btn-secondary btn-lg" onclick="location.reload();"><i
                        class="fa fa-sync"></i> Nueva Consulta</button>
            </div>
        </div>
    </form>

    <!-- Spinner -->
    <div id="spinner" class="spinner-container">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Cargando...</span>
        </div>
        <p class="mt-2">Consultando DTE en Hacienda...</p>
    </div>

    <!-- Resultado -->
    <div id="resultadoDte"></div>
</div>

<script>
document.getElementById('consultaDteForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;
    const data = new FormData(form);
    const resultado = document.getElementById('resultadoDte');
    const spinner = document.getElementById('spinner');

    resultado.innerHTML = '';
    spinner.style.display = 'block';

    fetch('consulta_dte.php', {
            method: 'POST',
            body: data
        })
        .then(res => res.text())
        .then(html => {
            spinner.style.display = 'none';
            resultado.innerHTML = html;
        })
        .catch(() => {
            spinner.style.display = 'none';
            resultado.innerHTML =
                "<div class='alert alert-danger'>Ocurrió un error al procesar la consulta.</div>";
        });
});
</script>