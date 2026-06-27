<?php
require_once __DIR__ . '/../session.php';
Session::init();

require_once __DIR__ . '/../env.php';
cargarEntorno(__DIR__ . '/../../../.env');

require_once __DIR__ . '/../../../Datos/config/database.php';

$database = new Database();
$db = $database->getConnection();

$base_path = "/PUNTO_VENTA";

// Filtros
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

// Consulta: Detalle de ventas
$query_detalle = "SELECT v.id, v.fecha, v.subtotal, v.descuento_total, v.total,
                  COALESCE(c.nombre, 'Consumidor Final') as cliente_nombre,
                  u.nombre as usuario_nombre
                  FROM ventas v
                  LEFT JOIN clientes c ON v.id_cliente = c.id
                  INNER JOIN usuarios u ON v.id_usuario = u.id
                  WHERE DATE(v.fecha) BETWEEN :fecha_inicio AND :fecha_fin
                  AND v.estado = 1
                  ORDER BY v.fecha DESC";

$stmt_detalle = $db->prepare($query_detalle);
$stmt_detalle->bindParam(':fecha_inicio', $fecha_inicio);
$stmt_detalle->bindParam(':fecha_fin', $fecha_fin);
$stmt_detalle->execute();
$ventas_detalle = $stmt_detalle;

// Consulta: Ventas por usuario (TODOS los que hicieron ventas)
$query_cajero = "SELECT u.nombre as cajero_nombre, u.usuario,
                 COUNT(v.id) as cantidad_ventas,
                 COALESCE(SUM(v.total), 0) as total_vendido
                 FROM usuarios u
                 LEFT JOIN ventas v ON u.id = v.id_usuario 
                 AND DATE(v.fecha) BETWEEN :fecha_inicio2 AND :fecha_fin2
                 AND v.estado = 1
                 WHERE u.estado = 1
                 GROUP BY u.id, u.nombre, u.usuario
                 ORDER BY total_vendido DESC";

$stmt_cajero = $db->prepare($query_cajero);
$stmt_cajero->bindParam(':fecha_inicio2', $fecha_inicio);
$stmt_cajero->bindParam(':fecha_fin2', $fecha_fin);
$stmt_cajero->execute();
$ventas_cajero = $stmt_cajero;

// Consulta: Resumen por fecha
$query_fecha = "SELECT DATE(v.fecha) as fecha_venta,
                COUNT(v.id) as cantidad_ventas,
                COALESCE(SUM(v.subtotal), 0) as subtotal,
                COALESCE(SUM(v.descuento_total), 0) as descuentos,
                COALESCE(SUM(v.total), 0) as total_vendido
                FROM ventas v
                WHERE DATE(v.fecha) BETWEEN :fecha_inicio3 AND :fecha_fin3
                AND v.estado = 1
                GROUP BY DATE(v.fecha)
                ORDER BY fecha_venta DESC";

$stmt_fecha = $db->prepare($query_fecha);
$stmt_fecha->bindParam(':fecha_inicio3', $fecha_inicio);
$stmt_fecha->bindParam(':fecha_fin3', $fecha_fin);
$stmt_fecha->execute();
$ventas_fecha = $stmt_fecha;

// CORREGIDO: Usar ruta absoluta con __DIR__
require_once __DIR__ . '/../../../Presentacion/view/reportes/index.php';
?>