<?php include_once "includes/header.php"; ?>
<link href="css/dashboar.css" rel="stylesheet">
<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <ul class="list-group">
            <div class="card-header bg-warning">
                <h4 class="modal-title text-white"> Gestion para cambio de contraseña</h4>
            </div>
                <form action="" method=" post" name="frmChangePass" id="frmChangePass" class="p-3">
                    <div class="form-group">
                        <label>Contraseña Actual</label>
                        <input type="password" name="actual" id="actual" placeholder="Clave Actual" required
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Nueva Contraseña</label>
                        <input type="password" name="nueva" id="nueva" placeholder="Nueva Clave" required
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Confirmar Contraseña</label>
                        <input type="password" name="confirmar" id="confirmar" placeholder="Confirmar clave" required
                            class="form-control">
                    </div>
                    <div class="alertChangePass" style="display:none;">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary btnChangePass">Cambiar Contraseña</button>
                    </div>
                </form>
            </ul>
        </div>
    </div>
</div>
<?php include_once "includes/footer.php"; ?>