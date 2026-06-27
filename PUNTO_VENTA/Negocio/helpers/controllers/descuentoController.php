<?php
require_once __DIR__ . '/../session.php';
Session::init();

require_once __DIR__ . '/../env.php';
cargarEntorno(__DIR__ . '/../../../.env');

require_once __DIR__ . '/../../../Datos/config/database.php';
require_once __DIR__ . '/../../../Datos/config/models/Descuento.php';
require_once __DIR__ . '/../../../Datos/config/models/Producto.php';

$database = new Database();
$db = $database->getConnection();
$descuento_model = new Descuento($db);
$producto_model = new Producto($db);

$base_path = "/PUNTO_VENTA";
$accion = $_GET['accion'] ?? 'listar';

switch ($accion) {
    case 'listar':
    default:
        require_once __DIR__ . '/../../../Presentacion/view/descuentos/listar.php';
        break;
        
    case 'crear':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $descuento_model->nombre = $_POST['nombre'];
            $descuento_model->tipo = $_POST['tipo'];
            $descuento_model->valor = $_POST['valor'];
            $descuento_model->descripcion = $_POST['descripcion'] ?? '';
            $descuento_model->fecha_inicio = $_POST['fecha_inicio'];
            $descuento_model->fecha_fin = $_POST['fecha_fin'];
            $descuento_model->estado = intval($_POST['estado'] ?? 1);
            
            if ($descuento_model->create()) {
                header("Location: " . $base_path . "/index.php?page=descuentos&mensaje=Descuento+creado+correctamente");
                exit();
            } else {
                $error = "Error al crear el descuento";
                require_once __DIR__ . '/../../../Presentacion/view/descuentos/crear.php';
            }
        } else {
            require_once __DIR__ . '/../../../Presentacion/view/descuentos/crear.php';
        }
        break;
        
    case 'editar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $descuento_model->id = intval($_POST['id']);
            $descuento_model->nombre = $_POST['nombre'];
            $descuento_model->tipo = $_POST['tipo'];
            $descuento_model->valor = $_POST['valor'];
            $descuento_model->descripcion = $_POST['descripcion'] ?? '';
            $descuento_model->fecha_inicio = $_POST['fecha_inicio'];
            $descuento_model->fecha_fin = $_POST['fecha_fin'];
            $descuento_model->estado = intval($_POST['estado'] ?? 1);
            
            if ($descuento_model->update()) {
                header("Location: " . $base_path . "/index.php?page=descuentos&mensaje=Descuento+actualizado+correctamente");
                exit();
            } else {
                $error = "Error al actualizar el descuento";
                $descuento_model->id = intval($_POST['id']);
                $descuento_model->readOne();
                require_once __DIR__ . '/../../../Presentacion/view/descuentos/editar.php';
            }
        } else {
            $id = intval($_GET['id'] ?? 0);
            if ($id > 0) {
                $descuento_model->id = $id;
                if ($descuento_model->readOne()) {
                    // Obtener productos asignados
                    $productos_asignados_raw = $descuento_model->getDescuentosByProducto($id);
                    $ids_asignados = [];
                    while ($pa = $productos_asignados_raw->fetch(PDO::FETCH_ASSOC)) {
                        $ids_asignados[] = $pa['id_producto'];
                    }
                    
                    // Obtener todos los productos
                    $todos_productos = $producto_model->readAll();
                    
                    require_once __DIR__ . '/../../../Presentacion/view/descuentos/editar.php';
                } else {
                    header("Location: " . $base_path . "/index.php?page=descuentos&error=No+se+pudo+cargar+el+descuento");
                    exit();
                }
            } else {
                header("Location: " . $base_path . "/index.php?page=descuentos&error=ID+inválido");
                exit();
            }
        }
        break;
        
    case 'asignar_productos':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_descuento = intval($_POST['id_descuento']);
            $productos_seleccionados = $_POST['productos'] ?? [];
            
            // Eliminar asignaciones anteriores
            $descuento_model->eliminarDescuentosDeProducto($id_descuento);
            
            // Asignar nuevos productos
            if (!empty($productos_seleccionados)) {
                foreach ($productos_seleccionados as $id_producto) {
                    $descuento_model->asignarAProducto(intval($id_producto), $id_descuento);
                }
            }
            
            header("Location: " . $base_path . "/index.php?page=descuentos&mensaje=Productos+asignados+correctamente");
            exit();
        }
        break;
        
    case 'eliminar':
        $descuento_model->id = intval($_GET['id'] ?? 0);
        if ($descuento_model->delete()) {
            header("Location: " . $base_path . "/index.php?page=descuentos&mensaje=Descuento+ocultado+correctamente");
            exit();
        } else {
            header("Location: " . $base_path . "/index.php?page=descuentos&error=Error+al+ocultar+descuento");
            exit();
        }
        break;
}
?>