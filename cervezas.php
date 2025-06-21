<?php
session_start();
require_once 'config/auth.php';

// Función para mostrar errores de manera segura
function mostrarError($mensaje) {
    echo "<!DOCTYPE html><html><head><title>Error</title><meta charset='UTF-8'></head><body>";
    echo "<div style='padding: 20px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px; font-family: Arial, sans-serif;'>";
    echo "<h3>🚨 Error en la aplicación:</h3><p>" . htmlspecialchars($mensaje) . "</p>";
    echo "<p><a href='index.php' style='color: #721c24;'>← Volver al inicio</a></p>";
    echo "</div></body></html>";
    exit;
}

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$password = ""; 
$db = "botilleria";

$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    mostrarError("No se pudo conectar a la base de datos. Verifica que MySQL esté ejecutándose en XAMPP.");
}

// Verificar que la tabla existe
$tabla_existe = $conn->query("SHOW TABLES LIKE 'productos'");
if (!$tabla_existe || $tabla_existe->num_rows == 0) {
    mostrarError("La tabla 'productos' no existe. Por favor, ejecuta el script SQL para crear la estructura de la base de datos.");
}

// Verificar que la columna categoria existe
$columna_existe = $conn->query("SHOW COLUMNS FROM productos LIKE 'categoria'");
if (!$columna_existe || $columna_existe->num_rows == 0) {
    mostrarError("La columna 'categoria' no existe en la tabla 'productos'. Por favor, ejecuta el script SQL para crear la estructura correcta.");
}

// Manejo de búsqueda
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

// Consulta segura con prepared statements
if (!empty($busqueda)) {
    $stmt = $conn->prepare("SELECT * FROM productos WHERE categoria = ? AND (nombre LIKE ? OR descripcion LIKE ?)");
    if ($stmt === false) {
        mostrarError("Error en la preparación de la consulta: " . $conn->error);
    }
    $categoria = 'cervezas';
    $busqueda_param = "%" . $busqueda . "%";
    $stmt->bind_param("sss", $categoria, $busqueda_param, $busqueda_param);
} else {
    $stmt = $conn->prepare("SELECT * FROM productos WHERE categoria = ?");
    if ($stmt === false) {
        mostrarError("Error en la preparación de la consulta: " . $conn->error);
    }
    $categoria = 'cervezas';
    $stmt->bind_param("s", $categoria);
}

if (!$stmt->execute()) {
    mostrarError("Error en la ejecución de la consulta: " . $stmt->error);
}

