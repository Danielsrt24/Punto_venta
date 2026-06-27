<?php
// Iniciar sesión solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si ya está logueado, ir al dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: index.php?page=dashboard");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!empty($usuario) && !empty($password)) {
        require_once __DIR__ . '/Datos/config/database.php';
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            $query = "SELECT id, usuario, password, nombre, rol, estado 
                      FROM usuarios 
                      WHERE usuario = :usuario AND estado = 1";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':usuario', $usuario);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_nombre'] = $user['nombre'];
                    $_SESSION['user_rol'] = $user['rol'];
                    $_SESSION['user_usuario'] = $user['usuario'];
                    
                    header("Location: index.php?page=dashboard");
                    exit();
                } else {
                    $error = 'Credenciales incorrectas';
                }
            } else {
                $error = 'Usuario no encontrado';
            }
        } catch (Exception $e) {
            $error = 'Error de conexión: ' . $e->getMessage();
        }
    } else {
        $error = 'Complete todos los campos';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-header text-center bg-primary text-white">
                        <h4><i class="bi bi-cart-check"></i> Sistema de Ventas</h4>
                    </div>
                    <div class="card-body">
                        <?php if($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Usuario</label>
                                <input type="text" name="usuario" class="form-control" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contraseña</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <small class="text-muted">
                            <strong>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>