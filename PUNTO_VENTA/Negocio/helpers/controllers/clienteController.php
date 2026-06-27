<?php
require_once __DIR__ . '/../session.php';
Session::init();

require_once __DIR__ . '/../env.php';
cargarEntorno(__DIR__ . '/../../../.env');

require_once __DIR__ . '/../../../Datos/config/database.php';
require_once __DIR__ . '/../../../Datos/config/models/Cliente.php';

$database = new Database();
$db = $database->getConnection();
$cliente_model = new Cliente($db);

$base_path = "/PUNTO_VENTA";
$accion = $_GET['accion'] ?? 'listar';

switch ($accion) {
    case 'listar':
    default:
        require_once __DIR__ . '/../../../Presentacion/view/clientes/listar.php';
        break;
        
    case 'crear':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // VALIDACIÓN: Verificar si el DUI ya existe
            $dui_existe = false;
            if (!empty($_POST['DUI'])) {
                $query_check = "SELECT id FROM clientes WHERE DUI = :dui";
                $stmt_check = $db->prepare($query_check);
                $stmt_check->bindParam(':dui', $_POST['DUI']);
                $stmt_check->execute();
                
                if ($stmt_check->rowCount() > 0) {
                    $dui_existe = true;
                }
            }
            
            if ($dui_existe) {
                $error = "Ya existe un cliente registrado con el DUI '" . htmlspecialchars($_POST['DUI']) . "'";
                require_once __DIR__ . '/../../../Presentacion/view/clientes/crear.php';
            } else {
                $cliente_model->nombre = $_POST['nombre'];
                $cliente_model->DUI = $_POST['DUI'] ?? null;
                $cliente_model->telefono = $_POST['telefono'] ?? null;
                $cliente_model->email = $_POST['email'] ?? null;
                $cliente_model->NIT = $_POST['NIT'] ?? null;
                $cliente_model->tipo_cliente = $_POST['tipo_cliente'] ?? 'NATURAL';
                $cliente_model->direccion = $_POST['direccion'] ?? null;
                $cliente_model->estado = intval($_POST['estado'] ?? 1);
                
                if ($cliente_model->create()) {
                    header("Location: " . $base_path . "/index.php?page=clientes&mensaje=Cliente+creado+correctamente");
                    exit();
                } else {
                    $error = "Error al crear el cliente";
                    require_once __DIR__ . '/../../../Presentacion/view/clientes/crear.php';
                }
            }
        } else {
            require_once __DIR__ . '/../../../Presentacion/view/clientes/crear.php';
        }
        break;
        
    case 'editar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // VALIDACIÓN: Verificar si el DUI ya existe en OTRO cliente
            $dui_existe = false;
            if (!empty($_POST['DUI'])) {
                $query_check = "SELECT id FROM clientes WHERE DUI = :dui AND id != :id";
                $stmt_check = $db->prepare($query_check);
                $stmt_check->bindParam(':dui', $_POST['DUI']);
                $stmt_check->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
                $stmt_check->execute();
                
                if ($stmt_check->rowCount() > 0) {
                    $dui_existe = true;
                }
            }
            
            if ($dui_existe) {
                $error = "Ya existe otro cliente registrado con el DUI '" . htmlspecialchars($_POST['DUI']) . "'";
                $cliente_model->id = intval($_POST['id']);
                $cliente_model->readOne();
                require_once __DIR__ . '/../../../Presentacion/view/clientes/editar.php';
            } else {
                $cliente_model->id = intval($_POST['id']);
                $cliente_model->nombre = $_POST['nombre'];
                $cliente_model->DUI = $_POST['DUI'] ?? null;
                $cliente_model->telefono = $_POST['telefono'] ?? null;
                $cliente_model->email = $_POST['email'] ?? null;
                $cliente_model->NIT = $_POST['NIT'] ?? null;
                $cliente_model->tipo_cliente = $_POST['tipo_cliente'] ?? 'NATURAL';
                $cliente_model->direccion = $_POST['direccion'] ?? null;
                $cliente_model->estado = intval($_POST['estado'] ?? 1);
                
                if ($cliente_model->update()) {
                    header("Location: " . $base_path . "/index.php?page=clientes&mensaje=Cliente+actualizado+correctamente");
                    exit();
                } else {
                    $error = "Error al actualizar el cliente";
                    $cliente_model->readOne();
                    require_once __DIR__ . '/../../../Presentacion/view/clientes/editar.php';
                }
            }
        } else {
            $cliente_model->id = intval($_GET['id'] ?? 0);
            if ($cliente_model->readOne()) {
                require_once __DIR__ . '/../../../Presentacion/view/clientes/editar.php';
            } else {
                header("Location: " . $base_path . "/index.php?page=clientes&error=Cliente+no+encontrado");
                exit();
            }
        }
        break;
        
    case 'eliminar':
        $cliente_model->id = intval($_GET['id'] ?? 0);
        if ($cliente_model->delete()) {
            header("Location: " . $base_path . "/index.php?page=clientes&mensaje=Cliente+eliminado+correctamente");
            exit();
        } else {
            header("Location: " . $base_path . "/index.php?page=clientes&error=Error+al+eliminar+cliente");
            exit();
        }
        break;
}
?>