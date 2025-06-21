<?php
// Remover esta línea: session_start();
require_once 'config/auth.php';

$message = '';
$message_type = '';

// Procesar formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    $result = $auth->login($email, $password);
    $message = $result['message'];
    $message_type = $result['success'] ? 'success' : 'danger';
    
    if ($result['success']) {
        header('Location: perfil.php');
        exit;
    }
}

// Procesar formulario de registro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);
    $ciudad = trim($_POST['ciudad']);
    
    if ($password !== $confirm_password) {
        $message = 'Las contraseñas no coinciden';
        $message_type = 'danger';
    } else {
        $result = $auth->registrar($nombre, $apellido, $email, $password, $telefono, $direccion, $ciudad);
        $message = $result['message'];
        $message_type = $result['success'] ? 'success' : 'danger';
        
        if ($result['success']) {
            // Auto-login después del registro
            $auth->login($email, $password);
            header('Location: perfil.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Ad Astra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2C3E50;
            --secondary-color: #E74C3C;
            --accent-color: #F1C40F;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            min-height: 100vh;
            font-family: 'Roboto', sans-serif;
        }
        
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .auth-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }
        
        .auth-tabs {
            background: var(--primary-color);
            color: white;
        }
        
        .nav-tabs .nav-link {
            color: rgba(255,255,255,0.7);
            border: none;
            background: transparent;
        }
        
        .nav-tabs .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            border: none;
        }
        
        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(241, 196, 15, 0.25);
        }
        
        .btn-primary {
            background: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-primary:hover {
            background: #c0392b;
            border-color: #c0392b;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="auth-card">
                        <div class="auth-tabs p-3">
                            <h3 class="text-center mb-3">
                                <i class="fas fa-wine-bottle me-2"></i>Ad Astra
                            </h3>
                            <ul class="nav nav-tabs justify-content-center" id="authTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">
                                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab">
                                        <i class="fas fa-user-plus me-2"></i>Registrarse
                                    </button>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="p-4">
                            <?php if ($message): ?>
                                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                                    <?php echo $message; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>
                            
                            <div class="tab-content" id="authTabsContent">
                                <!-- Login Form -->
                                <div class="tab-pane fade show active" id="login" role="tabpanel">
                                    <form method="POST" action="">
                                        <input type="hidden" name="action" value="login">
                                        <div class="mb-3">
                                            <label for="loginEmail" class="form-label">
                                                <i class="fas fa-envelope me-2"></i>Email
                                            </label>
                                            <input type="email" class="form-control" id="loginEmail" name="email" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="loginPassword" class="form-label">
                                                <i class="fas fa-lock me-2"></i>Contraseña
                                            </label>
                                            <input type="password" class="form-control" id="loginPassword" name="password" required>
                                        </div>
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="rememberMe">
                                            <label class="form-check-label" for="rememberMe">
                                                Recordarme
                                            </label>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100 mb-3">
                                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                                        </button>
                                        <div class="text-center">
                                            <a href="#" class="text-muted">¿Olvidaste tu contraseña?</a>
                                        </div>
                                    </form>
                                </div>
                                
                                <!-- Register Form -->
                                <div class="tab-pane fade" id="register" role="tabpanel">
                                    <form method="POST" action="">
                                        <input type="hidden" name="action" value="register">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="nombre" class="form-label">
                                                    <i class="fas fa-user me-2"></i>Nombre
                                                </label>
                                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="apellido" class="form-label">
                                                    <i class="fas fa-user me-2"></i>Apellido
                                                </label>
                                                <input type="text" class="form-control" id="apellido" name="apellido" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="registerEmail" class="form-label">
                                                <i class="fas fa-envelope me-2"></i>Email
                                            </label>
                                            <input type="email" class="form-control" id="registerEmail" name="email" required>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="registerPassword" class="form-label">
                                                    <i class="fas fa-lock me-2"></i>Contraseña
                                                </label>
                                                <input type="password" class="form-control" id="registerPassword" name="password" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="confirmPassword" class="form-label">
                                                    <i class="fas fa-lock me-2"></i>Confirmar Contraseña
                                                </label>
                                                <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="telefono" class="form-label">
                                                <i class="fas fa-phone me-2"></i>Teléfono
                                            </label>
                                            <input type="tel" class="form-control" id="telefono" name="telefono">
                                        </div>
                                        <div class="mb-3">
                                            <label for="direccion" class="form-label">
                                                <i class="fas fa-map-marker-alt me-2"></i>Dirección
                                            </label>
                                            <input type="text" class="form-control" id="direccion" name="direccion">
                                        </div>
                                        <div class="mb-3">
                                            <label for="ciudad" class="form-label">
                                                <i class="fas fa-city me-2"></i>Ciudad
                                            </label>
                                            <input type="text" class="form-control" id="ciudad" name="ciudad">
                                        </div>
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="acceptTerms" required>
                                            <label class="form-check-label" for="acceptTerms">
                                                Acepto los <a href="#">términos y condiciones</a>
                                            </label>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-user-plus me-2"></i>Registrarse
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="index.php" class="text-white">
                            <i class="fas fa-arrow-left me-2"></i>Volver al inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación de contraseñas
        document.getElementById('confirmPassword').addEventListener('input', function() {
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Las contraseñas no coinciden');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>