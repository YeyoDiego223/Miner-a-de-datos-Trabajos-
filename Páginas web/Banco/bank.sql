-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-06-2024 a las 23:18:55
-- Versión del servidor: 10.1.29-MariaDB
-- Versión de PHP: 7.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bank`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `google_auth_secret` varchar(255) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `sexo` enum('masculino','femenino') NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `edad` int(11) NOT NULL,
  `efectivo` decimal(10,2) DEFAULT '0.00',
  `numero_cuenta` varchar(20) NOT NULL,
  `nip` varchar(4) NOT NULL,
  `credito` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`, `google_auth_secret`, `nombre`, `apellidos`, `sexo`, `fecha_nacimiento`, `edad`, `efectivo`, `numero_cuenta`, `nip`, `credito`) VALUES
(1, 'gabriel@gmail.com', '$2y$10$YCK1B.hMwmGO1J20wBejyehi6Y6r5qPJvccYTR7PR3/UD3LmcW9.e', 'ZBYRISMNVMPUVGHB', 'Gabriel', 'Escamilla', 'masculino', '2002-07-06', 21, '1000.00', 'CU0000000001', '0997', NULL),
(3, 'itzael@gmail.com', '$2y$10$o2w6Y094jyIPhJdrePSY2.TphVsB10JlTYz196OlUZ/KtnouD7a5W', 'GDL4PFGKKOKFEV5O', 'itzael', 'Escamilla', 'femenino', '2000-02-12', 24, '0.00', 'CU0000000000', '3081', NULL),
(5, 'mark@gmail.com', '$2y$10$TniKtLvUVSVLEzFa2tUr.OHho9z8DpdgRNjJm274NGTOPHXQ.VnK2', 'CT46CWBPDX5IEYPI', 'Mark', 'Grayson', 'masculino', '1999-02-12', 25, '2418.40', '0000000000', '3947', '{\"monto\":718.4027777777778,\"prestamista\":\"Banco A\",\"intereses\":\"5%\",\"plazos\":\"6 meses\",\"pago\":\"semanal\"}'),
(7, 'nao@gmail.com', '$2y$10$nVqsNsUy4E3QlHGxGhPKrOw/4tsxnRVFPeMjvIcwfwORhL95X3WN.', '6KQPOZID5PLTI64E', 'Naochi', 'patata', 'femenino', '2000-11-12', 23, '1100.00', '618528607756', '8937', NULL),
(9, 'itzael1@gmail.com', '$2y$10$JNnwU9Fl0eqHCIYHQOrC7u77Q9lO8CtqRhYFxSbhiOSxlljLdGnt2', 'ESDV7WMAZ7JBHF7V', 'itzael', 'escamilla', 'masculino', '1212-12-12', 811, '100.00', '751870744621', '2775', NULL),
(10, 'klark@gmail.com', '$2y$10$9FQ6GIG0Wr8dOiPokgCKQuLeb2FsPFIALSAmS52m8ltILRl1E3VyG', 'MVDUHUKRISWMFOJ5', 'clark', 'kent', 'masculino', '2000-12-12', 23, '0.00', '973335542346', '9928', NULL),
(12, 'roberto@gmail.com', '$2y$10$40ECjamhO52SOlI5TRXyqupFWXUxczZUuaHykZXMK44pAzer/hMXi', '5PIALPEUXFCWAJJB', 'roberto', 'mijares', 'masculino', '2000-12-12', 23, '4800.00', '856616113739', '7972', '{\"monto\":4000,\"prestamista\":\"Banco B\",\"intereses\":\"4%\",\"plazos\":\"12 meses\",\"pago\":\"mensual\"}');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `numero_cuenta` (`numero_cuenta`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
