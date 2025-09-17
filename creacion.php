<?php
include('conexionfin.php');
// Array de consultas SQL
$sqlArray = [
  "CREATE TABLE `menu` (
  `idMenu` int(11) NOT NULL AUTO_INCREMENT,
  `Menu` varchar(255) NOT NULL,
  `Url` varchar(255) NOT NULL,
  `Parent` int(11) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
   PRIMARY KEY (`idMenu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",

"INSERT INTO `menu` (`Menu`, `Url`, `Parent`, `icon`) VALUES
('Dashboard', 'index.php?page=home', NULL, 'fas fa-fw fa-tachometer-alt'),
(' Vender', 'index.php?page=nueva_venta', NULL, 'fa fa-calculator'),
(' Administracion', '', NULL, 'fa fa-cogs'),
(' Perfil General', 'index.php?page=perfil_general', 3, 'fa fa-building'),
(' Catalogos', '', NULL, 'fa fa-cogs'),
(' Mant. Clientes', 'index.php?page=cliente', 5, 'fa fa-address-card'),
(' Mant. Proveedores', 'index.php?page=proveedor', 5, 'fa fa-users'),
(' Mant. Productos', 'index.php?page=productos', 5, 'fa fa-cube'),
(' Revision Kardex', 'index.php?page=kardex_productos', 5, 'fa fa-clipboard'),
(' Control de Caja', '', NULL, 'fas fa-cash-register'),
(' Apertura de Caja', 'index.php?page=caja', 10, 'fas fa-cash-register'),
(' Movimientos de Caja', 'index.php?page=movimientos', 10, 'fa fa-clipboard'),
(' Cerrar Sesion', 'ajax.php?action=logout', NULL, 'fa fa-power-off'),
(' Reportes', '', 3, 'fa fa-file-pdf'),
(' Ventas diarias', 'ajax.php?action=reportes', 14, 'fa fa-file-pdf');",

"CREATE TABLE `roles_menu` (
  `idRolMenu` int(11) NOT NULL AUTO_INCREMENT,
  `idMenu` int(11) NOT NULL,
  `idRol` int(11) NOT NULL,
  `estado` enum('activo','inactivo') NOT NULL,
  PRIMARY KEY (`idRolMenu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",

"INSERT INTO `roles_menu` (`idMenu`, `idRol`, `estado`) VALUES
(1, 1, 'activo'),
(2, 1, 'activo'),
(3, 1, 'activo'),
(4, 1, 'activo'),
(5, 1, 'activo'),
(6, 1, 'activo'),
(7, 1, 'activo'),
(8, 1, 'activo'),
(9, 1, 'activo'),
(10, 1, 'activo'),
(11, 1, 'activo'),
(12, 1, 'activo'),
(13, 1, 'activo'),
(14, 1, 'activo'),
(15, 1, 'activo');",

  "CREATE TABLE IF NOT EXISTS `apertura_caja` (
    `idcaja` int(11) NOT NULL AUTO_INCREMENT,
    `num_apertura` varchar(10) DEFAULT NULL,
    `saldo_inicial` decimal(10,2) DEFAULT NULL,
    `fch_hora_cierre` datetime DEFAULT NULL,
    `usuario` varchar(25) DEFAULT NULL,
    `caja` varchar(50) DEFAULT NULL,
    `estado` varchar(5) DEFAULT NULL,
    `saldo_venta_total` decimal(10,2) DEFAULT NULL,
    `fch_hora_apertura` datetime DEFAULT current_timestamp(),
    `gasto` decimal(10,2) DEFAULT NULL,
    `notas` varchar(75) DEFAULT NULL,
    `saldo_tarjeta` decimal(10,2) DEFAULT NULL,
    `saldo_credito` decimal(10,2) DEFAULT NULL,
    `entradas` decimal(10,2) DEFAULT NULL,
    `total_completo` decimal(10,2) DEFAULT NULL,
    PRIMARY KEY (`idcaja`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1;",
  
  "CREATE TABLE IF NOT EXISTS `categoria` (
    `categoria_id` int(11) NOT NULL AUTO_INCREMENT,
    `categoria_des` varchar(100) DEFAULT NULL,
    PRIMARY KEY (`categoria_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1;",
  
  "CREATE TABLE IF NOT EXISTS `cliente` (
    `idcliente` int(11) NOT NULL AUTO_INCREMENT,
    `dni` varchar(10) NOT NULL,
    `nombre` varchar(100) NOT NULL,
    `telefono` varchar(15) NOT NULL,
    `direccion` varchar(200) NOT NULL,
    `usuario_id` int(11) NOT NULL,
    PRIMARY KEY (`idcliente`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1;",
  
  "CREATE TABLE IF NOT EXISTS `configuracion` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `dni` varchar(20) NOT NULL,
    `nombre` varchar(100) NOT NULL,
    `razon_social` varchar(100) NOT NULL,
    `telefono` varchar(15) NOT NULL,
    `email` varchar(100) NOT NULL,
    `direccion` text NOT NULL,
    `igv` decimal(10,2) NOT NULL,
    `impresion` varchar(10) NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1;",
  "INSERT INTO `configuracion` (`dni`, `nombre`, `razon_social`, `telefono`, `email`, `direccion`, `igv`,`impresion`) VALUES
('33950-5', 'PALENZO COLLEZIONI', 'PALENZO COLLECIONI', '2254-8542', 'palenzo.atencion@gmail.com', 'Sonsonate - El Salvador', 13.00,'58mm');",
  
  "CREATE TABLE IF NOT EXISTS `consecutivos` (
    `idconsecutivos` int(11) NOT NULL AUTO_INCREMENT,
    `codigo_consecutivo` varchar(45) DEFAULT NULL,
    `descripcionconse` varchar(45) DEFAULT NULL,
    `valor` varchar(45) DEFAULT NULL,
    `mascara` varchar(45) DEFAULT NULL,
    `fecha_hora` datetime DEFAULT current_timestamp(),
    PRIMARY KEY (`idconsecutivos`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1;",
  
  "INSERT INTO `consecutivos` (`codigo_consecutivo`, `descripcionconse`, `valor`, `mascara`, `fecha_hora`) VALUES
  ('tick', 'ticket', 'tick00000', 'NNNN99999', '2024-06-09 00:00:00'),
  ('fcf', 'factura consumidor final', 'fcf000000', 'NNN999999', '2024-06-09 00:00:00'),
  ('ccf', 'comprobante de credito fiscal', 'ccf000000', 'NNN999999', '2024-06-09 00:00:00');",
  
  "CREATE TABLE IF NOT EXISTS `detallefactura` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `cod_producto` varchar(50) DEFAULT NULL,
    `precioventa` decimal(10,2) DEFAULT NULL,
    `cantidad` int(11) DEFAULT NULL,
    `idfactura` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1;",
  
  "CREATE TABLE IF NOT EXISTS `factura` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `tipofactura` varchar(50) DEFAULT NULL,
    `numerofactura` varchar(50) DEFAULT NULL,
    `subtotal` decimal(10,2) DEFAULT NULL,
    `iva_impuesto` decimal(10,2) DEFAULT NULL,
    `totalpagar` decimal(10,2) DEFAULT NULL,
    `letras` varchar(255) DEFAULT NULL,
    `forma_pago` int(11) DEFAULT NULL,
    `fechafactura` datetime DEFAULT current_timestamp(),
    `idusuario` int(11) DEFAULT NULL,
    `idcliente` int(11) DEFAULT NULL,
    `estado` varchar(20) DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1;",
  
  "CREATE TABLE IF NOT EXISTS `kardex_producto` (
    `idkardex` int(11) NOT NULL AUTO_INCREMENT,
    `producto` varchar(25) DEFAULT NULL,
    `movimiento` varchar(50) DEFAULT NULL,
    `entradas` decimal(10,2) DEFAULT NULL,
    `salidas` decimal(10,2) DEFAULT NULL,
    `devolucion` decimal(10,2) DEFAULT NULL,
    `stock_actual` decimal(10,2) DEFAULT NULL,
    `precio` decimal(10,2) DEFAULT NULL,
    `descripcion` varchar(250) DEFAULT NULL,
    `fecha_trans` datetime DEFAULT current_timestamp(),
    PRIMARY KEY (`idkardex`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1;",
  
  "CREATE TABLE IF NOT EXISTS `medio_pago` (
    `idmedio` int(11) NOT NULL AUTO_INCREMENT,
    `medio_pago` varchar(100) NOT NULL,
    PRIMARY KEY (`idmedio`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1;",
  
  "CREATE TABLE IF NOT EXISTS `movimientos_de_caja` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `fecha` datetime NOT NULL,
    `ingreso` decimal(18,2) DEFAULT NULL,
    `egreso` decimal(18,2) DEFAULT NULL,
    `comentario` text DEFAULT NULL,
    `usuario` varchar(30) DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1;",
  
  "CREATE TABLE IF NOT EXISTS `producto` (
    `codproducto` int(11) NOT NULL AUTO_INCREMENT,
    `descripcion` varchar(200) NOT NULL,
    `proveedor` int(11) NOT NULL,
    `precio` decimal(10,2) NOT NULL,
    `existencia` decimal(10,2) DEFAULT NULL,
    `exis_min` decimal(10,2) NOT NULL,
    `codBarra` varchar(50) DEFAULT NULL,
    `talla` varchar(5) DEFAULT NULL,
    `categoria` int(11) DEFAULT NULL,
    `usuario_id` int(11) NOT NULL,
    PRIMARY KEY (`codproducto`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1;",
  
  "CREATE TABLE IF NOT EXISTS `proveedor` (
    `idproveedor` int(11) NOT NULL AUTO_INCREMENT,
    `proveedor` varchar(100) NOT NULL,
    `contacto` varchar(100) NOT NULL,
    `telefono` varchar(15) NOT NULL,
    `direccion` varchar(100) NOT NULL,
    `usuario_id` int(11) NOT NULL,
    PRIMARY KEY (`idproveedor`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1;",
  
  "CREATE TABLE IF NOT EXISTS `rol` (
    `idrol` int(11) NOT NULL AUTO_INCREMENT,
    `rol` varchar(50) NOT NULL,
    PRIMARY KEY (`idrol`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1;",
  
  "INSERT INTO `rol` (`idrol`, `rol`) VALUES
  (1, 'Administrador'),
  (2, 'Vendedor');",
  
  "CREATE TABLE IF NOT EXISTS `usuario` (
    `idusuario` int(11) NOT NULL AUTO_INCREMENT,
    `nombre` varchar(100) NOT NULL,
    `correo` varchar(100) NOT NULL,
    `usuario` varchar(20) NOT NULL,
    `clave` varchar(50) NOT NULL,
    `rol` int(11) NOT NULL,
    PRIMARY KEY (`idusuario`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1;",
  
  "INSERT INTO `usuario` (`idusuario`, `nombre`, `correo`, `usuario`, `clave`, `rol`) VALUES
  (1, 'Gerson Alexander Escobar', 'systec.atencion@gmail.com', 'admin', '827ccb0eea8a706c4c34a16891f84e7b', 1);"
];

// Ejecutar cada consulta
foreach ($sqlArray as $sql) {
    if ($conexion->query($sql) === TRUE) {
        echo "Consulta ejecutada con éxito: " . substr($sql, 0, 30) . "...<br>";
    } else {
        echo "Error al ejecutar la consulta: " . $conexion->error . "<br>";
    }
}

// Verificar si se crearon tablas
$sql = "SHOW TABLES";
$resultado = $conexion->query($sql);

if ($resultado->num_rows > 0) {
    $tiene_tablas = 1;
} else {
    $tiene_tablas = 0;
}

// Cerrar la conexión
$conexion->close();

// Redirigir a la página principal con el resultado en la URL
header("Location: login.php?pv=$tiene_tablas");
exit;
