<?php
require_once __DIR__ . '/../session.php';
Session::init();

require_once __DIR__ . '/../env.php';
cargarEntorno(__DIR__ . '/../../../.env');

require_once __DIR__ . '/../../../Datos/config/database.php';
require_once __DIR__ . '/../../../Datos/config/models/Usuario.php';

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

$base_path = "/PUNTO_VENTA";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_input = $_POST['usuario'] ?? '';
    $password_input = $_POST['password'] ?? '';

    $datos_usuario = $usuario->login($usuario_input, $password_input);

    if ($datos_usuario) {
        Session::set('user_id', $datos_usuario['id']);
        Session::set('user_nombre', $datos_usuario['nombre']);
        Session::set('user_rol', $datos_usuario['rol']);
        
        header("Location: " . $base_path . "/index.php?page=dashboard");
        exit();
    } else {
        header("Location: " . $base_path . "/index.php?page=login&error=Usuario+o+contraseña+incorrectos");
        exit();
    }
} else {
    header("Location: " . $base_path . "/index.php?page=login");
    exit();
}
?>