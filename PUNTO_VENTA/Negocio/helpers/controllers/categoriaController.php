<?php
require_once __DIR__ . '/../session.php';
Session::init();

require_once __DIR__ . '/../env.php';
cargarEntorno(__DIR__ . '/../../../.env');

require_once __DIR__ . '/../../../Datos/config/database.php';
require_once __DIR__ . '/../../../Datos/config/models/Categoria.php';

$database = new Database();
$db = $database->getConnection();
$categoria_model = new Categoria($db);

$base_path = "/PUNTO_VENTA";
$accion = $_GET['accion'] ?? 'listar';

switch ($accion) {
    case 'listar':
    default:
        require_once __DIR__ . '/../../../Presentacion/view/categorias/listar.php';
        break;
        
    case 'crear':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $query_check = "SELECT id FROM categorias WHERE nombre = :nombre";
            $stmt_check = $db->prepare($query_check);
            $stmt_check->bindParam(':nombre', $_POST['nombre']);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() > 0) {
                $error = "Ya existe una categoría con ese nombre";
                require_once __DIR__ . '/../../../Presentacion/view/categorias/crear.php';
            } else {
                $categoria_model->nombre = $_POST['nombre'];
                $categoria_model->descripcion = $_POST['descripcion'] ?? '';
                $categoria_model->estado = 1; // Por defecto activo
                
                if ($categoria_model->create()) {
                    header("Location: " . $base_path . "/index.php?page=categorias&mensaje=Categoria+creada+correctamente");
                    exit();
                } else {
                    $error = "Error al crear la categoría";
                    require_once __DIR__ . '/../../../Presentacion/view/categorias/crear.php';
                }
            }
        } else {
            require_once __DIR__ . '/../../../Presentacion/view/categorias/crear.php';
        }
        break;
        
    case 'editar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $query_check = "SELECT id FROM categorias WHERE nombre = :nombre AND id != :id";
            $stmt_check = $db->prepare($query_check);
            $stmt_check->bindParam(':nombre', $_POST['nombre']);
            $stmt_check->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() > 0) {
                $error = "Ya existe otra categoría con ese nombre";
                $categoria_model->id = intval($_POST['id']);
                $categoria_model->readOne();
                require_once __DIR__ . '/../../../Presentacion/view/categorias/editar.php';
            } else {
                $categoria_model->id = intval($_POST['id']);
                $categoria_model->nombre = $_POST['nombre'];
                $categoria_model->descripcion = $_POST['descripcion'] ?? '';
                $categoria_model->estado = intval($_POST['estado'] ?? 1);
                
                if ($categoria_model->update()) {
                    header("Location: " . $base_path . "/index.php?page=categorias&mensaje=Categoria+actualizada+correctamente");
                    exit();
                } else {
                    $error = "Error al actualizar la categoría";
                    $categoria_model->readOne();
                    require_once __DIR__ . '/../../../Presentacion/view/categorias/editar.php';
                }
            }
        } else {
            $categoria_model->id = intval($_GET['id'] ?? 0);
            if ($categoria_model->readOne()) {
                require_once __DIR__ . '/../../../Presentacion/view/categorias/editar.php';
            } else {
                header("Location: " . $base_path . "/index.php?page=categorias&error=Categoria+no+encontrada");
                exit();
            }
        }
        break;
        
    case 'eliminar':
        $categoria_model->id = intval($_GET['id'] ?? 0);
        if ($categoria_model->delete()) {
            header("Location: " . $base_path . "/index.php?page=categorias&mensaje=Categoria+ocultada+correctamente");
            exit();
        } else {
            header("Location: " . $base_path . "/index.php?page=categorias&error=Error+al+ocultar+categoria");
            exit();
        }
        break;
}
?>