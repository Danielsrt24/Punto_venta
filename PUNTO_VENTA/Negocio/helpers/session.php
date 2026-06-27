<?php
class Session {
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function set($clave, $valor) {
        $_SESSION[$clave] = $valor;
    }
    
    public static function get($clave) {
        return isset($_SESSION[$clave]) ? $_SESSION[$clave] : null;
    }
    
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public static function destroy() {
        session_destroy();
    }
}
?>