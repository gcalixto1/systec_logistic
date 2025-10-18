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
	#region Etiquetas
	function save_etiqueta()
	{
		extract($_POST);
		$data = " etiqueta = '$etiqueta' ";
		if (empty($id)) {
			$save = $this->dbh->query("INSERT INTO etiquetas set " . $data);
		} else {
			$save = $this->dbh->query("UPDATE etiquetas set " . $data . " where id = " . $id);
		}
		if ($save) {
			return 1;
		}
	}
	function delete_etiqueta()
	{
		extract($_POST);
		$delete = $this->dbh->query("DELETE FROM etiquetas where id = " . $id);
		if ($delete)
			return 1;
	}
	public function Listaretiquetas()
	{
		$sql = "SELECT * FROM etiquetas ORDER BY etiqueta ASC";

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
	#region Tipo_Producto
	function save_tipo()
	{
		extract($_POST);
		$data = " tipo = '$tipo' ";
		if (empty($id)) {
			$save = $this->dbh->query("INSERT INTO tipo_producto set " . $data);
		} else {
			$save = $this->dbh->query("UPDATE tipo_producto set " . $data . " where id = " . $id);
		}
		if ($save) {
			return 1;
		}
	}
	function delete_tipo()
	{
		extract($_POST);
		$delete = $this->dbh->query("DELETE FROM tipo_producto where id = " . $id);
		if ($delete)
			return 1;
	}
	public function Listartipos()
	{
		$sql = "SELECT * FROM tipo_producto ORDER BY tipo	 ASC";

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
	#region UMB
	function save_umb()
	{
		extract($_POST);
		$data = " umb = '$umb' ";
		if (empty($id)) {
			$save = $this->dbh->query("INSERT INTO umbs set " . $data);
		} else {
			$save = $this->dbh->query("UPDATE umbs set " . $data . " where id = " . $id);
		}
		if ($save) {
			return 1;
		}
	}
	function delete_umb()
	{
		extract($_POST);
		$delete = $this->dbh->query("DELETE FROM umbs where id = " . $id);
		if ($delete)
			return 1;
	}
	public function Listarumbs()
	{
		$sql = "SELECT * FROM umbs ORDER BY umb	 ASC";

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
	#region Relaciones
	function save_relacion()
	{
		extract($_POST);
		$data = " relacion = '$relacion' ";
		if (empty($id)) {
			$save = $this->dbh->query("INSERT INTO relaciones set " . $data);
		} else {
			$save = $this->dbh->query("UPDATE relaciones set " . $data . " where id = " . $id);
		}
		if ($save) {
			return 1;
		}
	}
	function delete_relacion()
	{
		extract($_POST);
		$delete = $this->dbh->query("DELETE FROM relaciones where id = " . $id);
		if ($delete)
			return 1;
	}
	public function Listarrelaciones()
	{
		$sql = "SELECT * FROM relaciones ORDER BY relacion	 ASC";

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
	function save_almacen()
	{
		extract($_POST);
		$data = " codigo = '$codigo'";
		$data .= ", nombre = '$nombre'";
		if (empty($id)) {
			$save = $this->dbh->query("INSERT INTO almacenes set " . $data);
		} else {
			$save = $this->dbh->query("UPDATE almacenes set " . $data . " where id = " . $id);
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

		// Valores por defecto
		$estatus_cliente = isset($estatus_cliente) ? $estatus_cliente : 'ACTIVO';
		$tiene_sedes = isset($tiene_sedes) && $tiene_sedes == 'SI' ? "'SI'" : "'NO'";
		$plazos_pago_dias = isset($plazos_pago_dias) ? intval($plazos_pago_dias) : 0;
		$listas_precio_habilitadas = isset($listas_precio_habilitadas) ? intval($listas_precio_habilitadas) : 1;

		// Construcción segura de datos
		$data = "nit = '" . mysqli_real_escape_string($this->dbh, $nit) . "'";
		$data .= ", nombre_cliente = '" . mysqli_real_escape_string($this->dbh, $nombre_cliente) . "'";
		$data .= ", nombre_comercial = '" . mysqli_real_escape_string($this->dbh, $nombre_comercial) . "'";
		$data .= ", tiene_sedes = $tiene_sedes";
		$data .= ", estatus_cliente = '" . mysqli_real_escape_string($this->dbh, $estatus_cliente) . "'";
		$data .= ", asesor_handy_plast = '" . mysqli_real_escape_string($this->dbh, $asesor_handy_plast) . "'";
		$data .= ", plazos_pago_dias = $plazos_pago_dias";
		$data .= ", listas_precio_habilitadas = $listas_precio_habilitadas";
		$data .= ", nombre_sede = '" . mysqli_real_escape_string($this->dbh, $nombre_sede) . "'";
		$data .= ", direccion_sede = '" . mysqli_real_escape_string($this->dbh, $direccion_sede) . "'";
		$data .= ", ciudad = '" . mysqli_real_escape_string($this->dbh, $ciudad) . "'";
		$data .= ", departamento = '" . mysqli_real_escape_string($this->dbh, $departamento) . "'";
		$data .= ", telefono1 = '" . mysqli_real_escape_string($this->dbh, $telefono1) . "'";
		$data .= ", telefono2 = '" . mysqli_real_escape_string($this->dbh, $telefono2) . "'";
		$data .= ", correo_electronico = '" . mysqli_real_escape_string($this->dbh, $correo_electronico) . "'";

		// Insertar o actualizar
		if (empty($id)) {
			$save = $this->dbh->query("INSERT INTO clientes SET $data");
		} else {
			$id = mysqli_real_escape_string($this->dbh, $id);
			$save = $this->dbh->query("UPDATE clientes SET $data WHERE id = $id");
		}

		if ($save) {
			return 1; // éxito
		}

		return 0; // error
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
		$delete = $this->dbh->query("DELETE FROM cliente where id = " . $id);
		if ($delete)
			return 1;
	}
	public function listarclientes($filtro)
	{
		// Consulta para buscar por código (id o nit) o por nombre
		$consulta = "SELECT * 
                 FROM clientes 
                 WHERE id LIKE ? 
                    OR nit LIKE ? 
                    OR nombre_cliente LIKE ?";

		$stmt = $this->dbh->prepare($consulta);
		if ($stmt === false) {
			die("Error en la preparación de la consulta: " . $this->dbh->error);
		}

		// Agregar '%' para búsqueda parcial
		$filtro = "%" . $filtro . "%";

		// Como usamos 3 placeholders, hay que pasar el mismo valor 3 veces
		$stmt->bind_param("sss", $filtro, $filtro, $filtro);
		$stmt->execute();

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

		// Construcción segura de los datos
		$id = mysqli_real_escape_string($this->dbh, $id);
		$data = " tipo_id = '$tipo_id'";
		$data .= ", documento = '$documento'";
		$data .= ", nombre_proveedor = '$nombre_proveedor'";
		$data .= ", direccion = '$direccion'";
		$data .= ", plazo = '$plazo'";
		$data .= ", celular = '$celular'";
		$data .= ", email = '$email'";
		$data .= ", cupo_credito = '$cupo_credito'";
		$data .= ", nombre_contacto = '$nombre_contacto'";

		// Inserción o actualización
		if (empty($id)) {
			$save = $this->dbh->query("INSERT INTO proveedores SET " . $data);
		} else {

			$save = $this->dbh->query("UPDATE proveedores SET " . $data . " WHERE id = $id");
		}

		if ($save) {
			return 1;
		} else {
			return 0;
		}
	}

	function delete_proveedor()
	{
		extract($_POST);
		$delete = $this->dbh->query("DELETE FROM proveedores where id = " . $id);
		if ($delete)
			return 1;
	}
	function delete_pedidos()
	{
		extract($_POST);

		// Primero eliminar los registros relacionados en detalle_pedidos
		$delete_details = $this->dbh->query("DELETE FROM detalle_pedidos WHERE pedido_id = " . intval($idpedido));

		// Luego eliminar el pedido si se eliminaron los detalles
		if ($delete_details) {
			$delete_pedido = $this->dbh->query("DELETE FROM pedidos WHERE id = " . intval($idpedido));
			if ($delete_pedido) {
				return 1;
			}
		}

		return 0; // si algo falla
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
	// public function BuscarProveedores($search)
	// {
	// 	$sql = "SELECT idproveedor, proveedor FROM proveedores 
	// 			WHERE proveedor LIKE :search LIMIT 20";
	// 	$stmt = $this->dbh->prepare($sql);
	// 	$stmt->execute([':search' => '%' . $search . '%']);
	// 	return $stmt->fetchAll(PDO::FETCH_ASSOC);
	// }

	#endregion
	#region Producto
	function save_productos()
	{
		$campos = [
			'id_producto',
			'str_id',
			'cod_producto',
			'descripcion',
			'etiqueta',
			'micraje',
			'descripcion_sistema_contable',
			'tipo',
			'familia',
			'ref_1',
			'ref_2',
			'relacion',
			'calibre',
			'und_embalaje_minima',
			'peso_kg',
			'peso_kg_paca_caja',
			'umb',
			'ref_tubo',
			'peso_tubo',
			'tiempo_produccion_paca',
			'stock_minimo',
			'precio_lista_5',
			'precio_remision_lista_5',
			'activo',
			'lead_time'
		];

		$data = [];
		foreach ($campos as $campo) {
			$valor = $_POST[$campo] ?? null;
			$valor_escapado = mysqli_real_escape_string($this->dbh, $valor);
			if ($campo === 'activo') {
				$valor_escapado = ($valor_escapado === '0' || strtolower($valor_escapado) === 'false') ? 0 : 1;
			}
			if ($campo !== 'id_producto') { // No incluir el id en SET
				$data[] = "$campo = '$valor_escapado'";
			}
		}

		// Campos automáticos
		if (empty($_POST['id_producto'])) {
			$data[] = "fecha_creacion = NOW()";
		}
		$data[] = "ultima_actualizacion = NOW()";

		$setData = implode(", ", $data);
		$id_producto = mysqli_real_escape_string($this->dbh, $_POST['id_producto'] ?? '');

		if (empty($id_producto)) {
			// INSERT
			$query = "INSERT INTO producto SET $setData";
			$save = $this->dbh->query($query);
		} else {
			// UPDATE
			$query = "UPDATE producto SET $setData WHERE id_producto = '$id_producto'";
			$save = $this->dbh->query($query);
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
		$delete = $this->dbh->query("DELETE FROM producto where id_producto = " . $id_producto);
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
		// Consulta con los campos correctos de la tabla producto
		$consulta = "SELECT * 
                 FROM producto 
                 WHERE descripcion LIKE ?  
                    OR cod_producto LIKE ?  
                    OR str_id LIKE ?";

		// Preparar la consulta
		$stmt = $this->dbh->prepare($consulta);
		if ($stmt === false) {
			die("Error en la preparación de la consulta: " . $this->dbh->error);
		}

		// Comodines para búsqueda parcial
		$filtro = "%" . $filtro . "%";

		// Vincular parámetros (tres veces porque hay 3 LIKE)
		$stmt->bind_param("sss", $filtro, $filtro, $filtro);
		$stmt->execute();

		// Obtener resultados
		$result = $stmt->get_result();
		$productos = $result->fetch_all(MYSQLI_ASSOC);

		// Cerrar statement
		$stmt->close();

		// Retornar array de productos
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
	#region Pedido y Detalle
	public function save_pedido()
	{
		// Validar datos recibidos
		$cliente_id      = $this->dbh->real_escape_string($_POST['cliente_id'] ?? '');
		$muestra      = $this->dbh->real_escape_string($_POST['envioMuestra'] ?? '');
		$canal_venta     = $this->dbh->real_escape_string($_POST['canal_venta'] ?? '');
		$plazo_pago_dias = $this->dbh->real_escape_string($_POST['plazo_pago_dias'] ?? '');
		$tipo_transporte = $this->dbh->real_escape_string($_POST['tipo_transporte'] ?? '');
		$observaciones   = $this->dbh->real_escape_string($_POST['observaciones'] ?? '');
		$detalle         = json_decode($_POST['detalle'] ?? '[]', true);

		if (empty($cliente_id) || !is_array($detalle) || count($detalle) == 0) {
			echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
			exit;
		}

		$estatus = 'PENDIENTE';
		$idusuario = $_SESSION['login_idusuario'] ?? 1;

		// Correlativo
		$consecutivo = $this->generateCorrelativo("PED");
		$numero_pedido = "PED-" . $consecutivo;

		$this->dbh->begin_transaction();

		try {
			// Insertar pedido
			$sql = "INSERT INTO pedidos SET 
                numero_pedido='$numero_pedido',
                cliente_id='$cliente_id',
                canal_venta='$canal_venta',
                plazo_pago_dias='$plazo_pago_dias',
                tipo_transporte='$tipo_transporte',
                observaciones='$observaciones',
                estatus='$estatus',
				usuario_id = $idusuario";

			if (!$this->dbh->query($sql)) {
				throw new Exception("Error al insertar pedido: " . $this->dbh->error);
			}

			$pedido_id = $this->dbh->insert_id;

			if ($pedido_id == 0) {
				throw new Exception("No se pudo obtener ID del pedido");
			}

			// Insertar detalle
			// Insertar detalle
			foreach ($detalle as $d) {
				$pid     = $this->dbh->real_escape_string($d['productoId']);
				$cant    = $this->dbh->real_escape_string($d['cantidad']);
				$precio  = $this->dbh->real_escape_string($d['precio']);

				$subtotal = $cant * $precio;
				$iva      = $this->dbh->real_escape_string($d['iva']);
				$total    = $subtotal + $iva;

				// Determinar si es facturado
				$facturado = ($iva > 0) ? "SI" : "NO";

				$sql_det = "INSERT INTO detalle_pedidos SET
                pedido_id='$pedido_id',
                producto_id='$pid',
                cantidad='$cant',
                precio='$precio',
                subtotal='$subtotal',
                iva='$iva',
                total='$total',
                facturado='$facturado'"; // <-- IMPORTANTE: comillas para string

				if (!$this->dbh->query($sql_det)) {
					throw new Exception("Error al insertar detalle: " . $this->dbh->error);
				}
			}

			$this->dbh->commit();

			echo json_encode([
				'success' => true,
				'message' => 'Pedido guardado',
				'id_pedido' => $pedido_id,
				'numero_pedido' => $numero_pedido
			]);
		} catch (Exception $e) {
			$this->dbh->rollback();
			echo json_encode([
				'success' => false,
				'message' => $e->getMessage()
			]);
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
	function ingreso_orden_compra()
	{
		extract($_POST);

		// === Validaciones iniciales ===
		if (!isset($type)) {
			echo json_encode(['success' => false, 'message' => 'Tipo de acción no especificado']);
			return;
		}

		// === OBTENER DATOS DE ORDEN ===
		if ($type == 'get') {
			$id_oc = intval($id_oc);
			$res = $this->dbh->query("SELECT oc.*, p.nombre_proveedor AS proveedor 
			FROM orden_compra oc 
			JOIN proveedores p ON oc.proveedor_id = p.id 
			WHERE oc.id_oc = $id_oc");

			if (!$res || $res->num_rows == 0) {
				echo json_encode(['success' => false, 'message' => 'Orden no encontrada']);
				return;
			}

			$row = $res->fetch_assoc();
			$detalle = [];
			$resDet = $this->dbh->query("SELECT id_detalle, producto, cantidad, precio, subtotal, iva, total, cantidad_recibida 
			FROM orden_compra_detalle 
			WHERE id_oc = $id_oc");

			while ($r = $resDet->fetch_assoc()) {
				$detalle[] = $r;
			}

			echo json_encode([
				'success' => true,
				'proveedor' => $row['proveedor'],
				'fecha' => $row['fecha_oc'],
				'estado' => $row['estado'] ?? 'PENDIENTE',
				'detalle' => $detalle
			]);
			return;
		}

		// === GUARDAR INGRESO ===
		if ($type == 'save') {
			$id_oc = intval($id_oc);
			$estado = mysqli_real_escape_string($this->dbh, $estado);
			$detalle = json_decode($detalle, true);

			if (!$id_oc || !$detalle) {
				echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
				return;
			}

			// Actualizar detalle (cantidad recibida)
			foreach ($detalle as $d) {
				$id_detalle = intval($d['id_detalle']);
				$cant_recibida = floatval($d['cantidad_recibida']);

				$this->dbh->query("UPDATE orden_compra_detalle 
				SET cantidad_recibida = $cant_recibida 
				WHERE id_detalle = $id_detalle");

				// (Opcional) Actualizar stock
				$this->dbh->query("UPDATE productos p
				JOIN orden_compra_detalle d ON p.nombre = d.producto
				SET p.stock = p.stock + $cant_recibida
				WHERE d.id_detalle = $id_detalle");
			}

			// Actualizar estado general
			$this->dbh->query("UPDATE orden_compra SET estado = '$estado' WHERE id_oc = $id_oc");

			echo json_encode(['success' => true, 'message' => 'Ingreso de orden guardado correctamente']);
			return;
		}

		// === Acción no reconocida ===
		echo json_encode(['success' => false, 'message' => 'Acción inválida']);
	}
	function get_orden()
{
    extract($_POST);
    $id_oc = mysqli_real_escape_string($this->dbh, $id_oc);

    // === Obtener datos de la orden ===
    $orden = $this->dbh->query("SELECT o.numero_oc, o.fecha_oc, o.estado, p.nombre_proveedor AS proveedor
                                FROM orden_compra o
                                INNER JOIN proveedores p ON p.id = o.proveedor_id
                                WHERE o.id_oc = '$id_oc' LIMIT 1");

    if (!$orden || $orden->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'Orden no encontrada.']);
        return;
    }

    $info = $orden->fetch_assoc();

    // === Obtener detalle ===
    $detalle = [];
    $qdet = $this->dbh->query("SELECT 
                                    d.id_detalle, 
                                    d.producto, 
                                    p.str_id AS id_interno, 
                                    p.descripcion, 
                                    d.cantidad, 
                                    IFNULL(d.cantidad_recibida, 0) AS cantidad_recibida,
                                    d.precio, 
                                    p.umb, 
                                    p.und_embalaje_minima
                               FROM orden_compra_detalle d
                               INNER JOIN producto p ON p.id_producto = d.producto
                               WHERE d.id_oc = '$id_oc'");

    while ($d = $qdet->fetch_assoc()) {
        $detalle[] = [
            'id_detalle' => $d['id_detalle'],
            'producto' => $d['producto'],
            'id_interno' => $d['id_interno'],
            'descripcion' => $d['descripcion'],
            'cantidad' => floatval($d['cantidad']),
            'cantidad_recibida' => floatval($d['cantidad_recibida']),
            'precio' => floatval($d['precio']),
            'umb' => $d['umb'] ?? '',
            'und_embalaje_minima' => $d['und_embalaje_minima'] ?? ''
        ];
    }

    // === Obtener almacenes (id incluido) ===
    $almacenes = [];
    $resAlm = $this->dbh->query("SELECT id, nombre FROM almacenes ORDER BY nombre ASC");
    while ($a = $resAlm->fetch_assoc()) {
        $almacenes[] = [
            'id_almacen' => $a['id'],
            'nombre' => $a['nombre']
        ];
    }

    // === Respuesta final ===
    echo json_encode([
        'success' => true,
        'proveedor' => $info['proveedor'],
        'fecha' => $info['fecha_oc'],
        'estado' => $info['estado'],
        'detalle' => $detalle,
        'almacenes' => $almacenes
    ]);
}


	// ===================================================
	// GUARDAR INGRESO DE ORDEN DE COMPRA
	// ===================================================
	function save_ingreso()
{
    extract($_POST);
    $id_oc = mysqli_real_escape_string($this->dbh, $id_oc);
    $estado = mysqli_real_escape_string($this->dbh, $estado);
    $detalle = json_decode($detalle, true);

    if (empty($id_oc) || !is_array($detalle)) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
        return;
    }

    // === Obtener info de la orden ===
    $orden = $this->dbh->query("SELECT o.numero_oc, o.proveedor_id, p.nombre_proveedor AS proveedor
                                FROM orden_compra o
                                INNER JOIN proveedores p ON p.id = o.proveedor_id
                                WHERE o.id_oc = '$id_oc' LIMIT 1");
    $info = $orden->fetch_assoc();
    $numero_oc = $info['numero_oc'];
    $proveedor = $info['proveedor_id'];

    // === Generar correlativo de lote base ===
    $fechaActual = date('dmy');
    $res = $this->dbh->query("SELECT COUNT(*) AS total FROM movimientos_inventario");
    $row = $res->fetch_assoc();
    $correlativo = str_pad($row['total'] + 1, 5, '0', STR_PAD_LEFT);
    $loteBase = $correlativo . $fechaActual;

    // === Registrar detalle ===
    foreach ($detalle as $index => $d) {
        $id_detalle = intval($d['id_detalle']);
        $cant = floatval($d['cantidad']);
        $almacen_id = intval($d['almacen_id']); // desde el select del frontend
        $lote = !empty($d['lote']) ? mysqli_real_escape_string($this->dbh, $d['lote']) : ($loteBase . $index);

        // Actualizar cantidad recibida y lote/almacén
        $this->dbh->query("UPDATE orden_compra_detalle 
                           SET cantidad_recibida = '$cant', lote = '$lote', almacen_id = '$almacen_id'
                           WHERE id_detalle = '$id_detalle'");

        // Obtener datos del producto
        $prod = $this->dbh->query("SELECT d.producto, p.str_id AS id_interno, p.descripcion, d.precio, p.calibre, p.umb AS umb,p.ref_1,p.ref_2
                                   FROM orden_compra_detalle d
                                   INNER JOIN producto p ON p.id_producto = d.producto
                                   WHERE d.id_detalle = '$id_detalle' LIMIT 1")->fetch_assoc();

        $id_producto = $prod['producto'];
        $id_interno = $prod['id_interno'];
        $descripcion = $prod['descripcion'];
        $precio = $prod['precio'];
        $calibre = $prod['calibre'];
        $umb = $prod['umb'];
        $ref1 = $prod['ref_1'];
        $ref2 = $prod['ref_2'];
        $subtotal = $cant * $precio;

        // Insertar movimiento
        $this->dbh->query("INSERT INTO movimientos_inventario 
            (id_producto, id_interno, descripcion, cantidad, cliente_proveedor, fecha_movimiento, tipo_movimiento, num_documento, 
             costo_unitario, costo_total, calibre, umb, lote, almacen_id,ref1,ref2)
            VALUES ('$id_producto', '$id_interno', '$descripcion', '$cant', '$proveedor', NOW(), 
                    'Entrada OC', '$numero_oc', '$precio', '$subtotal', '$calibre', '$umb', '$lote', '$almacen_id','$ref1','$ref2')");
    }

    // === Actualizar estado general ===s
    $this->dbh->query("UPDATE orden_compra SET estado = '$estado' WHERE id_oc = '$id_oc'");

    echo json_encode(['success' => true, 'message' => 'Ingreso registrado correctamente con lote y almacén.']);
}


	function save_ingreso_manual() {
    extract($_POST);
    $detalle = json_decode($_POST['detalle'] ?? '[]', true);
    $almacen_id = intval($almacen_id ?? 0);

    if (empty($detalle)) {
        echo json_encode(['success'=>false,'message'=>'No hay productos para registrar.']); 
        return;
    }

    if ($almacen_id == 0) {
        echo json_encode(['success'=>false,'message'=>'Debe seleccionar un almacén.']); 
        return;
    }

    $fecha = mysqli_real_escape_string($this->dbh, $fecha);
    $proveedor = mysqli_real_escape_string($this->dbh, $proveedor);
    $observacion = mysqli_real_escape_string($this->dbh, $observacion);

    // Crear encabezado de ingreso manual
    $this->dbh->query("INSERT INTO ingreso_manual (fecha, proveedor_id, observacion, tipo_movimiento)
                       VALUES ('$fecha','$proveedor','$observacion','ENTRADA MANUAL')");
    $id_ingreso = $this->dbh->insert_id;

    // Generar lote global correlativo
    $resLote = $this->dbh->query("SELECT IFNULL(MAX(CAST(SUBSTRING(lote,1,5) AS UNSIGNED)),0)+1 AS correlativo 
                                  FROM ingreso_manual_detalle");
    $loteRow = $resLote->fetch_assoc();
    $correlativo = str_pad($loteRow['correlativo'],5,'0',STR_PAD_LEFT);
    $fechaLote = date('dmy');

    foreach ($detalle as $d) {
        $producto = $d['producto'];
        $cantidad = floatval($d['cantidad']);
        $loteProd = trim($d['lote']) ?: $correlativo.$fechaLote; // si no hay lote manual, genera automático
        $costo = floatval($d['costo']);
        $total = $cantidad * $costo;

        // Insertar detalle
        $this->dbh->query("INSERT INTO ingreso_manual_detalle 
            (id_ingreso, producto_id, cantidad, lote, almacen_id, costo_unitario, costo_total)
            VALUES ('$id_ingreso','$producto','$cantidad','$loteProd','$almacen_id','$costo','$total')");

        // Insertar movimiento
        $this->dbh->query("INSERT INTO movimientos_inventario 
            (id_producto, id_interno, descripcion, cantidad, lote, almacen_id, tipo_movimiento, cliente_proveedor, num_documento, costo_unitario, costo_total, fecha_movimiento,calibre, umb,ref1,ref2)
            SELECT p.id_producto,p.str_id, p.descripcion,'$cantidad','$loteProd','$almacen_id','Entrada manual','$proveedor','$id_ingreso','$costo','$total',NOW(), p.calibre, p.umb,p.ref_1,p.ref_2 
			FROM producto p  WHERE p.id_producto = '$producto'");
    }

    echo json_encode(['success'=>true,'message'=>'Ingreso manual registrado correctamente.']);
}
}
