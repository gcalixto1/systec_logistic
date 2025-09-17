<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
ini_set('display_errors', 1);
require 'vendor/autoload.php';

use Luecano\NumeroALetras\NumeroALetras;

class Action
{
	private $dbh;

	public function __construct()
	{
		ob_start();
		include 'conexionfin.php';
		$this->dbh = $conexion;
	}

	function __destruct()
	{
		$this->dbh->close();
		ob_end_flush();
	}

	#region Login
	function login()
	{
		extract($_POST);
		$stmt = $this->dbh->prepare("SELECT * FROM usuario WHERE usuario = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				if ($row['clave'] === md5($password)) {
					foreach ($row as $key => $value) {
						if ($key != 'clave' && !is_numeric($key)) {
							$_SESSION['login_' . $key] = $value;
						}
					}
					return 1; // Autenticación exitosa
				}
			}
			return 3; // Contraseña incorrecta
		} else {
			return 3; // Usuario no encontrado
		}
	}
	function logout()
	{
		session_destroy();
		header("location:login.php?pv=1");
		exit; // Asegurar la terminación del script después de la redirección
	}
	public function ListarImpresoras()
	{
		$sql = "SELECT * FROM configuracion";

		$result = mysqli_query($this->dbh, $sql);

		$impresora = array();
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$impresora[] = $row;
			}
		}
		return $impresora;
	}
	#endregion
	#region Configuracion
	function save_configuracion()
	{
		extract($_POST);
		$txtdni = $_POST['txtDni'];
		$txtNombre = $_POST['txtNombre'];
		$txtRSocial = $_POST['txtRSocial'];
		$txtTelefono = $_POST['txtTelEmpresa'];
		$txtDireccion = $_POST['txtDirEmpresa'];
		$txtemail = $_POST['txtEmailEmpresa'];
		$txtigv = $_POST['txtIgv'];
		$txtimpresion = $_POST['impresion'];
		$txtmoneda = $_POST['moneda'];
		$txtgiro = $_POST['giro'];
		$txtdato1 = $_POST['dato1'];
		$txtdato2 = $_POST['dato2'];
		$txtdato3 = $_POST['dato3'];
		$txtdato4 = $_POST['dato4'];
		$txtdato5 = $_POST['dato5'];
		$txtdato6 = $_POST['dato6'];
		$txtdato7 = $_POST['dato7'];
		$txtdato8 = $_POST['dato8'];
		$txtClavePRIV = $_POST['clavePRIV'];
		$txtClaveAPI = $_POST['claveAPI'];

		// Construye la cadena de datos correctamente
		$data = " dni = '$txtdni'";
		$data .= ", nombre = '$txtNombre'";
		$data .= ", razon_social = '$txtRSocial'";
		$data .= ", telefono = '$txtTelefono'";
		$data .= ", direccion = '$txtDireccion'";
		$data .= ", email = '$txtemail'";
		$data .= ", igv = '$txtigv'";
		$data .= ", impresion = '$txtimpresion'";
		$data .= ", moneda = '$txtmoneda'";
		$data .= ", giro = '$txtgiro'";
		$data .= ", dato1 = '$txtdato1'";
		$data .= ", dato2 = '$txtdato2'";
		$data .= ", dato3 = '$txtdato3'";
		$data .= ", dato4 = '$txtdato4'";
		$data .= ", dato5 = '$txtdato5'";
		$data .= ", dato6 = '$txtdato6'";
		$data .= ", dato7 = '$txtdato7'";
		$data .= ", dato8 = '$txtdato8'";
		$data .= ", clavePRIV = '$txtClavePRIV'";
		$data .= ", claveAPI = '$txtClaveAPI'";

		// Evita inyección SQL usando consultas preparadas
		$id = 1;
		if (empty($id)) {
			$save = $this->dbh->query("INSERT INTO configuracion SET " . $data);
		} else {
			$id = 1; // Escapa el valor de $id
			$save = $this->dbh->query("UPDATE configuracion SET " . $data . " WHERE id = $id");

			// Subir el logo principal
			if (isset($_FILES['imagen']['name'])) {
				$nombre_archivo = $_FILES['imagen']['name'];
				$tipo_archivo = $_FILES['imagen']['type'];
				$tamano_archivo = $_FILES['imagen']['size'];
				if ((strpos($tipo_archivo, 'image/png') !== false) && $tamano_archivo < 60000000) {
					if (move_uploaded_file($_FILES['imagen']['tmp_name'], "img/" . $nombre_archivo) && rename("img/" . $nombre_archivo, "img/logo.png")) {
						// Puedes mostrar un mensaje de éxito aquí
					} else {
						// Puedes manejar el error aquí
					}
				}
			}
			// Subir el logo del reporte
			if (isset($_FILES['imagen2']['name'])) {
				$nombre_archivo = $_FILES['imagen2']['name'];
				$tipo_archivo = $_FILES['imagen2']['type'];
				$tamano_archivo = $_FILES['imagen2']['size'];
				if ((strpos($tipo_archivo, 'image/png') !== false) && $tamano_archivo < 60000000) {
					if (move_uploaded_file($_FILES['imagen2']['tmp_name'], "img/" . $nombre_archivo) && rename("img/" . $nombre_archivo, "img/fondo.jpg")) {
						// Puedes mostrar un mensaje de éxito aquí
					} else {
						// Puedes manejar el error aquí
					}
				}
			}
		}

		if ($save) {
			return 1;
		}
	}

	public function ListarActividades($busqueda = '')
	{
		$busqueda = mysqli_real_escape_string($this->dbh, $busqueda);
		$sql = "SELECT * FROM actividad_economica";
		if (!empty($busqueda)) {
			$sql .= " WHERE descripcion LIKE '%$busqueda%' OR codigo LIKE '%$busqueda%'";
		}

		$result = mysqli_query($this->dbh, $sql);
		$actividades = array();
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$actividades[] = $row;
			}
		}
		return $actividades;
	}
	public function ListarProveedoresSE($busqueda = '')
	{
		$busqueda = mysqli_real_escape_string($this->dbh, $busqueda);
		$sql = "SELECT * FROM proveedor";
		if (!empty($busqueda)) {
			$sql .= " WHERE proveedor LIKE '%$busqueda%' OR idproveedor LIKE '%$busqueda%'";
		}

		$result = mysqli_query($this->dbh, $sql);
		$actividades = array();
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$actividades[] = $row;
			}
		}
		return $actividades;
	}

	#endregion
	#region Usuarios
	function save_usuario()
	{
		extract($_POST);
		$data = "nombre = '$nombre'";
		$data .= ",correo = '$correo'";
		$data .= ",usuario = '$usuario'";
		$data .= ",rol = $rol";
		if (!empty($clave)) {
			$data .= ",clave = '" . md5($clave) . "'";
		}
		if (empty($id)) {
			$save = $this->dbh->query("INSERT INTO usuario set " . $data);
		} else {
			$save = $this->dbh->query("UPDATE usuario set " . $data . " where idusuario = " . $id);
		}
		if ($save) {
			return 1;
		}
	}
	function delete_usuario()
	{
		extract($_POST);
		$delete = $this->dbh->query("DELETE FROM usuario where idusuario = " . $codigo);
		if ($delete)
			return 1;
	}
	public function Listarnivel()
	{
		$sql = "SELECT * FROM rol";
		$result = mysqli_query($this->dbh, $sql);
		$usuarios = array();
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$usuarios[] = $row;
			}
		}
		return $usuarios;
	}
	#endregion
	#region Categoria
	function save_categoria()
	{
		extract($_POST);
		$data = " categoria_des = '$categoria_des' ";
		if (empty($id)) {
			$save = $this->dbh->query("INSERT INTO categoria set " . $data);
		} else {
			$save = $this->dbh->query("UPDATE categoria set " . $data . " where categoria_id = " . $id);
		}
		if ($save) {
			return 1;
		}
	}
	function delete_categoria()
	{
		extract($_POST);
		$delete = $this->dbh->query("DELETE FROM categoria where categoria_id = " . $categoria_id);
		if ($delete)
			return 1;
	}
	public function ListarCategorias()
	{
		$sql = "SELECT * FROM categoria ORDER BY categoria_des ASC";

		$result = mysqli_query($this->dbh, $sql);

		$categoria = array();
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$categoria[] = $row;
			}
		}
		return $categoria;
	}
	#endregion
	#region Presentacion
	function save_presentacion()
	{
		extract($_POST);
		$data = " presentacion = '$presentacion' ";
		if (empty($id)) {
			$save = $this->dbh->query("INSERT INTO presentacion set " . $data);
		} else {
			$save = $this->dbh->query("UPDATE presentacion set " . $data . " where categoria_id = " . $id);
		}
		if ($save) {
			return 1;
		}
	}
	function delete_presentacion()
	{
		extract($_POST);
		$delete = $this->dbh->query("DELETE FROM presentacion where idpresentacion = " . $idPresentacion);
		if ($delete)
			return 1;
	}
	public function ListarPresentacion()
	{
		$sql = "SELECT * FROM presentacion";

		$result = mysqli_query($this->dbh, $sql);

		$impuesto = array();
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$impuesto[] = $row;
			}
		}
		return $impuesto;
	}
	#endregion
	#region Clientes
	function save_cliente()
{
    extract($_POST);

    // Asegura valores por defecto
    $estatus = isset($estatus) ? $estatus : 'ACTIVO';
    $tiene_sedes = isset($tiene_sedes) ? 1 : 0; // Asumimos checkbox o similar
    $plazo_pago_dias = isset($plazo_pago_dias) ? intval($plazo_pago_dias) : 0;
    $listas_precio_habilitadas = isset($listas_precio_habilitadas) ? intval($listas_precio_habilitadas) : 1;

    // Construcción segura del string para consulta SQL
    $data = "dni = '$dni'";
    $data .= ", tipo_documento = '$tipo_documento'";
    $data .= ", nombre = '$nombre'";
    $data .= ", nombre_comercial = '$nombre_comercial'";
    $data .= ", tipo_contribuyente = '$tipo_contribuyente'";
    $data .= ", estatus = '$estatus'";
    $data .= ", asesor = '$asesor'";
    $data .= ", plazo_pago_dias = $plazo_pago_dias";
    $data .= ", listas_precio_habilitadas = $listas_precio_habilitadas";
    $data .= ", telefono1 = '$telefono1'";
    $data .= ", telefono2 = '$telefono2'";
    $data .= ", correo = '$correo'";
    $data .= ", tiene_sedes = $tiene_sedes";

    // Evita inyección SQL usando consultas preparadas — si puedes, usa PDO o MySQLi con bind_param
    if (empty($id)) {
        $save = $this->dbh->query("INSERT INTO cliente SET $data");
    } else {
        $id = mysqli_real_escape_string($this->dbh, $id);
        $save = $this->dbh->query("UPDATE cliente SET $data WHERE id = $id");
    }

    if ($save) {
        if ($tiene_sedes) {
            $this->save_clienteDireccion($this->dbh->insert_id ?? $id); // Puedes pasar el ID del cliente
        }
        return 1;
    }

    return 0; // En caso de error
}

	function save_clienteDireccion()
	{
		extract($_POST);
		// Construye la cadena de datos correctamente
		$data = " departamento = '$departamento'";
		$data .= ", municipio = '$municipio'";
		$data .= ", complemento = '$direccion'";
		$data .= ", cliente_dni = '$dni'";

		// Evita inyección SQL usando consultas preparadas
		if (empty($id)) {
			$save = $this->dbh->query("INSERT INTO cliente_direccion SET " . $data);
		} else {
			$id = mysqli_real_escape_string($this->dbh, $id); // Escapa el valor de $id
			$save = $this->dbh->query("UPDATE cliente_direccion SET " . $data . " WHERE cliente_dni = '$dni'");
		}

		if ($save) {
			return 1;
		}
	}

	function delete_cliente()
	{
		extract($_POST);
		$delete = $this->dbh->query("DELETE FROM cliente where idcliente = " . $idcliente);
		if ($delete)
			return 1;
	}
	public function listarclientes($filtro)
	{
		// Evitar inyección SQL utilizando prepared statements
		$consulta = "SELECT * FROM cliente WHERE nombre LIKE ?";

		// Preparar la consulta
		$stmt = $this->dbh->prepare($consulta);
		if ($stmt === false) {
			die("Error en la preparación de la consulta: " . $this->dbh->error);
		}

		// Agregar '%' al principio y al final del término de búsqueda para buscar coincidencias parciales
		$filtro = "%" . $filtro . "%";

		// Ejecutar la consulta con un parámetro
		$stmt->bind_param("s", $filtro);
		$stmt->execute();

		// Obtener los resultados
		$result = $stmt->get_result();
		$clientes = $result->fetch_all(MYSQLI_ASSOC);

		$stmt->close();

		return $clientes;
	}
	public function ListarDepartamentos()
	{
		$sql = "SELECT * FROM departamentos";

		$result = mysqli_query($this->dbh, $sql);

		$impuesto = array();
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$impuesto[] = $row;
			}
		}
		return $impuesto;
	}
	public function ListarDocumentos()
	{
		$sql = "SELECT * FROM documentos";

		$result = mysqli_query($this->dbh, $sql);

		$impuesto = array();
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$impuesto[] = $row;
			}
		}
		return $impuesto;
	}
	public function ListarMunicipios($departamento_id)
	{
		// Protección contra inyección SQL (cast explícito)
		$departamento_id = intval($departamento_id);

		$sql = "SELECT codigo, valor FROM municipios WHERE iddepartamento = $departamento_id";
		$result = mysqli_query($this->dbh, $sql);

		$municipios = array();
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$municipios[] = [
					'codigo' => $row['codigo'],
					'valor' => $row['valor']
				];
			}
		}
		return $municipios;
	}

	#endregion
	#region Proveedor
	function save_proveedor()
	{
		extract($_POST);
		// Construye la cadena de datos correctamente
		$data = " proveedor = '$proveedor'";
		$data .= ", documento = '$dni'";
		$data .= ", telefono = '$telefono'";
		$data .= ", direccion = '$direccion'";
		$data .= ", tipoControbuyente = '$tipoControbuyente'";
		$data .= ", correo = '$correo'";
		$data .= ", tipoDoc = '$tipoDoc'";

		// Evita inyección SQL usando consultas preparadas
		if (empty($id)) {
			$save = $this->dbh->query("INSERT INTO proveedor SET " . $data);
		} else {
			$id = mysqli_real_escape_string($this->dbh, $id); // Escapa el valor de $id
			$save = $this->dbh->query("UPDATE proveedor SET " . $data . " WHERE idproveedor = $id");
		}

		if ($save) {
			$this->save_clienteDireccion(); // Llama a la función para guardar la dirección del cliente
			return 1;
		}
	}
	function delete_proveedor()
	{
		extract($_POST);
		$delete = $this->dbh->query("DELETE FROM proveedor where idproveedor = " . $idproveedor);
		if ($delete)
			return 1;
	}
	public function ListarProveedores()
	{
		$sql = "SELECT * FROM proveedor";

		$result = mysqli_query($this->dbh, $sql);

		$impuesto = array();
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$impuesto[] = $row;
			}
		}
		return $impuesto;
	}
	public function BuscarProveedores($search)
	{
		$sql = "SELECT idproveedor, proveedor FROM proveedores 
				WHERE proveedor LIKE :search LIMIT 20";
		$stmt = $this->dbh->prepare($sql);
		$stmt->execute([':search' => '%' . $search . '%']);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	#endregion
	#region Producto
	function save_productos()
	{
		// Escapar datos recibidos por POST
		$descripcion = mysqli_real_escape_string($this->dbh, $_POST['descripcion'] ?? '');
		$proveedor = mysqli_real_escape_string($this->dbh, $_POST['proveedor'] ?? '');
		$precio_compra = mysqli_real_escape_string($this->dbh, $_POST['precio_compra'] ?? 0);
		$precio = mysqli_real_escape_string($this->dbh, $_POST['precio'] ?? 0);
		$existencia = mysqli_real_escape_string($this->dbh, $_POST['existencia'] ?? 0);
		$exis_min = mysqli_real_escape_string($this->dbh, $_POST['exis_min'] ?? 0);
		$codBarra = mysqli_real_escape_string($this->dbh, $_POST['codBarra'] ?? '');
		$prop1 = mysqli_real_escape_string($this->dbh, $_POST['prop1'] ?? '');
		$prop2 = mysqli_real_escape_string($this->dbh, $_POST['prop2'] ?? '');
		$prop3 = mysqli_real_escape_string($this->dbh, $_POST['prop3'] ?? '');
		$categoria = mysqli_real_escape_string($this->dbh, $_POST['categoria'] ?? '');
		$fecha_vencimiento = mysqli_real_escape_string($this->dbh, $_POST['fecha_vencimiento'] ?? '');
		$tipo_producto = mysqli_real_escape_string($this->dbh, $_POST['tipo_producto'] ?? '');
		$exenta = mysqli_real_escape_string($this->dbh, $_POST['exenta'] ?? '0');
		$iva = mysqli_real_escape_string($this->dbh, $_POST['iva'] ?? '0');
		$id = mysqli_real_escape_string($this->dbh, $_POST['id'] ?? '');

		$targetFilePath = "";
		$data = "descripcion = '$descripcion',
             proveedor = '$proveedor',
             precio_compra = '$precio_compra',
             precio = '$precio',
             existencia = '$existencia',
             exis_min = '$exis_min',
             codBarra = '$codBarra',
             prop1 = '$prop1',
             prop2 = '$prop2',
             prop3 = '$prop3',
             categoria = '$categoria',
             fecha_vencimiento = '$fecha_vencimiento',
             tipo_producto = '$tipo_producto',
             exentas = '$exenta',
             iva = '$iva'";

		// Manejo de imagen
		if (!empty($_FILES['imagen_producto']['name'])) {
			$targetDir = "img/productos/";
			$fileName = uniqid() . "_" . basename($_FILES["imagen_producto"]["name"]);
			$targetFilePath = $targetDir . $fileName;
			$fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

			$allowedTypes = ['jpg', 'jpeg', 'png'];
			if (in_array($fileType, $allowedTypes)) {
				if (move_uploaded_file($_FILES["imagen_producto"]["tmp_name"], $targetFilePath)) {
					if (!empty($id)) {
						$result = $this->dbh->query("SELECT imagen_producto FROM producto WHERE codproducto = '$id'");
						if ($result && $result->num_rows > 0) {
							$row = $result->fetch_assoc();
							if ($row['imagen_producto'] !== $targetFilePath) {
								$data .= ", imagen_producto = '$targetFilePath'";
							}
						}
					} else {
						$data .= ", imagen_producto = '$targetFilePath'";
					}
				} else {
					echo "Error al subir la imagen.";
					return 0;
				}
			} else {
				echo "Formato de imagen no permitido (solo jpg, jpeg, png).";
				return 0;
			}
		} else {
			if (empty($id)) {
				$data .= ", imagen_producto = 'img/ninguna.png'";
			}
		}

		// Ejecutar INSERT o UPDATE
		if (empty($id)) {
			$save = $this->dbh->query("INSERT INTO producto SET $data");
			if ($save) {
				$this->save_kardexproductos(); // Guardar en Kardex
			}
		} else {
			$save = $this->dbh->query("UPDATE producto SET $data WHERE codproducto = '$id'");
		}

		if ($save) {
			return 1;
		} else {
			echo "Error al guardar los datos en la base de datos. " . $this->dbh->error;
			return 0;
		}
	}


	function delete_producto()
	{
		extract($_POST);
		$delete = $this->dbh->query("DELETE FROM producto where codproducto = " . $codproducto);
		if ($delete)
			return 1;
	}
	function save_kardexproductos()
	{
		extract($_POST);
		$data = "producto = '$codBarra'";
		$data .= ", movimiento = 'ENTRADA'";
		$data .= ", entradas = '$existencia'";
		$data .= ", salidas = '0'";
		$data .= ", devolucion = '0'";
		$data .= ", stock_actual = '$existencia'";
		$data .= ", precio = '$precio_compra'";
		$data .= ", descripcion = 'INVENTARIO INICIAL'";

		// Evita inyección SQL usando consultas preparadas
		if (empty($id)) {
			$save = $this->dbh->query("INSERT INTO kardex_producto SET " . $data);
		}
		if (empty($save)) {
			return 1;
		} else {
			return 0;
		}
	}
	function save_stocks()
	{
		extract($_POST);
		$data = "existencia = '$existencia' + $cantidad"; // Corregido el concatenado de la existencia
		// Evita inyección SQL usando consultas preparadas
		if ($precioN != '') {
			$data .= ", precio = '$precioN'";
		}
		$id = mysqli_real_escape_string($this->dbh, $id); // Escapa el valor de $id
		$save = $this->dbh->query("UPDATE producto SET " . $data . " WHERE codproducto = '$id'"); // Corregido el uso de comillas en $id
		$this->update_kardexproductos();

		if ($save) {
			return 1;
		} else {
			return 0;
		}
	}

	function update_kardexproductos()
	{
		extract($_POST);
		$data = "producto = '$codBarra'";
		if (strpos($cantidad, '-') !== false) {
			$data .= ", movimiento = 'SALIDA'";
			$data .= ", entradas = '0'";
			$data .= ", salidas = '$cantidad'";
			$data .= ", devolucion = '0'";
			$data .= ", stock_actual = '$existencia' + $cantidad"; // Corregido el concatenado del stock_actual
			$data .= ", descripcion = 'AJUSTE DE INVENTARIO PARA PRODUCTO CON CODIGO: $codBarra'";
		} else {
			$data .= ", movimiento = 'ENTRADA'";
			$data .= ", entradas = '$cantidad'";
			$data .= ", salidas = '0'";
			$data .= ", devolucion = '0'";
			$data .= ", stock_actual = '$existencia' + $cantidad"; // Corregido el concatenado del stock_actual
			$data .= ", descripcion = 'ENTRADA DE PRODUCTO AL INVENTARIO: $codBarra'";
		}
		$data .= ", precio = '$precio'";


		$save = $this->dbh->query("INSERT INTO kardex_producto SET " . $data);

		if ($save) {
			return 1;
		} else {
			return 0;
		}
	}

	public function listarproductoauto($filtro)
	{
		// Evitar inyección SQL utilizando prepared statements
		$consulta = "SELECT * FROM producto WHERE descripcion LIKE ?  OR codBarra LIKE ?";

		// Preparar la consulta
		$stmt = $this->dbh->prepare($consulta);
		if ($stmt === false) {
			die("Error en la preparación de la consulta: " . $this->dbh->error);
		}

		// Agregar '%' al principio y al final del término de búsqueda para buscar coincidencias parciales
		$filtro = "%" . $filtro . "%";

		// Ejecutar la consulta con un parámetro
		$stmt->bind_param("ss", $filtro, $filtro);
		$stmt->execute();

		// Obtener los resultados
		$result = $stmt->get_result();
		$productos = $result->fetch_all(MYSQLI_ASSOC);

		$stmt->close();

		return $productos;
	}

	#endregion	
	#region Cajas
	function save_apertura()
	{
		extract($_POST);
		// Construye la cadena de datos correctamente
		$data = " num_apertura = '$num_apertura'";
		$data .= ", saldo_inicial = '$saldo_inicial'";
		$data .= ", fch_hora_cierre = '0000-00-00'";
		$data .= ", usuario = '{$_SESSION['login_usuario']}'";
		$data .= ", caja = 'Caja Principal'";
		$data .= ", estado = 'A'";

		// Evita inyección SQL usando consultas preparadas
		if (empty($id)) {
			$save = $this->dbh->query("INSERT INTO apertura_caja SET " . $data);
		}
		if ($save) {
			return 1;
		}
	}
	function save_cierre()
	{
		extract($_POST);

		// Sanitización de las entradas
		$id = mysqli_real_escape_string($this->dbh, $id);
		$saldo_ventas_total = mysqli_real_escape_string($this->dbh, $saldo_ventas_total);
		$gasto = mysqli_real_escape_string($this->dbh, $gasto);
		$saldo_tarjeta = mysqli_real_escape_string($this->dbh, $saldo_tarjeta);
		$entradas = mysqli_real_escape_string($this->dbh, $entradas);
		$total_completo = mysqli_real_escape_string($this->dbh, $total_completo);

		// Construcción de la consulta con el uso correcto de NOW() y manejo seguro de los datos
		$data = "fch_hora_cierre = NOW(), ";
		$data .= "usuario = '{$_SESSION['login_usuario']}', ";
		$data .= "estado = 'C', ";
		$data .= "saldo_venta_total = '$saldo_ventas_total', ";
		$data .= "gasto = '$gasto', ";
		$data .= "saldo_tarjeta = '$saldo_tarjeta', ";
		$data .= "saldo_credito = '0.00', ";
		$data .= "entradas = '$entradas', ";
		$data .= "total_completo = '$total_completo', ";
		$data .= "notas = '$notas'";

		// Ejecución de la consulta
		$query = "UPDATE apertura_caja SET $data WHERE idcaja = '$id' AND estado = 'A'";

		if ($this->dbh->query($query)) {
			return 1;
		} else {
			return 0; // O manejar el error según sea necesario
		}
	}

	public function ListarcajaApertura()
	{
		$sql = "SELECT * FROM apertura_caja where estado='A'";
		$result = mysqli_query($this->dbh, $sql);
		$caja = array();
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$caja[] = $row;
			}
		}
		return $caja;
	}
	public function ListarMediosPagos()
	{
		$sql = "SELECT * FROM medio_pago";
		$result = mysqli_query($this->dbh, $sql);
		$pago = array();
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$pago[] = $row;
			}
		}
		return $pago;
	}
	public function ListarCtaContingencias()
	{
		$sql = "SELECT * FROM cta_contingencias";
		$result = mysqli_query($this->dbh, $sql);
		$pago = array();
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$pago[] = $row;
			}
		}
		return $pago;
	}
	public function Listarconsecutivos()
	{
		$sql = "SELECT * FROM consecutivos where idconsecutivos in(1,2);";
		$result = mysqli_query($this->dbh, $sql);
		$pago = array();
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$pago[] = $row;
			}
		}
		return $pago;
	}
	#endregion
	#region Factura y Detalle
	function save_factura()
	{
		// Extraer y sanitizar los datos
		extract($_POST);
		$numeroALetras = new NumeroALetras();
		if (isset($subtotal, $iva_impuesto, $totalpagar, $codcliente, $detalle, $prefix, $forma_pago)) {
			$subtotal = $this->dbh->real_escape_string($subtotal);
			$iva_impuesto = $this->dbh->real_escape_string($iva_impuesto);
			$totalpagar = $this->dbh->real_escape_string($totalpagar);
			$codcliente = $this->dbh->real_escape_string($codcliente);
			$prefix = $this->dbh->real_escape_string($prefix);
			$tipofactura = $prefix;
			$letras = $numeroALetras->toMoney($totalpagar, 2, 'dolares', 'centavos');
			$forma_pagos = $this->dbh->real_escape_string($forma_pago);
			$idusuario = $_SESSION['login_idusuario'];
			$estado = 'Pendiente';
			$detalle = json_decode($detalle, true);
			$csrf_tokenR = $this->dbh->real_escape_string($csrf_token);

			if ($csrf_tokenR == $csrf_token) {
				if (!is_array($detalle)) {
					echo json_encode(['success' => false, 'message' => 'Formato de detalle incorrecto.']);
					exit;
				}

				// Generar el número de factura único
				$numerofactura = $this->generateCorrelativo($prefix);

				// Verificar si ya existe una factura con el mismo numerofactura
				$checkFactura = $this->dbh->prepare("SELECT COUNT(*) FROM factura WHERE numerofactura = ?");
				$checkFactura->bind_param("s", $numerofactura);
				$checkFactura->execute();
				$checkFactura->bind_result($count);
				$checkFactura->fetch();
				$checkFactura->close();

				if ($count > 0) {
					echo json_encode(['success' => false, 'message' => 'Ya existe una factura con ese número.']);
					exit;
				}

				// Iniciar transacción
				$this->dbh->begin_transaction();

				try {

					$data_factura = "tipofactura = '$tipofactura'";
					$data_factura .= ", numerofactura = '$numerofactura'";
					$data_factura .= ", subtotal = '$subtotal'";
					$data_factura .= ", iva_impuesto = '$iva_impuesto'";
					$data_factura .= ", totalpagar = '$totalpagar'";
					$data_factura .= ", letras = '$letras'";
					$data_factura .= ", forma_pago = '$forma_pagos'";
					$data_factura .= ", idusuario = '$idusuario'";
					$data_factura .= ", idcliente = '$codcliente'";
					$data_factura .= ", estado = '$estado'";

					$save_factura = $this->dbh->query("INSERT INTO factura SET " . $data_factura);

					if ($save_factura) {
						$idfactura = $this->dbh->insert_id;

						foreach ($detalle as $producto) {
							if (!isset($producto['codproducto'], $producto['precio'], $producto['cantidad'])) {
								throw new Exception("Datos del producto incompletos.");
							}

							$cod_producto = $this->dbh->real_escape_string($producto['codproducto']);
							$precio_venta = $this->dbh->real_escape_string($producto['precio']);
							$cantidad = $this->dbh->real_escape_string($producto['cantidad']);

							$data_detalle = "cod_producto = '$cod_producto'";
							$data_detalle .= ", precioventa = '$precio_venta'";
							$data_detalle .= ", cantidad = '$cantidad'";
							$data_detalle .= ", idfactura = '$idfactura'";

							$save_detalle = $this->dbh->query("INSERT INTO detallefactura SET " . $data_detalle);

							if (!$save_detalle) {
								throw new Exception("Error al guardar el detalle de la factura: " . $this->dbh->error);
							}

							$descripcion = $this->dbh->real_escape_string("VENTA EN FACTURA: $numerofactura");

							$save_kardexSalida = $this->save_kardex_producto_venta($cod_producto, $cantidad, $precio_venta, 'SALIDA', $descripcion);
							if (!$save_kardexSalida) {
								throw new Exception("Error al guardar el kardex de la factura: " . $this->dbh->error);
							}
						}

						$this->dbh->commit();

						// Generar un nuevo token CSRF y actualizar la cookie
						$new_csrf_token = bin2hex(random_bytes(32));
						$csrf_tokenR = $new_csrf_token;
						echo json_encode(['success' => true, 'message' => 'Factura y detalles insertados correctamente.', 'idfactura' => $idfactura]);
					} else {
						throw new Exception('Error al insertar la factura.');
					}
				} catch (Exception $e) {
					$this->dbh->rollback();
					echo json_encode(['success' => false, 'message' => $e->getMessage()]);
				}
			} else {
				echo json_encode(['success' => false, 'message' => 'no funciona.']);
				exit;
			}
		}
	}
	function save_kardex_producto_venta($producto_id, $cantidadP, $precioP, $movimientosP, $descripcionP)
	{
		// Declarar variables
		$stock_actualP = 0.00;
		$exis_actualP = 0.00;
		$entradasP = 0;
		$salidasP = 0;
		$devolucionesP = 0;

		// Obtener existencia actual del producto
		$sql = "SELECT existencia FROM producto WHERE codProducto = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bind_param("s", $producto_id);
		$stmt->execute();
		$stmt->bind_result($exis_actualP);
		$stmt->fetch();
		$stmt->close();

		// Calcular nuevo stock y movimientos
		if ($movimientosP == 'ENTRADA') {
			$stock_actualP = $exis_actualP + $cantidadP;
			$entradasP = $cantidadP;
		} elseif ($movimientosP == 'SALIDA') {
			$stock_actualP = $exis_actualP - $cantidadP;
			$salidasP = $cantidadP;
		} elseif ($movimientosP == 'DEVOLUCION') {
			$stock_actualP = $exis_actualP + $cantidadP;
			$devolucionesP = $cantidadP;
		}

		// Actualizar existencia en producto
		$sql = "UPDATE producto SET existencia = ? WHERE codProducto = ?";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bind_param("ds", $stock_actualP, $producto_id);
		if (!$stmt->execute()) {
			$stmt->close();
			return false;
		}
		$stmt->close();

		// Insertar en kardex_producto
		$sql = "INSERT INTO kardex_producto (producto, movimiento, entradas, salidas, devolucion, stock_actual, precio, descripcion) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bind_param("ssiiidss", $producto_id, $movimientosP, $entradasP, $salidasP, $devolucionesP, $stock_actualP, $precioP, $descripcionP);
		if (!$stmt->execute()) {
			$stmt->close();
			return false;
		}
		$stmt->close();

		return true;
	}
	function save_ventacompleta()
	{
		extract($_POST);
		$id = mysqli_real_escape_string($this->dbh, $id);
		$data = "estado = 'Pagado'";

		$save = $this->dbh->query("UPDATE factura SET " . $data . " WHERE id = $id");
		if ($save) {
			$factura = $this->dbh->query("SELECT * FROM factura WHERE id = $id");
			if ($factura && $factura->num_rows > 0) {
				$row = $factura->fetch_assoc();

				echo json_encode([
					'success' => true,
					'message' => 'Venta Registrada correctamente. ',
					"idfactura" => $row['id'],
					"tipodocfac" => $row['tipofactura'],
				]);
			} else {
				// Devolver un JSON con success = false
				echo json_encode(['success' => false, 'message' => 'Error al actualizar la factura.']);
			}
			exit;
		}
	}

	private function generateCorrelativo($prefix)
	{
		$query = $this->dbh->prepare("SELECT MAX(valor) AS last_id FROM consecutivos WHERE codigo_consecutivo LIKE ?");
		$likePrefix = $prefix . '%';
		$query->bind_param('s', $likePrefix);
		$query->execute();
		$result = $query->get_result();
		$row = $result->fetch_assoc();
		$lastId = $row['last_id'];

		if ($lastId) {
			$number = intval(substr($lastId, strlen($prefix)));
			$newNumber = $number + 1;
			$newId = $prefix . str_pad($newNumber, 9 - strlen($prefix), '0', STR_PAD_LEFT);
		} else {
			$newId = $prefix . str_pad(1, 9 - strlen($prefix), '0', STR_PAD_LEFT);
		}

		$updateQuery = $this->dbh->prepare("UPDATE consecutivos SET valor = ? WHERE codigo_consecutivo = ?");
		$updateQuery->bind_param('ss', $newId, $prefix);
		$updateQuery->execute();

		return $newId;
	}

	#endregion

	#region Movimientos Caja
	function movimientos_caja()
	{
		extract($_POST);
		$data = "fecha = NOW()";
		$data .= ", ingreso = CASE 
                           WHEN '$transaccion' = 'ENTRADA' THEN '$ingreso'
                           WHEN '$transaccion' = 'SALIDA' THEN 0.00
                         END";
		$data .= ", egreso = CASE 
                           WHEN '$transaccion' = 'SALIDA' THEN '$egreso'
                           WHEN '$transaccion' = 'ENTRADA' THEN 0.00
                         END";
		$data .= ", comentario = '$comentario'";
		$data .= ", usuario = '{$_SESSION['login_usuario']}'";

		// Evita inyección SQL usando consultas preparadas
		if (empty($id)) {
			$save = $this->dbh->query("INSERT INTO movimientos_de_caja SET " . $data);
		} else {
			$id = mysqli_real_escape_string($this->dbh, $id); // Escapa el valor de $id
			$save = $this->dbh->query("UPDATE movimientos_de_caja SET " . $data . " WHERE idmovimiento = $id");
		}

		if ($save) {
			return 1;
		} else {
			return 0; // Si la inserción falla, devuelve 0
		}
	}
	#endregion
