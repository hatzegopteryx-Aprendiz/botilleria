<?php
require_once 'config/auth.php';

// Verificar si el usuario está logueado
if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user = $auth->getCurrentUser();
$message = '';
$message_type = '';

// Procesar actualización de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);
    $ciudad = trim($_POST['ciudad']);
    
    $result = $auth->actualizarPerfil($user['id'], $nombre, $apellido, $telefono, $direccion, $ciudad);
    $message = $result['message'];
    $message_type = $result['success'] ? 'success' : 'danger';
    
    if ($result['success']) {
        $user = $auth->getCurrentUser(); // Recargar datos
    }
}

// Procesar cambio de contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $password_actual = $_POST['password_actual'];
    $password_nueva = $_POST['password_nueva'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password_nueva !== $confirm_password) {
        $message = 'Las contraseñas nuevas no coinciden';
        $message_type = 'danger';
    } else {
        $result = $auth->cambiarPassword($user['id'], $password_actual, $password_nueva);
        $message = $result['message'];
        $message_type = $result['success'] ? 'success' : 'danger';
    }
}

// Obtener historial de pedidos
$stmt = $conn->prepare("SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY fecha_pedido DESC LIMIT 10");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$pedidos = $stmt->get_result();

// Obtener favoritos
$stmt = $conn->prepare("
    SELECT f.*, p.nombre, p.precio, p.imagen 
    FROM favoritos f 
    JOIN productos p ON f.producto_id = p.id 
    WHERE f.usuario_id = ? 
    ORDER BY f.fecha_agregado DESC
");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$favoritos = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Ad Astra</title>
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
        
        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem 0;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            background: var(--accent-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: var(--primary-color);
            margin: 0 auto 1rem;
        }
        
        .nav-pills .nav-link {
            color: var(--primary-color);
        }
        
        .nav-pills .nav-link.active {
            background-color: var(--secondary-color);
        }
        
        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .points-badge {
            background: var(--accent-color);
            color: var(--primary-color);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-wine-bottle me-2"></i>Ad Astra
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-home me-1"></i>Inicio
                </a>
                <a class="nav-link" href="carrito.php">
                    <i class="fas fa-shopping-cart me-1"></i>Carrito
                </a>
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3 text-center">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <h2><?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellido']); ?></h2>
                    <p class="mb-1">
                        <i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($user['email']); ?>
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-calendar me-2"></i>Miembro desde <?php echo date('d/m/Y', strtotime($user['fecha_registro'])); ?>
                    </p>
                </div>
                <div class="col-md-3 text-center">
                    <div class="points-badge">
                        <i class="fas fa-star me-2"></i><?php echo $user['puntos_fidelidad']; ?> puntos
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container py-4">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist">
                    <button class="nav-link active" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab">
                        <i class="fas fa-user me-2"></i>Mi Perfil
                    </button>
                    <button class="nav-link" id="v-pills-orders-tab" data-bs-toggle="pill" data-bs-target="#v-pills-orders" type="button" role="tab">
                        <i class="fas fa-shopping-bag me-2"></i>Mis Pedidos
                    </button>
                    <button class="nav-link" id="v-pills-favorites-tab" data-bs-toggle="pill" data-bs-target="#v-pills-favorites" type="button" role="tab">
                        <i class="fas fa-heart me-2"></i>Favoritos
                    </button>
                    <button class="nav-link" id="v-pills-security-tab" data-bs-toggle="pill" data-bs-target="#v-pills-security" type="button" role="tab">
                        <i class="fas fa-shield-alt me-2"></i>Seguridad
                    </button>
                </div>
            </div>
            
            <!-- Content -->
            <div class="col-md-9">
                <div class="tab-content" id="v-pills-tabContent">
                    <!-- Profile Tab -->
                    <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-user me-2"></i>Información Personal
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="update_profile">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="nombre" class="form-label">Nombre</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="apellido" class="form-label">Apellido</label>
                                            <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($user['apellido']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                        <div class="form-text">El email no se puede modificar</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="telefono" class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($user['telefono']); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="direccion" class="form-label">Dirección</label>
                                        <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo htmlspecialchars($user['direccion']); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="ciudad" class="form-label">Ciudad</label>
                                        <input type="text" class="form-control" id="ciudad" name="ciudad" value="<?php echo htmlspecialchars($user['ciudad']); ?>">
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Guardar Cambios
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Orders Tab -->
                    <div class="tab-pane fade" id="v-pills-orders" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-shopping-bag me-2"></i>Historial de Pedidos
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if ($pedidos->num_rows > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Pedido #</th>
                                                    <th>Fecha</th>
                                                    <th>Total</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while($pedido = $pedidos->fetch_assoc()): ?>
                                                    <tr>
                                                        <td>#<?php echo $pedido['id']; ?></td>
                                                        <td><?php echo date('d/m/Y', strtotime($pedido['fecha_pedido'])); ?></td>
                                                        <td>$<?php echo number_format($pedido['total'], 0, ',', '.'); ?></td>
                                                        <td>
                                                            <span class="badge bg-<?php 
                                                                echo $pedido['estado'] === 'entregado' ? 'success' : 
                                                                    ($pedido['estado'] === 'cancelado' ? 'danger' : 'warning');
                                                            ?>">
                                                                <?php echo ucfirst($pedido['estado']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye"></i> Ver
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                        <h5>No tienes pedidos aún</h5>
                                        <p class="text-muted">¡Explora nuestros productos y haz tu primer pedido!</p>
                                        <a href="index.php" class="btn btn-primary">
                                            <i class="fas fa-shopping-cart me-2"></i>Ir de Compras
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Favorites Tab -->
                    <div class="tab-pane fade" id="v-pills-favorites" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-heart me-2"></i>Mis Favoritos
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if ($favoritos->num_rows > 0): ?>
                                    <div class="row">
                                        <?php while($favorito = $favoritos->fetch_assoc()): ?>
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card h-100">
                                                    <img src="<?php echo $favorito['imagen'] ?: 'https://via.placeholder.com/200x200?text=Sin+Imagen'; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($favorito['nombre']); ?>" style="height: 200px; object-fit: cover;">
                                                    <div class="card-body">
                                                        <h6 class="card-title"><?php echo htmlspecialchars($favorito['nombre']); ?></h6>
                                                        <p class="text-danger h5">$<?php echo number_format($favorito['precio'], 0, ',', '.'); ?></p>
                                                        <div class="d-flex gap-2">
                                                            <button class="btn btn-sm btn-primary flex-fill">
                                                                <i class="fas fa-shopping-cart"></i> Agregar
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger remove-favorite" data-id="<?php echo $favorito['producto_id']; ?>">
                                                                <i class="fas fa-heart-broken"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                                        <h5>No tienes favoritos aún</h5>
                                        <p class="text-muted">Agrega productos a tu lista de favoritos para encontrarlos fácilmente</p>
                                        <a href="index.php" class="btn btn-primary">
                                            <i class="fas fa-search me-2"></i>Explorar Productos
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Security Tab -->
                    <div class="tab-pane fade" id="v-pills-security" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-shield-alt me-2"></i>Cambiar Contraseña
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="change_password">
                                    <div class="mb-3">
                                        <label for="password_actual" class="form-label">Contraseña Actual</label>
                                        <input type="password" class="form-control" id="password_actual" name="password_actual" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password_nueva" class="form-label">Nueva Contraseña</label>
                                        <input type="password" class="form-control" id="password_nueva" name="password_nueva" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirmar Nueva Contraseña</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-key me-2"></i>Cambiar Contraseña
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación de contraseñas
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password_nueva').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Las contraseñas no coinciden');
            } else {
                this.setCustomValidity('');
            }
        });
        
        // Remover favoritos
        document.querySelectorAll('.remove-favorite').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.id;
                // Aquí puedes agregar la lógica AJAX para remover el favorito
                console.log('Remover favorito:', productId);
            });
        });
    </script>
</body>
</html>