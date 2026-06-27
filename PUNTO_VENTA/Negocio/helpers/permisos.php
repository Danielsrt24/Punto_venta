<?php
function tienePermiso($rol, $modulo) {
    // Normalizar: quitar 'Controller' (sin importar mayúsculas) y convertir a minúsculas
    $modulo_limpio = strtolower(preg_replace('/controller$/i', '', $modulo));
    
    $permisos = [
        'ADMINISTRADOR' => ['dashboard', 'usuarios', 'clientes', 'categorias', 'marcas', 'productos', 'ventas', 'reportes'],
        'SUPERVISOR'    => ['dashboard', 'clientes', 'categorias', 'marcas', 'productos', 'ventas', 'reportes'],
        'CAJERO'        => ['dashboard', 'clientes', 'productos', 'ventas']
    ];
    
    if (!isset($permisos[$rol])) {
        return false;
    }
    
    return in_array($modulo_limpio, $permisos[$rol]);
}

function puedeEliminar($rol, $modulo) {
    // ADMINISTRADOR puede eliminar en todo
    if ($rol === 'ADMINISTRADOR') return true;
    
    // SUPERVISOR puede eliminar en todo excepto usuarios
    if ($rol === 'SUPERVISOR') return $modulo !== 'usuarios';
    
    // CAJERO NO puede eliminar en clientes ni ventas
    if ($rol === 'CAJERO') {
        if (in_array($modulo, ['clientes', 'ventas'])) return false;
        return true;
    }
    
    return false;
}

function puedeCrearEditar($rol, $modulo) {
    // ADMINISTRADOR puede crear/editar en todo
    if ($rol === 'ADMINISTRADOR') return true;
    
    // SUPERVISOR puede crear/editar en todo excepto usuarios
    if ($rol === 'SUPERVISOR') return $modulo !== 'usuarios';
    
    // CAJERO NO puede crear/editar en productos
    if ($rol === 'CAJERO') {
        if ($modulo === 'productos') return false;
        return true;
    }
    
    return false;
}

function obtenerModuloBase($page) {
    return strtolower(preg_replace('/controller$/i', '', $page));
}
?>