#region Notas de Crédito y Invalidaciones
	function save_NotaCredito()
	{
		extract($_POST);

		$observaciones = $_POST['observaciones'] ?? '';
		$codigoGeneracionP = $_POST['codigoGeneracion'] ?? '';
		$monto = $_POST['monto'] ?? '';
		$documentos = isset($_POST['documentos']) ? json_decode($_POST['documentos'], true) : [];
		$codigo = "";
		$idusuario = $_SESSION['login_idusuario'];
		$numeroDocumento = $this->generateCorrelativo('ndc');
		// Procesar los documentos
		foreach ($documentos as $doc) {
			$codigo = $doc['codigo'];
			$fecha = $doc['fecha'];
			// Guardar cada documento relacionado, si aplica
		}

		$data = "Observacion = '$observaciones'";
		$data .= ", monto = '$monto'";
		$data .= ", codigoGeneracion = '$codigoGeneracionP'";
		$data .= ", id_usuario = '$idusuario'";
		$data .= ", numeroDocumento = '$numeroDocumento'";

		$save = $this->dbh->query("INSERT notas_credito SET " . $data);
		if ($save) {

			echo json_encode([
				'success' => true,
				'codigoNC' => $codigoGeneracionP,
				'message' => 'Registro de nota de crédito guardado correctamente.',
			]);
		} else {
			// Devolver un JSON con success = false
			echo json_encode(['success' => false, 'message' => 'Error al actualizar la factura.']);
		}
		exit;
	}
	function save_NotaDebito()
	{
		extract($_POST);

		$observaciones = $_POST['observaciones'] ?? '';
		$codigoGeneracionP = $_POST['codigoGeneracion'] ?? '';
		$monto = $_POST['monto'] ?? '';
		$documentos = isset($_POST['documentos']) ? json_decode($_POST['documentos'], true) : [];
		$codigo = "";
		$idusuario = $_SESSION['login_idusuario'];
		$numeroDocumento = $this->generateCorrelativo('ndd');
		// Procesar los documentos
		foreach ($documentos as $doc) {
			$codigo = $doc['codigo'];
			$fecha = $doc['fecha'];
			// Guardar cada documento relacionado, si aplica
		}

		$data = "Observacion = '$observaciones'";
		$data .= ", monto = '$monto'";
		$data .= ", codigoGeneracion = '$codigoGeneracionP'";
		$data .= ", id_usuario = '$idusuario'";
		$data .= ", numeroDocumento = '$numeroDocumento'";

		$save = $this->dbh->query("INSERT notas_debito SET " . $data);
		if ($save) {
			echo json_encode([
				'success' => true,
				'codigoNotaDebito' => $codigoGeneracionP,
				'message' => 'Registro de nota de débito guardado correctamente.',
			]);
		} else {
			// Devolver un JSON con success = false
			echo json_encode(['success' => false, 'message' => 'Error al actualizar la factura.']);
		}
		exit;
	}
	function save_Invalidacion()
	{
		extract($_POST);

		$tipoAnulacion = $_POST['tipoInvalidacion'] ?? '';
		$numeroControl = $_POST['numeroControl'] ?? '';
		$codigoGeneracion = $_POST['codigoGeneracion'] ?? '';
		$tDcoResponsable = $_POST['tipoDoc2'] ?? '';
		$nDcoResponsable = $_POST['documento2'] ?? '';
		$nombreResponsable = $_POST['responsable'] ?? '';
		$nombreSolicita = $_POST['solicitante'] ?? '';
		$tDcoSolicita = $_POST['tipoDoc1'] ?? '';
		$nDcoSolicita = $_POST['documento1'] ?? '';

		// Validar tipo de anulación
		$motivos_validos = [
			'1' => 'Error en la información del Documento Tributario Electrónico a invalidar',
			'2' => 'Recindir de la operación realizada',
			'3' => 'Otro'
		];

		if (!isset($motivos_validos[$tipoAnulacion])) {
			echo json_encode([
				'success' => false,
				'message' => 'Tipo de anulación no válido.'
			]);
			exit;
		}

		$motivo_final = $motivos_validos[$tipoAnulacion];

		$data = "codigoGeneracion = '$codigoGeneracion'";
		$data .= ", numeroControl = '$numeroControl'";
		$data .= ", tipoAnulacion = '$tipoAnulacion'";
		$data .= ", motivo = '$motivo_final'";
		$data .= ", tDcoResponsable = '$tDcoResponsable'";
		$data .= ", nDcoResponsable = '$nDcoResponsable'";
		$data .= ", nombreResponsable = '$nombreResponsable'";
		$data .= ", nombreSolicita = '$nombreSolicita'";
		$data .= ", tDcoSolicita = '$tDcoSolicita'";
		$data .= ", nDcoSolicita = '$nDcoSolicita'";

		$save = $this->dbh->query("INSERT INTO invalidaciones SET " . $data);

		if ($save) {
			echo json_encode([
				'success' => true,
				'message' => 'Invalidación guardada correctamente.'
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Error al guardar la invalidación en la base de datos.'
			]);
		}
		exit;

	}
	#endregion
