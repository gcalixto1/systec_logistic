-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-05-2025 a las 08:51:10
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sis_venta`
--

DELIMITER $$
--
-- Funciones
--
CREATE DEFINER=`root`@`localhost` FUNCTION `InitCap` (`str` TEXT) RETURNS TEXT CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC BEGIN
    DECLARE c CHAR(1);
    DECLARE s TEXT DEFAULT '';
    DECLARE i INT DEFAULT 1;
    DECLARE flag INT DEFAULT 1;

    SET str = LOWER(str);

    WHILE i <= CHAR_LENGTH(str) DO
        SET c = SUBSTRING(str, i, 1);
        IF flag = 1 AND c REGEXP '[a-z]' THEN
            SET s = CONCAT(s, UPPER(c));
            SET flag = 0;
        ELSE
            SET s = CONCAT(s, c);
        END IF;

        IF c = ' ' THEN
            SET flag = 1;
        END IF;

        SET i = i + 1;
    END WHILE;

    RETURN s;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `apertura_caja`
--

CREATE TABLE `apertura_caja` (
  `idcaja` int(11) NOT NULL,
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
  `total_completo` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `apertura_caja`
--

INSERT INTO `apertura_caja` (`idcaja`, `num_apertura`, `saldo_inicial`, `fch_hora_cierre`, `usuario`, `caja`, `estado`, `saldo_venta_total`, `fch_hora_apertura`, `gasto`, `notas`, `saldo_tarjeta`, `saldo_credito`, `entradas`, `total_completo`) VALUES
(1, 'AC12202430', 25.00, '2025-01-26 17:28:40', 'admin', 'Caja Principal', 'C', 95.75, '2025-01-26 08:07:09', 0.00, 'Cierre fin del dia sin gastos o egresos extras', 0.00, 0.00, 0.00, 120.75),
(2, 'AC01202528', 50.00, '2025-05-05 19:00:57', 'admin', 'Caja Principal', 'C', 122.75, '2025-05-05 02:29:09', 0.00, '', 0.00, 0.00, 0.00, 172.75);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `categoria_id` int(11) NOT NULL,
  `categoria_des` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`categoria_id`, `categoria_des`) VALUES
(1, 'Miselaneos'),
(2, 'Herramientas'),
(3, 'PVC'),
(4, 'Cementos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `idcliente` int(11) NOT NULL,
  `dni` varchar(15) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `tipoDocumento` varchar(45) DEFAULT NULL,
  `tipoControbuyente` varchar(45) DEFAULT NULL,
  `dato1` varchar(45) DEFAULT NULL,
  `dato2` varchar(45) DEFAULT NULL,
  `dato3` varchar(45) DEFAULT NULL,
  `dato4` varchar(45) DEFAULT NULL,
  `dato5` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`idcliente`, `dni`, `nombre`, `telefono`, `correo`, `tipoDocumento`, `tipoControbuyente`, `dato1`, `dato2`, `dato3`, `dato4`, `dato5`) VALUES
(1, '01031767-9', 'Claudia Guadalupe Calixto de Fuentes', '6123-8974', 'alex.calix1992@gmail.com', '13', '1', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A'),
(2, '04707621-2', 'Gerson Alexander Escobar Calixto', '7399-9642', 'alex.calix1992@gmail.com', '13', '2', '33905-5', 'SYSTEC S.A. DE C.V.', 'N/A', 'N/A', 'N/A'),
(3, '000000000', 'Cliente General', '00000000', 'prueba@gmail.com', '37', '1', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A'),
(4, '777777777', 'PRUEBA', '77777777', 'prueba2@gmail.com', '37', '1', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente_direccion`
--

