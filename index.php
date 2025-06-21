<?php
session_start();
require_once 'config/auth.php'; // Agregar esta línea

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$password = ""; 
$db = "botilleria";

$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener ofertas
$ofertas = $conn->query("SELECT * FROM productos WHERE precio_oferta IS NOT NULL LIMIT 4");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Botillería Ad Astra</title>
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

        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1516594915697-87eb3b1c14ea?ixlib=rb-1.2.1');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            text-align: center;
        }

        .navbar {
            background-color: var(--primary-color);
        }

        .navbar-brand, .nav-link {
            color: white !important;
        }

        .ofertas-section {
            padding: 50px 0;
            background-color: white;
        }

        .oferta-card {
            border: none;
            transition: transform 0.3s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .oferta-card:hover {
            transform: translateY(-5px);
        }

        .footer {
            background-color: var(--primary-color);
            color: white;
            padding: 50px 0;
        }

        .social-icons i {
            font-size: 24px;
            margin: 0 10px;
            color: var(--accent-color);
        }

        .contact-info i {
            color: var(--accent-color);
            margin-right: 10px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="#">Ad Astra</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="d-flex align-items-center me-auto">
                <form class="d-flex me-3" style="min-width: 300px;">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Buscar productos...">
                        <button class="btn btn-warning" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="#inicio">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#ofertas">Ofertas</a>
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
                    <a class="nav-link" href="#contacto">Contacto</a>
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

<section class="hero-section" id="inicio">
    <div class="container">
        <h1 class="display-4">Bienvenidos a Ad Astra</h1>
        <p class="lead">Tu botillería de confianza con la mejor selección de bebidas y licores</p>
        <a href="#ofertas" class="btn btn-warning btn-lg mt-3">Ver Ofertas</a>
    </div>
</section>

<section class="servicios-section bg-light" id="servicios">
    <div class="container py-5">
        <h2 class="text-center mb-5">Servicios Especiales</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="servicio-card text-center">
                    <i class="fas fa-truck fa-2x mb-3"></i>
                    <h4>Delivery</h4>
                    <p>Entrega a domicilio en menos de 60 minutos</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="servicio-card text-center">
                    <i class="fas fa-gift fa-2x mb-3"></i>
                    <h4>Regalos</h4>
                    <p>Armamos packs personalizados para regalo</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="servicio-card text-center">
                    <i class="fas fa-glass-cheers fa-2x mb-3"></i>
                    <h4>Eventos</h4>
                    <p>Servicio especial para fiestas y eventos</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="ofertas-section" id="ofertas">
    <div class="container">
        <h2 class="text-center mb-5">Ofertas Especiales</h2>
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card oferta-card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Pack Cerveza Premium</h5>
                        <p class="card-text">6 cervezas artesanales</p>
                        <p class="text-danger h4">$12.990</p>
                        <small class="text-muted"><del>$15.990</del></small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card oferta-card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Vino Reserva</h5>
                        <p class="card-text">Carmenere 2020</p>
                        <p class="text-danger h4">$8.990</p>
                        <small class="text-muted"><del>$11.990</del></small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card oferta-card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Whisky Escocés</h5>
                        <p class="card-text">12 años</p>
                        <p class="text-danger h4">$25.990</p>
                        <small class="text-muted"><del>$32.990</del></small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card oferta-card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Pack Pisco Sour</h5>
                        <p class="card-text">Pisco + ingredientes</p>
                        <p class="text-danger h4">$15.990</p>
                        <small class="text-muted"><del>$19.990</del></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="categorias-section" id="categorias">
    <div class="container py-5">
        <h2 class="text-center mb-5">Nuestras Categorías</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="categoria-card text-center">
                    <a href="vinos.php" class="text-decoration-none">
                        <i class="fas fa-wine-bottle fa-3x mb-3"></i>
                        <h3>Vinos</h3>
                        <p>Tintos, blancos y espumantes de las mejores viñas</p>
                    </a>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="categoria-card text-center">
                    <a href="cervezas.php" class="text-decoration-none">
                        <i class="fas fa-beer fa-3x mb-3"></i>
                        <h3>Cervezas</h3>
                        <p>Artesanales, importadas y nacionales</p>
                    </a>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="categoria-card text-center">
                    <a href="destilados.php" class="text-decoration-none">
                        <i class="fas fa-glass-martini-alt fa-3x mb-3"></i>
                        <h3>Destilados</h3>
                        <p>Whisky, ron, vodka, gin y más</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

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
        <div class="text-center mt-4">
            <p>&copy; 2024 Ad Astra - Todos los derechos reservados</p>
        </div>
    </div>
</footer>

<style>
    .categoria-card {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: transform 0.3s;
    }
    
    .categoria-card:hover {
        transform: translateY(-10px);
    }
    
    .categoria-card i {
        color: var(--primary-color);
    }
    
    .servicio-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .servicio-card i {
        color: var(--secondary-color);
    }
</style>
<section class="newsletter-section py-4 bg-dark text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-0">Suscríbete a nuestras ofertas</h4>
                <p class="mb-0">Recibe promociones exclusivas y novedades</p>
            </div>
            <div class="col-md-6">
                <form class="d-flex">
                    <input type="email" class="form-control me-2" placeholder="Tu email">
                    <button class="btn btn-warning">Suscribirse</button>
                </form>
            </div>
        </div>
    </div>
</section>
<style>
    .categoria-card {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: transform 0.3s;
    }
    
    .categoria-card:hover {
        transform: translateY(-10px);
    }
    
    .categoria-card i {
        color: var(--primary-color);
    }
    
    .servicio-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .servicio-card i {
        color: var(--secondary-color);
    }
</style>

<style>
    .categoria-card {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: transform 0.3s;
    }
    
    .categoria-card:hover {
        transform: translateY(-10px);
    }
    
    .categoria-card i {
        color: var(--primary-color);
    }
    
    .servicio-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .servicio-card i {
        color: var(--secondary-color);
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>

<div class="promo-banner text-center py-2" style="background-color: var(--accent-color);">
    <p class="mb-0"><strong>¡NUEVO!</strong> Delivery gratis en compras sobre $30.000 🚚</p>
</div>

<a href="https://wa.me/56976191328" class="whatsapp-float" target="_blank">
    <i class="fab fa-whatsapp"></i>
</a>

<style>
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
}

.whatsapp-float:hover {
    color: white;
    background-color: #128C7E;
}
</style>
</body>
<div class="modal fade" id="ageVerificationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Verificación de Edad</h5>
            </div>
            <div class="modal-body text-center">
                <p>Debes ser mayor de 18 años para ingresar a este sitio.</p>
                <p>¿Confirmas que eres mayor de edad?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Sí, soy mayor de 18</button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='https://www.google.com'">No soy mayor de edad</button>
            </div>
        </div>
    </div>
</div>

<script>
window.onload = function() {
    if (!sessionStorage.getItem('ageVerified')) {
        var ageModal = new bootstrap.Modal(document.getElementById('ageVerificationModal'));
        ageModal.show();
        sessionStorage.setItem('ageVerified', 'true');
    }
}
</script>

<button id="backToTop" class="btn btn-primary back-to-top">
    <i class="fas fa-arrow-up"></i>
</button>

<style>
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
</style>

<script>
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
</script>
