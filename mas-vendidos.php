<?php
session_start();
require_once 'config/auth.php';

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$password = ""; 
$db = "botilleria";

$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los productos más vendidos
$masVendidos = $conn->query("SELECT * FROM productos ORDER BY ventas DESC LIMIT 8");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Los Más Vendidos - Ad Astra</title>
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

        .producto-card {
            border: none;
            transition: transform 0.3s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            height: 100%;
        }

        .producto-card:hover {
            transform: translateY(-5px);
        }

        .badge-top {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--accent-color);
            color: var(--primary-color);
            font-weight: bold;
            z-index: 10;
        }

        .footer {
            background-color: var(--primary-color);
            color: white;
            padding: 50px 0;
            margin-top: 50px;
        }
        
        .card-img-top {
            height: 250px;
            object-fit: cover;
        }
        
        .social-icons a {
            color: white;
            margin-right: 15px;
            font-size: 20px;
            text-decoration: none;
        }
        
        .social-icons a:hover {
            color: var(--accent-color);
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
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php#ofertas">Ofertas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cervezas.php">Cervezas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="vinos.php">Vinos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="destilados.php">Destilados</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php#contacto">Contacto</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="mas-vendidos.php">Lo más vendido</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="carrito.php">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="badge bg-warning text-dark">0</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Los Más Vendidos</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <h1 class="text-center mb-5">Los Productos Más Vendidos</h1>
    
    <div class="row">
        <?php if ($masVendidos && $masVendidos->num_rows > 0): ?>
            <?php $contador = 1; ?>
            <?php while($producto = $masVendidos->fetch_assoc()): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-4">
                    <div class="card producto-card position-relative">
                        <span class="badge badge-top">#<?php echo $contador; ?> Más Vendido</span>
                        <img src="<?php echo !empty($producto['imagen']) ? htmlspecialchars($producto['imagen']) : 'https://via.placeholder.com/300x400?text=Sin+Imagen'; ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                             onerror="this.src='https://via.placeholder.com/300x400?text=Sin+Imagen'">
                        <div class="card-body text-center d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                            <p class="text-muted flex-grow-1"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                            <p class="h4 text-danger mb-3">$<?php echo number_format($producto['precio'], 0, ',', '.'); ?></p>
                            <button class="btn btn-primary w-100">
                                <i class="fas fa-cart-plus"></i> Agregar al Carrito
                            </button>
                        </div>
                    </div>
                </div>
                <?php $contador++; ?>
            <?php endwhile; ?>
        <?php else: ?>
            <!-- Productos estáticos como respaldo -->
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-4">
                <div class="card producto-card position-relative">
                    <span class="badge badge-top">#1 Más Vendido</span>
                    <img src="https://via.placeholder.com/300x400?text=Pisco+Alto+del+Carmen" class="card-img-top" alt="Producto Top">
                    <div class="card-body text-center d-flex flex-column">
                        <h5 class="card-title">Pisco Alto del Carmen 35°</h5>
                        <p class="text-muted flex-grow-1">750ml</p>
                        <p class="h4 text-danger mb-3">$7.990</p>
                        <button class="btn btn-primary w-100">
                            <i class="fas fa-cart-plus"></i> Agregar al Carrito
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-4">
                <div class="card producto-card position-relative">
                    <span class="badge badge-top">#2 Más Vendido</span>
                    <img src="https://via.placeholder.com/300x400?text=Pack+Cerveza+Corona" class="card-img-top" alt="Cerveza">
                    <div class="card-body text-center d-flex flex-column">
                        <h5 class="card-title">Pack Cerveza Corona</h5>
                        <p class="text-muted flex-grow-1">6 unidades 355ml</p>
                        <p class="h4 text-danger mb-3">$6.990</p>
                        <button class="btn btn-primary w-100">
                            <i class="fas fa-cart-plus"></i> Agregar al Carrito
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-4">
                <div class="card producto-card position-relative">
                    <span class="badge badge-top">#3 Más Vendido</span>
                    <img src="https://via.placeholder.com/300x400?text=Vino+Casillero" class="card-img-top" alt="Vino">
                    <div class="card-body text-center d-flex flex-column">
                        <h5 class="card-title">Vino Casillero del Diablo</h5>
                        <p class="text-muted flex-grow-1">Cabernet Sauvignon 750ml</p>
                        <p class="h4 text-danger mb-3">$5.990</p>
                        <button class="btn btn-primary w-100">
                            <i class="fas fa-cart-plus"></i> Agregar al Carrito
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-4">
                <div class="card producto-card position-relative">
                    <span class="badge badge-top">#4 Más Vendido</span>
                    <img src="https://via.placeholder.com/300x400?text=Johnnie+Walker" class="card-img-top" alt="Whisky">
                    <div class="card-body text-center d-flex flex-column">
                        <h5 class="card-title">Johnnie Walker Red Label</h5>
                        <p class="text-muted flex-grow-1">750ml</p>
                        <p class="h4 text-danger mb-3">$12.990</p>
                        <button class="btn btn-primary w-100">
                            <i class="fas fa-cart-plus"></i> Agregar al Carrito
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer class="footer">
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
</body>
</html>

<?php $conn->close(); ?>