CREATE TABLE `cliente_direccion` (
  `idClienteD` int(11) NOT NULL,
  `departamento` char(2) DEFAULT NULL,
  `municipio` char(2) DEFAULT NULL,
  `complemento` varchar(100) DEFAULT NULL,
  `cliente_dni` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cliente_direccion`
--

INSERT INTO `cliente_direccion` (`idClienteD`, `departamento`, `municipio`, `complemento`, `cliente_dni`) VALUES
(1, '03', '18', '1 Calle Ote. Col. Santa Marta Final Pasaje Marin 1 Casa 11', '01031767-9'),
(2, '03', '18', '1 calle Ote. Col. Santa Marta Final PAsaje Marin 1 casa 11', '04707621-2'),
(3, '03', '20', 'N/A', '000000000'),
(4, '03', '20', 'N/A', '777777777');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `dni` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `razon_social` varchar(100) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `direccion` text NOT NULL,
  `igv` decimal(10,2) NOT NULL,
  `impresion` varchar(10) NOT NULL,
  `moneda` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `dni`, `nombre`, `razon_social`, `telefono`, `email`, `direccion`, `igv`, `impresion`, `moneda`) VALUES
(1, '33950-5', 'SYSTEC S.A DE C.V.', 'SYSTEC S.A DE C.V.', '2254-8542', 'palenzo.atencion@gmail.com', 'Sonsonate - El Salvador', 13.00, '80mm', '$');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consecutivos`
--

CREATE TABLE `consecutivos` (
  `idconsecutivos` int(11) NOT NULL,
  `codigo_consecutivo` varchar(45) DEFAULT NULL,
  `descripcionconse` varchar(45) DEFAULT NULL,
  `valor` varchar(45) DEFAULT NULL,
  `mascara` varchar(45) DEFAULT NULL,
  `fecha_hora` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `consecutivos`
--

INSERT INTO `consecutivos` (`idconsecutivos`, `codigo_consecutivo`, `descripcionconse`, `valor`, `mascara`, `fecha_hora`) VALUES
(1, 'tick', 'ticket', 'tick00029', 'NNNN99999', '2024-06-09 00:00:00'),
(2, 'fcf', 'factura consumidor final', 'fcf000002', 'NNN999999', '2024-06-09 00:00:00'),
(3, 'ccf', 'comprobante de credito fiscal', 'ccf000000', 'NNN999999', '2024-06-09 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamentos`
--

CREATE TABLE `departamentos` (
  `iddepartamento` int(11) NOT NULL,
  `codigo` char(2) DEFAULT NULL,
  `valor` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `departamentos`
--

INSERT INTO `departamentos` (`iddepartamento`, `codigo`, `valor`) VALUES
(1, '00', 'Otros(Para extranjeros)'),
(2, '01', 'Ahuachapan'),
(3, '02', 'Santa Ana'),
(4, '03', 'Sonsonate'),
(5, '04', 'Chalatenango'),
(6, '05', 'La Libertad'),
(7, '06', 'San Salvador'),
(8, '07', 'Cuscatlan'),
(9, '08', 'La Paz'),
(10, '09', 'Cabañas'),
(11, '10', 'San Vicente'),
(12, '11', 'Usulutan'),
(13, '12', 'San Miguel'),
(14, '13', 'Morazan'),
(15, '14', 'La Union');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallefactura`
--

CREATE TABLE `detallefactura` (
  `id` int(11) NOT NULL,
  `cod_producto` varchar(50) DEFAULT NULL,
  `precioventa` decimal(10,2) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `idfactura` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detallefactura`
--

INSERT INTO `detallefactura` (`id`, `cod_producto`, `precioventa`, `cantidad`, `idfactura`) VALUES
(1, '1', 1.80, 1, 1),
(2, '2', 8.20, 2, 1),
(3, '4', 1.10, 5, 1),
(4, '1', 1.80, 1, 2),
(5, '2', 8.20, 2, 2),
(6, '4', 1.10, 5, 2),
(7, '1', 1.80, 1, 3),
(8, '2', 8.20, 2, 3),
(9, '4', 1.10, 5, 3),
(10, '1', 1.80, 1, 4),
(11, '2', 8.20, 2, 4),
(12, '4', 1.10, 5, 4),
(13, '2', 8.20, 1, 5),
(14, '3', 9.75, 2, 5),
(15, '2', 8.20, 1, 6),
(16, '3', 9.75, 2, 6),
(17, '2', 8.20, 1, 7),
(18, '3', 9.75, 2, 7),
(19, '1', 1.80, 2, 8),
(20, '2', 8.20, 1, 8),
(21, '1', 1.80, 2, 9),
(22, '2', 8.20, 1, 9),
(23, '2', 8.20, 2, 10),
(24, '2', 8.20, 2, 11),
(25, '2', 8.20, 2, 12),
(26, '2', 8.20, 2, 13),
(27, '2', 8.20, 2, 14),
(28, '2', 8.20, 1, 15),
(29, '1', 1.80, 2, 15),
(30, '2', 8.20, 1, 16),
(31, '1', 1.80, 2, 16),
(32, '2', 8.20, 1, 17),
(33, '1', 1.80, 2, 17),
(34, '3', 9.75, 3, 18),
(35, '1', 1.80, 1, 18),
(36, '3', 9.75, 3, 19),
(37, '1', 1.80, 1, 19),
(38, '3', 9.75, 3, 20),
(39, '1', 1.80, 1, 20),
(40, '4', 1.10, 3, 21),
(41, '2', 8.20, 1, 21),
(42, '3', 9.75, 2, 21),
(43, '1', 1.80, 1, 21),
(44, '1', 1.80, 1, 22),
(45, '3', 9.75, 1, 23),
(46, '2', 8.20, 1, 24),
(47, '1', 1.80, 1, 24),
(48, '3', 9.75, 2, 25),
(49, '1', 1.80, 1, 25),
(50, '4', 1.10, 2, 25),
(51, '3', 9.75, 2, 26),
(52, '1', 1.80, 1, 26),
(53, '3', 9.75, 1, 27),
(54, '2', 8.20, 1, 28),
(55, '1', 1.80, 2, 28),
(56, '1', 1.80, 2, 29),
(57, '3', 9.75, 1, 29),
(58, '4', 1.10, 2, 29),
(59, '2', 8.20, 1, 29),
(60, '4', 1.10, 1, 30),
(61, '2', 8.20, 1, 31),
(62, '1', 1.80, 1, 31);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos`
--

CREATE TABLE `documentos` (
  `codigo` varchar(2) NOT NULL,
  `valor` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `documentos`
--

INSERT INTO `documentos` (`codigo`, `valor`) VALUES
('02', 'Carnet de Residente'),
('03', 'Pasaporte'),
('13', 'DUI'),
('36', 'NIT'),
('37', 'Otro');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura`
--

CREATE TABLE `factura` (
  `id` int(11) NOT NULL,
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
  `estado` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `factura`
--

INSERT INTO `factura` (`id`, `tipofactura`, `numerofactura`, `subtotal`, `iva_impuesto`, `totalpagar`, `letras`, `forma_pago`, `fechafactura`, `idusuario`, `idcliente`, `estado`) VALUES
(1, 'tick', 'tick00001', 20.97, 2.73, 23.70, 'VEINTITRÉS DOLARES CON SETENTA CENTAVOS', 0, '2025-04-26 12:10:50', 1, 1, 'Pendiente'),
(2, 'tick', 'tick00002', 20.97, 2.73, 23.70, 'VEINTITRÉS DOLARES CON SETENTA CENTAVOS', 0, '2025-04-26 12:48:40', 1, 1, 'Pendiente'),
(3, 'tick', 'tick00003', 20.97, 2.73, 23.70, 'VEINTITRÉS DOLARES CON SETENTA CENTAVOS', 0, '2025-04-26 12:48:52', 1, 1, 'Pendiente'),
(4, 'tick', 'tick00004', 20.97, 2.73, 23.70, 'VEINTITRÉS DOLARES CON SETENTA CENTAVOS', 1, '2025-04-26 12:49:13', 1, 1, 'Pagado'),
(5, 'tick', 'tick00005', 24.51, 3.19, 27.70, 'VEINTISIETE DOLARES CON SETENTA CENTAVOS', 0, '2025-05-03 18:00:19', 1, 1, 'Pendiente'),
(6, 'tick', 'tick00006', 24.51, 3.19, 27.70, 'VEINTISIETE DOLARES CON SETENTA CENTAVOS', 0, '2025-05-03 18:02:45', 1, 1, 'Pendiente'),
(7, 'tick', 'tick00007', 24.51, 3.19, 27.70, 'VEINTISIETE DOLARES CON SETENTA CENTAVOS', 1, '2025-05-03 18:03:28', 1, 1, 'Pagado'),
(8, 'tick', 'tick00008', 10.44, 1.36, 11.80, 'ONCE DOLARES CON OCHENTA CENTAVOS', 0, '2025-05-03 18:21:59', 1, 1, 'Pendiente'),
(9, 'tick', 'tick00009', 10.44, 1.36, 11.80, 'ONCE DOLARES CON OCHENTA CENTAVOS', 1, '2025-05-03 18:25:21', 1, 1, 'Pagado'),
(10, 'tick', 'tick00010', 14.51, 1.89, 16.40, 'DIECISÉIS DOLARES CON CUARENTA CENTAVOS', 0, '2025-05-03 18:27:23', 1, 1, 'Pendiente'),
(11, 'tick', 'tick00011', 14.51, 1.89, 16.40, 'DIECISÉIS DOLARES CON CUARENTA CENTAVOS', 0, '2025-05-03 18:34:45', 1, 1, 'Pendiente'),
(12, 'tick', 'tick00012', 14.51, 1.89, 16.40, 'DIECISÉIS DOLARES CON CUARENTA CENTAVOS', 0, '2025-05-03 18:35:43', 1, 1, 'Pendiente'),
(13, 'tick', 'tick00013', 14.51, 1.89, 16.40, 'DIECISÉIS DOLARES CON CUARENTA CENTAVOS', 0, '2025-05-03 18:37:46', 1, 1, 'Pendiente'),
(14, 'tick', 'tick00014', 14.51, 1.89, 16.40, 'DIECISÉIS DOLARES CON CUARENTA CENTAVOS', 1, '2025-05-03 18:38:37', 1, 1, 'Pagado'),
(15, 'tick', 'tick00015', 10.44, 1.36, 11.80, 'ONCE DOLARES CON OCHENTA CENTAVOS', 1, '2025-05-03 18:52:44', 1, 1, 'Pagado'),
(16, 'tick', 'tick00016', 10.44, 1.36, 11.80, 'ONCE DOLARES CON OCHENTA CENTAVOS', 1, '2025-05-03 18:53:51', 1, 1, 'Pagado'),
(17, 'tick', 'tick00017', 10.44, 1.36, 11.80, 'ONCE DOLARES CON OCHENTA CENTAVOS', 1, '2025-05-03 19:01:55', 1, 1, 'Pagado'),
(18, 'tick', 'tick00018', 27.48, 3.57, 31.05, 'TREINTA Y UNO DOLARES CON CINCO CENTAVOS', 0, '2025-05-04 19:56:06', 1, 1, 'Pendiente'),
(19, 'tick', 'tick00019', 27.48, 3.57, 31.05, 'TREINTA Y UNO DOLARES CON CINCO CENTAVOS', 1, '2025-05-04 19:56:40', 1, 1, 'Pagado'),
(20, 'tick', 'tick00020', 27.48, 3.57, 31.05, 'TREINTA Y UNO DOLARES CON CINCO CENTAVOS', 1, '2025-05-04 20:01:45', 1, 1, 'Pagado'),
(21, 'tick', 'tick00021', 29.03, 3.77, 32.80, 'TREINTA Y DOS DOLARES CON OCHENTA CENTAVOS', 1, '2025-05-04 22:39:27', 1, 1, 'Pagado'),
(22, 'tick', 'tick00022', 1.59, 0.21, 1.80, 'UNO DOLARES CON OCHENTA CENTAVOS', 1, '2025-05-05 08:19:00', 1, 1, 'Pagado'),
(23, 'tick', 'tick00023', 8.63, 1.12, 9.75, 'NUEVE DOLARES CON SETENTA Y CINCO CENTAVOS', 1, '2025-05-05 08:21:09', 1, 1, 'Pagado'),
(24, 'tick', 'tick00024', 8.85, 1.15, 10.00, 'DIEZ DOLARES', 1, '2025-05-05 11:04:56', 1, 1, 'Pagado'),
(25, 'tick', 'tick00025', 20.80, 2.70, 23.50, 'VEINTITRÉS DOLARES CON CINCUENTA CENTAVOS', 1, '2025-05-05 12:36:58', 1, 1, 'Pagado'),
(26, 'tick', 'tick00026', 18.85, 2.45, 21.30, 'VEINTIUNO DOLARES CON TREINTA CENTAVOS', 1, '2025-05-05 12:44:49', 1, 1, 'Pagado'),
(27, 'tick', 'tick00027', 8.63, 1.12, 9.75, 'NUEVE DOLARES CON SETENTA Y CINCO CENTAVOS', 1, '2025-05-05 12:55:57', 1, 1, 'Pagado'),
(28, 'tick', 'tick00028', 10.44, 1.36, 11.80, 'ONCE DOLARES CON OCHENTA CENTAVOS', 1, '2025-05-05 12:57:09', 1, 1, 'Pagado'),
(29, 'tick', 'tick00029', 21.02, 2.73, 23.75, 'VEINTITRÉS DOLARES CON SETENTA Y CINCO CENTAVOS', 1, '2025-05-05 12:59:47', 1, 1, 'Pagado'),
(30, 'fcf', 'fcf000001', 0.97, 0.13, 1.10, 'UNO DOLARES CON DIEZ CENTAVOS', 1, '2025-05-05 13:34:13', 1, 1, 'Pagado'),
(31, 'fcf', 'fcf000002', 8.85, 1.15, 10.00, 'DIEZ DOLARES', 1, '2025-05-05 13:36:35', 1, 1, 'Pagado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `kardex_producto`
--

CREATE TABLE `kardex_producto` (
  `idkardex` int(11) NOT NULL,
  `producto` varchar(25) DEFAULT NULL,
  `movimiento` varchar(50) DEFAULT NULL,
  `entradas` decimal(10,2) DEFAULT NULL,
  `salidas` decimal(10,2) DEFAULT NULL,
  `devolucion` decimal(10,2) DEFAULT NULL,
  `stock_actual` decimal(10,2) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `descripcion` varchar(250) DEFAULT NULL,
  `fecha_trans` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `kardex_producto`
--

INSERT INTO `kardex_producto` (`idkardex`, `producto`, `movimiento`, `entradas`, `salidas`, `devolucion`, `stock_actual`, `precio`, `descripcion`, `fecha_trans`) VALUES
(1, '748598746312', 'ENTRADA', 10.00, 0.00, 0.00, 10.00, 1.40, 'INVENTARIO INICIAL', '2025-04-26 10:22:18'),
(2, '798541782649', 'ENTRADA', 100.00, 0.00, 0.00, 100.00, 6.30, 'INVENTARIO INICIAL', '2025-04-26 10:26:50'),
(3, '718548964539', 'ENTRADA', 100.00, 0.00, 0.00, 100.00, 7.50, 'INVENTARIO INICIAL', '2025-04-26 10:32:55'),
(4, '784572136522', 'ENTRADA', 500.00, 0.00, 0.00, 500.00, 0.65, 'INVENTARIO INICIAL', '2025-04-26 10:35:29'),
(5, '1', 'SALIDA', 0.00, 1.00, 0.00, 9.00, 1.80, 'VENTA EN FACTURA: tick00001', '2025-04-26 12:10:50'),
(6, '2', 'SALIDA', 0.00, 2.00, 0.00, 98.00, 8.20, 'VENTA EN FACTURA: tick00001', '2025-04-26 12:10:50'),
(7, '4', 'SALIDA', 0.00, 5.00, 0.00, 495.00, 1.10, 'VENTA EN FACTURA: tick00001', '2025-04-26 12:10:50'),
(8, '1', 'SALIDA', 0.00, 1.00, 0.00, 8.00, 1.80, 'VENTA EN FACTURA: tick00002', '2025-04-26 12:48:40'),
(9, '2', 'SALIDA', 0.00, 2.00, 0.00, 96.00, 8.20, 'VENTA EN FACTURA: tick00002', '2025-04-26 12:48:40'),
(10, '4', 'SALIDA', 0.00, 5.00, 0.00, 490.00, 1.10, 'VENTA EN FACTURA: tick00002', '2025-04-26 12:48:40'),
(11, '1', 'SALIDA', 0.00, 1.00, 0.00, 7.00, 1.80, 'VENTA EN FACTURA: tick00003', '2025-04-26 12:48:52'),
(12, '2', 'SALIDA', 0.00, 2.00, 0.00, 94.00, 8.20, 'VENTA EN FACTURA: tick00003', '2025-04-26 12:48:52'),
(13, '4', 'SALIDA', 0.00, 5.00, 0.00, 485.00, 1.10, 'VENTA EN FACTURA: tick00003', '2025-04-26 12:48:52'),
(14, '1', 'SALIDA', 0.00, 1.00, 0.00, 6.00, 1.80, 'VENTA EN FACTURA: tick00004', '2025-04-26 12:49:13'),
(15, '2', 'SALIDA', 0.00, 2.00, 0.00, 92.00, 8.20, 'VENTA EN FACTURA: tick00004', '2025-04-26 12:49:13'),
(16, '4', 'SALIDA', 0.00, 5.00, 0.00, 480.00, 1.10, 'VENTA EN FACTURA: tick00004', '2025-04-26 12:49:13'),
(17, '2', 'SALIDA', 0.00, 1.00, 0.00, 91.00, 8.20, 'VENTA EN FACTURA: tick00005', '2025-05-03 18:00:19'),
(18, '3', 'SALIDA', 0.00, 2.00, 0.00, 98.00, 9.75, 'VENTA EN FACTURA: tick00005', '2025-05-03 18:00:19'),
(19, '2', 'SALIDA', 0.00, 1.00, 0.00, 90.00, 8.20, 'VENTA EN FACTURA: tick00006', '2025-05-03 18:02:45'),
(20, '3', 'SALIDA', 0.00, 2.00, 0.00, 96.00, 9.75, 'VENTA EN FACTURA: tick00006', '2025-05-03 18:02:45'),
(21, '2', 'SALIDA', 0.00, 1.00, 0.00, 89.00, 8.20, 'VENTA EN FACTURA: tick00007', '2025-05-03 18:03:28'),
(22, '3', 'SALIDA', 0.00, 2.00, 0.00, 94.00, 9.75, 'VENTA EN FACTURA: tick00007', '2025-05-03 18:03:28'),
(23, '1', 'SALIDA', 0.00, 2.00, 0.00, 4.00, 1.80, 'VENTA EN FACTURA: tick00008', '2025-05-03 18:21:59'),
(24, '2', 'SALIDA', 0.00, 1.00, 0.00, 88.00, 8.20, 'VENTA EN FACTURA: tick00008', '2025-05-03 18:21:59'),
(25, '1', 'SALIDA', 0.00, 2.00, 0.00, 2.00, 1.80, 'VENTA EN FACTURA: tick00009', '2025-05-03 18:25:21'),
(26, '2', 'SALIDA', 0.00, 1.00, 0.00, 87.00, 8.20, 'VENTA EN FACTURA: tick00009', '2025-05-03 18:25:21'),
(27, '2', 'SALIDA', 0.00, 2.00, 0.00, 85.00, 8.20, 'VENTA EN FACTURA: tick00010', '2025-05-03 18:27:23'),
(28, '2', 'SALIDA', 0.00, 2.00, 0.00, 83.00, 8.20, 'VENTA EN FACTURA: tick00011', '2025-05-03 18:34:45'),
(29, '2', 'SALIDA', 0.00, 2.00, 0.00, 81.00, 8.20, 'VENTA EN FACTURA: tick00012', '2025-05-03 18:35:43'),
(30, '2', 'SALIDA', 0.00, 2.00, 0.00, 79.00, 8.20, 'VENTA EN FACTURA: tick00013', '2025-05-03 18:37:46'),
(31, '2', 'SALIDA', 0.00, 2.00, 0.00, 77.00, 8.20, 'VENTA EN FACTURA: tick00014', '2025-05-03 18:38:37'),
(32, '2', 'SALIDA', 0.00, 1.00, 0.00, 76.00, 8.20, 'VENTA EN FACTURA: tick00015', '2025-05-03 18:52:44'),
(33, '1', 'SALIDA', 0.00, 2.00, 0.00, 0.00, 1.80, 'VENTA EN FACTURA: tick00015', '2025-05-03 18:52:44'),
(34, '2', 'SALIDA', 0.00, 1.00, 0.00, 75.00, 8.20, 'VENTA EN FACTURA: tick00016', '2025-05-03 18:53:51'),
(35, '1', 'SALIDA', 0.00, 2.00, 0.00, -2.00, 1.80, 'VENTA EN FACTURA: tick00016', '2025-05-03 18:53:51'),
(36, '2', 'SALIDA', 0.00, 1.00, 0.00, 74.00, 8.20, 'VENTA EN FACTURA: tick00017', '2025-05-03 19:01:55'),
(37, '1', 'SALIDA', 0.00, 2.00, 0.00, -4.00, 1.80, 'VENTA EN FACTURA: tick00017', '2025-05-03 19:01:55'),
(38, '3', 'SALIDA', 0.00, 3.00, 0.00, 91.00, 9.75, 'VENTA EN FACTURA: tick00018', '2025-05-04 19:56:06'),
(39, '1', 'SALIDA', 0.00, 1.00, 0.00, -5.00, 1.80, 'VENTA EN FACTURA: tick00018', '2025-05-04 19:56:06'),
(40, '3', 'SALIDA', 0.00, 3.00, 0.00, 88.00, 9.75, 'VENTA EN FACTURA: tick00019', '2025-05-04 19:56:40'),
(41, '1', 'SALIDA', 0.00, 1.00, 0.00, -6.00, 1.80, 'VENTA EN FACTURA: tick00019', '2025-05-04 19:56:40'),
(42, '3', 'SALIDA', 0.00, 3.00, 0.00, 85.00, 9.75, 'VENTA EN FACTURA: tick00020', '2025-05-04 20:01:45'),
(43, '1', 'SALIDA', 0.00, 1.00, 0.00, -7.00, 1.80, 'VENTA EN FACTURA: tick00020', '2025-05-04 20:01:45'),
(44, '4', 'SALIDA', 0.00, 3.00, 0.00, 477.00, 1.10, 'VENTA EN FACTURA: tick00021', '2025-05-04 22:39:27'),
(45, '2', 'SALIDA', 0.00, 1.00, 0.00, 73.00, 8.20, 'VENTA EN FACTURA: tick00021', '2025-05-04 22:39:27'),
(46, '3', 'SALIDA', 0.00, 2.00, 0.00, 83.00, 9.75, 'VENTA EN FACTURA: tick00021', '2025-05-04 22:39:27'),
(47, '1', 'SALIDA', 0.00, 1.00, 0.00, -8.00, 1.80, 'VENTA EN FACTURA: tick00021', '2025-05-04 22:39:27'),
(48, '1', 'SALIDA', 0.00, 1.00, 0.00, -9.00, 1.80, 'VENTA EN FACTURA: tick00022', '2025-05-05 08:19:00'),
(49, '3', 'SALIDA', 0.00, 1.00, 0.00, 82.00, 9.75, 'VENTA EN FACTURA: tick00023', '2025-05-05 08:21:09'),
(50, '2', 'SALIDA', 0.00, 1.00, 0.00, 72.00, 8.20, 'VENTA EN FACTURA: tick00024', '2025-05-05 11:04:56'),
(51, '1', 'SALIDA', 0.00, 1.00, 0.00, -10.00, 1.80, 'VENTA EN FACTURA: tick00024', '2025-05-05 11:04:56'),
(52, '3', 'SALIDA', 0.00, 2.00, 0.00, 80.00, 9.75, 'VENTA EN FACTURA: tick00025', '2025-05-05 12:36:58'),
(53, '1', 'SALIDA', 0.00, 1.00, 0.00, -11.00, 1.80, 'VENTA EN FACTURA: tick00025', '2025-05-05 12:36:58'),
(54, '4', 'SALIDA', 0.00, 2.00, 0.00, 475.00, 1.10, 'VENTA EN FACTURA: tick00025', '2025-05-05 12:36:58'),
(55, '3', 'SALIDA', 0.00, 2.00, 0.00, 78.00, 9.75, 'VENTA EN FACTURA: tick00026', '2025-05-05 12:44:49'),
(56, '1', 'SALIDA', 0.00, 1.00, 0.00, -12.00, 1.80, 'VENTA EN FACTURA: tick00026', '2025-05-05 12:44:49'),
(57, '3', 'SALIDA', 0.00, 1.00, 0.00, 77.00, 9.75, 'VENTA EN FACTURA: tick00027', '2025-05-05 12:55:57'),
(58, '2', 'SALIDA', 0.00, 1.00, 0.00, 71.00, 8.20, 'VENTA EN FACTURA: tick00028', '2025-05-05 12:57:09'),
(59, '1', 'SALIDA', 0.00, 2.00, 0.00, -14.00, 1.80, 'VENTA EN FACTURA: tick00028', '2025-05-05 12:57:09'),
(60, '1', 'SALIDA', 0.00, 2.00, 0.00, -16.00, 1.80, 'VENTA EN FACTURA: tick00029', '2025-05-05 12:59:47'),
(61, '3', 'SALIDA', 0.00, 1.00, 0.00, 76.00, 9.75, 'VENTA EN FACTURA: tick00029', '2025-05-05 12:59:47'),
(62, '4', 'SALIDA', 0.00, 2.00, 0.00, 473.00, 1.10, 'VENTA EN FACTURA: tick00029', '2025-05-05 12:59:47'),
(63, '2', 'SALIDA', 0.00, 1.00, 0.00, 70.00, 8.20, 'VENTA EN FACTURA: tick00029', '2025-05-05 12:59:47'),
(64, '4', 'SALIDA', 0.00, 1.00, 0.00, 472.00, 1.10, 'VENTA EN FACTURA: fcf000001', '2025-05-05 13:34:13'),
(65, '2', 'SALIDA', 0.00, 1.00, 0.00, 69.00, 8.20, 'VENTA EN FACTURA: fcf000002', '2025-05-05 13:36:35'),
(66, '1', 'SALIDA', 0.00, 1.00, 0.00, -17.00, 1.80, 'VENTA EN FACTURA: fcf000002', '2025-05-05 13:36:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medio_pago`
--

CREATE TABLE `medio_pago` (
  `idmedio` int(11) NOT NULL,
  `medio_pago` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `medio_pago`
--

INSERT INTO `medio_pago` (`idmedio`, `medio_pago`) VALUES
(1, 'Efectivo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu`
--

CREATE TABLE `menu` (
  `idMenu` int(11) NOT NULL,
  `Menu` varchar(255) NOT NULL,
  `Url` varchar(255) NOT NULL,
  `Parent` int(11) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `index` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `menu`
--

INSERT INTO `menu` (`idMenu`, `Menu`, `Url`, `Parent`, `icon`, `index`) VALUES
(1, '    Principal', 'index.php?page=home', NULL, 'bx bx-home side-menu__icon', 1),
(2, '    Terminal POS', 'index.php?page=terminalPOS', NULL, 'fa fa-calculator', 2),
(3, '    Almacen', '', NULL, 'bx bx-package side-menu__icon', 3),
(4, '    Categoria', 'index.php?page=categorias', 3, 'fa fa-list', 4),
(5, '    Compras y Movimientos', '', NULL, 'fa fa-shopping-cart', 8),
(6, '  Mant. Clientes', 'index.php?page=cliente', 10, 'fa fa-address-card', 13),
(7, '       Mant. Proveedores', 'index.php?page=proveedor', 5, 'fa fa-users', 9),
(8, '    Productos', 'index.php?page=productos', 3, 'fa fa-folder', 5),
(9, '    Kardex por Producto', 'index.php?page=kardex_productos', 3, 'fa fa-clipboard', 7),
(10, '  Administrar Caja', '', NULL, 'fas fa-cash-register', 11),
(11, '  Apertura de Caja', 'index.php?page=caja', 10, 'fas fa-cash-register', 12),
(12, '  Gastos', 'index.php?page=movimientos', 5, 'fa fa-clipboard', 10),
(13, '  Cerrar Sesion', 'ajax.php?action=logout', NULL, 'fa fa-power-off', 20),
(14, '  Reportes', '', 0, 'fa fa-file-pdf', 14),
(15, ' Ventas diarias', 'ajax.php?action=reportes', 14, 'fa fa-file-pdf', 15),
(16, '    Presentacion de Producto', 'index.php?page=presentacion', 3, 'fa fa-list', 6),
(17, ' Configuracion', '', NULL, 'fa fa-cog', 16),
(18, ' Ventas', '', NULL, 'fa fa-file', 14),
(19, '    Consultar ventas DTE', 'index.php?page=consultadte', 18, 'fa fa-search', 15),
(20, '   Empresa', 'index.php?page=perfil_general', 17, 'fa fa-building', 19);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_de_caja`
--

CREATE TABLE `movimientos_de_caja` (
  `id` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `ingreso` decimal(18,2) DEFAULT NULL,
  `egreso` decimal(18,2) DEFAULT NULL,
  `comentario` text DEFAULT NULL,
  `usuario` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `municipios`
--

CREATE TABLE `municipios` (
  `idmunicipios` int(11) NOT NULL,
  `codigo` varchar(20) DEFAULT NULL,
  `valor` varchar(45) DEFAULT NULL,
  `iddepartamento` char(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `municipios`
--

INSERT INTO `municipios` (`idmunicipios`, `codigo`, `valor`, `iddepartamento`) VALUES
(1, '00', 'Otro(ParaExtranjeros)', '00'),
(2, '13', 'Ahuachapan Norte', '01'),
(3, '14', 'Ahuachapan Centro', '01'),
(4, '15', 'Ahuachapan Sur', '01'),
(5, '14', 'Santa Ana Norte', '02'),
(6, '15', 'Santa Ana Centro', '02'),
(7, '16', 'Santa Ana Este', '02'),
(8, '17', 'Santa Ana Oeste', '02'),
(9, '17', 'Sonsonate Norte', '03'),
(10, '18', 'Sonsonate Centro', '03'),
(11, '19', 'Sonsonate Este', '03'),
(12, '20', 'Sonsonate Oeste', '03'),
(13, '34', 'Chalatenango Norte', '04'),
(14, '35', 'Chalatenango Centro', '04'),
(15, '36', 'Chalatenango Sur', '04'),
(16, '23', 'La Libertad Norte', '05'),
(17, '24', 'La Libertad Centro', '05'),
(18, '25', 'La Libertad Oeste', '05'),
(19, '26', 'La Libertad Este', '05'),
(20, '27', 'La Libertad Costa', '05'),
(21, '28', 'La Libertad Sur', '05'),
(22, '20', 'San Salvador Norte', '06'),
(23, '21', 'San Salvador Oeste', '06'),
(24, '22', 'San Salvador Este', '06'),
(25, '23', 'San Salvador Centro', '06'),
(26, '24', 'San Salvador Sur', '06'),
(27, '17', 'Cuscatlan Norte', '07'),
(28, '18', 'Cuscatlan Sur', '07'),
(29, '23', 'La Paz Oeste', '08'),
(30, '24', 'La Paz Centro', '08'),
(31, '25', 'La Paz Este', '08'),
(32, '10', 'Cabañas Oeste', '09'),
(33, '11', 'Cabañas Este', '09'),
(34, '14', 'San Vicente Norte', '10'),
(35, '15', 'San Vicente Sur', '10'),
(36, '24', 'Usulutan Norte', '11'),
(37, '25', 'Usulutan Este', '11'),
(38, '26', 'Usulutan Oeste', '11'),
(39, '21', 'San Miguel Norte', '12'),
(40, '22', 'San Miguel Centro', '12'),
(41, '23', 'San Miguel Oeste', '12'),
(42, '27', 'Morazan Norte', '13'),
(43, '28', 'Morazan Sur', '13'),
(44, '19', 'La Union Norte', '14'),
(45, '20', 'La Union Sur', '14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presentacion`
--

CREATE TABLE `presentacion` (
  `idpresentacion` int(11) NOT NULL,
  `presentacion` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `presentacion`
--

INSERT INTO `presentacion` (`idpresentacion`, `presentacion`) VALUES
(1, 'Unidad');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `codproducto` int(11) NOT NULL,
  `descripcion` varchar(200) NOT NULL,
  `proveedor` int(11) NOT NULL,
  `precio_compra` decimal(10,2) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `existencia` decimal(10,2) DEFAULT NULL,
  `exis_min` decimal(10,2) NOT NULL,
  `codBarra` varchar(50) DEFAULT NULL,
  `prop1` varchar(5) DEFAULT NULL,
  `prop2` varchar(45) DEFAULT NULL,
  `prop3` varchar(45) DEFAULT NULL,
  `categoria` int(11) DEFAULT NULL,
  `fecha_vencimiento` datetime DEFAULT current_timestamp(),
  `imagen_producto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`codproducto`, `descripcion`, `proveedor`, `precio_compra`, `precio`, `existencia`, `exis_min`, `codBarra`, `prop1`, `prop2`, `prop3`, `categoria`, `fecha_vencimiento`, `imagen_producto`) VALUES
(1, 'Destornillador phillips no. 1 x 3 pulg', 5, 1.40, 1.80, -17.00, 2.00, '748598746312', '29', 'Unidad', 'A', 1, '2025-04-26 00:00:00', 'img/productos/680d083ac0f0f_148525.jpg'),
(2, 'Cemento WP 42.5 KG', 2, 6.30, 8.20, 69.00, 20.00, '798541782649', '30', 'Unidad', 'A', 4, '2025-04-26 00:00:00', 'img/productos/680d094af1f04_mockup-wp-v1.png'),
(3, 'Cemento Fuerte Holcim 50 KG', 2, 7.50, 9.75, 76.00, 20.00, '718548964539', '30', 'Unidad', 'A', 4, '2025-04-26 00:00:00', 'img/productos/680d0ab78e630_800x800.jpg'),
(4, 'Espoja para Cemento 35cm X 35cm', 5, 0.65, 1.10, 472.00, 10.00, '784572136522', '30', 'Unidad', 'A', 1, '2025-04-26 00:00:00', 'img/ninguna.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `idproveedor` int(11) NOT NULL,
  `proveedor` varchar(100) NOT NULL,
  `contacto` varchar(100) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `direccion` varchar(100) NOT NULL,
  `tipoControbuyente` int(11) NOT NULL,
  `correo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`idproveedor`, `proveedor`, `contacto`, `telefono`, `direccion`, `tipoControbuyente`, `correo`) VALUES
(1, 'Carwash', '0000000', '00000000', 'ND', 1, 'systec.info@gmail.com'),
(2, 'Multiservicios brazil', '336541-2', '2451-9875', 'Santa Ana - Santa Ana', 2, 'ferreteriabrazil@gmail.com'),
(5, 'Milton Fajer', '99351-7', '2278-9642', 'Los Cobanos - Sonsonate', 1, 'ferreterialosbrothers@outlok.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuestadte`
--

CREATE TABLE `respuestadte` (
  `id` int(11) NOT NULL,
  `id_factura` int(11) NOT NULL,
  `version` int(11) DEFAULT NULL,
  `ambiente` varchar(2) DEFAULT NULL,
  `versionApp` int(11) DEFAULT NULL,
  `estado` varchar(20) DEFAULT NULL,
  `codigoGeneracion` varchar(50) DEFAULT NULL,
  `selloRecibido` varchar(100) DEFAULT NULL,
  `fhProcesamiento` datetime DEFAULT NULL,
  `clasificaMsg` varchar(10) DEFAULT NULL,
  `codigoMsg` varchar(10) DEFAULT NULL,
  `descripcionMsg` varchar(255) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_guardado` timestamp NOT NULL DEFAULT current_timestamp(),
  `jsondte` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `respuestadte`
--

INSERT INTO `respuestadte` (`id`, `id_factura`, `version`, `ambiente`, `versionApp`, `estado`, `codigoGeneracion`, `selloRecibido`, `fhProcesamiento`, `clasificaMsg`, `codigoMsg`, `descripcionMsg`, `observaciones`, `fecha_guardado`, `jsondte`) VALUES
(1, 200, 2, '00', 2, 'PROCESADO', '9C79708D-A1C9-A76C-B9F8-7F5EC73B6992', '202532C3E6A45F674B9BAACFAFFA77F5C2F8NM4W', '2025-04-24 19:52:25', '10', '001', 'RECIBIDO', '[]', '2025-04-25 01:52:25', NULL),
(2, 201, 2, '00', 2, 'PROCESADO', '5D17A124-221F-D442-5D8C-5D7A2D2C17A4', '202555062F59AC3C45AA9A2C0FE5AF6B1FEFLH5Y', '2025-04-25 09:35:38', '10', '001', 'RECIBIDO', '[]', '2025-04-25 15:35:37', NULL),
(3, 1, 2, '00', 2, 'RECHAZADO', 'DC7EE40F-D5DD-7153-272E-40B5BAB60B8F', NULL, '2025-04-26 12:11:00', '11', '004', '[identificacion.numeroControl] YA EXISTE UN REGISTRO CON ESE VALOR', '[]', '2025-04-26 18:10:57', NULL),
(4, 2, 2, '00', 2, 'RECHAZADO', '7B6DD47D-952A-7F68-E922-BAFB6FD3F070', NULL, '2025-04-26 12:48:45', '11', '004', '[identificacion.numeroControl] YA EXISTE UN REGISTRO CON ESE VALOR', '[]', '2025-04-26 18:48:42', NULL),
(5, 3, 2, '00', 2, 'PROCESADO', 'D2336841-113E-40C8-AE64-7A8E12DF0F34', '2025A8CC10421E37491BBE05DD32E2754CEDBAAK', '2025-04-26 12:48:57', '10', '001', 'RECIBIDO', '[]', '2025-04-26 18:48:54', NULL),
(6, 4, 2, '00', 2, 'PROCESADO', 'D50A7905-4CF4-E5B3-9E79-48A4B6A5A384', '202528635E93F250427DB5A2CE7B4C87365EWYY6', '2025-04-26 12:49:19', '10', '001', 'RECIBIDO', '[]', '2025-04-26 18:49:16', NULL),
(7, 6, 2, '00', 2, 'PROCESADO', 'EDE5916C-FE3D-D424-495A-4E6C0B7A8ECA', '2025C72C1A88593A4EAFAAF54656D121C7DCP7BO', '2025-05-03 18:02:51', '10', '001', 'RECIBIDO', '[]', '2025-05-04 00:02:50', NULL),
(8, 7, 2, '00', 2, 'PROCESADO', '579A314C-3AAC-DB1F-4F37-E5931D9260DC', '20251092F4BAE2C64B01A0D2DC2866C6759DL4JB', '2025-05-03 18:03:32', '10', '001', 'RECIBIDO', '[]', '2025-05-04 00:03:31', NULL),
(9, 9, 2, '00', 2, 'PROCESADO', '70140D15-1A0E-59F6-85EF-0BBF72977FE1', '20254C1AA88498324FE9A235850214C461B4QBA2', '2025-05-03 18:25:24', '10', '001', 'RECIBIDO', '[]', '2025-05-04 00:25:24', 'eyJhbGciOiJSUzUxMiJ9.ew0KICAiaWRlbnRpZmljYWNpb24iIDogew0KICAgICJ2ZXJzaW9uIiA6IDEsDQogICAgImFtYmllbnRlIiA6ICIwMCIsDQogICAgInRpcG9EdGUiIDogIjAxIiwNCiAgICAibnVtZXJvQ29udHJvbCIgOiAiRFRFLTAxLU0wMDFQMDAxLTAwMDAwMDAwMDAwMDAwOSIsDQogICAgImNvZGlnb0dlbmVyYWNpb24iIDogIjcwMTQwRDE1LTFBMEUtNTlGNi04NUVGLTBCQkY3Mjk3N0ZFMSIsDQogICAgInRpcG9Nb2RlbG8iIDogMSwNCiAgICAidGlwb09wZXJhY2lvbiIgOiAxLA0KICAgICJ0aXBvQ29udGluZ2VuY2lhIiA6IG51bGwsDQogICAgIm1vdGl2b0NvbnRpbiIgOiBudWxsLA0KICAgICJmZWNFbWkiIDogIjIwMjUtMDUtMDMiLA0KICAgICJob3JFbWkiIDogIjE4OjI1OjIxIiwNCiAgICAidGlwb01vbmVkYSIgOiAiVVNEIg0KICB9LA0KICAiZG9jdW1lbnRvUmVsYWNpb25hZG8iIDogbnVsbCwNCiAgImVtaXNvciIgOiB7DQogICAgIm5pdCIgOiAiMDMwMTE1MDQ3NjEwMjEiLA0KICAgICJucmMiIDogIjMxNzMxMzAiLA0KICAgICJub21icmUiIDogIk9zY2FyIEVkdWFyZG8gRnVlbnRlcyBNYW5jaWEiLA0KICAgICJjb2RBY3RpdmlkYWQiIDogIjQ2NjMyIiwNCiAgICAiZGVzY0FjdGl2aWRhZCIgOiAidmVudGEgYWwgcG9yIG1heW9yIGRlIGFydGljdWxvcyBkZSBmZXJyZXRlcmlhIHkgcGludHVyZXJpYXMiLA0KICAgICJub21icmVDb21lcmNpYWwiIDogIkZFUlJFVEVSSUEgRlVFTlRFUyIsDQogICAgInRpcG9Fc3RhYmxlY2ltaWVudG8iIDogIjAyIiwNCiAgICAiZGlyZWNjaW9uIiA6IHsNCiAgICAgICJkZXBhcnRhbWVudG8iIDogIjAzIiwNCiAgICAgICJtdW5pY2lwaW8iIDogIjIwIiwNCiAgICAgICJjb21wbGVtZW50byIgOiAiQ2FycmV0ZXJhIGEgbG9zIGNvYmFub3MgcHJlc2EgZGVsIHZlbmFkbyINCiAgICB9LA0KICAgICJ0ZWxlZm9ubyIgOiAiNzM5OTk2NDIiLA0KICAgICJjb3JyZW8iIDogImFsZXguY2FsaXgxOTkyQGdtYWlsLmNvbSIsDQogICAgImNvZEVzdGFibGUiIDogbnVsbCwNCiAgICAiY29kUHVudG9WZW50YSIgOiBudWxsLA0KICAgICJjb2RFc3RhYmxlTUgiIDogbnVsbCwNCiAgICAiY29kUHVudG9WZW50YU1IIiA6IG51bGwNCiAgfSwNCiAgInJlY2VwdG9yIiA6IHsNCiAgICAibnJjIiA6IG51bGwsDQogICAgInRpcG9Eb2N1bWVudG8iIDogIjEzIiwNCiAgICAibnVtRG9jdW1lbnRvIiA6ICIwMTAzMTc2Ny05IiwNCiAgICAibm9tYnJlIiA6ICJDbGF1ZGlhIEd1YWRhbHVwZSBDYWxpeHRvIGRlIEZ1ZW50ZXMiLA0KICAgICJjb2RBY3RpdmlkYWQiIDogbnVsbCwNCiAgICAiZGVzY0FjdGl2aWRhZCIgOiBudWxsLA0KICAgICJkaXJlY2Npb24iIDogew0KICAgICAgImRlcGFydGFtZW50byIgOiAiMDMiLA0KICAgICAgIm11bmljaXBpbyIgOiAiMTgiLA0KICAgICAgImNvbXBsZW1lbnRvIiA6ICJDb2xvbmlhIFNhbnRhIE1hcnRhIg0KICAgIH0sDQogICAgInRlbGVmb25vIiA6IG51bGwsDQogICAgImNvcnJlbyIgOiBudWxsDQogIH0sDQogICJvdHJvc0RvY3VtZW50b3MiIDogbnVsbCwNCiAgInZlbnRhVGVyY2VybyIgOiBudWxsLA0KICAiY3VlcnBvRG9jdW1lbnRvIiA6IFsgew0KICAgICJudW1JdGVtIiA6IDEsDQogICAgIm51bWVyb0RvY3VtZW50byIgOiBudWxsLA0KICAgICJ0aXBvSXRlbSIgOiAxLA0KICAgICJjYW50aWRhZCIgOiAyLA0KICAgICJjb2RpZ28iIDogIjAyIiwNCiAgICAidW5pTWVkaWRhIiA6IDU5LA0KICAgICJkZXNjcmlwY2lvbiIgOiAiRGVzdG9ybmlsbGFkb3IgcGhpbGxpcHMgbm8uIDEgeCAzIHB1bGciLA0KICAgICJwcmVjaW9VbmkiIDogMS44LA0KICAgICJtb250b0Rlc2N1IiA6IDAsDQogICAgImNvZFRyaWJ1dG8iIDogbnVsbCwNCiAgICAidmVudGFOb1N1aiIgOiAwLA0KICAgICJ2ZW50YUV4ZW50YSIgOiAwLA0KICAgICJ2ZW50YUdyYXZhZGEiIDogMy42LA0KICAgICJpdmFJdGVtIiA6IDAuNDEsDQogICAgInRyaWJ1dG9zIiA6IG51bGwsDQogICAgInBzdiIgOiAzLjYsDQogICAgIm5vR3JhdmFkbyIgOiAwDQogIH0sIHsNCiAgICAibnVtSXRlbSIgOiAyLA0KICAgICJudW1lcm9Eb2N1bWVudG8iIDogbnVsbCwNCiAgICAidGlwb0l0ZW0iIDogMSwNCiAgICAiY2FudGlkYWQiIDogMSwNCiAgICAiY29kaWdvIiA6ICIwMiIsDQogICAgInVuaU1lZGlkYSIgOiA1OSwNCiAgICAiZGVzY3JpcGNpb24iIDogIkNlbWVudG8gV1AgNDIuNSBLRyIsDQogICAgInByZWNpb1VuaSIgOiA4LjIsDQogICAgIm1vbnRvRGVzY3UiIDogMCwNCiAgICAiY29kVHJpYnV0byIgOiBudWxsLA0KICAgICJ2ZW50YU5vU3VqIiA6IDAsDQogICAgInZlbnRhRXhlbnRhIiA6IDAsDQogICAgInZlbnRhR3JhdmFkYSIgOiA4LjIsDQogICAgIml2YUl0ZW0iIDogMC45NCwNCiAgICAidHJpYnV0b3MiIDogbnVsbCwNCiAgICAicHN2IiA6IDguMiwNCiAgICAibm9HcmF2YWRvIiA6IDANCiAgfSBdLA0KICAicmVzdW1lbiIgOiB7DQogICAgInRvdGFsTm9TdWoiIDogMCwNCiAgICAidG90YWxFeGVudGEiIDogMCwNCiAgICAidG90YWxHcmF2YWRhIiA6IDExLjgsDQogICAgInN1YlRvdGFsVmVudGFzIiA6IDExLjgsDQogICAgImRlc2N1Tm9TdWoiIDogMCwNCiAgICAiZGVzY3VFeGVudGEiIDogMCwNCiAgICAiZGVzY3VHcmF2YWRhIiA6IDAsDQogICAgInRvdGFsRGVzY3UiIDogMCwNCiAgICAicG9yY2VudGFqZURlc2N1ZW50byIgOiAwLA0KICAgICJ0cmlidXRvcyIgOiBudWxsLA0KICAgICJzdWJUb3RhbCIgOiAxMS44LA0KICAgICJ0b3RhbEl2YSIgOiAxLjM1LA0KICAgICJpdmFSZXRlMSIgOiAwLA0KICAgICJyZXRlUmVudGEiIDogMCwNCiAgICAibW9udG9Ub3RhbE9wZXJhY2lvbiIgOiAxMS44LA0KICAgICJ0b3RhbE5vR3JhdmFkbyIgOiAwLA0KICAgICJ0b3RhbFBhZ2FyIiA6IDExLjgsDQogICAgInRvdGFsTGV0cmFzIiA6ICJPTkNFIERPTEFSRVMgQ09OIE9DSEVOVEEgQ0VOVEFWT1MiLA0KICAgICJzYWxkb0Zhdm9yIiA6IDAsDQogICAgImNvbmRpY2lvbk9wZXJhY2lvbiIgOiAxLA0KICAgICJwYWdvcyIgOiBudWxsLA0KICAgICJudW1QYWdvRWxlY3Ryb25pY28iIDogbnVsbA0KICB9LA0KICAiZXh0ZW5zaW9uIiA6IG51bGwsDQogICJhcGVuZGljZSIgOiBudWxsDQp9.VG_GGa-MQbXDpc9lVWdDcMeRmw3XB9vYnuLGQNP8Wx2aZUxFAWp-fpkYGnVXezPGsPSEbJeNOafhl5q5adCtD402s33zl4iv1nEJM64laQzG6uPVeBPDYVxaNjxlzbtso-QR0TOZLWHRruC4lGJ_QXs-JCFsUYO1qvv8h68ygczkMa_XA58S4TMoRncHp7YD9Z1U89OJijpD6xJUrn-uu9o7bzNf9WAFBHysSTWNq9kyQgWZ3-SRFd0SPWn-YrpydKougyKp125Ov98NoLzsOvvPJliM7u6ST4BgfxmRQLy0NlPOHaX7cLwqzaXiuYNpYLkNNU9ox7x8VnWK16DEtA'),
(10, 10, 2, '00', 2, 'PROCESADO', '94A5CAE5-CB26-ABC2-2EE6-08DA862BBE52', '2025B675A7B9637346A084B91877994DC3FE4A3P', '2025-05-03 18:27:27', '10', '001', 'RECIBIDO', '[]', '2025-05-04 00:27:26', 'Array'),
(11, 12, 2, '00', 2, 'PROCESADO', 'DAB2D909-A739-0B52-03A7-A5879B2DA3C6', '202572EFCE272F98462C81B678FDBC8D5D6D2JTK', '2025-05-03 18:35:46', '10', '001', 'RECIBIDO', '[]', '2025-05-04 00:35:46', 'Array'),
(12, 13, 2, '00', 2, 'PROCESADO', '6E595F75-3FED-9FB3-141A-687BE6B368D4', '2025E003A5F0FA4F42E7882E40F5339BE6FA3CQ8', '2025-05-03 18:37:51', '10', '001', 'RECIBIDO', '[]', '2025-05-04 00:37:51', 'Array'),
(13, 14, 2, '00', 2, 'PROCESADO', '8FCF06D8-7290-4A3E-B2F1-EBA70D1CCCBB', '2025905129A2A8754B319719F2607E865172RV5F', '2025-05-03 18:38:41', '10', '001', 'RECIBIDO', '[]', '2025-05-04 00:38:40', '{\"identificacion\":{\"version\":1,\"ambiente\":\"00\",\"tipoDte\":\"01\",\"numeroControl\":\"DTE-01-M001P001-000000000000014\",\"codigoGeneracion\":\"8FCF06D8-7290-4A3E-B2F1-EBA70D1CCCBB\",\"tipoModelo\":1,\"tipoOperacion\":1,\"tipoContingencia\":null,\"motivoContin\":null,\"fecEmi\":\"2025-05-03\",\"horEmi\":\"18:38:37\",\"tipoMoneda\":\"USD\"},\"documentoRelacionado\":null,\"emisor\":{\"nit\":\"03011504761021\",\"nrc\":\"3173130\",\"nombre\":\"Oscar Eduardo Fuentes Mancia\",\"codActividad\":\"46632\",\"descActividad\":\"venta al por mayor de articulos de ferreteria y pinturerias\",\"nombreComercial\":\"FERRETERIA FUENTES\",\"tipoEstablecimiento\":\"02\",\"direccion\":{\"departamento\":\"03\",\"municipio\":\"20\",\"complemento\":\"Carretera a los cobanos presa del venado\"},\"telefono\":\"73999642\",\"correo\":\"alex.calix1992@gmail.com\",\"codEstable\":null,\"codPuntoVenta\":null,\"codEstableMH\":null,\"codPuntoVentaMH\":null},\"receptor\":{\"nrc\":null,\"tipoDocumento\":\"13\",\"numDocumento\":\"01031767-9\",\"nombre\":\"Claudia Guadalupe Calixto de Fuentes\",\"codActividad\":null,\"descActividad\":null,\"direccion\":{\"departamento\":\"03\",\"municipio\":\"18\",\"complemento\":\"Colonia Santa Marta\"},\"telefono\":null,\"correo\":null},\"otrosDocumentos\":null,\"ventaTercero\":null,\"cuerpoDocumento\":[{\"numItem\":1,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":2,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Cemento WP 42.5 KG\",\"precioUni\":8.2,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":16.4,\"ivaItem\":1.89,\"tributos\":null,\"psv\":16.4,\"noGravado\":0}],\"resumen\":{\"totalNoSuj\":0,\"totalExenta\":0,\"totalGravada\":16.4,\"subTotalVentas\":16.4,\"descuNoSuj\":0,\"descuExenta\":0,\"descuGravada\":0,\"totalDescu\":0,\"porcentajeDescuento\":0,\"tributos\":null,\"subTotal\":16.4,\"totalIva\":1.89,\"ivaRete1\":0,\"reteRenta\":0,\"montoTotalOperacion\":16.4,\"totalNoGravado\":0,\"totalPagar\":16.4,\"totalLetras\":\"DIECIS\\u00c9IS DOLARES CON CUARENTA CENTAVOS\",\"saldoFavor\":0,\"condicionOperacion\":1,\"pagos\":null,\"numPagoElectronico\":null},\"extension\":null,\"apendice\":null}'),
(14, 15, 2, '00', 2, 'PROCESADO', 'D6B78B0D-BD2D-9BEB-0083-5A1977610807', '2025EAEF2492A816443AAD3B71F0A1A8A7648QZV', '2025-05-03 18:52:47', '10', '001', 'RECIBIDO', '[]', '2025-05-04 00:52:46', '{\"identificacion\":{\"version\":1,\"ambiente\":\"00\",\"tipoDte\":\"01\",\"numeroControl\":\"DTE-01-M001P001-000000000000015\",\"codigoGeneracion\":\"D6B78B0D-BD2D-9BEB-0083-5A1977610807\",\"tipoModelo\":1,\"tipoOperacion\":1,\"tipoContingencia\":null,\"motivoContin\":null,\"fecEmi\":\"2025-05-03\",\"horEmi\":\"18:52:44\",\"tipoMoneda\":\"USD\"},\"documentoRelacionado\":null,\"emisor\":{\"nit\":\"03011504761021\",\"nrc\":\"3173130\",\"nombre\":\"Oscar Eduardo Fuentes Mancia\",\"codActividad\":\"46632\",\"descActividad\":\"venta al por mayor de articulos de ferreteria y pinturerias\",\"nombreComercial\":\"FERRETERIA FUENTES\",\"tipoEstablecimiento\":\"02\",\"direccion\":{\"departamento\":\"03\",\"municipio\":\"20\",\"complemento\":\"Carretera a los cobanos presa del venado\"},\"telefono\":\"73999642\",\"correo\":\"alex.calix1992@gmail.com\",\"codEstable\":null,\"codPuntoVenta\":null,\"codEstableMH\":null,\"codPuntoVentaMH\":null},\"receptor\":{\"nrc\":null,\"tipoDocumento\":\"13\",\"numDocumento\":\"01031767-9\",\"nombre\":\"Claudia Guadalupe Calixto de Fuentes\",\"codActividad\":null,\"descActividad\":null,\"direccion\":{\"departamento\":\"03\",\"municipio\":\"18\",\"complemento\":\"Colonia Santa Marta\"},\"telefono\":null,\"correo\":null},\"otrosDocumentos\":null,\"ventaTercero\":null,\"cuerpoDocumento\":[{\"numItem\":1,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":1,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Cemento WP 42.5 KG\",\"precioUni\":8.2,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":8.2,\"ivaItem\":0.94,\"tributos\":null,\"psv\":8.2,\"noGravado\":0},{\"numItem\":2,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":2,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Destornillador phillips no. 1 x 3 pulg\",\"precioUni\":1.8,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":3.6,\"ivaItem\":0.41,\"tributos\":null,\"psv\":3.6,\"noGravado\":0}],\"resumen\":{\"totalNoSuj\":0,\"totalExenta\":0,\"totalGravada\":11.8,\"subTotalVentas\":11.8,\"descuNoSuj\":0,\"descuExenta\":0,\"descuGravada\":0,\"totalDescu\":0,\"porcentajeDescuento\":0,\"tributos\":null,\"subTotal\":11.8,\"totalIva\":1.35,\"ivaRete1\":0,\"reteRenta\":0,\"montoTotalOperacion\":11.8,\"totalNoGravado\":0,\"totalPagar\":11.8,\"totalLetras\":\"ONCE DOLARES CON OCHENTA CENTAVOS\",\"saldoFavor\":0,\"condicionOperacion\":1,\"pagos\":null,\"numPagoElectronico\":null},\"extension\":null,\"apendice\":null}'),
(15, 16, 2, '00', 2, 'PROCESADO', '9157FB4B-CA53-3C71-BDEB-450D788096A8', '20259A16FBE456544F09B44555F06FCC09E3MKUF', '2025-05-03 18:53:57', '10', '001', 'RECIBIDO', '[]', '2025-05-04 00:53:56', '{\"identificacion\":{\"version\":1,\"ambiente\":\"00\",\"tipoDte\":\"01\",\"numeroControl\":\"DTE-01-M001P001-000000000000016\",\"codigoGeneracion\":\"9157FB4B-CA53-3C71-BDEB-450D788096A8\",\"tipoModelo\":1,\"tipoOperacion\":1,\"tipoContingencia\":null,\"motivoContin\":null,\"fecEmi\":\"2025-05-03\",\"horEmi\":\"18:53:54\",\"tipoMoneda\":\"USD\"},\"documentoRelacionado\":null,\"emisor\":{\"nit\":\"03011504761021\",\"nrc\":\"3173130\",\"nombre\":\"Oscar Eduardo Fuentes Mancia\",\"codActividad\":\"46632\",\"descActividad\":\"venta al por mayor de articulos de ferreteria y pinturerias\",\"nombreComercial\":\"FERRETERIA FUENTES\",\"tipoEstablecimiento\":\"02\",\"direccion\":{\"departamento\":\"03\",\"municipio\":\"20\",\"complemento\":\"Carretera a los cobanos presa del venado\"},\"telefono\":\"73999642\",\"correo\":\"alex.calix1992@gmail.com\",\"codEstable\":null,\"codPuntoVenta\":null,\"codEstableMH\":null,\"codPuntoVentaMH\":null},\"receptor\":{\"nrc\":null,\"tipoDocumento\":\"13\",\"numDocumento\":\"01031767-9\",\"nombre\":\"Claudia Guadalupe Calixto de Fuentes\",\"codActividad\":null,\"descActividad\":null,\"direccion\":{\"departamento\":\"03\",\"municipio\":\"18\",\"complemento\":\"Colonia Santa Marta\"},\"telefono\":null,\"correo\":null},\"otrosDocumentos\":null,\"ventaTercero\":null,\"cuerpoDocumento\":[{\"numItem\":1,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":1,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Cemento WP 42.5 KG\",\"precioUni\":8.2,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":8.2,\"ivaItem\":0.94,\"tributos\":null,\"psv\":8.2,\"noGravado\":0},{\"numItem\":2,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":2,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Destornillador phillips no. 1 x 3 pulg\",\"precioUni\":1.8,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":3.6,\"ivaItem\":0.41,\"tributos\":null,\"psv\":3.6,\"noGravado\":0}],\"resumen\":{\"totalNoSuj\":0,\"totalExenta\":0,\"totalGravada\":11.8,\"subTotalVentas\":11.8,\"descuNoSuj\":0,\"descuExenta\":0,\"descuGravada\":0,\"totalDescu\":0,\"porcentajeDescuento\":0,\"tributos\":null,\"subTotal\":11.8,\"totalIva\":1.35,\"ivaRete1\":0,\"reteRenta\":0,\"montoTotalOperacion\":11.8,\"totalNoGravado\":0,\"totalPagar\":11.8,\"totalLetras\":\"ONCE DOLARES CON OCHENTA CENTAVOS\",\"saldoFavor\":0,\"condicionOperacion\":1,\"pagos\":null,\"numPagoElectronico\":null},\"extension\":null,\"apendice\":null}'),
(16, 17, 2, '00', 2, 'PROCESADO', 'DEBD53EB-61C4-B790-D352-44AA7621655C', '2025474217EE5E9B4FABAD433F3C83BFF86CNFYR', '2025-05-03 19:01:58', '10', '001', 'RECIBIDO', '[]', '2025-05-04 01:01:57', '{\"identificacion\":{\"version\":1,\"ambiente\":\"00\",\"tipoDte\":\"01\",\"numeroControl\":\"DTE-01-M001P001-000000000000017\",\"codigoGeneracion\":\"DEBD53EB-61C4-B790-D352-44AA7621655C\",\"tipoModelo\":1,\"tipoOperacion\":1,\"tipoContingencia\":null,\"motivoContin\":null,\"fecEmi\":\"2025-05-03\",\"horEmi\":\"19:01:55\",\"tipoMoneda\":\"USD\"},\"documentoRelacionado\":null,\"emisor\":{\"nit\":\"03011504761021\",\"nrc\":\"3173130\",\"nombre\":\"Oscar Eduardo Fuentes Mancia\",\"codActividad\":\"46632\",\"descActividad\":\"venta al por mayor de articulos de ferreteria y pinturerias\",\"nombreComercial\":\"FERRETERIA FUENTES\",\"tipoEstablecimiento\":\"02\",\"direccion\":{\"departamento\":\"03\",\"municipio\":\"20\",\"complemento\":\"Carretera a los cobanos presa del venado\"},\"telefono\":\"73999642\",\"correo\":\"alex.calix1992@gmail.com\",\"codEstable\":null,\"codPuntoVenta\":null,\"codEstableMH\":null,\"codPuntoVentaMH\":null},\"receptor\":{\"nrc\":null,\"tipoDocumento\":\"13\",\"numDocumento\":\"01031767-9\",\"nombre\":\"Claudia Guadalupe Calixto de Fuentes\",\"codActividad\":null,\"descActividad\":null,\"direccion\":{\"departamento\":\"03\",\"municipio\":\"18\",\"complemento\":\"Colonia Santa Marta\"},\"telefono\":null,\"correo\":null},\"otrosDocumentos\":null,\"ventaTercero\":null,\"cuerpoDocumento\":[{\"numItem\":1,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":1,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Cemento WP 42.5 KG\",\"precioUni\":8.2,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":8.2,\"ivaItem\":0.94,\"tributos\":null,\"psv\":8.2,\"noGravado\":0},{\"numItem\":2,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":2,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Destornillador phillips no. 1 x 3 pulg\",\"precioUni\":1.8,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":3.6,\"ivaItem\":0.41,\"tributos\":null,\"psv\":3.6,\"noGravado\":0}],\"resumen\":{\"totalNoSuj\":0,\"totalExenta\":0,\"totalGravada\":11.8,\"subTotalVentas\":11.8,\"descuNoSuj\":0,\"descuExenta\":0,\"descuGravada\":0,\"totalDescu\":0,\"porcentajeDescuento\":0,\"tributos\":null,\"subTotal\":11.8,\"totalIva\":1.35,\"ivaRete1\":0,\"reteRenta\":0,\"montoTotalOperacion\":11.8,\"totalNoGravado\":0,\"totalPagar\":11.8,\"totalLetras\":\"ONCE DOLARES CON OCHENTA CENTAVOS\",\"saldoFavor\":0,\"condicionOperacion\":1,\"pagos\":null,\"numPagoElectronico\":null},\"extension\":null,\"apendice\":null}'),
(17, 19, 2, '00', 2, 'RECHAZADO', '3CE6B46D-2F3E-3343-445F-5045CAD20F65', NULL, '2025-05-04 19:56:45', '98', '096', 'DOCUMENTO NO CUMPLE ESQUEMA JSON', '[\"Campo #\\/receptor\\/direccion\\/departamento no cumple el formato requerido\",\"Campo #\\/receptor\\/direccion contiene un valor inv\\u00e1lido\",\"Campo #\\/receptor contiene un valor inv\\u00e1lido\"]', '2025-05-05 01:56:45', '{\"identificacion\":{\"version\":1,\"ambiente\":\"00\",\"tipoDte\":\"01\",\"numeroControl\":\"DTE-01-M001P001-000000000000019\",\"codigoGeneracion\":\"3CE6B46D-2F3E-3343-445F-5045CAD20F65\",\"tipoModelo\":1,\"tipoOperacion\":1,\"tipoContingencia\":null,\"motivoContin\":null,\"fecEmi\":\"2025-05-04\",\"horEmi\":\"19:56:43\",\"tipoMoneda\":\"USD\"},\"documentoRelacionado\":null,\"emisor\":{\"nit\":\"03011504761021\",\"nrc\":\"3173130\",\"nombre\":\"Oscar Eduardo Fuentes Mancia\",\"codActividad\":\"46632\",\"descActividad\":\"venta al por mayor de articulos de ferreteria y pinturerias\",\"nombreComercial\":\"FERRETERIA FUENTES\",\"tipoEstablecimiento\":\"02\",\"direccion\":{\"departamento\":\"03\",\"municipio\":\"20\",\"complemento\":\"Carretera a los cobanos presa del venado\"},\"telefono\":\"73999642\",\"correo\":\"alex.calix1992@gmail.com\",\"codEstable\":null,\"codPuntoVenta\":null,\"codEstableMH\":null,\"codPuntoVentaMH\":null},\"receptor\":{\"nrc\":null,\"tipoDocumento\":\"13\",\"numDocumento\":\"01031767-9\",\"nombre\":\"Claudia Guadalupe Calixto de Fuentes\",\"codActividad\":null,\"descActividad\":null,\"direccion\":{\"departamento\":\"3\",\"municipio\":\"18\",\"complemento\":\"1 Calle Ote. Col. Santa Marta Final Pasaje Marin 1 Casa 11\"},\"telefono\":\"6123-8974\",\"correo\":\"alex.calix1992@gmail.com\"},\"otrosDocumentos\":null,\"ventaTercero\":null,\"cuerpoDocumento\":[{\"numItem\":1,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":3,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Cemento Fuerte Holcim 50 KG\",\"precioUni\":9.75,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":29.25,\"ivaItem\":3.36,\"tributos\":null,\"psv\":29.25,\"noGravado\":0},{\"numItem\":2,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":1,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Destornillador phillips no. 1 x 3 pulg\",\"precioUni\":1.8,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":1.8,\"ivaItem\":0.21,\"tributos\":null,\"psv\":1.8,\"noGravado\":0}],\"resumen\":{\"totalNoSuj\":0,\"totalExenta\":0,\"totalGravada\":31.05,\"subTotalVentas\":31.05,\"descuNoSuj\":0,\"descuExenta\":0,\"descuGravada\":0,\"totalDescu\":0,\"porcentajeDescuento\":0,\"tributos\":null,\"subTotal\":31.05,\"totalIva\":3.57,\"ivaRete1\":0,\"reteRenta\":0,\"montoTotalOperacion\":31.05,\"totalNoGravado\":0,\"totalPagar\":31.05,\"totalLetras\":\"TREINTA Y UNO DOLARES CON CINCO CENTAVOS\",\"saldoFavor\":0,\"condicionOperacion\":1,\"pagos\":null,\"numPagoElectronico\":null},\"extension\":null,\"apendice\":null}'),
(18, 20, 2, '00', 2, 'PROCESADO', '08DF1EFA-5F32-6C43-F043-B3D37F245F9D', '20253FC737E3A0F94BDEAA18F860EE8033D5NJYT', '2025-05-04 20:01:47', '10', '001', 'RECIBIDO', '[]', '2025-05-05 02:01:47', '{\"identificacion\":{\"version\":1,\"ambiente\":\"00\",\"tipoDte\":\"01\",\"numeroControl\":\"DTE-01-M001P001-000000000000020\",\"codigoGeneracion\":\"08DF1EFA-5F32-6C43-F043-B3D37F245F9D\",\"tipoModelo\":1,\"tipoOperacion\":1,\"tipoContingencia\":null,\"motivoContin\":null,\"fecEmi\":\"2025-05-04\",\"horEmi\":\"20:01:45\",\"tipoMoneda\":\"USD\"},\"documentoRelacionado\":null,\"emisor\":{\"nit\":\"03011504761021\",\"nrc\":\"3173130\",\"nombre\":\"Oscar Eduardo Fuentes Mancia\",\"codActividad\":\"46632\",\"descActividad\":\"venta al por mayor de articulos de ferreteria y pinturerias\",\"nombreComercial\":\"FERRETERIA FUENTES\",\"tipoEstablecimiento\":\"02\",\"direccion\":{\"departamento\":\"03\",\"municipio\":\"20\",\"complemento\":\"Carretera a los cobanos presa del venado\"},\"telefono\":\"73999642\",\"correo\":\"alex.calix1992@gmail.com\",\"codEstable\":null,\"codPuntoVenta\":null,\"codEstableMH\":null,\"codPuntoVentaMH\":null},\"receptor\":{\"nrc\":null,\"tipoDocumento\":\"13\",\"numDocumento\":\"01031767-9\",\"nombre\":\"Claudia Guadalupe Calixto de Fuentes\",\"codActividad\":null,\"descActividad\":null,\"direccion\":{\"departamento\":\"03\",\"municipio\":\"18\",\"complemento\":\"1 Calle Ote. Col. Santa Marta Final Pasaje Marin 1 Casa 11\"},\"telefono\":\"6123-8974\",\"correo\":\"alex.calix1992@gmail.com\"},\"otrosDocumentos\":null,\"ventaTercero\":null,\"cuerpoDocumento\":[{\"numItem\":1,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":3,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Cemento Fuerte Holcim 50 KG\",\"precioUni\":9.75,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":29.25,\"ivaItem\":3.36,\"tributos\":null,\"psv\":29.25,\"noGravado\":0},{\"numItem\":2,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":1,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Destornillador phillips no. 1 x 3 pulg\",\"precioUni\":1.8,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":1.8,\"ivaItem\":0.21,\"tributos\":null,\"psv\":1.8,\"noGravado\":0}],\"resumen\":{\"totalNoSuj\":0,\"totalExenta\":0,\"totalGravada\":31.05,\"subTotalVentas\":31.05,\"descuNoSuj\":0,\"descuExenta\":0,\"descuGravada\":0,\"totalDescu\":0,\"porcentajeDescuento\":0,\"tributos\":null,\"subTotal\":31.05,\"totalIva\":3.57,\"ivaRete1\":0,\"reteRenta\":0,\"montoTotalOperacion\":31.05,\"totalNoGravado\":0,\"totalPagar\":31.05,\"totalLetras\":\"TREINTA Y UNO DOLARES CON CINCO CENTAVOS\",\"saldoFavor\":0,\"condicionOperacion\":1,\"pagos\":null,\"numPagoElectronico\":null},\"extension\":null,\"apendice\":null}'),
(19, 21, 2, '00', 2, 'RECHAZADO', 'B1E809B5-40F2-40B1-10C8-5B25910C8FF8', NULL, '2025-05-04 22:39:32', '98', '096', 'DOCUMENTO NO CUMPLE ESQUEMA JSON', '[\"#\\/cuerpoDocumento\\/0\\/ventaGravada: 3.3000000000000003 is not a multiple of 1.0E-8\",\"#\\/cuerpoDocumento\\/0\\/psv: 3.3000000000000003 is not a multiple of 1.0E-8\"]', '2025-05-05 04:39:32', '{\"identificacion\":{\"version\":1,\"ambiente\":\"00\",\"tipoDte\":\"01\",\"numeroControl\":\"DTE-01-M001P001-000000000000021\",\"codigoGeneracion\":\"B1E809B5-40F2-40B1-10C8-5B25910C8FF8\",\"tipoModelo\":1,\"tipoOperacion\":1,\"tipoContingencia\":null,\"motivoContin\":null,\"fecEmi\":\"2025-05-04\",\"horEmi\":\"22:39:29\",\"tipoMoneda\":\"USD\"},\"documentoRelacionado\":null,\"emisor\":{\"nit\":\"03011504761021\",\"nrc\":\"3173130\",\"nombre\":\"Oscar Eduardo Fuentes Mancia\",\"codActividad\":\"46632\",\"descActividad\":\"venta al por mayor de articulos de ferreteria y pinturerias\",\"nombreComercial\":\"FERRETERIA FUENTES\",\"tipoEstablecimiento\":\"02\",\"direccion\":{\"departamento\":\"03\",\"municipio\":\"20\",\"complemento\":\"Canton Punta Remedios, Caserio Los Cobanos,Acajutla\"},\"telefono\":\"73999642\",\"correo\":\"ferreteriafuentes@gmail.com\",\"codEstable\":null,\"codPuntoVenta\":null,\"codEstableMH\":null,\"codPuntoVentaMH\":null},\"receptor\":{\"nrc\":null,\"tipoDocumento\":\"13\",\"numDocumento\":\"01031767-9\",\"nombre\":\"Claudia Guadalupe Calixto de Fuentes\",\"codActividad\":null,\"descActividad\":null,\"direccion\":{\"departamento\":\"03\",\"municipio\":\"18\",\"complemento\":\"1 Calle Ote. Col. Santa Marta Final Pasaje Marin 1 Casa 11\"},\"telefono\":\"6123-8974\",\"correo\":\"alex.calix1992@gmail.com\"},\"otrosDocumentos\":null,\"ventaTercero\":null,\"cuerpoDocumento\":[{\"numItem\":1,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":3,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Espoja para Cemento 35cm X 35cm\",\"precioUni\":1.1,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":3.3000000000000003,\"ivaItem\":0.38,\"tributos\":null,\"psv\":3.3000000000000003,\"noGravado\":0},{\"numItem\":2,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":1,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Cemento WP 42.5 KG\",\"precioUni\":8.2,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":8.2,\"ivaItem\":0.94,\"tributos\":null,\"psv\":8.2,\"noGravado\":0},{\"numItem\":3,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":2,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Cemento Fuerte Holcim 50 KG\",\"precioUni\":9.75,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":19.5,\"ivaItem\":2.24,\"tributos\":null,\"psv\":19.5,\"noGravado\":0},{\"numItem\":4,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":1,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Destornillador phillips no. 1 x 3 pulg\",\"precioUni\":1.8,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":1.8,\"ivaItem\":0.21,\"tributos\":null,\"psv\":1.8,\"noGravado\":0}],\"resumen\":{\"totalNoSuj\":0,\"totalExenta\":0,\"totalGravada\":32.8,\"subTotalVentas\":32.8,\"descuNoSuj\":0,\"descuExenta\":0,\"descuGravada\":0,\"totalDescu\":0,\"porcentajeDescuento\":0,\"tributos\":null,\"subTotal\":32.8,\"totalIva\":3.77,\"ivaRete1\":0,\"reteRenta\":0,\"montoTotalOperacion\":32.8,\"totalNoGravado\":0,\"totalPagar\":32.8,\"totalLetras\":\"TREINTA Y DOS DOLARES CON OCHENTA CENTAVOS\",\"saldoFavor\":0,\"condicionOperacion\":1,\"pagos\":null,\"numPagoElectronico\":null},\"extension\":null,\"apendice\":null}'),
(20, 22, 2, '00', 2, 'PROCESADO', '90FEE3AD-2583-5EA6-3998-18DCFF35106D', '202583904FCF59C94CD192D2E538BDC155647VAW', '2025-05-05 08:19:09', '10', '001', 'RECIBIDO', '[]', '2025-05-05 14:19:09', '{\"identificacion\":{\"version\":1,\"ambiente\":\"00\",\"tipoDte\":\"01\",\"numeroControl\":\"DTE-01-M001P001-000000000000022\",\"codigoGeneracion\":\"90FEE3AD-2583-5EA6-3998-18DCFF35106D\",\"tipoModelo\":1,\"tipoOperacion\":1,\"tipoContingencia\":null,\"motivoContin\":null,\"fecEmi\":\"2025-05-05\",\"horEmi\":\"08:19:06\",\"tipoMoneda\":\"USD\"},\"documentoRelacionado\":null,\"emisor\":{\"nit\":\"03011504761021\",\"nrc\":\"3173130\",\"nombre\":\"Oscar Eduardo Fuentes Mancia\",\"codActividad\":\"46632\",\"descActividad\":\"venta al por mayor de articulos de ferreteria y pinturerias\",\"nombreComercial\":\"FERRETERIA FUENTES\",\"tipoEstablecimiento\":\"02\",\"direccion\":{\"departamento\":\"03\",\"municipio\":\"20\",\"complemento\":\"Canton Punta Remedios, Caserio Los Cobanos,Acajutla\"},\"telefono\":\"73999642\",\"correo\":\"ferreteriafuentes@gmail.com\",\"codEstable\":null,\"codPuntoVenta\":null,\"codEstableMH\":null,\"codPuntoVentaMH\":null},\"receptor\":{\"nrc\":null,\"tipoDocumento\":\"13\",\"numDocumento\":\"01031767-9\",\"nombre\":\"Claudia Guadalupe Calixto de Fuentes\",\"codActividad\":null,\"descActividad\":null,\"direccion\":{\"departamento\":\"03\",\"municipio\":\"18\",\"complemento\":\"1 Calle Ote. Col. Santa Marta Final Pasaje Marin 1 Casa 11\"},\"telefono\":\"6123-8974\",\"correo\":\"alex.calix1992@gmail.com\"},\"otrosDocumentos\":null,\"ventaTercero\":null,\"cuerpoDocumento\":[{\"numItem\":1,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":1,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Destornillador phillips no. 1 x 3 pulg\",\"precioUni\":1.8,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":1.8,\"ivaItem\":0.21,\"tributos\":null,\"psv\":1.8,\"noGravado\":0}],\"resumen\":{\"totalNoSuj\":0,\"totalExenta\":0,\"totalGravada\":1.8,\"subTotalVentas\":1.8,\"descuNoSuj\":0,\"descuExenta\":0,\"descuGravada\":0,\"totalDescu\":0,\"porcentajeDescuento\":0,\"tributos\":null,\"subTotal\":1.8,\"totalIva\":0.21,\"ivaRete1\":0,\"reteRenta\":0,\"montoTotalOperacion\":1.8,\"totalNoGravado\":0,\"totalPagar\":1.8,\"totalLetras\":\"UNO DOLARES CON OCHENTA CENTAVOS\",\"saldoFavor\":0,\"condicionOperacion\":1,\"pagos\":null,\"numPagoElectronico\":null},\"extension\":null,\"apendice\":null}'),
(21, 23, 2, '00', 2, 'PROCESADO', 'F0A648C1-06EB-4B7D-148F-0027BF857713', '202561827C764379429AA0EDB17CD8A739714K1U', '2025-05-05 08:21:12', '10', '001', 'RECIBIDO', '[]', '2025-05-05 14:21:12', '{\"identificacion\":{\"version\":1,\"ambiente\":\"00\",\"tipoDte\":\"01\",\"numeroControl\":\"DTE-01-M001P001-000000000000023\",\"codigoGeneracion\":\"F0A648C1-06EB-4B7D-148F-0027BF857713\",\"tipoModelo\":1,\"tipoOperacion\":1,\"tipoContingencia\":null,\"motivoContin\":null,\"fecEmi\":\"2025-05-05\",\"horEmi\":\"08:21:09\",\"tipoMoneda\":\"USD\"},\"documentoRelacionado\":null,\"emisor\":{\"nit\":\"03011504761021\",\"nrc\":\"3173130\",\"nombre\":\"Oscar Eduardo Fuentes Mancia\",\"codActividad\":\"46632\",\"descActividad\":\"venta al por mayor de articulos de ferreteria y pinturerias\",\"nombreComercial\":\"FERRETERIA FUENTES\",\"tipoEstablecimiento\":\"02\",\"direccion\":{\"departamento\":\"03\",\"municipio\":\"20\",\"complemento\":\"Canton Punta Remedios, Caserio Los Cobanos,Acajutla\"},\"telefono\":\"73999642\",\"correo\":\"ferreteriafuentes@gmail.com\",\"codEstable\":null,\"codPuntoVenta\":null,\"codEstableMH\":null,\"codPuntoVentaMH\":null},\"receptor\":{\"nrc\":null,\"tipoDocumento\":\"13\",\"numDocumento\":\"01031767-9\",\"nombre\":\"Claudia Guadalupe Calixto de Fuentes\",\"codActividad\":null,\"descActividad\":null,\"direccion\":{\"departamento\":\"03\",\"municipio\":\"18\",\"complemento\":\"1 Calle Ote. Col. Santa Marta Final Pasaje Marin 1 Casa 11\"},\"telefono\":\"6123-8974\",\"correo\":\"alex.calix1992@gmail.com\"},\"otrosDocumentos\":null,\"ventaTercero\":null,\"cuerpoDocumento\":[{\"numItem\":1,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":1,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Cemento Fuerte Holcim 50 KG\",\"precioUni\":9.75,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":9.75,\"ivaItem\":1.12,\"tributos\":null,\"psv\":9.75,\"noGravado\":0}],\"resumen\":{\"totalNoSuj\":0,\"totalExenta\":0,\"totalGravada\":9.75,\"subTotalVentas\":9.75,\"descuNoSuj\":0,\"descuExenta\":0,\"descuGravada\":0,\"totalDescu\":0,\"porcentajeDescuento\":0,\"tributos\":null,\"subTotal\":9.75,\"totalIva\":1.12,\"ivaRete1\":0,\"reteRenta\":0,\"montoTotalOperacion\":9.75,\"totalNoGravado\":0,\"totalPagar\":9.75,\"totalLetras\":\"NUEVE DOLARES CON SETENTA Y CINCO CENTAVOS\",\"saldoFavor\":0,\"condicionOperacion\":1,\"pagos\":null,\"numPagoElectronico\":null},\"extension\":null,\"apendice\":null}'),
(22, 24, 2, '00', 2, 'PROCESADO', '325F36EE-3510-2ECC-993C-4A3508F20B92', '2025567E5900F0B544168789215532BAE0E7M7RQ', '2025-05-05 11:05:02', '10', '001', 'RECIBIDO', '[]', '2025-05-05 17:05:01', '{\"identificacion\":{\"version\":1,\"ambiente\":\"00\",\"tipoDte\":\"01\",\"numeroControl\":\"DTE-01-M001P001-000000000000024\",\"codigoGeneracion\":\"325F36EE-3510-2ECC-993C-4A3508F20B92\",\"tipoModelo\":1,\"tipoOperacion\":1,\"tipoContingencia\":null,\"motivoContin\":null,\"fecEmi\":\"2025-05-05\",\"horEmi\":\"11:04:58\",\"tipoMoneda\":\"USD\"},\"documentoRelacionado\":null,\"emisor\":{\"nit\":\"03011504761021\",\"nrc\":\"3173130\",\"nombre\":\"Oscar Eduardo Fuentes Mancia\",\"codActividad\":\"46632\",\"descActividad\":\"venta al por mayor de articulos de ferreteria y pinturerias\",\"nombreComercial\":\"FERRETERIA FUENTES\",\"tipoEstablecimiento\":\"02\",\"direccion\":{\"departamento\":\"03\",\"municipio\":\"20\",\"complemento\":\"Canton Punta Remedios, Caserio Los Cobanos,Acajutla\"},\"telefono\":\"73999642\",\"correo\":\"ferreteriafuentes@gmail.com\",\"codEstable\":null,\"codPuntoVenta\":null,\"codEstableMH\":null,\"codPuntoVentaMH\":null},\"receptor\":{\"nrc\":null,\"tipoDocumento\":\"13\",\"numDocumento\":\"01031767-9\",\"nombre\":\"Claudia Guadalupe Calixto de Fuentes\",\"codActividad\":null,\"descActividad\":null,\"direccion\":{\"departamento\":\"03\",\"municipio\":\"18\",\"complemento\":\"1 Calle Ote. Col. Santa Marta Final Pasaje Marin 1 Casa 11\"},\"telefono\":\"6123-8974\",\"correo\":\"alex.calix1992@gmail.com\"},\"otrosDocumentos\":null,\"ventaTercero\":null,\"cuerpoDocumento\":[{\"numItem\":1,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":1,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Cemento WP 42.5 KG\",\"precioUni\":8.2,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":8.2,\"ivaItem\":0.94,\"tributos\":null,\"psv\":8.2,\"noGravado\":0},{\"numItem\":2,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":1,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Destornillador phillips no. 1 x 3 pulg\",\"precioUni\":1.8,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":1.8,\"ivaItem\":0.21,\"tributos\":null,\"psv\":1.8,\"noGravado\":0}],\"resumen\":{\"totalNoSuj\":0,\"totalExenta\":0,\"totalGravada\":10,\"subTotalVentas\":10,\"descuNoSuj\":0,\"descuExenta\":0,\"descuGravada\":0,\"totalDescu\":0,\"porcentajeDescuento\":0,\"tributos\":null,\"subTotal\":10,\"totalIva\":1.15,\"ivaRete1\":0,\"reteRenta\":0,\"montoTotalOperacion\":10,\"totalNoGravado\":0,\"totalPagar\":10,\"totalLetras\":\"DIEZ DOLARES\",\"saldoFavor\":0,\"condicionOperacion\":1,\"pagos\":null,\"numPagoElectronico\":null},\"extension\":null,\"apendice\":null}'),
(23, 25, 2, '00', 2, 'PROCESADO', '962D8B35-F123-7095-019C-5CF68B1459F6', '20256D9962EC849543E78D67077E10A2F621YUV9', '2025-05-05 12:37:04', '10', '001', 'RECIBIDO', '[]', '2025-05-05 18:37:03', '{\"identificacion\":{\"version\":1,\"ambiente\":\"00\",\"tipoDte\":\"01\",\"numeroControl\":\"DTE-01-M001P001-000000000000025\",\"codigoGeneracion\":\"962D8B35-F123-7095-019C-5CF68B1459F6\",\"tipoModelo\":1,\"tipoOperacion\":1,\"tipoContingencia\":null,\"motivoContin\":null,\"fecEmi\":\"2025-05-05\",\"horEmi\":\"12:37:01\",\"tipoMoneda\":\"USD\"},\"documentoRelacionado\":null,\"emisor\":{\"nit\":\"03011504761021\",\"nrc\":\"3173130\",\"nombre\":\"Oscar Eduardo Fuentes Mancia\",\"codActividad\":\"46632\",\"descActividad\":\"venta al por mayor de articulos de ferreteria y pinturerias\",\"nombreComercial\":\"FERRETERIA FUENTES\",\"tipoEstablecimiento\":\"02\",\"direccion\":{\"departamento\":\"03\",\"municipio\":\"20\",\"complemento\":\"Canton Punta Remedios, Caserio Los Cobanos,Acajutla\"},\"telefono\":\"73999642\",\"correo\":\"ferreteriafuentes@gmail.com\",\"codEstable\":null,\"codPuntoVenta\":null,\"codEstableMH\":null,\"codPuntoVentaMH\":null},\"receptor\":{\"nrc\":null,\"tipoDocumento\":\"13\",\"numDocumento\":\"01031767-9\",\"nombre\":\"Claudia Guadalupe Calixto de Fuentes\",\"codActividad\":null,\"descActividad\":null,\"direccion\":{\"departamento\":\"03\",\"municipio\":\"18\",\"complemento\":\"1 Calle Ote. Col. Santa Marta Final Pasaje Marin 1 Casa 11\"},\"telefono\":\"6123-8974\",\"correo\":\"alex.calix1992@gmail.com\"},\"otrosDocumentos\":null,\"ventaTercero\":null,\"cuerpoDocumento\":[{\"numItem\":1,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":2,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Cemento Fuerte Holcim 50 KG\",\"precioUni\":9.75,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":19.5,\"ivaItem\":2.24,\"tributos\":null,\"psv\":19.5,\"noGravado\":0},{\"numItem\":2,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":1,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Destornillador phillips no. 1 x 3 pulg\",\"precioUni\":1.8,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":1.8,\"ivaItem\":0.21,\"tributos\":null,\"psv\":1.8,\"noGravado\":0},{\"numItem\":3,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":2,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Espoja para Cemento 35cm X 35cm\",\"precioUni\":1.1,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":2.2,\"ivaItem\":0.25,\"tributos\":null,\"psv\":2.2,\"noGravado\":0}],\"resumen\":{\"totalNoSuj\":0,\"totalExenta\":0,\"totalGravada\":23.5,\"subTotalVentas\":23.5,\"descuNoSuj\":0,\"descuExenta\":0,\"descuGravada\":0,\"totalDescu\":0,\"porcentajeDescuento\":0,\"tributos\":null,\"subTotal\":23.5,\"totalIva\":2.7,\"ivaRete1\":0,\"reteRenta\":0,\"montoTotalOperacion\":23.5,\"totalNoGravado\":0,\"totalPagar\":23.5,\"totalLetras\":\"VEINTITR\\u00c9S DOLARES CON CINCUENTA CENTAVOS\",\"saldoFavor\":0,\"condicionOperacion\":1,\"pagos\":null,\"numPagoElectronico\":null},\"extension\":null,\"apendice\":null}'),
(24, 26, 2, '00', 2, 'PROCESADO', '33B878DE-7362-7E7A-42B7-295D6835E6B8', '2025D8FEC986791A499BAC32F02F481AF233TMJ9', '2025-05-05 12:44:52', '10', '001', 'RECIBIDO', '[]', '2025-05-05 18:44:51', '{\"identificacion\":{\"version\":1,\"ambiente\":\"00\",\"tipoDte\":\"01\",\"numeroControl\":\"DTE-01-M001P001-000000000000026\",\"codigoGeneracion\":\"33B878DE-7362-7E7A-42B7-295D6835E6B8\",\"tipoModelo\":1,\"tipoOperacion\":1,\"tipoContingencia\":null,\"motivoContin\":null,\"fecEmi\":\"2025-05-05\",\"horEmi\":\"12:44:49\",\"tipoMoneda\":\"USD\"},\"documentoRelacionado\":null,\"emisor\":{\"nit\":\"03011504761021\",\"nrc\":\"3173130\",\"nombre\":\"Oscar Eduardo Fuentes Mancia\",\"codActividad\":\"46632\",\"descActividad\":\"venta al por mayor de articulos de ferreteria y pinturerias\",\"nombreComercial\":\"FERRETERIA FUENTES\",\"tipoEstablecimiento\":\"02\",\"direccion\":{\"departamento\":\"03\",\"municipio\":\"20\",\"complemento\":\"Canton Punta Remedios, Caserio Los Cobanos,Acajutla\"},\"telefono\":\"73999642\",\"correo\":\"ferreteriafuentes@gmail.com\",\"codEstable\":null,\"codPuntoVenta\":null,\"codEstableMH\":null,\"codPuntoVentaMH\":null},\"receptor\":{\"nrc\":null,\"tipoDocumento\":\"13\",\"numDocumento\":\"01031767-9\",\"nombre\":\"Claudia Guadalupe Calixto de Fuentes\",\"codActividad\":null,\"descActividad\":null,\"direccion\":{\"departamento\":\"03\",\"municipio\":\"18\",\"complemento\":\"1 Calle Ote. Col. Santa Marta Final Pasaje Marin 1 Casa 11\"},\"telefono\":\"6123-8974\",\"correo\":\"alex.calix1992@gmail.com\"},\"otrosDocumentos\":null,\"ventaTercero\":null,\"cuerpoDocumento\":[{\"numItem\":1,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":2,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Cemento Fuerte Holcim 50 KG\",\"precioUni\":9.75,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":19.5,\"ivaItem\":2.24,\"tributos\":null,\"psv\":19.5,\"noGravado\":0},{\"numItem\":2,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":1,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Destornillador phillips no. 1 x 3 pulg\",\"precioUni\":1.8,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":1.8,\"ivaItem\":0.21,\"tributos\":null,\"psv\":1.8,\"noGravado\":0}],\"resumen\":{\"totalNoSuj\":0,\"totalExenta\":0,\"totalGravada\":21.3,\"subTotalVentas\":21.3,\"descuNoSuj\":0,\"descuExenta\":0,\"descuGravada\":0,\"totalDescu\":0,\"porcentajeDescuento\":0,\"tributos\":null,\"subTotal\":21.3,\"totalIva\":2.45,\"ivaRete1\":0,\"reteRenta\":0,\"montoTotalOperacion\":21.3,\"totalNoGravado\":0,\"totalPagar\":21.3,\"totalLetras\":\"VEINTIUNO DOLARES CON TREINTA CENTAVOS\",\"saldoFavor\":0,\"condicionOperacion\":1,\"pagos\":null,\"numPagoElectronico\":null},\"extension\":null,\"apendice\":null}'),
(25, 27, 2, '00', 2, 'PROCESADO', 'F81AFD57-62DF-07BF-4EFC-645B38EEA346', '2025A9876197693C4BDCAC313320EF6EDFEAC50X', '2025-05-05 12:56:00', '10', '001', 'RECIBIDO', '[]', '2025-05-05 18:56:00', '{\"identificacion\":{\"version\":1,\"ambiente\":\"00\",\"tipoDte\":\"01\",\"numeroControl\":\"DTE-01-M001P001-000000000000027\",\"codigoGeneracion\":\"F81AFD57-62DF-07BF-4EFC-645B38EEA346\",\"tipoModelo\":1,\"tipoOperacion\":1,\"tipoContingencia\":null,\"motivoContin\":null,\"fecEmi\":\"2025-05-05\",\"horEmi\":\"12:55:57\",\"tipoMoneda\":\"USD\"},\"documentoRelacionado\":null,\"emisor\":{\"nit\":\"03011504761021\",\"nrc\":\"3173130\",\"nombre\":\"Oscar Eduardo Fuentes Mancia\",\"codActividad\":\"46632\",\"descActividad\":\"venta al por mayor de articulos de ferreteria y pinturerias\",\"nombreComercial\":\"FERRETERIA FUENTES\",\"tipoEstablecimiento\":\"02\",\"direccion\":{\"departamento\":\"03\",\"municipio\":\"20\",\"complemento\":\"Canton Punta Remedios, Caserio Los Cobanos,Acajutla\"},\"telefono\":\"73999642\",\"correo\":\"ferreteriafuentes019@gmail.com\",\"codEstable\":null,\"codPuntoVenta\":null,\"codEstableMH\":null,\"codPuntoVentaMH\":null},\"receptor\":{\"nrc\":null,\"tipoDocumento\":\"13\",\"numDocumento\":\"01031767-9\",\"nombre\":\"Claudia Guadalupe Calixto de Fuentes\",\"codActividad\":null,\"descActividad\":null,\"direccion\":{\"departamento\":\"03\",\"municipio\":\"18\",\"complemento\":\"1 Calle Ote. Col. Santa Marta Final Pasaje Marin 1 Casa 11\"},\"telefono\":\"6123-8974\",\"correo\":\"alex.calix1992@gmail.com\"},\"otrosDocumentos\":null,\"ventaTercero\":null,\"cuerpoDocumento\":[{\"numItem\":1,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":1,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Cemento Fuerte Holcim 50 KG\",\"precioUni\":9.75,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":9.75,\"ivaItem\":1.12,\"tributos\":null,\"psv\":9.75,\"noGravado\":0}],\"resumen\":{\"totalNoSuj\":0,\"totalExenta\":0,\"totalGravada\":9.75,\"subTotalVentas\":9.75,\"descuNoSuj\":0,\"descuExenta\":0,\"descuGravada\":0,\"totalDescu\":0,\"porcentajeDescuento\":0,\"tributos\":null,\"subTotal\":9.75,\"totalIva\":1.12,\"ivaRete1\":0,\"reteRenta\":0,\"montoTotalOperacion\":9.75,\"totalNoGravado\":0,\"totalPagar\":9.75,\"totalLetras\":\"NUEVE DOLARES CON SETENTA Y CINCO CENTAVOS\",\"saldoFavor\":0,\"condicionOperacion\":1,\"pagos\":null,\"numPagoElectronico\":null},\"extension\":null,\"apendice\":null}'),
(26, 28, 2, '00', 2, 'PROCESADO', 'DF127C99-87B6-56B8-0847-D2CCA549D4D2', '2025B51FE3CE7878419498AE43AC39B7D7DBILLY', '2025-05-05 12:57:12', '10', '001', 'RECIBIDO', '[]', '2025-05-05 18:57:11', '{\"identificacion\":{\"version\":1,\"ambiente\":\"00\",\"tipoDte\":\"01\",\"numeroControl\":\"DTE-01-M001P001-000000000000028\",\"codigoGeneracion\":\"DF127C99-87B6-56B8-0847-D2CCA549D4D2\",\"tipoModelo\":1,\"tipoOperacion\":1,\"tipoContingencia\":null,\"motivoContin\":null,\"fecEmi\":\"2025-05-05\",\"horEmi\":\"12:57:09\",\"tipoMoneda\":\"USD\"},\"documentoRelacionado\":null,\"emisor\":{\"nit\":\"03011504761021\",\"nrc\":\"3173130\",\"nombre\":\"Oscar Eduardo Fuentes Mancia\",\"codActividad\":\"46632\",\"descActividad\":\"venta al por mayor de articulos de ferreteria y pinturerias\",\"nombreComercial\":\"FERRETERIA FUENTES\",\"tipoEstablecimiento\":\"02\",\"direccion\":{\"departamento\":\"03\",\"municipio\":\"20\",\"complemento\":\"Canton Punta Remedios, Caserio Los Cobanos,Acajutla\"},\"telefono\":\"73999642\",\"correo\":\"ferreteriafuentes019@gmail.com\",\"codEstable\":null,\"codPuntoVenta\":null,\"codEstableMH\":null,\"codPuntoVentaMH\":null},\"receptor\":{\"nrc\":null,\"tipoDocumento\":\"13\",\"numDocumento\":\"01031767-9\",\"nombre\":\"Claudia Guadalupe Calixto de Fuentes\",\"codActividad\":null,\"descActividad\":null,\"direccion\":{\"departamento\":\"03\",\"municipio\":\"18\",\"complemento\":\"1 Calle Ote. Col. Santa Marta Final Pasaje Marin 1 Casa 11\"},\"telefono\":\"6123-8974\",\"correo\":\"alex.calix1992@gmail.com\"},\"otrosDocumentos\":null,\"ventaTercero\":null,\"cuerpoDocumento\":[{\"numItem\":1,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":1,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Cemento WP 42.5 KG\",\"precioUni\":8.2,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":8.2,\"ivaItem\":0.94,\"tributos\":null,\"psv\":8.2,\"noGravado\":0},{\"numItem\":2,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":2,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Destornillador phillips no. 1 x 3 pulg\",\"precioUni\":1.8,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":3.6,\"ivaItem\":0.41,\"tributos\":null,\"psv\":3.6,\"noGravado\":0}],\"resumen\":{\"totalNoSuj\":0,\"totalExenta\":0,\"totalGravada\":11.8,\"subTotalVentas\":11.8,\"descuNoSuj\":0,\"descuExenta\":0,\"descuGravada\":0,\"totalDescu\":0,\"porcentajeDescuento\":0,\"tributos\":null,\"subTotal\":11.8,\"totalIva\":1.35,\"ivaRete1\":0,\"reteRenta\":0,\"montoTotalOperacion\":11.8,\"totalNoGravado\":0,\"totalPagar\":11.8,\"totalLetras\":\"ONCE DOLARES CON OCHENTA CENTAVOS\",\"saldoFavor\":0,\"condicionOperacion\":1,\"pagos\":null,\"numPagoElectronico\":null},\"extension\":null,\"apendice\":null}'),
(27, 29, 2, '00', 2, 'PROCESADO', '840B8EA9-E6FF-621D-FBD4-602E0CCBCF3E', '202507A41DDE92F148748EFCF5E30BEAA726CJHD', '2025-05-05 12:59:50', '10', '001', 'RECIBIDO', '[]', '2025-05-05 18:59:49', '{\"identificacion\":{\"version\":1,\"ambiente\":\"00\",\"tipoDte\":\"01\",\"numeroControl\":\"DTE-01-M001P001-000000000000029\",\"codigoGeneracion\":\"840B8EA9-E6FF-621D-FBD4-602E0CCBCF3E\",\"tipoModelo\":1,\"tipoOperacion\":1,\"tipoContingencia\":null,\"motivoContin\":null,\"fecEmi\":\"2025-05-05\",\"horEmi\":\"12:59:47\",\"tipoMoneda\":\"USD\"},\"documentoRelacionado\":null,\"emisor\":{\"nit\":\"03011504761021\",\"nrc\":\"3173130\",\"nombre\":\"Oscar Eduardo Fuentes Mancia\",\"codActividad\":\"46632\",\"descActividad\":\"venta al por mayor de articulos de ferreteria y pinturerias\",\"nombreComercial\":\"FERRETERIA FUENTES\",\"tipoEstablecimiento\":\"02\",\"direccion\":{\"departamento\":\"03\",\"municipio\":\"20\",\"complemento\":\"Canton Punta Remedios, Caserio Los Cobanos,Acajutla\"},\"telefono\":\"73999642\",\"correo\":\"ferreteriafuentes019@gmail.com\",\"codEstable\":null,\"codPuntoVenta\":null,\"codEstableMH\":null,\"codPuntoVentaMH\":null},\"receptor\":{\"nrc\":null,\"tipoDocumento\":\"13\",\"numDocumento\":\"01031767-9\",\"nombre\":\"Claudia Guadalupe Calixto de Fuentes\",\"codActividad\":null,\"descActividad\":null,\"direccion\":{\"departamento\":\"03\",\"municipio\":\"18\",\"complemento\":\"1 Calle Ote. Col. Santa Marta Final Pasaje Marin 1 Casa 11\"},\"telefono\":\"6123-8974\",\"correo\":\"alex.calix1992@gmail.com\"},\"otrosDocumentos\":null,\"ventaTercero\":null,\"cuerpoDocumento\":[{\"numItem\":1,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":2,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Destornillador phillips no. 1 x 3 pulg\",\"precioUni\":1.8,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":3.6,\"ivaItem\":0.41,\"tributos\":null,\"psv\":3.6,\"noGravado\":0},{\"numItem\":2,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":1,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Cemento Fuerte Holcim 50 KG\",\"precioUni\":9.75,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":9.75,\"ivaItem\":1.12,\"tributos\":null,\"psv\":9.75,\"noGravado\":0},{\"numItem\":3,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":2,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Espoja para Cemento 35cm X 35cm\",\"precioUni\":1.1,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":2.2,\"ivaItem\":0.25,\"tributos\":null,\"psv\":2.2,\"noGravado\":0},{\"numItem\":4,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":1,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Cemento WP 42.5 KG\",\"precioUni\":8.2,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":8.2,\"ivaItem\":0.94,\"tributos\":null,\"psv\":8.2,\"noGravado\":0}],\"resumen\":{\"totalNoSuj\":0,\"totalExenta\":0,\"totalGravada\":23.75,\"subTotalVentas\":23.75,\"descuNoSuj\":0,\"descuExenta\":0,\"descuGravada\":0,\"totalDescu\":0,\"porcentajeDescuento\":0,\"tributos\":null,\"subTotal\":23.75,\"totalIva\":2.72,\"ivaRete1\":0,\"reteRenta\":0,\"montoTotalOperacion\":23.75,\"totalNoGravado\":0,\"totalPagar\":23.75,\"totalLetras\":\"VEINTITR\\u00c9S DOLARES CON SETENTA Y CINCO CENTAVOS\",\"saldoFavor\":0,\"condicionOperacion\":1,\"pagos\":null,\"numPagoElectronico\":null},\"extension\":null,\"apendice\":null}');
INSERT INTO `respuestadte` (`id`, `id_factura`, `version`, `ambiente`, `versionApp`, `estado`, `codigoGeneracion`, `selloRecibido`, `fhProcesamiento`, `clasificaMsg`, `codigoMsg`, `descripcionMsg`, `observaciones`, `fecha_guardado`, `jsondte`) VALUES
(28, 30, 2, '00', 2, 'PROCESADO', 'F17579A7-CC71-617A-4899-B8AFF205C0DE', '2025E4EBC4488F654F91BBB2CBA860696238UDUI', '2025-05-05 13:34:18', '10', '001', 'RECIBIDO', '[]', '2025-05-05 19:34:18', '{\"identificacion\":{\"version\":1,\"ambiente\":\"00\",\"tipoDte\":\"01\",\"numeroControl\":\"DTE-01-M001P001-000000000000030\",\"codigoGeneracion\":\"F17579A7-CC71-617A-4899-B8AFF205C0DE\",\"tipoModelo\":1,\"tipoOperacion\":1,\"tipoContingencia\":null,\"motivoContin\":null,\"fecEmi\":\"2025-05-05\",\"horEmi\":\"13:34:15\",\"tipoMoneda\":\"USD\"},\"documentoRelacionado\":null,\"emisor\":{\"nit\":\"03011504761021\",\"nrc\":\"3173130\",\"nombre\":\"Oscar Eduardo Fuentes Mancia\",\"codActividad\":\"46632\",\"descActividad\":\"venta al por mayor de articulos de ferreteria y pinturerias\",\"nombreComercial\":\"FERRETERIA FUENTES\",\"tipoEstablecimiento\":\"02\",\"direccion\":{\"departamento\":\"03\",\"municipio\":\"20\",\"complemento\":\"Canton Punta Remedios, Caserio Los Cobanos,Acajutla\"},\"telefono\":\"73999642\",\"correo\":\"ferreteriafuentes019@gmail.com\",\"codEstable\":null,\"codPuntoVenta\":null,\"codEstableMH\":null,\"codPuntoVentaMH\":null},\"receptor\":{\"nrc\":null,\"tipoDocumento\":\"13\",\"numDocumento\":\"01031767-9\",\"nombre\":\"Claudia Guadalupe Calixto de Fuentes\",\"codActividad\":null,\"descActividad\":null,\"direccion\":{\"departamento\":\"03\",\"municipio\":\"18\",\"complemento\":\"1 Calle Ote. Col. Santa Marta Final Pasaje Marin 1 Casa 11\"},\"telefono\":\"6123-8974\",\"correo\":\"alex.calix1992@gmail.com\"},\"otrosDocumentos\":null,\"ventaTercero\":null,\"cuerpoDocumento\":[{\"numItem\":1,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":1,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Espoja para Cemento 35cm X 35cm\",\"precioUni\":1.1,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":1.1,\"ivaItem\":0.13,\"tributos\":null,\"psv\":1.1,\"noGravado\":0}],\"resumen\":{\"totalNoSuj\":0,\"totalExenta\":0,\"totalGravada\":1.1,\"subTotalVentas\":1.1,\"descuNoSuj\":0,\"descuExenta\":0,\"descuGravada\":0,\"totalDescu\":0,\"porcentajeDescuento\":0,\"tributos\":null,\"subTotal\":1.1,\"totalIva\":0.13,\"ivaRete1\":0,\"reteRenta\":0,\"montoTotalOperacion\":1.1,\"totalNoGravado\":0,\"totalPagar\":1.1,\"totalLetras\":\"UNO DOLARES CON DIEZ CENTAVOS\",\"saldoFavor\":0,\"condicionOperacion\":1,\"pagos\":null,\"numPagoElectronico\":null},\"extension\":null,\"apendice\":null}'),
(29, 31, 2, '00', 2, 'PROCESADO', 'C2833FFA-47E5-9755-D98D-0E422B91D6E3', '2025F4C81B046789441AA35D38FC56D63BE6MLWZ', '2025-05-05 13:36:38', '10', '001', 'RECIBIDO', '[]', '2025-05-05 19:36:37', '{\"identificacion\":{\"version\":1,\"ambiente\":\"00\",\"tipoDte\":\"01\",\"numeroControl\":\"DTE-01-M001P001-000000000000031\",\"codigoGeneracion\":\"C2833FFA-47E5-9755-D98D-0E422B91D6E3\",\"tipoModelo\":1,\"tipoOperacion\":1,\"tipoContingencia\":null,\"motivoContin\":null,\"fecEmi\":\"2025-05-05\",\"horEmi\":\"13:36:35\",\"tipoMoneda\":\"USD\"},\"documentoRelacionado\":null,\"emisor\":{\"nit\":\"03011504761021\",\"nrc\":\"3173130\",\"nombre\":\"Oscar Eduardo Fuentes Mancia\",\"codActividad\":\"46632\",\"descActividad\":\"venta al por mayor de articulos de ferreteria y pinturerias\",\"nombreComercial\":\"FERRETERIA FUENTES\",\"tipoEstablecimiento\":\"02\",\"direccion\":{\"departamento\":\"03\",\"municipio\":\"20\",\"complemento\":\"Canton Punta Remedios, Caserio Los Cobanos,Acajutla\"},\"telefono\":\"73999642\",\"correo\":\"ferreteriafuentes019@gmail.com\",\"codEstable\":null,\"codPuntoVenta\":null,\"codEstableMH\":null,\"codPuntoVentaMH\":null},\"receptor\":{\"nrc\":null,\"tipoDocumento\":\"13\",\"numDocumento\":\"01031767-9\",\"nombre\":\"Claudia Guadalupe Calixto de Fuentes\",\"codActividad\":null,\"descActividad\":null,\"direccion\":{\"departamento\":\"03\",\"municipio\":\"18\",\"complemento\":\"1 Calle Ote. Col. Santa Marta Final Pasaje Marin 1 Casa 11\"},\"telefono\":\"6123-8974\",\"correo\":\"alex.calix1992@gmail.com\"},\"otrosDocumentos\":null,\"ventaTercero\":null,\"cuerpoDocumento\":[{\"numItem\":1,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":1,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Cemento WP 42.5 KG\",\"precioUni\":8.2,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":8.2,\"ivaItem\":0.94,\"tributos\":null,\"psv\":8.2,\"noGravado\":0},{\"numItem\":2,\"numeroDocumento\":null,\"tipoItem\":1,\"cantidad\":1,\"codigo\":\"02\",\"uniMedida\":59,\"descripcion\":\"Destornillador phillips no. 1 x 3 pulg\",\"precioUni\":1.8,\"montoDescu\":0,\"codTributo\":null,\"ventaNoSuj\":0,\"ventaExenta\":0,\"ventaGravada\":1.8,\"ivaItem\":0.21,\"tributos\":null,\"psv\":1.8,\"noGravado\":0}],\"resumen\":{\"totalNoSuj\":0,\"totalExenta\":0,\"totalGravada\":10,\"subTotalVentas\":10,\"descuNoSuj\":0,\"descuExenta\":0,\"descuGravada\":0,\"totalDescu\":0,\"porcentajeDescuento\":0,\"tributos\":null,\"subTotal\":10,\"totalIva\":1.15,\"ivaRete1\":0,\"reteRenta\":0,\"montoTotalOperacion\":10,\"totalNoGravado\":0,\"totalPagar\":10,\"totalLetras\":\"DIEZ DOLARES\",\"saldoFavor\":0,\"condicionOperacion\":1,\"pagos\":null,\"numPagoElectronico\":null},\"extension\":null,\"apendice\":null}');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `idrol` int(11) NOT NULL,
  `rol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`idrol`, `rol`) VALUES
(1, 'Administrador'),
(2, 'Vendedor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles_menu`
--

CREATE TABLE `roles_menu` (
  `idRolMenu` int(11) NOT NULL,
  `idMenu` int(11) NOT NULL,
  `idRol` int(11) NOT NULL,
  `estado` enum('activo','inactivo') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles_menu`
--

INSERT INTO `roles_menu` (`idRolMenu`, `idMenu`, `idRol`, `estado`) VALUES
(1, 1, 1, 'activo'),
(2, 2, 1, 'activo'),
(3, 3, 1, 'activo'),
(4, 4, 1, 'activo'),
(5, 5, 1, 'activo'),
(6, 6, 1, 'activo'),
(7, 7, 1, 'activo'),
(8, 8, 1, 'activo'),
(9, 9, 1, 'activo'),
(10, 10, 1, 'activo'),
(11, 11, 1, 'activo'),
(12, 12, 1, 'activo'),
(13, 13, 1, 'activo'),
(14, 14, 1, 'inactivo'),
(15, 15, 1, 'inactivo'),
(16, 16, 1, 'activo'),
(17, 17, 1, 'activo'),
(18, 18, 1, 'activo'),
(19, 19, 1, 'activo'),
(20, 20, 1, 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idusuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `usuario` varchar(20) NOT NULL,
  `clave` varchar(50) NOT NULL,
  `rol` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idusuario`, `nombre`, `correo`, `usuario`, `clave`, `rol`) VALUES
(1, 'Gerson Alexander Escobar', 'systec.atencion@gmail.com', 'admin', '827ccb0eea8a706c4c34a16891f84e7b', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `apertura_caja`
--
ALTER TABLE `apertura_caja`
  ADD PRIMARY KEY (`idcaja`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`categoria_id`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`idcliente`);

--
-- Indices de la tabla `cliente_direccion`
--
ALTER TABLE `cliente_direccion`
  ADD PRIMARY KEY (`idClienteD`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `consecutivos`
--
ALTER TABLE `consecutivos`
  ADD PRIMARY KEY (`idconsecutivos`);

--
-- Indices de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  ADD PRIMARY KEY (`iddepartamento`);

--
-- Indices de la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `kardex_producto`
--
ALTER TABLE `kardex_producto`
  ADD PRIMARY KEY (`idkardex`);

--
-- Indices de la tabla `medio_pago`
--
ALTER TABLE `medio_pago`
  ADD PRIMARY KEY (`idmedio`);

--
-- Indices de la tabla `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`idMenu`);

--
-- Indices de la tabla `movimientos_de_caja`
--
ALTER TABLE `movimientos_de_caja`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `municipios`
--
ALTER TABLE `municipios`
  ADD PRIMARY KEY (`idmunicipios`);

--
-- Indices de la tabla `presentacion`
--
ALTER TABLE `presentacion`
  ADD PRIMARY KEY (`idpresentacion`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`codproducto`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`idproveedor`);

--
-- Indices de la tabla `respuestadte`
--
ALTER TABLE `respuestadte`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`idrol`);

--
-- Indices de la tabla `roles_menu`
--
ALTER TABLE `roles_menu`
  ADD PRIMARY KEY (`idRolMenu`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idusuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `apertura_caja`
--
ALTER TABLE `apertura_caja`
  MODIFY `idcaja` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `categoria_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `idcliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `cliente_direccion`
--
ALTER TABLE `cliente_direccion`
  MODIFY `idClienteD` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `consecutivos`
--
ALTER TABLE `consecutivos`
  MODIFY `idconsecutivos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  MODIFY `iddepartamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT de la tabla `factura`
--
ALTER TABLE `factura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `kardex_producto`
--
ALTER TABLE `kardex_producto`
  MODIFY `idkardex` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT de la tabla `medio_pago`
--
ALTER TABLE `medio_pago`
  MODIFY `idmedio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `menu`
--
ALTER TABLE `menu`
  MODIFY `idMenu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `movimientos_de_caja`
--
ALTER TABLE `movimientos_de_caja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `municipios`
--
ALTER TABLE `municipios`
  MODIFY `idmunicipios` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `codproducto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `idproveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `respuestadte`
--
ALTER TABLE `respuestadte`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `idrol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `roles_menu`
--
ALTER TABLE `roles_menu`
  MODIFY `idRolMenu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idusuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
