-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-05-2026 a las 01:08:12
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
-- Base de datos: `biblioteca`
--
CREATE DATABASE IF NOT EXISTS `biblioteca` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `biblioteca`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `id` int(11) NOT NULL,
  `tabla_afectada` varchar(100) DEFAULT NULL,
  `accion` varchar(50) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `usuario_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auditoria`
--

INSERT INTO `auditoria` (`id`, `tabla_afectada`, `accion`, `descripcion`, `fecha`, `usuario_id`) VALUES
(1, 'prestamos', 'UPDATE', 'Estado cambiado de prestado a devuelto', '2026-05-12 17:58:17', NULL),
(2, 'prestamos', 'INSERT', 'Nuevo préstamo. Usuario ID: 1 Libro ID: 84', '2026-05-12 17:58:39', NULL),
(3, 'prestamos', 'INSERT', 'Nuevo préstamo. Usuario ID: 1 Libro ID: 86', '2026-05-12 17:59:05', NULL),
(4, 'prestamos', 'UPDATE', 'Estado cambiado de prestado a devuelto', '2026-05-12 17:59:16', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libros`
--

CREATE TABLE `libros` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `autor` varchar(255) NOT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `isbn` varchar(50) DEFAULT NULL,
  `editorial` varchar(150) DEFAULT NULL,
  `anio_publicacion` int(11) DEFAULT NULL,
  `stock` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `libros`
--

INSERT INTO `libros` (`id`, `titulo`, `autor`, `categoria`, `isbn`, `editorial`, `anio_publicacion`, `stock`) VALUES
(83, 'Cien años de soledad', 'Gabriel García Márquez', 'Novela', '9780307474728', 'Sudamericana', 1967, 5),
(84, 'Don Quijote de la Mancha', 'Miguel de Cervantes', 'Clásico', '9788491050297', 'Francisco de Robles', 1605, 5),
(85, '1984', 'George Orwell', 'Distopía', '9780451524935', 'Secker & Warburg', 1949, 5),
(86, 'Rebelión en la granja', 'George Orwell', 'Política', '9780451526342', 'Secker & Warburg', 1945, 4),
(87, 'El principito', 'Antoine de Saint-Exupéry', 'Fábula', '9780156013987', 'Reynal & Hitchcock', 1943, 5),
(88, 'Crimen y castigo', 'Fiódor Dostoyevski', 'Novela', '9780140449136', 'The Russian Messenger', 1866, 4),
(89, 'Orgullo y prejuicio', 'Jane Austen', 'Romance', '9780141439518', 'T. Egerton', 1813, 5),
(90, 'Matar a un ruiseñor', 'Harper Lee', 'Drama', '9780061120084', 'J.B. Lippincott & Co.', 1960, 5),
(91, 'El gran Gatsby', 'F. Scott Fitzgerald', 'Novela', '9780743273565', 'Scribner', 1925, 5),
(92, 'Ulises', 'James Joyce', 'Modernismo', '9780199535675', 'Shakespeare and Company', 1922, 5),
(93, 'La Odisea', 'Homero', 'Épico', '9780140268867', 'Grecia Antigua', -800, 5),
(94, 'La Ilíada', 'Homero', 'Épico', '9780140275360', 'Grecia Antigua', -750, 5),
(95, 'El señor de los anillos', 'J.R.R. Tolkien', 'Fantasía', '9780618640157', 'Allen & Unwin', 1954, 5),
(96, 'El hobbit', 'J.R.R. Tolkien', 'Fantasía', '9780547928227', 'Allen & Unwin', 1937, 5),
(97, 'Harry Potter y la piedra filosofal', 'J.K. Rowling', 'Fantasía', '9788478884452', 'Bloomsbury', 1997, 5),
(98, 'Los juegos del hambre', 'Suzanne Collins', 'Distopía', '9780439023481', 'Scholastic', 2008, 5),
(99, 'El código Da Vinci', 'Dan Brown', 'Misterio', '9780307474278', 'Doubleday', 2003, 5),
(100, 'La sombra del viento', 'Carlos Ruiz Zafón', 'Novela', '9788408172178', 'Planeta', 2001, 5),
(101, 'It', 'Stephen King', 'Terror', '9780450411434', 'Viking', 1986, 5),
(102, 'Drácula', 'Bram Stoker', 'Terror', '9780486411095', 'Archibald Constable', 1897, 5),
(103, 'Frankenstein', 'Mary Shelley', 'Terror', '9780486282114', 'Lackington', 1818, 5),
(104, 'El alquimista', 'Paulo Coelho', 'Ficción', '9780061122415', 'HarperOne', 1988, 5),
(105, 'Ensayo sobre la ceguera', 'José Saramago', 'Ficción', '9780156007757', 'Caminho', 1995, 5),
(106, 'El amor en los tiempos del cólera', 'Gabriel García Márquez', 'Romance', '9780307389732', 'Oveja Negra', 1985, 5),
(107, 'Rayuela', 'Julio Cortázar', 'Novela', '9788437604947', 'Sudamericana', 1963, 5),
(108, 'Pedro Páramo', 'Juan Rulfo', 'Novela', '9789681608009', 'FCE', 1955, 5),
(109, 'Fahrenheit 451', 'Ray Bradbury', 'Distopía', '9781451673319', 'Ballantine Books', 1953, 5),
(110, 'Un mundo feliz', 'Aldous Huxley', 'Distopía', '9780060850524', 'Chatto & Windus', 1932, 5),
(111, 'El retrato de Dorian Gray', 'Oscar Wilde', 'Filosofía', '9780141439570', 'Ward Lock', 1890, 5),
(112, 'Hamlet', 'William Shakespeare', 'Drama', '9780743477123', 'Shakespeare Press', 1603, 5),
(173, 'En llamas', 'Suzanne Collins', 'Ciencia ficción', '9780439023498', 'Scholastic', 2009, 5),
(174, 'Sinsajo', 'Suzanne Collins', 'Ciencia ficción', '9780439023511', 'Scholastic', 2010, 5),
(175, 'Ángeles y demonios', 'Dan Brown', 'Misterio', '9781416524793', 'Pocket Books', 2000, 5),
(176, 'Veronika decide morir', 'Paulo Coelho', 'Drama', '9780061124266', 'HarperOne', 1998, 5),
(177, 'El juego del ángel', 'Carlos Ruiz Zafón', 'Novela', '9788408081180', 'Planeta', 2008, 5),
(179, 'El resplandor', 'Stephen King', 'Terror', '9780307743657', 'Doubleday', 1977, 5),
(180, 'El psicoanalista', 'John Katzenbach', 'Thriller', '9788497937448', 'Ediciones B', 2002, 5),
(181, 'Los hombres que no amaban a las mujeres', 'Stieg Larsson', 'Misterio', '9780307454546', 'Norstedts', 2005, 5),
(182, 'Comer, rezar, amar', 'Elizabeth Gilbert', 'Biografía', '9780143038412', 'Penguin', 2006, 5),
(183, 'La chica del tren', 'Paula Hawkins', 'Thriller', '9781594634024', 'Riverhead Books', 2015, 5),
(184, 'El marciano', 'Andy Weir', 'Ciencia ficción', '9780553418026', 'Crown', 2011, 5),
(185, 'Ready Player One', 'Ernest Cline', 'Ciencia ficción', '9780307887443', 'Crown', 2011, 5),
(186, 'El nombre del viento', 'Patrick Rothfuss', 'Fantasía', '9788401352836', 'DAW Books', 2007, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pqrs`
--

CREATE TABLE `pqrs` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `tipo` enum('Petición','Queja','Reclamo','Sugerencia') NOT NULL,
  `asunto` varchar(150) NOT NULL,
  `mensaje` text NOT NULL,
  `estado` enum('Pendiente','En proceso','Resuelto') DEFAULT 'Pendiente',
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pqrs`
--

INSERT INTO `pqrs` (`id`, `nombre`, `correo`, `tipo`, `asunto`, `mensaje`, `estado`, `fecha`) VALUES
(1, 'Juan José', 'juanjose.sepu50@gmail.com', 'Petición', 'ssss', 'xxxx', 'Pendiente', '2026-04-20 23:07:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamos`
--

CREATE TABLE `prestamos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `libro_id` int(11) NOT NULL,
  `fecha_prestamo` date NOT NULL,
  `fecha_entrega` date NOT NULL,
  `estado` enum('pendiente','prestado','devuelto') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `prestamos`
--

INSERT INTO `prestamos` (`id`, `usuario_id`, `libro_id`, `fecha_prestamo`, `fecha_entrega`, `estado`) VALUES
(2, 1, 83, '2026-04-21', '2026-04-28', 'devuelto'),
(3, 1, 102, '2026-04-21', '2026-04-28', 'devuelto'),
(4, 1, 88, '2026-04-20', '2026-04-23', 'devuelto'),
(5, 1, 97, '2026-04-20', '2026-04-23', 'devuelto'),
(6, 1, 84, '2026-04-20', '2026-04-23', 'devuelto'),
(9, 1, 86, '2026-04-23', '2026-04-23', 'devuelto'),
(11, 1, 88, '2026-04-23', '2026-04-23', 'devuelto'),
(12, 1, 88, '2026-04-23', '2026-04-23', 'devuelto'),
(13, 1, 97, '2026-04-23', '2026-04-23', 'devuelto'),
(14, 1, 86, '2026-04-23', '2026-04-23', 'devuelto'),
(15, 1, 86, '2026-04-23', '2026-04-23', 'devuelto'),
(16, 1, 84, '2026-04-23', '2026-05-03', 'devuelto'),
(17, 1, 97, '2026-04-23', '2026-05-12', 'devuelto'),
(18, 1, 86, '2026-04-23', '2026-04-23', 'devuelto'),
(19, 1, 88, '2026-04-23', '2026-05-12', 'devuelto'),
(20, 1, 86, '2026-05-12', '2026-05-13', 'devuelto'),
(21, 1, 84, '2026-05-13', '2026-05-13', 'devuelto'),
(22, 1, 86, '2026-05-13', '2026-05-20', 'prestado');

--
-- Disparadores `prestamos`
--
DELIMITER $$
CREATE TRIGGER `trg_prestamo_delete` AFTER DELETE ON `prestamos` FOR EACH ROW BEGIN

    INSERT INTO auditoria(
        tabla_afectada,
        accion,
        descripcion
    )
    VALUES(
        'prestamos',
        'DELETE',
        CONCAT(
            'Se eliminó préstamo ID: ',
            OLD.id
        )
    );

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_prestamo_insert` AFTER INSERT ON `prestamos` FOR EACH ROW BEGIN

    INSERT INTO auditoria(
        tabla_afectada,
        accion,
        descripcion
    )
    VALUES(
        'prestamos',
        'INSERT',
        CONCAT(
            'Nuevo préstamo. Usuario ID: ',
            NEW.usuario_id,
            ' Libro ID: ',
            NEW.libro_id
        )
    );

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_prestamo_update` AFTER UPDATE ON `prestamos` FOR EACH ROW BEGIN

    IF NEW.estado <> OLD.estado THEN

        INSERT INTO auditoria(
            tabla_afectada,
            accion,
            descripcion
        )
        VALUES(
            'prestamos',
            'UPDATE',
            CONCAT(
                'Estado cambiado de ',
                OLD.estado,
                ' a ',
                NEW.estado
            )
        );

    END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sanciones`
--

CREATE TABLE `sanciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `prestamo_id` int(11) NOT NULL,
  `motivo` varchar(255) NOT NULL,
  `dias_retraso` int(11) DEFAULT 0,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado` enum('activa','cumplida') DEFAULT 'activa',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sanciones`
--

INSERT INTO `sanciones` (`id`, `usuario_id`, `prestamo_id`, `motivo`, `dias_retraso`, `fecha_inicio`, `fecha_fin`, `estado`, `created_at`) VALUES
(1, 1, 19, 'Entrega tardía', 3, '2026-05-12', '2026-05-12', 'cumplida', '2026-05-12 21:39:36');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

CREATE TABLE `solicitudes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `libro_id` int(11) NOT NULL,
  `fecha_solicitud` date NOT NULL,
  `estado` enum('pendiente','aprobado','rechazado') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes`
--

INSERT INTO `solicitudes` (`id`, `usuario_id`, `libro_id`, `fecha_solicitud`, `estado`) VALUES
(1, 1, 86, '2026-04-23', 'aprobado'),
(2, 1, 86, '2026-04-23', 'aprobado'),
(3, 1, 97, '2026-04-23', 'aprobado'),
(4, 1, 84, '2026-04-23', 'aprobado'),
(5, 1, 97, '2026-04-23', 'aprobado'),
(6, 1, 86, '2026-04-23', 'aprobado'),
(7, 1, 88, '2026-04-23', 'aprobado'),
(8, 1, 86, '2026-05-12', 'aprobado'),
(9, 1, 84, '2026-05-12', 'aprobado'),
(10, 1, 86, '2026-05-12', 'aprobado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `edad` int(11) NOT NULL,
  `tipo_doc` varchar(20) NOT NULL,
  `num_doc` varchar(50) NOT NULL,
  `celular` varchar(20) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `rol` enum('usuario','admin') DEFAULT 'usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `edad`, `tipo_doc`, `num_doc`, `celular`, `correo`, `password`, `fecha_registro`, `rol`) VALUES
(1, 'Juan José', 'Sepúlveda Álvarez', 15, 'CC', '1027804638', '3136796612', 'juanjose.sepu50@gmail.com', '$2y$10$Pf9PthGHEjCsrp1Q8VJCRuEmRJVsXzlGunMdBqOL6Bkuv/Ms1Lmu.', '2026-04-19 03:14:32', 'admin'),
(16, 'juanito', 'alimaña', 16, 'CC', '10278046384', '3136796612', 'juanjose.sepu500@gmail.com', '$2y$10$hjVQ7a5Zmvef71ma3PXbne/AGpHcF.LyjZkzkq4Afq6fGzdeGZeMm', '2026-05-03 02:58:58', 'usuario');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_auditoria_usuario` (`usuario_id`);

--
-- Indices de la tabla `libros`
--
ALTER TABLE `libros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn` (`isbn`);

--
-- Indices de la tabla `pqrs`
--
ALTER TABLE `pqrs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `libro_id` (`libro_id`);

--
-- Indices de la tabla `sanciones`
--
ALTER TABLE `sanciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `prestamo_id` (`prestamo_id`);

--
-- Indices de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_solicitud_usuario` (`usuario_id`),
  ADD KEY `fk_solicitud_libro` (`libro_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `num_doc` (`num_doc`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `libros`
--
ALTER TABLE `libros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=187;

--
-- AUTO_INCREMENT de la tabla `pqrs`
--
ALTER TABLE `pqrs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `sanciones`
--
ALTER TABLE `sanciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD CONSTRAINT `fk_auditoria_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD CONSTRAINT `prestamos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `prestamos_ibfk_2` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`);

--
-- Filtros para la tabla `sanciones`
--
ALTER TABLE `sanciones`
  ADD CONSTRAINT `sanciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `sanciones_ibfk_2` FOREIGN KEY (`prestamo_id`) REFERENCES `prestamos` (`id`);

--
-- Filtros para la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD CONSTRAINT `fk_solicitud_libro` FOREIGN KEY (`libro_id`) REFERENCES `libros` (`id`),
  ADD CONSTRAINT `fk_solicitud_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
