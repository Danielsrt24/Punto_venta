<?php
require_once __DIR__ . '/../session.php';
Session::init();

require_once __DIR__ . '/../env.php';
cargarEntorno(__DIR__ . '/../../../.env');

require_once __DIR__ . '/../../../Datos/config/database.php';
require_once __DIR__ . '/../../../Datos/config/models/Usuario.php';

$database = new Database();
$db = $database->getConnection();
$usuario_model = new Usuario($db);

$base_path = "/PUNTO_VENTA";
$accion = $_GET['accion'] ?? 'listar';

// Obtener ID del usuario con sesión activa
$usuario_sesion_id = Session::get('user_id');

switch ($accion) {
    case 'listar':
        require_once __DIR__ . '/../../../Presentacion/view/usuarios/listar.php';
        break;
        
    case 'crear':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // VALIDACIÓN: Verificar si el usuario ya existe
            $usuario_existe = false;
            $query_check = "SELECT id FROM usuarios WHERE usuario = :usuario";
            $stmt_check = $db->prepare($query_check);
            $stmt_check->bindParam(':usuario', $_POST['usuario']);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() > 0) {
                $usuario_existe = true;
            }
            
            if ($usuario_existe) {
                $error = "Ya existe un usuario con el nombre '" . htmlspecialchars($_POST['usuario']) . "'";
                require_once __DIR__ . '/../../../Presentacion/view/usuarios/crear.php';
            } else {
                $usuario_model->nombre = $_POST['nombre'];
                $usuario_model->usuario = $_POST['usuario'];
                $usuario_model->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $usuario_model->rol = $_POST['rol'];
                $usuario_model->estado = intval($_POST['estado'] ?? 1);
                
                if ($usuario_model->create()) {
                    header("Location: " . $base_path . "/index.php?page=usuarios&mensaje=Usuario+creado+correctamente");
                    exit();
                } else {
                    $error = "Error al crear el usuario";
                    require_once __DIR__ . '/../../../Presentacion/view/usuarios/crear.php';
                }
            }
        } else {
            require_once __DIR__ . '/../../../Presentacion/view/usuarios/crear.php';
        }
        break;
        
    case 'editar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_editar = intval($_POST['id']);
            $nuevo_estado = intval($_POST['estado'] ?? 1);
            
            // VALIDACIÓN: Verificar si el usuario ya existe en OTRO usuario
            $usuario_existe = false;
            $query_check = "SELECT id FROM usuarios WHERE usuario = :usuario AND id != :id";
            $stmt_check = $db->prepare($query_check);
            $stmt_check->bindParam(':usuario', $_POST['usuario']);
            $stmt_check->bindParam(':id', $id_editar, PDO::PARAM_INT);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() > 0) {
                $usuario_existe = true;
            }
            
            if ($usuario_existe) {
                $error = "Ya existe otro usuario con el nombre '" . htmlspecialchars($_POST['usuario']) . "'";
                $usuario_model->id = $id_editar;
                $usuario_model->readOne();
                require_once __DIR__ . '/../../../Presentacion/view/usuarios/editar.php';
            } else {
                $usuario_model->id = $id_editar;
                $usuario_model->nombre = $_POST['nombre'];
                $usuario_model->usuario = $_POST['usuario'];
                $usuario_model->rol = $_POST['rol'];
                $usuario_model->estado = $nuevo_estado;
                
                // Si cambió la contraseña, actualizarla
                if (!empty($_POST['password'])) {
                    $usuario_model->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                }
                
                if ($usuario_model->update()) {
                    // VERIFICAR: Si el usuario se deshabilitó a sí mismo, cerrar sesión
                    if ($id_editar == $usuario_sesion_id && $nuevo_estado == 0) {
                        Session::destroy();
                        header("Location: " . $base_path . "/index.php?page=login&mensaje=Tu+cuenta+ha+sido+deshabilitada");
                        exit();
                    }
                    
                    header("Location: " . $base_path . "/index.php?page=usuarios&mensaje=Usuario+actualizado+correctamente");
                    exit();
                } else {
                    $error = "Error al actualizar el usuario";
                    $usuario_model->readOne();
                    require_once __DIR__ . '/../../../Presentacion/view/usuarios/editar.php';
                }
            }
        } else {
            $usuario_model->id = intval($_GET['id'] ?? 0);
            $usuario_model->readOne();
            require_once __DIR__ . '/../../../Presentacion/view/usuarios/editar.php';
        }
        break;
        
    case 'eliminar':
        $id_eliminar = intval($_GET['id'] ?? 0);
        
        if ($usuario_model->delete($id_eliminar)) {
            // VERIFICAR: Si el usuario se eliminó a sí mismo, cerrar sesión
            if ($id_eliminar == $usuario_sesion_id) {
                Session::destroy();
                header("Location: " . $base_path . "/index.php?page=login&mensaje=Tu+cuenta+ha+sido+eliminada");
                exit();
            }
            
            header("Location: " . $base_path . "/index.php?page=usuarios&mensaje=Usuario+eliminado+correctamente");
            exit();
        } else {
            header("Location: " . $base_path . "/index.php?page=usuarios&error=Error+al+eliminar+usuario");
            exit();
        }
        break;
        
    default:
        require_once __DIR__ . '/../../../Presentacion/view/usuarios/listar.php';
        break;
}
?>