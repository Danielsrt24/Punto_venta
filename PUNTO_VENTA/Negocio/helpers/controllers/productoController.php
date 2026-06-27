<?php
require_once __DIR__ . '/../session.php';
Session::init();

require_once __DIR__ . '/../env.php';
cargarEntorno(__DIR__ . '/../../../.env');

require_once __DIR__ . '/../../../Datos/config/database.php';
require_once __DIR__ . '/../../../Datos/config/models/Producto.php';
require_once __DIR__ . '/../../../Datos/config/models/Categoria.php';
require_once __DIR__ . '/../../../Datos/config/models/Marca.php';
require_once __DIR__ . '/../../../Datos/config/models/Descuento.php';

$database = new Database();
$db = $database->getConnection();
$producto = new Producto($db);
$categoria_model = new Categoria($db);
$marca_model = new Marca($db);
$descuento_model = new Descuento($db);

$base_path = "/PUNTO_VENTA";
$accion = $_GET['accion'] ?? 'listar';

switch ($accion) {
    case 'listar':
        require_once __DIR__ . '/../../../Presentacion/view/productos/listar.php';
        break;
        
    case 'crear':
        $categorias = $categoria_model->readAll();
        $marcas = $marca_model->readAll();
        $descuentos = $descuento_model->readActivos();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // VALIDACIÓN: Stock no negativo
            $stock = intval($_POST['stock'] ?? 0);
            if ($stock < 0) {
                $error = "El stock no puede ser negativo";
                require_once __DIR__ . '/../../../Presentacion/view/productos/crear.php';
                break;
            }
            
            // VALIDACIÓN: Precio no negativo
            $precio = floatval($_POST['precio'] ?? 0);
            if ($precio < 0) {
                $error = "El precio no puede ser negativo";
                require_once __DIR__ . '/../../../Presentacion/view/productos/crear.php';
                break;
            }
            
            // VALIDACIÓN: Verificar si el código ya existe
            $codigo_existe = false;
            $query_check = "SELECT id FROM productos WHERE codigo = :codigo";
            $stmt_check = $db->prepare($query_check);
            $stmt_check->bindParam(':codigo', $_POST['codigo']);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() > 0) {
                $codigo_existe = true;
            }
            
            if ($codigo_existe) {
                $error = "Ya existe un producto con el código '" . htmlspecialchars($_POST['codigo']) . "'. Use un código diferente.";
                require_once __DIR__ . '/../../../Presentacion/view/productos/crear.php';
            } else {
                $producto->codigo = $_POST['codigo'];
                $producto->nombre = $_POST['nombre'];
                $producto->descripcion = $_POST['descripcion'] ?? '';
                $producto->precio = $precio;
                $producto->stock = $stock;
                $producto->id_categoria = intval($_POST['id_categoria']);
                $producto->id_marca = !empty($_POST['id_marca']) ? intval($_POST['id_marca']) : null;
                $producto->IVA = floatval($_POST['IVA'] ?? 13);
                $producto->estado = intval($_POST['estado'] ?? 1);
                
                if ($producto->create()) {
                    // VERIFICAR que se obtuvo el ID
                    if ($producto->id && !empty($_POST['descuentos'])) {
                        foreach ($_POST['descuentos'] as $id_descuento) {
                            if ($id_descuento) {
                                $descuento_model->asignarAProducto($producto->id, intval($id_descuento));
                            }
                        }
                    }
                    
                    header("Location: " . $base_path . "/index.php?page=productos&mensaje=Producto+creado+correctamente");
                    exit();
                } else {
                    $error = "Error al crear el producto";
                    require_once __DIR__ . '/../../../Presentacion/view/productos/crear.php';
                }
            }
        } else {
            require_once __DIR__ . '/../../../Presentacion/view/productos/crear.php';
        }
        break;
        
    case 'editar':
        $categorias = $categoria_model->readAll();
        $marcas = $marca_model->readAll();
        $descuentos = $descuento_model->readActivos();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // VALIDACIÓN: Stock no negativo
            $stock = intval($_POST['stock'] ?? 0);
            if ($stock < 0) {
                $error = "El stock no puede ser negativo";
                $producto->id = intval($_POST['id']);
                $producto->readOne();
                
                // Obtener descuentos asignados
                $descuentos_asignados = $descuento_model->getDescuentosByProducto($producto->id);
                $ids_descuentos = [];
                while ($desc = $descuentos_asignados->fetch(PDO::FETCH_ASSOC)) {
                    $ids_descuentos[] = $desc['id'];
                }
                
                require_once __DIR__ . '/../../../Presentacion/view/productos/editar.php';
                break;
            }
            
            // VALIDACIÓN: Precio no negativo
            $precio = floatval($_POST['precio'] ?? 0);
            if ($precio < 0) {
                $error = "El precio no puede ser negativo";
                $producto->id = intval($_POST['id']);
                $producto->readOne();
                
                // Obtener descuentos asignados
                $descuentos_asignados = $descuento_model->getDescuentosByProducto($producto->id);
                $ids_descuentos = [];
                while ($desc = $descuentos_asignados->fetch(PDO::FETCH_ASSOC)) {
                    $ids_descuentos[] = $desc['id'];
                }
                
                require_once __DIR__ . '/../../../Presentacion/view/productos/editar.php';
                break;
            }
            
            // VALIDACIÓN: Verificar si el código ya existe en OTRO producto
            $codigo_existe = false;
            $query_check = "SELECT id FROM productos WHERE codigo = :codigo AND id != :id";
            $stmt_check = $db->prepare($query_check);
            $stmt_check->bindParam(':codigo', $_POST['codigo']);
            $stmt_check->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() > 0) {
                $codigo_existe = true;
            }
            
            if ($codigo_existe) {
                $error = "Ya existe otro producto con el código '" . htmlspecialchars($_POST['codigo']) . "'. Use un código diferente.";
                $producto->id = intval($_POST['id']);
                $producto->readOne();
                
                // Obtener descuentos asignados
                $descuentos_asignados = $descuento_model->getDescuentosByProducto($producto->id);
                $ids_descuentos = [];
                while ($desc = $descuentos_asignados->fetch(PDO::FETCH_ASSOC)) {
                    $ids_descuentos[] = $desc['id'];
                }
                
                require_once __DIR__ . '/../../../Presentacion/view/productos/editar.php';
            } else {
                $producto->id = intval($_POST['id']);
                $producto->codigo = $_POST['codigo'];
                $producto->nombre = $_POST['nombre'];
                $producto->descripcion = $_POST['descripcion'] ?? '';
                $producto->precio = $precio;
                $producto->stock = $stock;
                $producto->id_categoria = intval($_POST['id_categoria']);
                $producto->id_marca = !empty($_POST['id_marca']) ? intval($_POST['id_marca']) : null;
                $producto->IVA = floatval($_POST['IVA'] ?? 13);
                $producto->estado = intval($_POST['estado'] ?? 1);
                
                if ($producto->update()) {
                    // Eliminar descuentos anteriores
                    $query = "DELETE FROM producto_descuento WHERE id_producto = :id";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':id', $producto->id, PDO::PARAM_INT);
                    $stmt->execute();
                    
                    // Asignar nuevos descuentos
                    if (!empty($_POST['descuentos'])) {
                        foreach ($_POST['descuentos'] as $id_descuento) {
                            if ($id_descuento) {
                                $descuento_model->asignarAProducto($producto->id, intval($id_descuento));
                            }
                        }
                    }
                    
                    header("Location: " . $base_path . "/index.php?page=productos&mensaje=Producto+actualizado+correctamente");
                    exit();
                } else {
                    $error = "Error al actualizar el producto";
                    $producto->readOne();
                    
                    // Obtener descuentos asignados
                    $descuentos_asignados = $descuento_model->getDescuentosByProducto($producto->id);
                    $ids_descuentos = [];
                    while ($desc = $descuentos_asignados->fetch(PDO::FETCH_ASSOC)) {
                        $ids_descuentos[] = $desc['id'];
                    }
                    
                    require_once __DIR__ . '/../../../Presentacion/view/productos/editar.php';
                }
            }
        } else {
            $producto->id = intval($_GET['id'] ?? 0);
            $producto->readOne();
            
            // Obtener descuentos asignados al producto
            $descuentos_asignados = $descuento_model->getDescuentosByProducto($producto->id);
            $ids_descuentos = [];
            while ($desc = $descuentos_asignados->fetch(PDO::FETCH_ASSOC)) {
                $ids_descuentos[] = $desc['id'];
            }
        }
        require_once __DIR__ . '/../../../Presentacion/view/productos/editar.php';
        break;
        
    case 'eliminar':
        $producto->id = intval($_GET['id'] ?? 0);
        if ($producto->delete()) {
            // Eliminar relaciones con descuentos
            $query = "DELETE FROM producto_descuento WHERE id_producto = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $producto->id, PDO::PARAM_INT);
            $stmt->execute();
            
            header("Location: " . $base_path . "/index.php?page=productos&mensaje=Producto+eliminado+correctamente");
            exit();
        }
        break;
        
    default:
        require_once __DIR__ . '/../../../Presentacion/view/productos/listar.php';
        break;
}
?>