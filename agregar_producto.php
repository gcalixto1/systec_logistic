<?php
include_once "includes/header.php";
include ("conexionfin.php");
// Validar producto

if (empty($_REQUEST['codproducto'])) {
    header("Location: productos.php");
} else {
    $id_producto = $_REQUEST['codproducto'];
    if (!is_numeric($id_producto)) {
        header("Location: productos.php");
    }
    $query_producto = mysqli_query($conexion, "SELECT * FROM producto WHERE codproducto = $id_producto");
    $result_producto = mysqli_num_rows($query_producto);

    if ($result_producto > 0) {
        $data_producto = mysqli_fetch_assoc($query_producto);
    } else {
        header("Location: productos.php");
    }
}
// Agregar Productos a entrada
if (!empty($_POST)) {
    $alert = "";
    if (!empty($_POST['cantidad']) || !empty($_POST['precio']) || !empty($_POST['producto_id'])) {
        $precio = $_POST['precio'];
        $cantidad = $_POST['cantidad'];
        $producto_id = $_GET['id'];
        $usuario_id = $_SESSION['idUser'];
        $query_insert = mysqli_query($conexion, "INSERT INTO entradas(codproducto,cantidad,precio,usuario_id) VALUES ($producto_id, $cantidad, $precio, $usuario_id)");
        if ($query_insert) {
            // ejecutar procedimiento almacenado
            $query_upd = mysqli_query($conexion, "CALL actualizar_precio_producto($cantidad,$precio,$producto_id)");
            $result_pro = mysqli_num_rows($query_upd);
            if ($result_pro > 0) {
                $alert = '<script>
                Swal.fire({
                    title: "Éxito!",
                    text: "El registro se guardó con éxito",
                    icon: "success",
                    confirmButtonColor: "#28a745",
                    confirmButtonText: "OK"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "productos.php";
                    }
                });
            </script>';
            }
        } else {
            echo "error";
        }
        mysqli_close($conexion);
    } else {
        echo "error";
    }
}
?>
<style>
    .label {
        font-weight: bold;
        font-size: 28px;
        position: relative;
        text-align: center;
        color: #614AB0;
    }
    .labelsb {
        font-size: 18px;
        font-family: Comic Sans MS;
        text-align: center;
        margin-left: 85px;;
        color: #A51C0F;
    }
</style>
<div class="container-fluid">
    <div class="card">
        <form class="form form-material" method="post" action="#" enctype="multipart/form-data" name="savestock"
            id="savestock">
            <div id="save">
            </div>
            <div class="form-body">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group has-feedback">
                            <input type="hidden" name="id"
                                value="<?php echo isset($_GET['codproducto']) ? $_GET['codproducto'] : '' ?>">
                        </div>
                        <div class="form-group col-md-12">
                            <input type="hidden" name="codBarra" id="codBarra"
                                value="<?php echo isset($data_producto['codBarra']) ? $data_producto['codBarra'] : '' ?>">
                        </div>
                        <div class="form-group col-md-12">
                            <label class="label" for="precio"><?php echo $data_producto['descripcion']; ?></label>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="precio">Precio Actual</label>
                            <input type="number" name="precio" id="precio" class="form-control" value="<?php echo $data_producto['precio']; ?>"
                                >
                        </div>
                        <div class="form-group col-md-6">
                            <label for="precio">Cantidad Disponibles</label>
                            <input type="number" name="existencia" id="existencia" class="form-control"
                                value="<?php echo $data_producto['existencia']; ?>" >
                        </div>
                        <div class="form-group col-md-6">
                            <label for="precio">Nuevo Precio</label>
                            <input type="number" placeholder="Ingrese nombre del precio" name="precioN" id="precioN"
                                class="form-control" value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="cantidad">Agregar Cantidad</label>
                            <input type="number" placeholder="Ingrese cantidad" name="cantidad" id="cantidad"
                                class="form-control">
                        </div>
                        <div class="form-group col-md-12">
                        <?php if ($data_producto['existencia'] < $data_producto['exis_min']) { ?>
                            <label class="labelsb" for="precio">Stock de este producto bajo</label>
                                <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </form>
</div>
<script type="text/javascript" src="assets/script/script2.js"></script>
<script src="assets/plugins/fileupload/bootstrap-fileupload.min.js"></script>
<script type="text/javascript" src="assets/script/titulos.js"></script>
<script type="text/javascript" src="assets/script/script2.js"></script>
<script type="text/javascript" src="assets/script/validation.min.js"></script>
<script src="assets/plugins/noty/packaged/jquery.noty.packaged.min.js"></script>
<script>
$('#savestock').submit(function(e) {
    e.preventDefault();
    start_load();
    $.ajax({
        url: 'ajax.php?action=save_stock',
        method: 'POST',
        data: $(this).serialize(),
            success: function (resp) {
                if (resp == 1) {
                    Swal.fire({
                        title: 'Exito!',
                        text: 'El registro se guardo con Exito',
                        icon: 'success',
                        confirmButtonColor: '#28a745',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                }
            }
        });
    });
</script>
