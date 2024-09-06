-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-09-2024 a las 18:49:46
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
(1, 33, 14, '2024-09-04 16:48:00', 'facundo', 'carignani', '14124214', '');

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
(3, 33, 0, '2024-09-09', 'sf'),
(5, 33, 0, '2024-08-08', 'HOsdgdfhdfhshsHOsdgdfhdfhshsHgdfhdfhshsHOsdgdfhdfhshsHOsdgdfhdfhshsHOsdgd'),
(6, 33, 0, '2024-08-08', 'HOLAA'),
(8, 33, 14, '2024-02-02', 'thfhfhfg'),
(9, 33, 14, '2024-08-20', '42423423'),
(12, 34, 14, '2000-02-02', 'ANDA JOYAAA');

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
(15, 'Santiago Forno', 'fornosantiago@gmail.com', '$2y$10$vH5G7VzCB6VgKnS06SN8NOxvEONBjjKvxCpNox4RphAEH3M5i8iBy');

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
(33, 0, 'Facundo', 'Carignani', 22, '14124214', 'IO', 'facucari@gmail.com', '1421412412'),
(34, 0, 'tomas', 'angelico', 24, '55006600', 'OSDE', 'ludi@gmail.com', '3364009977'),
(35, 0, 'manuel', 'costoya', 22, '40404040', 'IOMA', 'manu@gmail.com', '3364009988'),
(38, 0, 'santiago', 'forno', 19, '45987903', 'OSAP', 'fornosantiago@gmail.com', '3364607112');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes_eliminados`
--

CREATE TABLE `pacientes_eliminados` (
  `paciente_eliminado_id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
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

INSERT INTO `pacientes_eliminados` (`paciente_eliminado_id`, `paciente_id`, `medico_id`, `nombre`, `apellido`, `edad`, `dni`, `mutual`, `email`, `telefono`) VALUES
(7, 32, 0, 'lionel', 'messi', 68, '34876543', 'INTER', 'messi@gmail.com', '336408067');

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
(23, 34, 0, '2024-09-04', '19:00:00');

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
  MODIFY `atendido_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `historial`
--
ALTER TABLE `historial`
  MODIFY `historial_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `medicos`
--
ALTER TABLE `medicos`
  MODIFY `medico_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `paciente_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `pacientes_eliminados`
--
ALTER TABLE `pacientes_eliminados`
  MODIFY `paciente_eliminado_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `turnos`
--
ALTER TABLE `turnos`
  MODIFY `turno_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