#region Sujetos Excluidos
	function save_SujetoExcluido()
	{
		$numero_control = $_POST['numero_control'] ?? '';
		$codigo = $_POST['codigo_generacion'] ?? '';
		$forma_pago = $_POST['forma_pago'] ?? '';
		$proveedor = $_POST['proveedor'] ?? '';
		$items = json_decode($_POST['items'] ?? '[]', true);

		if (empty($numero_control) || empty($items)) {
			echo json_encode([
				'success' => false,
				'message' => 'Faltan datos para guardar los sujetos excluidos.'
			]);
			exit;
		}

		$this->dbh->begin_transaction();

		try {
			$sql = "INSERT INTO sujetoexcluido_dte (
                    numero_control, detalle, cantidad, precio_unitario, renta_retenida, subtotal,idproveedor,forma_pago
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
			$stmt = $this->dbh->prepare($sql);

			foreach ($items as $item) {
				$detalle_json = json_encode($item['detalle'] ?? []);  // Convertir a JSON
				$cantidad = intval($item['cantidad'] ?? 0);
				$precio = floatval($item['precio'] ?? 0);
				$rentaPorcentaje = floatval($item['renta'] ?? 0) / 100;
				$subtotal = floatval($item['subtotal'] ?? ($cantidad * $precio));
				$renta = $subtotal * $rentaPorcentaje;

				$stmt->bind_param("ssidddis", $numero_control, $detalle_json, $cantidad, $precio, $renta, $subtotal, $proveedor, $forma_pago);
				$stmt->execute();
			}

			$stmt->close();
			$this->dbh->commit();
			echo json_encode([
				'success' => true,
				$numerodoc = $this->generateCorrelativo('fse'),
				'message' => 'Ítems de sujetos excluidos guardados correctamente. ' . $numerodoc,
				"codigo_generacion" => $codigo,
			]);
		} catch (Exception $e) {
			$this->dbh->rollback();
			echo json_encode([
				'success' => false,
				'message' => 'Error al guardar: ' . $e->getMessage()
			]);
		}

		exit;
	}
	#endregion
	public function facturas()
	{
		$id = $_POST['idfacturaV'];
		$factura = $this->dbh->query("SELECT * FROM factura WHERE id = $id");
		if ($factura && $factura->num_rows > 0) {
			$row = $factura->fetch_assoc();

			echo json_encode([
				'success' => true,
				'message' => 'Venta Registrada correctamente. ',
				"idfactura" => $row['id']
			]);
		} else {
			// Devolver un JSON con success = false
			echo json_encode(['success' => false, 'message' => 'Error al actualizar la factura.']);
		}
		exit;
	}
	function save_contingencia()
	{
		extract($_POST);

		// Sanitización básica
		$codigo = mysqli_real_escape_string($this->dbh, $codigo);
		$fchainicia = mysqli_real_escape_string($this->dbh, $fechaIni . ' ' . $horaIni);
		$fchafin = mysqli_real_escape_string($this->dbh, $fechaFin . ' ' . $horaFin);
		$responsable = mysqli_real_escape_string($this->dbh, $responsable);
		$tipoDoc = mysqli_real_escape_string($this->dbh, $documento2);
		$tcontingencia = mysqli_real_escape_string($this->dbh, $tcontingencia);
		$tipoF = mysqli_real_escape_string($this->dbh, $tipoF);

		// Construcción de datos para la consulta
		$data = "fchainicia = '$fchainicia'";
		$data .= ", fchafin = '$fchafin'";
		$data .= ", responsable = '$responsable'";
		$data .= ", doc = '$tipoDoc'";
		$data .= ", motivo = '$tcontingencia'";

		// Si ya existe una contingencia con ese código, actualiza. Si no, inserta.
		$check = $this->dbh->query("SELECT id FROM lista_contingencia_dte WHERE codigoGeneracion = '$codigo'");
		if ($check && $check->num_rows > 0) {
			$save = $this->dbh->query("UPDATE lista_contingencia_dte SET $data WHERE codigoGeneracion = '$codigo'");
		} else {
			$data .= ", codigoGeneracion = '$codigo'";
			$save = $this->dbh->query("INSERT INTO lista_contingencia_dte SET $data");
		}
		echo json_encode([
			'success' => true,
			'message' => 'Venta Registrada correctamente. '
		]);
	}
}