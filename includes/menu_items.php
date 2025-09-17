<?php
include('conexionfin.php');
$rol_actual = $_SESSION['login_rol'];

// Obtener los menús para el rol actual
$sql = "
    SELECT m.idMenu, m.Menu, m.Url, m.Parent, m.icon 
    FROM menu m 
    INNER JOIN roles_menu rm ON m.idMenu = rm.idMenu 
    WHERE rm.idrol = ? AND rm.estado = 1 order by m.index asc";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $rol_actual);
$stmt->execute();
$result = $stmt->get_result();
$menus = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Función para estructurar los menús en un array jerárquico
function construirArbolMenu($menus, $parent_id = 0)
{
    $arbol = [];
    foreach ($menus as $menu) {
        if ($menu['Parent'] == $parent_id) {
            $menu['submenu'] = construirArbolMenu($menus, $menu['idMenu']);
            $arbol[] = $menu;
        }
    }
    return $arbol;
}

// Construir el árbol de menú
$menu_arbol = construirArbolMenu($menus);

function imprimirMenu($menu_arbol)
{
    echo '<ul class="list-unstyled components">';
    foreach ($menu_arbol as $menu) {
        echo '<li class="sidebar-item">';
        if (!empty($menu['submenu'])) {
            echo '<a class="sidebar-link has-arrow waves-dark" href="javascript:void(0)" style="font-family:Arial;font-weight: 400; " aria-expanded="false">';
            echo '<i class="' . $menu['icon'] . '"></i><span class="hide-menu">' . $menu['Menu'] . ' <i class="fa fa-caret-down"></i></span></a>';
            echo '<ul aria-expanded="false" class="collapse second-level">';
            imprimirMenu($menu['submenu']);
            echo '</ul>';
        } else {
            echo '<a href="' . $menu['Url'] . '" class="sidebar-link">';
            echo '<i class="' . $menu['icon'] . '"></i><span class="hide-menu">' . $menu['Menu'] . '</span></a>';
        }
        echo '</li>';
    }
    echo '</ul>';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto de venta</title>
    <!-- Agrega tus enlaces CSS aquí -->
    <link rel="stylesheet" href="assets/css/style_Menu.css">
</head>

<body>
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
                <div
                    class="sidebar-brand d-flex align-items-center justify-content-center sidebar-brand-icon rotate-n-15">
                    <img src="img/logo.png" class="img-thumbnail">
                </div>
                <br>
                <br>
                <?php imprimirMenu($menu_arbol); ?>
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
    <!-- JavaScript -->
    <!-- Agrega tus scripts JavaScript aquí -->
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/js_Menu/popper.js"></script>
    <script src="assets/js_Menu/bootstrap.min.js"></script>
    <script src="assets/js_Menu/main.js"></script>
    <script>
        $(document).ready(function () {
            $('.has-arrow').click(function () {
                $(this).next('.collapse').slideToggle();
            });
        });
        $('.nav-<?php echo isset($_GET['page']) ? $_GET['page'] : '' ?>').addClass('active')
    </script>
</body>

</html>
<?php
// Cerrar la conexión
$conexion->close();
?>