$cervezas = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cervezas - Ad Astra</title>
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

        .image-container {
            overflow: hidden;
            border-radius: 0.375rem 0.375rem 0 0;
        }
        
        .card-img-top {
            height: 250px;
            object-fit: cover;
            background-color: #f8f9fa;
            transition: transform 0.3s ease, opacity 0.3s ease;
            width: 100%;
        }
        
        .card-img-top:hover {
            transform: scale(1.05);
            opacity: 0.9;
        }
        
        .product-card {
            border: none;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            height: 100%;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 20px rgba(0,0,0,0.15);
        }
        
        .price-container {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 0.375rem;
            margin: 10px 0;
        }
        
        .badge {
            font-size: 0.7rem;
            padding: 0.4rem 0.6rem;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
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
                        <input type="text" name="buscar" class="form-control" placeholder="Buscar cervezas..." value="<?php echo htmlspecialchars($busqueda); ?>">
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
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="perfil.php"><i class="fas fa-user-circle"></i> Mi Perfil</a></li>
                            <li><a class="dropdown-item" href="perfil.php#pedidos"><i class="fas fa-box"></i> Mis Pedidos</a></li>
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
    <h1 class="text-center mb-5">Nuestra Selección de Cervezas</h1>
    
    <?php if (!empty($busqueda)): ?>
        <div class="alert alert-info">
            <i class="fas fa-search"></i> Resultados para: "<strong><?php echo htmlspecialchars($busqueda); ?></strong>"
        </div>
    <?php endif; ?>
    
    <div class="row">
        <?php if ($cervezas && $cervezas->num_rows > 0): ?>
            <?php while($producto = $cervezas->fetch_assoc()): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-4">
                    <div class="card product-card">
                        <?php 
                        // Mapeo más específico de imágenes según el tipo de cerveza
                        $nombre_lower = strtolower($producto['nombre']);
                        $descripcion_lower = strtolower($producto['descripcion']);
                        
                        // Determinar la imagen más apropiada
                        if (strpos($nombre_lower, 'ipa') !== false || strpos($descripcion_lower, 'ipa') !== false || strpos($descripcion_lower, 'india pale ale') !== false) {
                            $imagen_src = 'https://images.unsplash.com/photo-1608270586620-248524c67de9?w=400&h=400&fit=crop&crop=center'; // IPA dorada
                        } elseif (strpos($nombre_lower, 'lager') !== false || strpos($descripcion_lower, 'lager') !== false || strpos($descripcion_lower, 'rubia') !== false) {
                            $imagen_src = 'https://images.unsplash.com/photo-1618885472179-5e474019f2a9?w=400&h=400&fit=crop&crop=center'; // Lager clara
                        } elseif (strpos($nombre_lower, 'stout') !== false || strpos($descripcion_lower, 'stout') !== false || strpos($descripcion_lower, 'negra') !== false || strpos($descripcion_lower, 'cremosa') !== false) {
                            $imagen_src = 'https://images.unsplash.com/photo-1569529465841-dfecdab7503b?w=400&h=400&fit=crop&crop=center'; // Stout oscura
                        } elseif (strpos($nombre_lower, 'trigo') !== false || strpos($descripcion_lower, 'trigo') !== false || strpos($descripcion_lower, 'alemana') !== false) {
                            $imagen_src = 'https://images.unsplash.com/photo-1535958636474-b021ee887b13?w=400&h=400&fit=crop&crop=center'; // Cerveza de trigo
                        } elseif (strpos($nombre_lower, 'pale ale') !== false || strpos($descripcion_lower, 'pale ale') !== false || strpos($descripcion_lower, 'american') !== false) {
                            $imagen_src = 'https://images.unsplash.com/photo-1608270586620-248524c67de9?w=400&h=400&fit=crop&crop=center'; // Pale Ale
                        } elseif (strpos($nombre_lower, 'pilsner') !== false || strpos($descripcion_lower, 'pilsner') !== false || strpos($descripcion_lower, 'checa') !== false || strpos($descripcion_lower, 'tradicional') !== false) {
                            $imagen_src = 'https://images.unsplash.com/photo-1618885472179-5e474019f2a9?w=400&h=400&fit=crop&crop=center'; // Pilsner
                        } else {
                            // Imagen por defecto para cervezas genéricas
                            $imagen_src = 'https://images.unsplash.com/photo-1608270586620-248524c67de9?w=400&h=400&fit=crop&crop=center';
                        }
                        
                        // Si hay una imagen específica en la base de datos, usarla
                        if (!empty($producto['imagen']) && filter_var($producto['imagen'], FILTER_VALIDATE_URL)) {
                            $imagen_src = $producto['imagen'];
                        }
                        ?>
                        <div class="image-container position-relative">
                            <img src="<?php echo htmlspecialchars($imagen_src); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                 onerror="this.src='https://via.placeholder.com/400x400/2C3E50/ffffff?text=<?php echo urlencode($producto['nombre']); ?>';"
                                 loading="lazy">
                            <?php if (strpos($nombre_lower, 'ipa') !== false): ?>
                                <span class="badge bg-warning position-absolute top-0 end-0 m-2">IPA</span>
                            <?php elseif (strpos($nombre_lower, 'stout') !== false): ?>
                                <span class="badge bg-dark position-absolute top-0 end-0 m-2">STOUT</span>
                            <?php elseif (strpos($nombre_lower, 'lager') !== false): ?>
                                <span class="badge bg-success position-absolute top-0 end-0 m-2">LAGER</span>
                            <?php elseif (strpos($nombre_lower, 'trigo') !== false): ?>
                                <span class="badge bg-info position-absolute top-0 end-0 m-2">TRIGO</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body text-center d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                            <p class="card-text flex-grow-1 text-muted small"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                            <div class="price-container mb-3">
                                <p class="text-danger h4 mb-0">$<?php echo number_format($producto['precio'], 0, ',', '.'); ?></p>
                                <small class="text-muted">Precio por unidad</small>
                            </div>
                            <button class="btn btn-primary w-100 agregar-carrito" 
                                    data-id="<?php echo $producto['id']; ?>" 
                                    data-nombre="<?php echo htmlspecialchars($producto['nombre']); ?>" 
                                    data-precio="<?php echo $producto['precio']; ?>">
                                <i class="fas fa-cart-plus"></i> Agregar al Carrito
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php if (!empty($busqueda)): ?>
                        No se encontraron cervezas que coincidan con tu búsqueda.
                    <?php else: ?>
                        No hay cervezas disponibles en este momento.
                    <?php endif; ?>
                </div>
                <?php if (!empty($busqueda)): ?>
                    <a href="cervezas.php" class="btn btn-primary">Ver todas las cervezas</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer class="footer" style="background-color: var(--primary-color); color: white; padding: 40px 0; margin-top: 50px;">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h4>Contacto</h4>
                <p><i class="fas fa-phone"></i> +56 9 7619 1328</p>
                <p><i class="fas fa-envelope"></i> contacto@adastra.cl</p>
            </div>
            <div class="col-md-4">
                <h4>Ubicación</h4>
                <p><i class="fas fa-map-marker-alt"></i> Zocima Barrea 834, Los Torunos Graneros</p>
            </div>
            <div class="col-md-4">
                <h4>Síguenos</h4>
                <div class="social-icons">
                    <a href="#" style="color: white; margin-right: 10px;"><i class="fab fa-facebook"></i></a>
                    <a href="#" style="color: white; margin-right: 10px;"><i class="fab fa-instagram"></i></a>
                    <a href="#" style="color: white;"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
    </div>
</footer>

<script>
// Funcionalidad del carrito reactivo
document.addEventListener('DOMContentLoaded', function() {
    const botonesAgregar = document.querySelectorAll('.agregar-carrito');
    
    botonesAgregar.forEach(boton => {
        boton.addEventListener('click', function() {
            const producto = {
                id: this.dataset.id,
                nombre: this.dataset.nombre,
                precio: parseFloat(this.dataset.precio),
                imagen: this.dataset.imagen || 'https://images.unsplash.com/photo-1608270586620-248524c67de9?w=400&h=400&fit=crop'
            };
            
            // Agregar al carrito
            if (window.agregarAlCarrito) {
                window.agregarAlCarrito(producto);
            } else {
                // Fallback si no está disponible la función global
                fetch('carrito.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=agregar&id=${producto.id}&nombre=${encodeURIComponent(producto.nombre)}&precio=${producto.precio}&imagen=${encodeURIComponent(producto.imagen)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarNotificacion(`${producto.nombre} agregado al carrito`, 'success');
                        actualizarContadorCarrito();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarNotificacion('Error al agregar producto', 'error');
                });
            }
            
            // Feedback visual del botón
            this.innerHTML = '<i class="fas fa-check"></i> ¡Agregado!';
            this.classList.remove('btn-primary');
            this.classList.add('btn-success');
            
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-cart-plus"></i> Agregar al Carrito';
                this.classList.remove('btn-success');
                this.classList.add('btn-primary');
            }, 2000);
        });
    });
    
    // Cargar contador del carrito al iniciar
    actualizarContadorCarrito();
});

function actualizarContadorCarrito() {
    fetch('carrito.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=obtener'
    })
    .then(response => response.json())
    .then(data => {
        const carrito = data.carrito || {};
        const totalItems = Object.values(carrito).reduce((sum, item) => sum + item.cantidad, 0);
        const cartBadge = document.querySelector('.badge');
        
        if (cartBadge) {
            cartBadge.textContent = totalItems;
            cartBadge.style.display = totalItems > 0 ? 'inline' : 'none';
        }
    })
    .catch(error => console.error('Error al actualizar contador:', error));
}

function mostrarNotificacion(mensaje, tipo) {
    const notificacion = document.createElement('div');
    notificacion.className = `alert alert-${tipo === 'success' ? 'success' : 'danger'} position-fixed`;
    notificacion.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notificacion.innerHTML = `
        <i class="fas fa-${tipo === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        ${mensaje}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;
    
    document.body.appendChild(notificacion);
    
    setTimeout(() => {
        if (notificacion.parentElement) {
            notificacion.remove();
        }
    }, 3000);
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php 
$stmt->close();
$conn->close(); 
?>
</body>
</html>