<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Punto de venta</title>

    <?php include('header_desing.php'); ?>
    <?php
    $conexionfin_included = false;

    if (!isset($_GET['pv'])) {
        include('conexion.php');
    } else {
        // include('conexionfin.php');
    }
    ?>
    <?php

    if (isset($_SESSION['login_idusuario']))
        header("location:index.php?page=home");
    ?>

</head>
<style>
body {
    background-image: url('img/fondo.jpg');
    /* Ruta de la imagen de fondo */
    background-size: cover;
    /* Cubre todo el área del body */
    background-position: center;
    /* Centra la imagen */
    background-attachment: fixed;
    /* Fija la imagen de fondo para que no se desplace con el contenido */
    backdrop-filter: blur(500px);
    /* Aplica un desenfoque al fondo */
}
</style>

<body>

    <div class="wrapper fadeInDown">
        <div id="formContent">

            <div class="fadeIn first">
                <br>
                <center><img class="img-responsive" id="icon" alt="User Icon" style="width:250px;" src="img/logo.png" />
                    <br>
                </center>
            </div>
            <br>
            <br>
            <form id="login-form">
                <div class="form-group">
                    <label for="username" class="control-label" style="color:#f6a016;">Usuario</label>
                    <input type="text" id="username" name="username" class="form-control">
                </div>
                <div class="form-group">
                    <label for="password" class="control-label" style="color:#f6a016;">Clave de acceso</label>
                    <input type="password" id="password" name="password" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Entrar al sistema</button>
                <!-- Cambiado type a "submit" -->
            </form>
            <br>
        </div>
        <br>
        <?php
        // Verificar si el parámetro tiene_tablas está definido
        if (isset($_GET['pv'])) {
            if ($_GET['pv'] == 0) {
                ?>
        <button type='button' class='btn btn-warning' style="color:#000;" onclick="crearConfiguraciones()">CREAR
            CONFIGURACIONES PARA EL CLIENTE <img width="30" height="30"
                src="https://img.icons8.com/ios-filled/30/database.png" alt="database" /></button>
        <?php
            } else {

            }
        } else {
            echo "No se pudo determinar el estado de las tablas.";
        }
        ?>
    </div>

    <a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $('#login-form').submit(function(e) {
        e.preventDefault()
        $('#login-form button[type="submit"]').attr('disabled', true).html(
        'Iniciando sesión...'); // Cambiado a "submit"
        if ($(this).find('.alert-danger').length > 0)
            $(this).find('.alert-danger').remove();

        $.ajax({
            url: 'ajax.php?action=login',
            method: 'POST',
            data: $(this).serialize(),
            error: function(err) {
                console.log(err);
                $('#login-form button[type="submit"]').removeAttr('disabled').html(
                    'Entrar al sistema'); // Cambiado a "submit"
            },
            success: function(resp) {
                if (resp == 1) {
                    location.href = 'index.php?page=home';
                } else if (resp == 3) {
                    $('#login-form').prepend(
                        '<div class="alert alert-danger">El usuario o la clave de acceso son incorrectos.</div>'
                        );
                    $('#login-form button[type="submit"]').removeAttr('disabled').html(
                        'Entrar al sistema'); // Cambiado a "submit"
                }
            }
        })
    })

    function crearConfiguraciones() {
        window.location.href = 'creacion.php';
    }
    </script>

</body>

</html>