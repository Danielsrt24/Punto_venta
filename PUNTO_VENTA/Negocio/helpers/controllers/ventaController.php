<?php
require_once __DIR__ . '/../session.php';
Session::init();

require_once __DIR__ . '/../env.php';
cargarEntorno(__DIR__ . '/../../../.env');

require_once __DIR__ . '/../../../Datos/config/database.php';
require_once __DIR__ . '/../../../Datos/config/models/Venta.php';
require_once __DIR__ . '/../../../Datos/config/models/DetalleVenta.php';
require_once __DIR__ . '/../../../Datos/config/models/Producto.php';
require_once __DIR__ . '/../../../Datos/config/models/Cliente.php';
require_once __DIR__ . '/../../../Datos/config/models/Descuento.php';

$database = new Database();
$db = $database->getConnection();

$venta = new Venta($db);
$detalle_venta = new DetalleVenta($db);
$producto_model = new Producto($db);
$cliente_model = new Cliente($db);
$descuento_model = new Descuento($db);

$base_path = "/PUNTO_VENTA";
$accion = $_GET['accion'] ?? 'listar';

// Función para obtener descuentos de un producto
function obtenerDescuentosProducto($db, $id_producto) {
    $query = "SELECT d.id, d.nombre, d.tipo, d.valor 
              FROM descuentos d
              INNER JOIN producto_descuento pd ON d.id = pd.id_descuento
              WHERE pd.id_producto = :id_producto
              AND d.estado = 1
              AND d.fecha_inicio <= CURDATE()
              AND d.fecha_fin >= CURDATE()";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

switch ($accion) {
    case 'listar':
        require_once __DIR__ . '/../../../Presentacion/view/ventas/listar.php';
        break;
        
    case 'crear':
    $clientes = $cliente_model->readAll();
    
    // Obtener productos con stock > 0 y sus descuentos
    $productos_raw = $producto_model->readAll();
    $productos_con_descuentos = [];
    
    while($prod = $productos_raw->fetch(PDO::FETCH_ASSOC)) {
        if ($prod['stock'] > 0) {
            $descuentos_prod = obtenerDescuentosProducto($db, $prod['id']);
            $prod['descuentos'] = $descuentos_prod;
            $productos_con_descuentos[] = $prod;
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $venta->fecha = date('Y-m-d H:i:s');
        $venta->id_usuario = Session::get('user_id');
        
        // Guardar id_cliente correctamente
        if (!empty($_POST['id_cliente'])) {
            $venta->id_cliente = intval($_POST['id_cliente']);
        } else {
            $venta->id_cliente = null;
        }
        
        $venta->estado = 1;
        
        $productos_carrito = $_POST['producto_id'] ?? [];
        $cantidades = $_POST['cantidad'] ?? [];
        
        // VALIDACIÓN DE STOCK ANTES DE PROCESAR
        $errores_stock = [];
        for ($i = 0; $i < count($productos_carrito); $i++) {
            $id_producto = intval($productos_carrito[$i]);
            $cantidad_solicitada = intval($cantidades[$i]);
            
            $query = "SELECT nombre, stock FROM productos WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id_producto, PDO::PARAM_INT);
            $stmt->execute();
            $prod_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$prod_data) {
                $errores_stock[] = "Producto ID {$id_producto} no encontrado";
                continue;
            }
            
            if ($cantidad_solicitada > $prod_data['stock']) {
                $errores_stock[] = "Stock insuficiente para '{$prod_data['nombre']}': disponible {$prod_data['stock']}, solicitado {$cantidad_solicitada}";
            }
        }
        
        if (!empty($errores_stock)) {
            $error = "No se puede procesar la venta:<br>" . implode('<br>', $errores_stock);
            require_once __DIR__ . '/../../../Presentacion/view/ventas/crear.php';
            break;
        }
        
        $subtotal_general = 0;
        $total_descuentos_productos = 0;
        $detalles_venta = [];
        
        // Calcular totales por producto
        for ($i = 0; $i < count($productos_carrito); $i++) {
            $id_producto = intval($productos_carrito[$i]);
            $cantidad = intval($cantidades[$i]);
            
            // Obtener precio del producto desde la BD
            $query = "SELECT precio FROM productos WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id_producto, PDO::PARAM_INT);
            $stmt->execute();
            $prod_data = $stmt->fetch(PDO::FETCH_ASSOC);
            $precio = floatval($prod_data['precio']);
            
            $subtotal = $precio * $cantidad;
            
            // Calcular descuentos aplicados a este producto
            $descuento_producto = 0;
            $descuentos_prod = obtenerDescuentosProducto($db, $id_producto);
            
            foreach ($descuentos_prod as $desc) {
                $tipo_desc = strtolower($desc['tipo']);
                if ($tipo_desc === 'porcentaje') {
                    $descuento_producto += $subtotal * ($desc['valor'] / 100);
                } else {
                    $descuento_producto += floatval($desc['valor']);
                }
            }
            
            $total_descuentos_productos += $descuento_producto;
            $subtotal_general += $subtotal;
            
            $detalles_venta[] = [
                'id_producto' => $id_producto,
                'cantidad' => $cantidad,
                'precio_unitario' => $precio,
                'subtotal' => $subtotal,
                'descuento' => $descuento_producto
            ];
        }
        
        // Calcular totales finales - AQUÍ ESTABA EL ERROR
        $total_descuentos = $total_descuentos_productos;
        $base_gravada = $subtotal_general - $total_descuentos;
        $iva = $base_gravada * 0.13;
        
        $venta->subtotal = $subtotal_general;
        $venta->descuento_total = $total_descuentos; // ← LÍNEA CLAVE
        $venta->total = $base_gravada + $iva;
        
        if ($venta->create()) {
            $todo_ok = true;
            foreach ($detalles_venta as $detalle) {
                $detalle_venta->id_venta = $venta->id;
                $detalle_venta->id_producto = $detalle['id_producto'];
                $detalle_venta->cantidad = $detalle['cantidad'];
                $detalle_venta->precio_unitario = $detalle['precio_unitario'];
                $detalle_venta->subtotal = $detalle['subtotal'];
                $detalle_venta->precio_total = $detalle['subtotal'];
                $detalle_venta->descuento = $detalle['descuento'];
                
                if (!$detalle_venta->create()) {
                    $todo_ok = false;
                    break;
                }
                
                // Actualizar stock
                $producto_model->id = $detalle['id_producto'];
                $producto_model->readOne();
                $nuevo_stock = $producto_model->stock - $detalle['cantidad'];
                
                $update_stock = $db->prepare("UPDATE productos SET stock = :stock WHERE id = :id");
                $update_stock->execute(['stock' => $nuevo_stock, 'id' => $detalle['id_producto']]);
            }
            
            if ($todo_ok) {
                header("Location: " . $base_path . "/index.php?page=ventas&mensaje=Venta+registrada+correctamente");
                exit();
            }
        }
    }
    
    require_once __DIR__ . '/../../../Presentacion/view/ventas/crear.php';
    break;
        
        require_once __DIR__ . '/../../../Presentacion/view/ventas/crear.php';
        break;
        
    case 'ver':
        $venta->id = intval($_GET['id'] ?? 0);
        $venta->readOne();
        $detalles = $detalle_venta->readByVenta($venta->id);
        require_once __DIR__ . '/../../../Presentacion/view/ventas/ver.php';
        break;
        
    case 'eliminar':
        $venta->id = intval($_GET['id'] ?? 0);
        if ($venta->delete()) {
            header("Location: " . $base_path . "/index.php?page=ventas&mensaje=Venta+eliminada+correctamente");
            exit();
        }
        break;
        
    default:
        require_once __DIR__ . '/../../../Presentacion/view/ventas/listar.php';
        break;
}
?>