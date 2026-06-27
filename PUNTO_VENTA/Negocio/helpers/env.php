<?php
function cargarEntorno($archivo) {
    if (!file_exists($archivo)) {
        throw new Exception("El archivo .env no encontró en " . $archivo);
    }
    
    $lineas = file($archivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lineas as $linea) {
        if (strpos(trim($linea), '#') === 0) {
            continue;
        }
        
        list($nombre, $valor) = explode('=', $linea, 2);
        $_ENV[trim($nombre)] = trim($valor);
        putenv(trim($nombre) . '=' . trim($valor));
    }
}
?>