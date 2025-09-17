<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Punto de venta</title>
<!-- Agrega tus enlaces CSS aquí -->
<link rel="stylesheet" href="assets/css/style_Menu.css">
<link href="assets/vendor/icofont/icofont.min.css" rel="stylesheet">
<link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
<link href="assets/DataTables/dataTables.min.css" rel="stylesheet">

<?php
// Define el arreglo de opciones de menú
$opciones_menu = array(
    'administrador' => array(
        array('url' => 'index.php?page=home', 'texto' => '  Dashboard', 'icono' => 'fas fa-fw fa-tachometer-alt'),
        array('url' => 'index.php?page=nueva_venta', 'texto' => '  POS Venta', 'icono' => 'fa fa-calculator'),
        array(
            'texto' => '  Configuracion',
            'icono' => 'fa fa-cogs',
            'submenu' => array(
                array('url' => 'index.php?page=perfil_general', 'texto' => '  Perfil General', 'icono' => 'fa fa-building'),
                array('url' => 'index.php?page=usuarios', 'texto' => '  Usuarios', 'icono' => 'fa fa-user'),
            )
        ),
        array(
            'texto' => ' Catalogos',
            'icono' => 'fa fa-folder',
            'submenu' => array(
                array('url' => 'index.php?page=cliente', 'texto' => '  Clientes', 'icono' => 'fa fa-address-card'),
                array('url' => 'index.php?page=proveedor', 'texto' => '  Proveedores', 'icono' => 'fa fa-users'),
                array('url' => 'index.php?page=productos', 'texto' => '  Productos', 'icono' => 'fa fa-cube'),
                array('url' => 'index.php?page=kardex_productos', 'texto' => ' Kardex de Productos', 'icono' => 'fa fa-clipboard')
            )
        ),
        array(
            'texto' => ' Control de Caja',
            'icono' => 'fas fa-cash-register',
            'submenu' => array(
                array('url' => 'index.php?page=caja', 'texto' => '  Apertura de Caja', 'icono' => 'fas fa-cash-register'),
                array('url' => 'index.php?page=movimientos', 'texto' => '  Movimientos de Caja', 'icono' => 'fa fa-clipboard')
            )
        ),
        array('url' => 'ajax.php?action=logout', 'texto' => '  Cerrar Sesión', 'icono' => 'fa fa-power-off')
    )
);

function imprimirSubMenu($submenu)
{
    echo '<ul aria-expanded="false" class="collapse second-level">';
    foreach ($submenu as $subopcion) {
        echo '<li class="sidebar-item">';
        if (isset($subopcion['submenu'])) {
            echo '<a class="sidebar-link waves-effect waves-dark has-arrow" href="javascript:void(0)" aria-expanded="false">';
            echo '<i class="' . $subopcion['icono'] . '"></i>' . $subopcion['texto'] . ' <i class="fa fa-caret-down"></i></a>';
            imprimirSubMenu($subopcion['submenu']); // Llamada recursiva para manejar submenús anidados
        } else {
            echo '<a class="sidebar-link waves-effect waves-dark" href="' . $subopcion['url'] . '" aria-expanded="false">';
            echo '<i class="' . $subopcion['icono'] . '"></i>' . $subopcion['texto'] . '</a>';
        }
        echo '</li>';
    }
    echo '</ul>';
}
// Genera el menú
foreach ($opciones_menu as $menu_usuario) {
    ?>
<div class="wrapper d-flex align-items-stretch">
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="custom-menu">
            <button type="button" id="sidebarCollapse" class="btn btn-primary">
                <i class="fa fa-bars fa-lg" style="float: right;margin-right:-11px;"></i>
                <span class="sr-only">Toggle Menu</span>
            </button>
        </div>
        <div class="p-4">
            <div class="sidebar-brand d-flex align-items-center justify-content-center sidebar-brand-icon rotate-n-15">
                <img src="img/logo.png" class="img-thumbnail">
            </div>
            <br>
            <br>
            <ul class="list-unstyled components mb-5">
                <?php foreach ($menu_usuario as $opcion): ?>
                <?php if (isset($opcion['submenu'])): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link has-arrow waves-dark" href="javascript:void(0)" aria-expanded="false">
                        <i class="<?php echo $opcion['icono']; ?>"></i><span class="hide-menu">
                            <?php echo $opcion['texto']; ?> <i class="fa fa-caret-down"></i>
                        </span>
                    </a>
                    <?php imprimirSubMenu($opcion['submenu']); ?>
                </li>
                <?php else: ?>
                <li class="sidebar-item">
                    <a href="<?php echo isset($opcion['url']) ? $opcion['url'] : 'javascript:void(0)'; ?>"
                        class="sidebar-link">
                        <i class="<?php echo $opcion['icono']; ?>"></i><span class="hide-menu">
                            <?php echo $opcion['texto']; ?>
                        </span>
                    </a>
                </li>
                <?php endif; ?>
                <?php endforeach; ?>
            </ul>
            <!-- Footer -->
            <div class="footer">
                <p>&copy;
                    <script>
                    document.write(new Date().getFullYear());
                    </script>
                </p>
            </div>
        </div>
    </nav>
    <!-- Page Content -->
    <div id="content" class="p-4 p-md-4 pt-4">
        <main id="container">
            <?php
                ini_set('display_errors', 1);
                ini_set('display_startup_errors', 1);
                error_reporting(E_ALL);

                $page = isset($_GET['page']) ? $_GET['page'] : 'home';
                $file = $page . '.php';

                if (file_exists($file)) {
                    include $file;
                } else {
                    echo 'Página no encontrada';
                }
                ?>
        </main>
    </div>
</div>
<?php
}
?>
<!-- JavaScript -->
<!-- Agrega tus scripts JavaScript aquí -->
<script src="assets/vendor/jquery/jquery.min.js"></script>
<script src="assets/js_Menu/popper.js"></script>
<script src="assets/js_Menu/bootstrap.min.js"></script>
<script src="assets/js_Menu/main.js"></script>
<script>
$(document).ready(function() {
    // Activa el despliegue de submenús
    $('.has-arrow').click(function() {
        $(this).next('.collapse').slideToggle();
    });
});
$('.nav-<?php echo isset($_GET['page']) ? $_GET['page'] : '' ?>').addClass('active')
</script>