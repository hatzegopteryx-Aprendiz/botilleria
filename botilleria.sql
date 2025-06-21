-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-06-2025 a las 03:27:51
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
-- Base de datos: `botilleria`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedidos`
--

CREATE TABLE `detalle_pedidos` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direcciones_usuario`
--

CREATE TABLE `direcciones_usuario` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `nombre_direccion` varchar(100) DEFAULT NULL,
  `direccion` text NOT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `codigo_postal` varchar(10) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `es_principal` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `favoritos`
--

CREATE TABLE `favoritos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `fecha_agregado` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','confirmado','enviado','entregado','cancelado') DEFAULT 'pendiente',
  `direccion_entrega` text DEFAULT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `fecha_pedido` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_entrega` timestamp NULL DEFAULT NULL,
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `categoria` varchar(100) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `categoria`, `imagen`, `stock`, `created_at`) VALUES
(1, 'Cerveza Artesanal IPA', 'India Pale Ale 330ml con notas cítricas', 2990.00, 'cervezas', 'cerveza1.jpg', 50, '2025-06-18 02:15:14'),
(2, 'Cerveza Lager Premium', 'Cerveza rubia suave y refrescante 355ml', 1990.00, 'cervezas', 'cerveza2.jpg', 30, '2025-06-18 02:15:14'),
(3, 'Cerveza Stout Cremosa', 'Cerveza negra cremosa con sabor a café 330ml', 3490.00, 'cervezas', 'cerveza3.jpg', 25, '2025-06-18 02:15:14'),
(4, 'Cerveza de Trigo', 'Cerveza de trigo alemana 330ml', 2790.00, 'cervezas', 'cerveza4.jpg', 40, '2025-06-18 02:15:14'),
(5, 'Cerveza Pale Ale', 'American Pale Ale 355ml', 2590.00, 'cervezas', 'cerveza5.jpg', 35, '2025-06-18 02:15:14'),
(6, 'Cerveza Pilsner', 'Pilsner checa tradicional 330ml', 2290.00, 'cervezas', 'cerveza6.jpg', 45, '2025-06-18 02:15:14'),
(7, 'Vino Tinto Cabernet Sauvignon', 'Vino tinto de cuerpo completo 750ml con notas de frutas rojas', 8990.00, 'vinos', 'vino1.jpg', 30, '2025-06-18 07:49:37'),
(8, 'Vino Blanco Sauvignon Blanc', 'Vino blanco fresco y aromático 750ml con notas cítricas', 7290.00, 'vinos', 'vino2.jpg', 25, '2025-06-18 07:49:37'),
(9, 'Vino Rosé Premium', 'Vino rosado ligero y refrescante 750ml', 6890.00, 'vinos', 'vino3.jpg', 20, '2025-06-18 07:49:37'),
(10, 'Vino Tinto Merlot', 'Vino tinto suave 750ml con sabores a ciruela', 9490.00, 'vinos', 'vino4.jpg', 18, '2025-06-18 07:49:37'),
(11, 'Vino Blanco Chardonnay', 'Vino blanco cremoso 750ml con notas de vainilla', 8790.00, 'vinos', 'vino5.jpg', 22, '2025-06-18 07:49:37'),
(12, 'Vino Espumoso Prosecco', 'Vino espumoso italiano 750ml ligero y burbujeante', 12590.00, 'vinos', 'vino6.jpg', 15, '2025-06-18 07:49:37'),
(13, 'Whisky Escocés Premium', 'Whisky escocés de malta 750ml con notas ahumadas', 45990.00, 'destilados', 'destilado1.jpg', 15, '2025-06-18 07:59:07'),
(14, 'Ron Añejo Caribeño', 'Ron añejo 750ml con sabor suave y dulce', 28990.00, 'destilados', 'destilado2.jpg', 20, '2025-06-18 07:59:07'),
(15, 'Vodka Premium', 'Vodka premium 750ml destilado cinco veces', 22990.00, 'destilados', 'destilado3.jpg', 25, '2025-06-18 07:59:07'),
(16, 'Pisco Artesanal', 'Pisco chileno artesanal 750ml de uva moscatel', 18990.00, 'destilados', 'destilado4.jpg', 30, '2025-06-18 07:59:07'),
(17, 'Gin London Dry', 'Gin inglés 750ml con botánicos tradicionales', 32990.00, 'destilados', 'destilado5.jpg', 18, '2025-06-18 07:59:07'),
(18, 'Tequila Reposado', 'Tequila mexicano reposado 750ml 100% agave', 38990.00, 'destilados', 'destilado6.jpg', 12, '2025-06-18 07:59:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultimo_acceso` timestamp NULL DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `puntos_fidelidad` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `password`, `telefono`, `direccion`, `ciudad`, `fecha_registro`, `ultimo_acceso`, `activo`, `puntos_fidelidad`) VALUES
(1, 'pancho', 'ulloa', 'asdasd@asdasd.com', '$2y$10$QoJqsg5itkJvrjHeRq6IAeEvmSU1dhVb7A1JL1ibrS.QhTUfwmHSC', '123456789', 'si', 'rancagua', '2025-06-18 05:04:42', '2025-06-19 01:05:14', 1, 0),
(2, 'leo', 'saldaña', 'leo@asdasd.com', '$2y$10$JNo.5E5IhZrcpx1Bl/K3IeBOhtNt9dO.NDjjZUFomGKA/Un11f2Kq', '123456789', 'si', 'machali', '2025-06-18 07:43:02', '2025-06-19 01:18:47', 1, 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `direcciones_usuario`
--
ALTER TABLE `direcciones_usuario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `favoritos`
--
ALTER TABLE `favoritos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favorito` (`usuario_id`,`producto_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `direcciones_usuario`
--
ALTER TABLE `direcciones_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `favoritos`
--
ALTER TABLE `favoritos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  ADD CONSTRAINT `detalle_pedidos_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`),
  ADD CONSTRAINT `detalle_pedidos_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `direcciones_usuario`
--
ALTER TABLE `direcciones_usuario`
  ADD CONSTRAINT `direcciones_usuario_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `favoritos`
--
ALTER TABLE `favoritos`
  ADD CONSTRAINT `favoritos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `favoritos_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
