<?php
session_start();

$page = $_GET['page'] ?? 'dashboard';

// Si NO está logueado, mandarlo al login
if (!isset($_SESSION['user_id'])) {
    if ($page !== 'login') {
        header("Location: login.php");
        exit();
    }
}

// Si YA está logueado y va al login, mandarlo al dashboard
if (isset($_SESSION['user_id']) && $page === 'login') {
    header("Location: index.php?page=dashboard");
    exit();
}

// Validación de permisos
if (isset($_SESSION['user_id']) && $page !== 'dashboard' && $page !== 'logout') {
    $rol = $_SESSION['user_rol'] ?? 'CAJERO';
    
    $modulo = strtolower(str_replace('Controller', '', $page));
    
    $mapeo_modulos = [
        'usuario' => 'usuarios',
        'usuarios' => 'usuarios',
        'cliente' => 'clientes',
        'clientes' => 'clientes',
        'categoria' => 'categorias',
        'categorias' => 'categorias',
        'marca' => 'marcas',
        'marcas' => 'marcas',
        'descuento' => 'descuentos',
        'descuentos' => 'descuentos',
        'producto' => 'productos',
        'productos' => 'productos',
        'venta' => 'ventas',
        'ventas' => 'ventas',
        'reporte' => 'reportes',
        'reportes' => 'reportes'
    ];
    
    $modulo_normalizado = $mapeo_modulos[$modulo] ?? $modulo;
    
    $accesos = [
        'ADMINISTRADOR' => ['usuarios', 'clientes', 'categorias', 'marcas', 'descuentos', 'productos', 'ventas', 'reportes'],
        'SUPERVISOR'    => ['clientes', 'categorias', 'marcas', 'descuentos', 'productos', 'ventas', 'reportes'],
        'CAJERO'        => ['clientes', 'productos', 'ventas']
    ];
    
    if (!in_array($modulo_normalizado, $accesos[$rol] ?? [])) {
        header("Location: index.php?page=dashboard");
        exit();
    }
}

switch ($page) {
    case 'login':
        require_once 'login.php';
        break;
        
    case 'dashboard':
        require_once 'Presentacion/view/dashboard.php';
        break;
        
    case 'usuarios':
    case 'usuarioController':
        require_once 'Negocio/helpers/controllers/usuarioController.php';
        break;
        
    case 'clientes':
    case 'clienteController':
        require_once 'Negocio/helpers/controllers/clienteController.php';
        break;
        
    case 'categorias':
    case 'categoriaController':
        require_once 'Negocio/helpers/controllers/categoriaController.php';
        break;
        
    case 'marcas':
    case 'marcaController':
        require_once 'Negocio/helpers/controllers/marcaController.php';
        break;
        
    case 'descuentos':
    case 'descuentoController':
        require_once 'Negocio/helpers/controllers/descuentoController.php';
        break;
        
    case 'productos':
    case 'productosController':
        require_once 'Negocio/helpers/controllers/productoController.php';
        break;
        
    case 'ventas':
    case 'ventasController':
        require_once 'Negocio/helpers/controllers/ventaController.php';
        break;
        
    case 'reportes':
    case 'reportesController':
        require_once 'Negocio/helpers/controllers/reporteController.php';
        break;
        
    case 'logout':
        require_once 'logout.php';
        break;
        
    default:
        if (isset($_SESSION['user_id'])) {
            require_once 'Presentacion/view/dashboard.php';
        } else {
            header("Location: login.php");
            exit();
        }
        break;
}
?>