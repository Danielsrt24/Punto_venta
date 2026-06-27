<?php
require_once __DIR__ . '/../session.php';
Session::init();

require_once __DIR__ . '/../env.php';
cargarEntorno(__DIR__ . '/../../../.env');

require_once __DIR__ . '/../../../Datos/config/database.php';
require_once __DIR__ . '/../../../Datos/config/models/Marca.php';

$database = new Database();
$db = $database->getConnection();
$marca = new Marca($db);

$base_path = "/PUNTO_VENTA";
$accion = $_GET['accion'] ?? 'listar';

switch ($accion) {
    case 'listar':
        require_once __DIR__ . '/../../../Presentacion/view/marcas/listar.php';
        break;
        
    case 'crear':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $marca->nombre = $_POST['nombre'];
            $marca->descripcion = $_POST['descripcion'] ?? '';
            $marca->estado = $_POST['estado'] ?? 1;
            
            if ($marca->create()) {
                header("Location: " . $base_path . "/index.php?page=marcas&mensaje=Marca+creada+correctamente");
                exit();
            } else {
                $error = "Error al crear la marca";
                require_once __DIR__ . '/../../../Presentacion/view/marcas/crear.php';
            }
        } else {
            require_once __DIR__ . '/../../../Presentacion/view/marcas/crear.php';
        }
        break;
        
    case 'editar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $marca->id = $_POST['id'];
            $marca->nombre = $_POST['nombre'];
            $marca->descripcion = $_POST['descripcion'] ?? '';
            $marca->estado = $_POST['estado'] ?? 1;
            
            if ($marca->update()) {
                header("Location: " . $base_path . "/index.php?page=marcas&mensaje=Marca+actualizada+correctamente");
                exit();
            } else {
                $error = "Error al actualizar la marca";
                $marca->readOne();
                require_once __DIR__ . '/../../../Presentacion/view/marcas/editar.php';
            }
        } else {
            $marca->id = $_GET['id'] ?? 0;
            $marca->readOne();
            require_once __DIR__ . '/../../../Presentacion/view/marcas/editar.php';
        }
        break;
        
    case 'eliminar':
        $marca->id = $_GET['id'] ?? 0;
        if ($marca->delete()) {
            header("Location: " . $base_path . "/index.php?page=marcas&mensaje=Marca+eliminada+correctamente");
            exit();
        } else {
            header("Location: " . $base_path . "/index.php?page=marcas&error=Error+al+eliminar+marca");
            exit();
        }
        break;
        
    default:
        require_once __DIR__ . '/../../../Presentacion/view/marcas/listar.php';
        break;
}
?>