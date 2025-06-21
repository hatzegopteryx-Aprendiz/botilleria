<?php
// Iniciar sesión solo si no está ya activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración de la base de datos
$host = "localhost";
$user = "root";
$password = "";
$db = "botilleria";

$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Funciones de autenticación
class Auth {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    // Registrar nuevo usuario
    public function registrar($nombre, $apellido, $email, $password, $telefono = '', $direccion = '', $ciudad = '') {
        // Verificar si el email ya existe
        $stmt = $this->conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return ['success' => false, 'message' => 'El email ya está registrado'];
        }
        
        // Encriptar contraseña
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insertar usuario
        $stmt = $this->conn->prepare("INSERT INTO usuarios (nombre, apellido, email, password, telefono, direccion, ciudad) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $nombre, $apellido, $email, $password_hash, $telefono, $direccion, $ciudad);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Usuario registrado exitosamente', 'user_id' => $this->conn->insert_id];
        } else {
            return ['success' => false, 'message' => 'Error al registrar usuario'];
        }
    }
    
    // Iniciar sesión
    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT id, nombre, apellido, email, password, puntos_fidelidad FROM usuarios WHERE email = ? AND activo = 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                // Actualizar último acceso
                $this->actualizarUltimoAcceso($user['id']);
                
                // Guardar datos en sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nombre'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_points'] = $user['puntos_fidelidad'];
                
                return ['success' => true, 'message' => 'Inicio de sesión exitoso', 'user' => $user];
            }
        }
        
        return ['success' => false, 'message' => 'Email o contraseña incorrectos'];
    }
    
    // Cerrar sesión
    public function logout() {
        session_destroy();
        return ['success' => true, 'message' => 'Sesión cerrada exitosamente'];
    }
    
    // Verificar si el usuario está logueado
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    // Obtener datos del usuario actual
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    // Actualizar perfil de usuario
    public function actualizarPerfil($user_id, $nombre, $apellido, $telefono, $direccion, $ciudad) {
        $stmt = $this->conn->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, telefono = ?, direccion = ?, ciudad = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $nombre, $apellido, $telefono, $direccion, $ciudad, $user_id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Perfil actualizado exitosamente'];
        } else {
            return ['success' => false, 'message' => 'Error al actualizar perfil'];
        }
    }
    
    // Cambiar contraseña
    public function cambiarPassword($user_id, $password_actual, $password_nueva) {
        // Verificar contraseña actual
        $stmt = $this->conn->prepare("SELECT password FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if (!password_verify($password_actual, $user['password'])) {
            return ['success' => false, 'message' => 'Contraseña actual incorrecta'];
        }
        
        // Actualizar contraseña
        $password_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $password_hash, $user_id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Contraseña actualizada exitosamente'];
        } else {
            return ['success' => false, 'message' => 'Error al actualizar contraseña'];
        }
    }
    
    private function actualizarUltimoAcceso($user_id) {
        $stmt = $this->conn->prepare("UPDATE usuarios SET ultimo_acceso = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }
}

// Instanciar clase de autenticación
$auth = new Auth($conn);
?>