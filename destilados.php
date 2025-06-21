<?php
session_start();
require_once 'config/auth.php';

// Función para mostrar errores de manera amigable
function mostrarError($mensaje) {
    return "<div class='alert alert-warning' role='alert'><i class='fas fa-exclamation-triangle'></i> $mensaje</div>";
}

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$password = ""; 
$db = "botilleria";

$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si la tabla productos existe
$tabla_existe = $conn->query("SHOW TABLES LIKE 'productos'");
if ($tabla_existe->num_rows == 0) {
    echo mostrarError("La tabla 'productos' no existe en la base de datos.");
    exit;
}

// Verificar si la columna categoria existe
$columna_existe = $conn->query("SHOW COLUMNS FROM productos LIKE 'categoria'");
if ($columna_existe->num_rows == 0) {
    echo mostrarError("La columna 'categoria' no existe en la tabla productos.");
    exit;
}

// Manejar búsqueda
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$sql = "SELECT * FROM productos WHERE categoria = 'destilados'";
if (!empty($busqueda)) {
    $sql .= " AND (nombre LIKE ? OR descripcion LIKE ?)";
}

$stmt = $conn->prepare($sql);
if (!empty($busqueda)) {
    $busqueda_param = "%$busqueda%";
    $stmt->bind_param("ss", $busqueda_param, $busqueda_param);
}
$stmt->execute();
$result = $stmt->get_result();

// Función para obtener imagen apropiada según el tipo de destilado
function obtenerImagenDestilado($nombre, $descripcion) {
    $texto_completo = strtolower($nombre . ' ' . $descripcion);
    
    // Mapeo de tipos de destilado a imágenes de Unsplash
    if (strpos($texto_completo, 'whisky') !== false || strpos($texto_completo, 'whiskey') !== false) {
        return 'https://images.unsplash.com/photo-1527281400683-1aae777175f8?w=400&h=400&fit=crop';
    } elseif (strpos($texto_completo, 'vodka') !== false) {
        return 'https://images.unsplash.com/photo-1551538827-9c037cb4f32a?w=400&h=400&fit=crop';
    } elseif (strpos($texto_completo, 'ron') !== false || strpos($texto_completo, 'rum') !== false) {
        return 'https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?w=400&h=400&fit=crop';
    } elseif (strpos($texto_completo, 'gin') !== false || strpos($texto_completo, 'ginebra') !== false) {
        return 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=400&h=400&fit=crop';
    } elseif (strpos($texto_completo, 'tequila') !== false) {
        return 'https://images.unsplash.com/photo-1615332579937-23bbaa4ffa26?w=400&h=400&fit=crop';
    } elseif (strpos($texto_completo, 'brandy') !== false || strpos($texto_completo, 'cognac') !== false) {
        return 'https://images.unsplash.com/photo-1569529465841-dfecdab7503b?w=400&h=400&fit=crop';
    } elseif (strpos($texto_completo, 'pisco') !== false) {
        return 'https://images.unsplash.com/photo-1551538827-9c037cb4f32a?w=400&h=400&fit=crop';
    } else {
        // Imagen genérica de destilado
        return 'https://images.unsplash.com/photo-1527281400683-1aae777175f8?w=400&h=400&fit=crop';
    }
}

