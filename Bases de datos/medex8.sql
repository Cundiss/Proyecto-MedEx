-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-09-2024 a las 22:11:52
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `medex`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `atendidos`
--

CREATE TABLE `atendidos` (
  `atendido_id` int(11) NOT NULL,
  `pacientes_id` int(11) NOT NULL,
  `medico_id` int(11) NOT NULL,
  `fecha_atencion` datetime NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `dni` varchar(10) NOT NULL,
  `motivo` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `atendidos`
--

INSERT INTO `atendidos` (`atendido_id`, `pacientes_id`, `medico_id`, `fecha_atencion`, `nombre`, `apellido`, `dni`, `motivo`) VALUES
(4, 54, 17, '2024-09-17 23:13:55', 'Pepinillo', 'Monasterolo', '4545454545', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial`
--

CREATE TABLE `historial` (
  `historial_id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `medico_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `detalle` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial`
--

INSERT INTO `historial` (`historial_id`, `paciente_id`, `medico_id`, `fecha`, `detalle`) VALUES
(3, 33, 0, '2024-09-09', 'Se meó'),
(5, 33, 0, '2024-08-08', 'SE CAGÓ'),
(6, 33, 0, '2024-08-08', 'HOLAA'),
(8, 33, 14, '2024-02-02', 'thfhfhfg'),
(9, 33, 14, '2024-08-20', '42423423'),
(12, 34, 14, '2000-02-02', 'ANDA JOYAAA'),
(13, 40, 14, '2024-09-17', '4234'),
(15, 46, 16, '2024-09-18', 'fas'),
(16, 42, 14, '2024-09-18', 'fasf'),
(17, 56, 14, '2024-09-18', 'LOOOOL'),
(18, 58, 14, '2024-09-18', 'zzz'),
(19, 57, 14, '2024-09-18', 'sgsdghdddddhfdhfghfghfgh'),
(21, 60, 14, '2024-09-18', 'gsdg'),
(22, 61, 14, '2024-09-18', 'afsf'),
(23, 61, 14, '2024-09-18', 'gdgdfg'),
(24, 62, 14, '2024-09-18', 'ffafasf'),
(25, 57, 14, '2024-09-18', 'jfjfgj'),
(27, 57, 14, '2024-09-18', 'dhdfh'),
(30, 63, 14, '2024-09-18', 'fasf');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medicos`
--

CREATE TABLE `medicos` (
  `medico_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `medicos`
--

INSERT INTO `medicos` (`medico_id`, `nombre`, `email`, `password`) VALUES
(14, 'Gilberto', 'ElGilberXD@gmail.com', '$2y$10$fNzBbCsk9s6gGqbosd4vuuCiTagjSBEn7o78hcUnLc88USlZcqZLS'),
(15, 'Santiago Forno', 'fornosantiago@gmail.com', '$2y$10$vH5G7VzCB6VgKnS06SN8NOxvEONBjjKvxCpNox4RphAEH3M5i8iBy'),
(16, 'Maria', 'kaka@gmail.com', '$2y$10$8u5O/sfZuIoqQ1KaTsmqp.uxyoC0Uo7LvBR87dVwDlBKGBeKNvKgu'),
(17, 'Maria', 'cundoloco186@gmail.com', '$2y$10$KU0bk1Dht0YjTQ.5lO9Kle7Cu3tY28f/Dcb0poFGa4U75yVMxTsBm'),
(18, 'Maria', 'facucarignani@gmail.com', '$2y$10$j3Jwe27TVg5XtKZ3LeJqJOu2E8O7bxjNUlbsZahEo7nMd9Jis2W22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes`
--

CREATE TABLE `pacientes` (
  `paciente_id` int(11) NOT NULL,
  `medico_id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `edad` int(3) NOT NULL,
  `dni` varchar(10) NOT NULL,
  `mutual` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pacientes`
--

INSERT INTO `pacientes` (`paciente_id`, `medico_id`, `nombre`, `apellido`, `edad`, `dni`, `mutual`, `email`, `telefono`) VALUES
(33, 0, 'Facundo', 'Carignani', 22, '14124214', 'IO', 'facucari@gmail.com', '1564145965'),
(34, 0, 'tomas', 'angelico', 24, '55006600', 'OSDE', 'ludi@gmail.com', '3364009977'),
(35, 0, 'manuel', 'costoya', 22, '40404040', 'IOMA', 'manu@gmail.com', '3364009988'),
(38, 0, 'santiago', 'forno', 19, '45987903', 'OSAP', 'fornosantiago@gmail.com', '3364607112'),
(39, 0, 'Maria', 'Moffa', 40, '35235235', 'IOMA', 'dasdas@gmail.com', '1421412412'),
(46, 16, 'Maria', 'Locaso', 24, '14124214', 'IOMA', 'kaka@gmail.com', '214'),
(54, 17, 'Pepinillo', 'Monasterolo', 50, '4545454545', 'OSAP', 'Pepinillo@gmail.com', '3364654984596'),
(55, 17, 'Franc0', 'Rossi', 20, '459871529', 'PAMI', 'fannco.12@gmail.com', '14214124124'),
(63, 14, 'Facundo', 'carignani', 23, '14124214', 'IOMA', 'ElGilberXD@gmail.com', '14212');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes_eliminados`
--

CREATE TABLE `pacientes_eliminados` (
  `paciente_eliminado_id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `historial_id` int(11) NOT NULL,
  `medico_id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `edad` int(3) NOT NULL,
  `dni` varchar(10) NOT NULL,
  `mutual` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pacientes_eliminados`
--

INSERT INTO `pacientes_eliminados` (`paciente_eliminado_id`, `paciente_id`, `historial_id`, `medico_id`, `nombre`, `apellido`, `edad`, `dni`, `mutual`, `email`, `telefono`) VALUES
(18, 49, 0, 14, 'Maria', 'Locaso', 23, '14124214', 'IOMA', 'cundoloco186@gmail.com', '1421412412'),
(19, 51, 0, 16, 'facundo', 'carignani', 23, '14124214', 'IOM', 'ElGilberXD@gmail.com', '14214124124');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes_eliminados_historial`
--

CREATE TABLE `pacientes_eliminados_historial` (
  `historial_id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `medico_id` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turnos`
--

CREATE TABLE `turnos` (
  `turno_id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `medico_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `horario` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `turnos`
--

INSERT INTO `turnos` (`turno_id`, `paciente_id`, `medico_id`, `fecha`, `horario`) VALUES
(15, 16, 0, '2024-09-22', '16:44:00'),
(16, 17, 0, '2024-09-20', '15:22:00'),
(17, 32, 0, '2024-09-20', '14:22:00'),
(18, 16, 0, '2024-10-05', '16:54:00'),
(24, 33, 0, '2024-09-08', '08:30:00'),
(26, 40, 0, '2024-09-23', '15:12:00'),
(29, 42, 0, '2024-09-02', '10:01:00'),
(30, 50, 0, '2024-09-02', '10:15:00'),
(31, 55, 0, '2024-09-10', '10:00:00'),
(33, 62, 0, '2024-09-23', '08:01:00'),
(34, 57, 0, '2024-09-03', '10:00:00');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `atendidos`
--
ALTER TABLE `atendidos`
  ADD PRIMARY KEY (`atendido_id`);

--
-- Indices de la tabla `historial`
--
ALTER TABLE `historial`
  ADD PRIMARY KEY (`historial_id`);

--
-- Indices de la tabla `medicos`
--
ALTER TABLE `medicos`
  ADD PRIMARY KEY (`medico_id`);

--
-- Indices de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  ADD PRIMARY KEY (`paciente_id`);

--
-- Indices de la tabla `pacientes_eliminados`
--
ALTER TABLE `pacientes_eliminados`
  ADD PRIMARY KEY (`paciente_eliminado_id`);

--
-- Indices de la tabla `pacientes_eliminados_historial`
--
ALTER TABLE `pacientes_eliminados_historial`
  ADD PRIMARY KEY (`historial_id`);

--
-- Indices de la tabla `turnos`
--
ALTER TABLE `turnos`
  ADD PRIMARY KEY (`turno_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `atendidos`
--
ALTER TABLE `atendidos`
  MODIFY `atendido_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `historial`
--
ALTER TABLE `historial`
  MODIFY `historial_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `medicos`
--
ALTER TABLE `medicos`
  MODIFY `medico_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `paciente_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT de la tabla `pacientes_eliminados`
--
ALTER TABLE `pacientes_eliminados`
  MODIFY `paciente_eliminado_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `pacientes_eliminados_historial`
--
ALTER TABLE `pacientes_eliminados_historial`
  MODIFY `historial_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `turnos`
--
ALTER TABLE `turnos`
  MODIFY `turno_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
