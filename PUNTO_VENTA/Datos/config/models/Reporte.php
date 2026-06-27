<?php
class Reporte {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Reporte de productos con bajo stock
    public function productosBajoStock($limite = 10) {
        $query = "SELECT p.*, c.nombre AS nombre_categoria 
                  FROM productos p
                  INNER JOIN categorias c ON p.id_categoria = c.id
                  WHERE p.estado = 1 AND p.stock <= :limite
                  ORDER BY p.stock ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Reporte de ventas por fecha (con filtro opcional por usuario)
    public function ventasPorFecha($fecha_inicio, $fecha_fin, $usuario_id = null) {
        $query = "SELECT v.*, u.nombre AS nombre_usuario, c.nombre AS nombre_cliente 
                  FROM ventas v
                  INNER JOIN usuarios u ON v.id_usuario = u.id
                  LEFT JOIN clientes c ON v.id_cliente = c.id
                  WHERE v.estado = 1 
                  AND DATE(v.fecha) BETWEEN :fecha_inicio AND :fecha_fin";
        
        if ($usuario_id !== null && $usuario_id !== '') {
            $query .= " AND v.id_usuario = :usuario_id";
        }
        
        $query .= " ORDER BY v.fecha DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_fin', $fecha_fin);
        
        if ($usuario_id !== null && $usuario_id !== '') {
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt;
    }

    // Total vendido por fecha (con filtro opcional por usuario)
    public function totalVendidoPorFecha($fecha_inicio, $fecha_fin, $usuario_id = null) {
        $query = "SELECT 
                    DATE(fecha) AS fecha,
                    COUNT(*) AS cantidad_ventas,
                    SUM(subtotal) AS total_subtotal,
                    SUM(descuento_total) AS total_descuentos,
                    SUM(total) AS total_vendido
                  FROM ventas
                  WHERE estado = 1 
                  AND DATE(fecha) BETWEEN :fecha_inicio AND :fecha_fin";
        
        if ($usuario_id !== null && $usuario_id !== '') {
            $query .= " AND id_usuario = :usuario_id";
        }
        
        $query .= " GROUP BY DATE(fecha) ORDER BY fecha DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_fin', $fecha_fin);
        
        if ($usuario_id !== null && $usuario_id !== '') {
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt;
    }

    // Productos más vendidos
    public function productosMasVendidos($limite = 10) {
        $query = "SELECT 
                    p.nombre AS nombre_producto,
                    p.codigo,
                    c.nombre AS nombre_categoria,
                    SUM(dv.cantidad) AS total_vendido,
                    SUM(dv.subtotal) AS total_ingresos
                  FROM detalle_venta dv
                  INNER JOIN productos p ON dv.id_producto = p.id
                  INNER JOIN categorias c ON p.id_categoria = c.id
                  INNER JOIN ventas v ON dv.id_venta = v.id
                  WHERE v.estado = 1
                  GROUP BY p.id
                  ORDER BY total_vendido DESC
                  LIMIT :limite";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Resumen general
    public function resumenGeneral() {
        $stmt1 = $this->conn->query("SELECT COUNT(*) AS total FROM productos WHERE estado = 1");
        $total_productos = $stmt1->fetch(PDO::FETCH_ASSOC)['total'];

        $stmt2 = $this->conn->query("SELECT COUNT(*) AS total FROM clientes WHERE estado = 1");
        $total_clientes = $stmt2->fetch(PDO::FETCH_ASSOC)['total'];

        $stmt3 = $this->conn->query("SELECT COUNT(*) AS total FROM ventas WHERE estado = 1");
        $total_ventas = $stmt3->fetch(PDO::FETCH_ASSOC)['total'];

        $stmt4 = $this->conn->query("SELECT SUM(total) AS total FROM ventas WHERE estado = 1");
        $total_vendido = $stmt4->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        $stmt5 = $this->conn->query("SELECT COUNT(*) AS total FROM productos WHERE estado = 1 AND stock <= 10");
        $bajo_stock = $stmt5->fetch(PDO::FETCH_ASSOC)['total'];

        return [
            'total_productos' => $total_productos,
            'total_clientes' => $total_clientes,
            'total_ventas' => $total_ventas,
            'total_vendido' => $total_vendido,
            'bajo_stock' => $bajo_stock
        ];
    }

    // Obtener todos los CAJEROS (para el select de filtro)
    public function obtenerUsuarios() {
        $query = "SELECT id, nombre, rol FROM usuarios WHERE estado = 1 AND rol = 'CAJERO' ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Total vendido por CAJERO en un rango de fechas
    public function totalPorUsuario($fecha_inicio, $fecha_fin) {
        $query = "SELECT 
                    u.id,
                    u.nombre,
                    u.rol,
                    COUNT(v.id) AS cantidad_ventas,
                    COALESCE(SUM(v.total), 0) AS total_vendido
                  FROM usuarios u
                  LEFT JOIN ventas v ON u.id = v.id_usuario 
                    AND v.estado = 1 
                    AND DATE(v.fecha) BETWEEN :fecha_inicio AND :fecha_fin
                  WHERE u.estado = 1 
                  AND u.rol = 'CAJERO'
                  GROUP BY u.id
                  ORDER BY total_vendido DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_fin', $fecha_fin);
        $stmt->execute();
        return $stmt;
    }
}
?>