-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-10-2024 a las 20:11:14
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
  `motivo` varchar(250) NOT NULL,
  `aplazado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `atendidos`
--

INSERT INTO `atendidos` (`atendido_id`, `pacientes_id`, `medico_id`, `fecha_atencion`, `nombre`, `apellido`, `dni`, `motivo`, `aplazado`) VALUES
(43, 90, 2, '2024-10-22 13:27:25', 'Gilberto', 'Locaso', '35235235', '', 0),
(45, 89, 2, '2024-10-23 15:02:58', 'Franco', 'Locaso Locaso Gonzales', '1412525252', '', 1);

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
(51, 89, 2, '2024-09-26', 'XD'),
(52, 89, 2, '2024-10-15', 'gsdgsdgsdg'),
(54, 90, 2, '2024-10-18', 'el lol le lo hizo engordar 60 kilos en una semana'),
(55, 95, 8, '2024-10-22', 'xDD');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medicos`
--

CREATE TABLE `medicos` (
  `medico_id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('medico','secretario') DEFAULT 'medico'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `medicos`
--

INSERT INTO `medicos` (`medico_id`, `nombre`, `email`, `password`, `rol`) VALUES
(1, 'Franco', 'franco@gmail.com', '$2y$10$Z.xcgFsPrn3kkrs8aXrEq.JGhjhcAGP4Y3aboJKVeCX2pv.Z4IhIi', 'secretario'),
(2, 'Gilberto', 'ElGilberXD@gmail.com', '$2y$10$YT9v3GLPThDdBqPKZdeCK.dLS.uNSJIpa8nw4saYjmxwvIaraWGq6', 'medico'),
(4, 'Grande Fausto', 'grande@gmail.com', '$2y$10$.qvYSI/WH1QzZpNsr8r6o.qrtcwLdpAmSin52qsn/AFwuM6n8ni1C', 'medico'),
(5, 'Hola', 'Manola@gmail.com', '$2y$10$I9xWpR1ybFMtfvDzGwrnOutYOPUp1FhKcHXTk9KlTdpjyCcDafWfi', 'medico'),
(8, 'Maria', 'kaka@gmail.com', '$2y$10$V0.uM8/ScXB1u.K1CMFef.jR/lmaJyNyE1EUTLhdiAY0dG0fsM2P.', 'medico');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes`
--

CREATE TABLE `pacientes` (
  `paciente_id` int(11) NOT NULL,
  `medico_id` int(11) NOT NULL,
  `historial_id` int(11) NOT NULL,
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

INSERT INTO `pacientes` (`paciente_id`, `medico_id`, `historial_id`, `nombre`, `apellido`, `edad`, `dni`, `mutual`, `email`, `telefono`) VALUES
(89, 2, 0, 'Franco', 'Locaso Locaso Gonzales', 23, '1412525252', 'IOMA', 'ElGilberXD@gmail.com', '1523'),
(90, 2, 0, 'Gilberto', 'Locaso', 27, '35235235', 'OSAP', 'franco@gmail.com', '148678686786786'),
(94, 2, 0, 'Gilberto', 'Locaso', 23, '35235235', 'IOMA', 'dasdas@gmail.com', '1421412412'),
(95, 8, 0, 'Maria', 'Moffa', 52, '252352', 'IOMA', 'fersha@gmail.com', '42523525235');

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
(55, 91, 0, 2, '4232', '42342', 42342, '423423', '234234', '42342@gmail.omc', '324'),
(56, 92, 0, 2, 'Gilberto', 'afs', 23, '414124', '123424', '423422342@523523', '41212142');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes_eliminados_historial`
--

CREATE TABLE `pacientes_eliminados_historial` (
  `historial_id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `medico_id` int(11) NOT NULL,
  `detalle` text NOT NULL,
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
(37, 85, 0, '2024-10-02', '08:10:00'),
(38, 85, 0, '2025-10-02', '08:00:00'),
(40, 87, 0, '2024-02-20', '08:10:00'),
(84, 89, 0, '2024-10-28', '10:00:00'),
(85, 90, 0, '2024-10-30', '10:01:00');

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
  ADD PRIMARY KEY (`medico_id`),
  ADD UNIQUE KEY `email` (`email`);

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
  MODIFY `atendido_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `historial`
--
ALTER TABLE `historial`
  MODIFY `historial_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT de la tabla `medicos`
--
ALTER TABLE `medicos`
  MODIFY `medico_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `paciente_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT de la tabla `pacientes_eliminados`
--
ALTER TABLE `pacientes_eliminados`
  MODIFY `paciente_eliminado_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT de la tabla `pacientes_eliminados_historial`
--
ALTER TABLE `pacientes_eliminados_historial`
  MODIFY `historial_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de la tabla `turnos`
--
ALTER TABLE `turnos`
  MODIFY `turno_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