// Función para obtener badge del tipo de destilado
function obtenerBadgeDestilado($nombre, $descripcion) {
    $texto_completo = strtolower($nombre . ' ' . $descripcion);
    
    if (strpos($texto_completo, 'whisky') !== false || strpos($texto_completo, 'whiskey') !== false) return 'Whisky';
    if (strpos($texto_completo, 'vodka') !== false) return 'Vodka';
    if (strpos($texto_completo, 'ron') !== false || strpos($texto_completo, 'rum') !== false) return 'Ron';
    if (strpos($texto_completo, 'gin') !== false || strpos($texto_completo, 'ginebra') !== false) return 'Gin';
    if (strpos($texto_completo, 'tequila') !== false) return 'Tequila';
    if (strpos($texto_completo, 'brandy') !== false) return 'Brandy';
    if (strpos($texto_completo, 'cognac') !== false) return 'Cognac';
    if (strpos($texto_completo, 'pisco') !== false) return 'Pisco';
    
    return 'Destilado';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destilados - Ad Astra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2C3E50;
            --secondary-color: #E74C3C;
            --accent-color: #F1C40F;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: var(--primary-color);
        }

        .navbar-brand, .nav-link {
            color: white !important;
        }

        .product-card {
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }

        .card-img-top {
            height: 250px;
            object-fit: cover;
            background-color: #f8f9fa;
            transition: transform 0.3s ease;
        }

        .card-img-top:hover {
            transform: scale(1.05);
        }

        .price-badge {
            background: linear-gradient(45deg, var(--secondary-color), #c0392b);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 1.1em;
        }

        .spirit-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8em;
            z-index: 2;
        }

        .whatsapp-float {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #25D366;
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            text-align: center;
            font-size: 30px;
            box-shadow: 2px 2px 3px #999;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .whatsapp-float:hover {
            color: white;
            background-color: #128C7E;
        }

        .back-to-top {
            position: fixed;
            bottom: 90px;
            right: 20px;
            display: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            z-index: 99;
        }

        .footer {
            background-color: var(--primary-color);
            color: white;
            padding: 40px 0;
            margin-top: 50px;
        }

        .social-icons a {
            color: white;
            font-size: 1.5em;
            margin-right: 15px;
            transition: color 0.3s;
        }

        .social-icons a:hover {
            color: var(--accent-color);
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            min-width: 300px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Ad Astra</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="d-flex align-items-center me-auto">
                <form class="d-flex me-3" method="GET" style="min-width: 300px;">
                    <div class="input-group">
                        <input type="text" name="buscar" class="form-control" placeholder="Buscar destilados..." value="<?php echo htmlspecialchars($busqueda); ?>">
                        <button class="btn btn-warning" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php#ofertas">Ofertas</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-list"></i> Categorías
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="cervezas.php"><i class="fas fa-beer"></i> Cervezas</a></li>
                        <li><a class="dropdown-item" href="vinos.php"><i class="fas fa-wine-glass"></i> Vinos</a></li>
                        <li><a class="dropdown-item" href="destilados.php"><i class="fas fa-cocktail"></i> Destilados</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="mas-vendidos.php">Lo más vendido</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="carrito.php">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="badge bg-warning text-dark" id="cart-count">0</span>
                    </a>
                </li>
                <?php if ($auth->isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($auth->getCurrentUser()['nombre']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="perfil.php"><i class="fas fa-user-circle"></i> Mi Perfil</a></li>
                            <li><a class="dropdown-item" href="perfil.php#pedidos"><i class="fas fa-shopping-bag"></i> Mis Pedidos</a></li>
                            <li><a class="dropdown-item" href="perfil.php#favoritos"><i class="fas fa-heart"></i> Favoritos</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php#registro">
                            <i class="fas fa-user-plus"></i> Registrarse
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="promo-banner text-center py-2" style="background-color: var(--accent-color);">
    <p class="mb-0"><strong>¡NUEVO!</strong> Delivery gratis en compras sobre $30.000 🚚</p>
</div>

<div class="container py-5">
    <h1 class="text-center mb-5">Nuestra Selección de Destilados</h1>
    
    <?php if (!empty($busqueda)): ?>
        <div class="alert alert-info">
            <i class="fas fa-search"></i> Resultados para: "<strong><?php echo htmlspecialchars($busqueda); ?></strong>"
            <a href="destilados.php" class="btn btn-sm btn-outline-primary ms-2">Ver todos</a>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <?php 
        if ($result && $result->num_rows > 0) {
            while($producto = $result->fetch_assoc()) {
                $imagen_url = obtenerImagenDestilado($producto['nombre'], $producto['descripcion'] ?? '');
                $badge_tipo = obtenerBadgeDestilado($producto['nombre'], $producto['descripcion'] ?? '');
                ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card product-card h-100 position-relative">
                        <div class="spirit-badge"><?php echo $badge_tipo; ?></div>
                        <img src="<?php echo $imagen_url; ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                             loading="lazy"
                             onerror="this.src='https://via.placeholder.com/400x250?text=Destilado+No+Disponible'">
                        <div class="card-body text-center d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                            <p class="card-text text-muted flex-grow-1"><?php echo htmlspecialchars($producto['descripcion'] ?? 'Excelente destilado de nuestra selección'); ?></p>
                            <div class="mt-auto">
                                <div class="price-badge mb-3">$<?php echo number_format($producto['precio'], 0, ',', '.'); ?></div>
                                <button class="btn btn-primary w-100 add-to-cart" 
                                        data-id="<?php echo $producto['id']; ?>"
                                        data-nombre="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                        data-precio="<?php echo $producto['precio']; ?>"
                                        data-imagen="<?php echo $imagen_url; ?>">
                                    <i class="fas fa-shopping-cart me-2"></i>Agregar al Carrito
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            // Productos estáticos como respaldo
            $productos_estaticos = [
                ['nombre' => 'Whisky Escocés 12 años', 'descripcion' => 'Single Malt premium 750ml', 'precio' => 25990],
                ['nombre' => 'Vodka Premium', 'descripcion' => 'Vodka ultra suave 750ml', 'precio' => 18990],
                ['nombre' => 'Ron Añejo', 'descripcion' => 'Ron caribeño 7 años 750ml', 'precio' => 22990],
                ['nombre' => 'Gin London Dry', 'descripcion' => 'Ginebra clásica inglesa 750ml', 'precio' => 16990],
                ['nombre' => 'Tequila Reposado', 'descripcion' => 'Tequila 100% agave 750ml', 'precio' => 28990],
                ['nombre' => 'Brandy Reserva', 'descripcion' => 'Brandy español añejo 750ml', 'precio' => 19990],
                ['nombre' => 'Pisco Especial', 'descripcion' => 'Pisco chileno premium 750ml', 'precio' => 12990],
                ['nombre' => 'Whisky Bourbon', 'descripcion' => 'Bourbon americano 750ml', 'precio' => 24990]
            ];
            
            foreach($productos_estaticos as $index => $producto) {
                $imagen_url = obtenerImagenDestilado($producto['nombre'], $producto['descripcion']);
                $badge_tipo = obtenerBadgeDestilado($producto['nombre'], $producto['descripcion']);
                ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card product-card h-100 position-relative">
                        <div class="spirit-badge"><?php echo $badge_tipo; ?></div>
                        <img src="<?php echo $imagen_url; ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                             loading="lazy"
                             onerror="this.src='https://via.placeholder.com/400x250?text=Destilado+No+Disponible'">
                        <div class="card-body text-center d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                            <p class="card-text text-muted flex-grow-1"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                            <div class="mt-auto">
                                <div class="price-badge mb-3">$<?php echo number_format($producto['precio'], 0, ',', '.'); ?></div>
                                <button class="btn btn-primary w-100 add-to-cart" 
                                        data-id="spirit_<?php echo $index + 1; ?>"
                                        data-nombre="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                        data-precio="<?php echo $producto['precio']; ?>"
                                        data-imagen="<?php echo $imagen_url; ?>">
                                    <i class="fas fa-shopping-cart me-2"></i>Agregar al Carrito
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>

<a href="https://wa.me/56976191328" class="whatsapp-float" target="_blank">
    <i class="fab fa-whatsapp"></i>
</a>

<button id="backToTop" class="btn btn-primary back-to-top">
    <i class="fas fa-arrow-up"></i>
</button>

<footer class="footer" id="contacto">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h4>Contacto</h4>
                <p><i class="fas fa-phone"></i> +56 9 7619 1328</p>
                <p><i class="fas fa-envelope"></i> contacto@adastra.cl</p>
                <p><i class="fas fa-clock"></i> Lunes a Jueves: 10:00 - 00:00</p>
                <p><i class="fas fa-clock"></i> Viernes a Domingo: 10:00 - 02:00</p>
            </div>
            <div class="col-md-4">
                <h4>Ubicación</h4>
                <p><i class="fas fa-map-marker-alt"></i> Zocima Barrea 834, Los Torunos Graneros</p>
            </div>
            <div class="col-md-4">
                <h4>Síguenos</h4>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Funcionalidad del carrito
function actualizarContadorCarrito() {
    const carrito = JSON.parse(localStorage.getItem('carrito') || '[]');
    const contador = carrito.reduce((total, item) => total + item.cantidad, 0);
    document.getElementById('cart-count').textContent = contador;
}

// Función para mostrar notificaciones
function mostrarNotificacion(mensaje, tipo = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${tipo} alert-dismissible fade show notification`;
    notification.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 3000);
}

// Agregar al carrito
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('add-to-cart') || e.target.closest('.add-to-cart')) {
        const button = e.target.classList.contains('add-to-cart') ? e.target : e.target.closest('.add-to-cart');
        
        const producto = {
            id: button.dataset.id,
            nombre: button.dataset.nombre,
            precio: parseFloat(button.dataset.precio),
            imagen: button.dataset.imagen,
            cantidad: 1
        };
        
        // Obtener carrito actual
        let carrito = JSON.parse(localStorage.getItem('carrito') || '[]');
        
        // Verificar si el producto ya existe
        const existente = carrito.find(item => item.id === producto.id);
        
        if (existente) {
            existente.cantidad += 1;
            mostrarNotificacion(`<i class="fas fa-check"></i> Cantidad actualizada: ${producto.nombre}`);
        } else {
            carrito.push(producto);
            mostrarNotificacion(`<i class="fas fa-check"></i> ${producto.nombre} agregado al carrito`);
        }
        
        // Guardar carrito
        localStorage.setItem('carrito', JSON.stringify(carrito));
        
        // Actualizar contador
        actualizarContadorCarrito();
        
        // Efecto visual en el botón
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i> ¡Agregado!';
        button.classList.add('btn-success');
        button.classList.remove('btn-primary');
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('btn-success');
            button.classList.add('btn-primary');
        }, 1500);
    }
});

// Scroll to top functionality
window.onscroll = function() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        document.getElementById("backToTop").style.display = "block";
    } else {
        document.getElementById("backToTop").style.display = "none";
    }
};

document.getElementById("backToTop").onclick = function() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
};

// Inicializar contador al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    actualizarContadorCarrito();
});
</script>
</body>
</html>

<?php 
$stmt->close();
$conn->close(); 
?>