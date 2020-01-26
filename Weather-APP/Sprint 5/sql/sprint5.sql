-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-01-2020 a las 16:45:52
-- Versión del servidor: 10.4.8-MariaDB
-- Versión de PHP: 7.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sprint5`
--
CREATE DATABASE sprint5;
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `city`
--

CREATE TABLE `city` (
  `id_city` int(11) NOT NULL,
  `name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `city`
--

INSERT INTO `city` (`id_city`, `name`) VALUES
(2643743, 'Londres'),
(2950159, 'Berlín'),
(3169070, 'Roma'),
(5128638, 'Nueva York'),
(6356055, 'Barcelona'),
(6359304, 'Madrid'),
(6455259, 'París');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cityuser`
--

CREATE TABLE `cityuser` (
  `id_cityUser` int(11) NOT NULL,
  `fk_user` varchar(42) NOT NULL,
  `fk_city` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `cityuser`
--

INSERT INTO `cityuser` (`id_cityUser`, `fk_user`, `fk_city`) VALUES
(1, 'rodri@gmail.com', 2950159),
(2, 'rodri@gmail.com', 6356055),
(3, 'rodri@gmail.com', 3169070);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `email` varchar(42) NOT NULL,
  `password` varchar(128) NOT NULL,
  `type` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`email`, `password`, `type`) VALUES
('rodri@gmail.com', '$2y$10$xgRNxlp0pCPV9lReXYTfRuo0XeQJ17ggpuc100ll0hO9Z2yh3cCZe', 'u');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`id_city`);

--
-- Indices de la tabla `cityuser`
--
ALTER TABLE `cityuser`
  ADD PRIMARY KEY (`id_cityUser`),
  ADD KEY `fk_user` (`fk_user`),
  ADD KEY `fk_city` (`fk_city`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cityuser`
--
ALTER TABLE `cityuser`
  MODIFY `id_cityUser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cityuser`
--
ALTER TABLE `cityuser`
  ADD CONSTRAINT `cityuser_ibfk_1` FOREIGN KEY (`fk_user`) REFERENCES `users` (`email`),
  ADD CONSTRAINT `cityuser_ibfk_2` FOREIGN KEY (`fk_city`) REFERENCES `city` (`id_city`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
