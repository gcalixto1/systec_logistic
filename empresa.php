<?php
include "conexionfin.php";
$alert = '';
$txtdni = $_POST['txtDni'];
$txtNombre = $_POST['txtNombre'];
$txtRSocial = $_POST['txtRSocial'];
$txtTelefono = $_POST['txtTelEmpresa'];
$txtDireccion = $_POST['txtDirEmpresa'];
$txtemail = $_POST['txtEmailEmpresa'];
$txtigv = $_POST['txtIgv'];
$txtimpresion = $_POST['impresion'];
$actualizar_empresa = mysqli_query($conexion, "UPDATE configuracion SET dni = '$txtdni', nombre = '$txtNombre', razon_social = '$txtRSocial', telefono = '$txtTelefono', email = '$txtemail', direccion = '$txtDireccion', igv = $txtigv, impresion = '$txtimpresion'");
mysqli_close($conexion);

if ($actualizar_empresa) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    $alert = '<script>
        Swal.fire({
            title: "Éxito!",
            text: "El registro se guardó con éxito",
            icon: "success",
            confirmButtonColor: "#28a745",
            confirmButtonText: "OK"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "index.php?page=perfil_general";
            }
        });
    </script>';
    // Subir el logo principal
		if (isset($_FILES['imagen']['name'])) {
			$nombre_archivo = $_FILES['imagen']['name'];
			$tipo_archivo = $_FILES['imagen']['type'];
			$tamano_archivo = $_FILES['imagen']['size'];
			if ((strpos($tipo_archivo, 'image/png') !== false) && $tamano_archivo < 2000000) {
				if (move_uploaded_file($_FILES['imagen']['tmp_name'], "img/" . $nombre_archivo) && rename("img/" . $nombre_archivo, "img/logo.png")) {
					// Puedes mostrar un mensaje de éxito aquí
				} else {
					// Puedes manejar el error aquí
				}
			}
		}
        if (isset($_FILES['imagen2']['name'])) {
			$nombre_archivo = $_FILES['imagen2']['name'];
			$tipo_archivo = $_FILES['imagen2']['type'];
			$tamano_archivo = $_FILES['imagen2']['size'];
			if ((strpos($tipo_archivo, 'image/png') !== false) && $tamano_archivo < 6000000) {
				if (move_uploaded_file($_FILES['imagen2']['tmp_name'], "img/" . $nombre_archivo) && rename("img/" . $nombre_archivo, "img/fondo.jpg")) {
					// Puedes mostrar un mensaje de éxito aquí
				} else {
					// Puedes manejar el error aquí
				}
			}
		}
  
} else {
  $alert = '<p class="msg_error">Error al Actualizar la Configuración de empresa</p>';
}
?>
<?php include "includes/footer.php"; ?>
<?= $alert; ?>
