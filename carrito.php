<?php
session_start();
require_once 'config/auth.php';

// Inicializar carrito en sesión si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Manejar acciones AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'agregar':
            $id = $_POST['id'];
            $nombre = $_POST['nombre'];
            $precio = $_POST['precio'];
            $imagen = $_POST['imagen'] ?? 'https://via.placeholder.com/100';
            
            if (isset($_SESSION['carrito'][$id])) {
                $_SESSION['carrito'][$id]['cantidad']++;
            } else {
                $_SESSION['carrito'][$id] = [
                    'id' => $id,
                    'nombre' => $nombre,
                    'precio' => $precio,
                    'imagen' => $imagen,
                    'cantidad' => 1
                ];
            }
            echo json_encode(['success' => true, 'carrito' => $_SESSION['carrito']]);
            exit;
            
        case 'actualizar':
            $id = $_POST['id'];
            $cantidad = max(1, intval($_POST['cantidad']));
            
            if (isset($_SESSION['carrito'][$id])) {
                $_SESSION['carrito'][$id]['cantidad'] = $cantidad;
            }
            echo json_encode(['success' => true, 'carrito' => $_SESSION['carrito']]);
            exit;
            
        case 'eliminar':
            $id = $_POST['id'];
            unset($_SESSION['carrito'][$id]);
            echo json_encode(['success' => true, 'carrito' => $_SESSION['carrito']]);
            exit;
            
        case 'obtener':
            echo json_encode(['carrito' => $_SESSION['carrito']]);
            exit;
            
        case 'vaciar':
            $_SESSION['carrito'] = [];
            echo json_encode(['success' => true, 'carrito' => $_SESSION['carrito']]);
            exit;
    }
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - Ad Astra</title>
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

        .cart-item {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            padding: 20px;
            transition: all 0.3s ease;
        }
        
        .cart-item.removing {
            opacity: 0;
            transform: translateX(-100%);
        }

        .cart-summary {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            padding: 20px;
            position: sticky;
            top: 20px;
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .quantity-btn {
            width: 35px;
            height: 35px;
            border: 1px solid #ddd;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .quantity-btn:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .quantity-input {
            width: 60px;
            text-align: center;
            border: 1px solid #ddd;
            border-left: none;
            border-right: none;
            height: 35px;
        }
        
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
        
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--secondary-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
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
                <form class="d-flex me-3" method="GET" action="index.php" style="min-width: 300px;">
                    <div class="input-group">
                        <input type="text" name="buscar" class="form-control" placeholder="Buscar productos...">
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
                    <a class="nav-link position-relative" href="carrito.php">
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

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Tu Carrito de Compras</h1>
        <button class="btn btn-outline-danger" id="vaciar-carrito" style="display: none;">
            <i class="fas fa-trash"></i> Vaciar Carrito
        </button>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div id="cart-items">
                <!-- Los items del carrito se cargarán aquí dinámicamente -->
            </div>
            
            <div id="empty-cart" class="empty-cart">
                <i class="fas fa-shopping-cart fa-4x mb-3"></i>
                <h3>Tu carrito está vacío</h3>
                <p>Agrega algunos productos para comenzar tu compra</p>
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Continuar Comprando
                </a>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="cart-summary" id="cart-summary">
                <h4>Resumen del Pedido</h4>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal</span>
                    <span id="subtotal">$0</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Envío</span>
                    <span id="envio">$0</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-4">
                    <span class="h5">Total</span>
                    <span class="h5" id="total">$0</span>
                </div>
                <button class="btn btn-primary w-100" id="proceder-pago" onclick="generarBoleta()" disabled>
                    Proceder al Pago
                </button>
            </div>
        </div>
    </div>
</div>

<footer class="footer" style="background-color: var(--primary-color); color: white; padding: 40px 0; margin-top: 50px;">
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
                    <a href="#" style="color: white; margin-right: 10px;"><i class="fab fa-facebook"></i></a>
                    <a href="#" style="color: white; margin-right: 10px;"><i class="fab fa-instagram"></i></a>
                    <a href="#" style="color: white;"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Notificaciones Toast -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="notification-toast" class="toast" role="alert">
        <div class="toast-header">
            <i class="fas fa-shopping-cart text-primary me-2"></i>
            <strong class="me-auto">Carrito</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body" id="toast-message">
            <!-- Mensaje dinámico -->
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
class CarritoManager {
    constructor() {
        this.carrito = {};
        this.init();
    }
    
    init() {
        this.cargarCarrito();
        this.setupEventListeners();
    }
    
    setupEventListeners() {
        // Vaciar carrito
        document.getElementById('vaciar-carrito').addEventListener('click', () => {
            this.vaciarCarrito();
        });
        
        // Escuchar eventos de otros archivos
        window.addEventListener('agregarAlCarrito', (e) => {
            this.agregarProducto(e.detail);
        });
    }
    
    async cargarCarrito() {
        try {
            const response = await fetch('carrito.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=obtener'
            });
            
            const data = await response.json();
            this.carrito = data.carrito || {};
            this.renderCarrito();
        } catch (error) {
            console.error('Error al cargar carrito:', error);
        }
    }
    
    async agregarProducto(producto) {
        try {
            const response = await fetch('carrito.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=agregar&id=${producto.id}&nombre=${encodeURIComponent(producto.nombre)}&precio=${producto.precio}&imagen=${encodeURIComponent(producto.imagen || '')}`
            });
            
            const data = await response.json();
            if (data.success) {
                this.carrito = data.carrito;
                this.renderCarrito();
                this.mostrarNotificacion(`${producto.nombre} agregado al carrito`, 'success');
            }
        } catch (error) {
            console.error('Error al agregar producto:', error);
            this.mostrarNotificacion('Error al agregar producto', 'error');
        }
    }
    
    async actualizarCantidad(id, cantidad) {
        try {
            const response = await fetch('carrito.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=actualizar&id=${id}&cantidad=${cantidad}`
            });
            
            const data = await response.json();
            if (data.success) {
                this.carrito = data.carrito;
                this.renderCarrito();
            }
        } catch (error) {
            console.error('Error al actualizar cantidad:', error);
        }
    }
    
    async eliminarProducto(id) {
        try {
            const itemElement = document.querySelector(`[data-id="${id}"]`);
            if (itemElement) {
                itemElement.classList.add('removing');
                
                setTimeout(async () => {
                    const response = await fetch('carrito.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=eliminar&id=${id}`
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        this.carrito = data.carrito;
                        this.renderCarrito();
                        this.mostrarNotificacion('Producto eliminado del carrito', 'info');
                    }
                }, 300);
            }
        } catch (error) {
            console.error('Error al eliminar producto:', error);
        }
    }
    
    async vaciarCarrito() {
        if (confirm('¿Estás seguro de que quieres vaciar el carrito?')) {
            try {
                const response = await fetch('carrito.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=vaciar'
                });
                
                const data = await response.json();
                if (data.success) {
                    this.carrito = {};
                    this.renderCarrito();
                    this.mostrarNotificacion('Carrito vaciado', 'info');
                }
            } catch (error) {
                console.error('Error al vaciar carrito:', error);
            }
        }
    }
    
    renderCarrito() {
        const cartItemsContainer = document.getElementById('cart-items');
        const emptyCartDiv = document.getElementById('empty-cart');
        const vaciarBtn = document.getElementById('vaciar-carrito');
        
        const items = Object.values(this.carrito);
        
        if (items.length === 0) {
            cartItemsContainer.innerHTML = '';
            emptyCartDiv.style.display = 'block';
            vaciarBtn.style.display = 'none';
        } else {
            emptyCartDiv.style.display = 'none';
            vaciarBtn.style.display = 'block';
            
            cartItemsContainer.innerHTML = items.map(item => `
                <div class="cart-item" data-id="${item.id}">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="${item.imagen}" class="img-fluid rounded" alt="${item.nombre}" 
                                 onerror="this.src='https://via.placeholder.com/100/2C3E50/ffffff?text=Producto'">
                        </div>
                        <div class="col-md-4">
                            <h5 class="mb-1">${item.nombre}</h5>
                            <p class="text-muted mb-0">Precio unitario: $${this.formatearPrecio(item.precio)}</p>
                        </div>
                        <div class="col-md-2">
                            <div class="quantity-controls">
                                <button class="quantity-btn" onclick="carritoManager.actualizarCantidad('${item.id}', ${item.cantidad - 1})" ${item.cantidad <= 1 ? 'disabled' : ''}>
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="quantity-input" value="${item.cantidad}" 
                                       onchange="carritoManager.actualizarCantidad('${item.id}', this.value)" min="1">
                                <button class="quantity-btn" onclick="carritoManager.actualizarCantidad('${item.id}', ${item.cantidad + 1})">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <p class="h5 mb-0">$${this.formatearPrecio(item.precio * item.cantidad)}</p>
                        </div>
                        <div class="col-md-2 text-end">
                            <button class="btn btn-danger btn-sm" onclick="carritoManager.eliminarProducto('${item.id}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }
        
        this.actualizarResumen();
        this.actualizarContadorCarrito();
    }
    
    actualizarResumen() {
        const items = Object.values(this.carrito);
        const subtotal = items.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
        const envio = subtotal > 30000 ? 0 : (subtotal > 0 ? 2500 : 0);
        const total = subtotal + envio;
        
        document.getElementById('subtotal').textContent = `$${this.formatearPrecio(subtotal)}`;
        document.getElementById('envio').textContent = envio === 0 && subtotal > 0 ? 'GRATIS' : `$${this.formatearPrecio(envio)}`;
        document.getElementById('total').textContent = `$${this.formatearPrecio(total)}`;
        
        const procederBtn = document.getElementById('proceder-pago');
        procederBtn.disabled = items.length === 0;
        
        // Mostrar/ocultar resumen
        const cartSummary = document.getElementById('cart-summary');
        cartSummary.style.display = items.length === 0 ? 'none' : 'block';
    }
    
    actualizarContadorCarrito() {
        const items = Object.values(this.carrito);
        const totalItems = items.reduce((sum, item) => sum + item.cantidad, 0);
        const cartCount = document.getElementById('cart-count');
        
        if (cartCount) {
            cartCount.textContent = totalItems;
            cartCount.style.display = totalItems > 0 ? 'flex' : 'none';
        }
    }
    
    formatearPrecio(precio) {
        return new Intl.NumberFormat('es-CL').format(precio);
    }
    
    mostrarNotificacion(mensaje, tipo) {
        const toast = document.getElementById('notification-toast');
        const toastMessage = document.getElementById('toast-message');
        
        toastMessage.textContent = mensaje;
        
        // Cambiar color según tipo
        toast.className = 'toast';
        if (tipo === 'success') {
            toast.classList.add('border-success');
        } else if (tipo === 'error') {
            toast.classList.add('border-danger');
        } else {
            toast.classList.add('border-info');
        }
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
    }
}

// Inicializar el carrito
const carritoManager = new CarritoManager();

// Función para generar boleta
function generarBoleta() {
    const items = Object.values(carritoManager.carrito);
    if (items.length === 0) return;
    
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    
    // Configurar fuente y tamaño
    doc.setFont("helvetica");
    doc.setFontSize(20);
    
    // Agregar título
    doc.text("Ad Astra Botillería", 105, 20, { align: "center" });
    
    // Información de la empresa
    doc.setFontSize(10);
    doc.text("Dirección: Zocima Barrea 834, Los Torunos Graneros", 105, 30, { align: "center" });
    doc.text("Teléfono: +56 9 7619 1328", 105, 35, { align: "center" });
    doc.text("Email: contacto@adastra.cl", 105, 40, { align: "center" });
    
    // Fecha y número de boleta
    const fecha = new Date().toLocaleDateString();
    const numeroBoleta = Math.floor(Math.random() * 10000);
    doc.text(`Fecha: ${fecha}`, 20, 50);
    doc.text(`Boleta N°: ${numeroBoleta}`, 20, 55);
    
    // Encabezados de la tabla
    doc.setFontSize(12);
    doc.text("Producto", 20, 70);
    doc.text("Cant.", 120, 70);
    doc.text("Precio Unit.", 140, 70);
    doc.text("Total", 170, 70);
    
    // Línea separadora
    doc.line(20, 72, 190, 72);
    
    // Productos
    doc.setFontSize(10);
    let y = 80;
    items.forEach(item => {
        doc.text(item.nombre.substring(0, 30), 20, y);
        doc.text(item.cantidad.toString(), 120, y);
        doc.text(`$${carritoManager.formatearPrecio(item.precio)}`, 140, y);
        doc.text(`$${carritoManager.formatearPrecio(item.precio * item.cantidad)}`, 170, y);
        y += 8;
    });
    
    // Totales
    const subtotal = items.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
    const envio = subtotal > 30000 ? 0 : 2500;
    const total = subtotal + envio;
    
    y += 10;
    doc.line(20, y, 190, y);
    y += 10;
    
    doc.text("Subtotal:", 140, y);
    doc.text(`$${carritoManager.formatearPrecio(subtotal)}`, 170, y);
    y += 8;
    
    doc.text("Envío:", 140, y);
    doc.text(envio === 0 ? "GRATIS" : `$${carritoManager.formatearPrecio(envio)}`, 170, y);
    y += 8;
    
    doc.setFont("helvetica", "bold");
    doc.text("Total:", 140, y);
    doc.text(`$${carritoManager.formatearPrecio(total)}`, 170, y);
    
    // Pie de página
    doc.setFont("helvetica", "normal");
    doc.setFontSize(8);
    doc.text("Gracias por su compra", 105, 280, { align: "center" });
    
    // Guardar el PDF
    doc.save(`boleta_ad_astra_${numeroBoleta}.pdf`);
    
    // Mostrar notificación de éxito
    carritoManager.mostrarNotificacion('Boleta generada exitosamente', 'success');
}

// Función global para agregar productos desde otras páginas
window.agregarAlCarrito = function(producto) {
    carritoManager.agregarProducto(producto);
};
</script>
</body>
</html>

<?php $conn->close(); ?>