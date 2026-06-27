<?php
class Venta {
    private $conn;
    private $table = 'ventas';
    
    public $id, $fecha, $subtotal, $descuento_total, $total, $estado;
    public $id_usuario, $id_cliente;
    public $nombre_usuario, $nombre_cliente;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT v.*, u.nombre AS nombre_usuario, c.nombre AS nombre_cliente 
                  FROM " . $this->table . " v
                  INNER JOIN usuarios u ON v.id_usuario = u.id
                  LEFT JOIN clientes c ON v.id_cliente = c.id
                  WHERE v.estado = 1 
                  ORDER BY v.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT v.*, u.nombre AS nombre_usuario, c.nombre AS nombre_cliente 
                  FROM " . $this->table . " v
                  INNER JOIN usuarios u ON v.id_usuario = u.id
                  LEFT JOIN clientes c ON v.id_cliente = c.id
                  WHERE v.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $this->fecha = $row['fecha'];
            $this->subtotal = $row['subtotal'];
            $this->descuento_total = $row['descuento_total'];
            $this->total = $row['total'];
            $this->estado = $row['estado'];
            $this->id_usuario = $row['id_usuario'];
            $this->id_cliente = $row['id_cliente'];
            $this->nombre_usuario = $row['nombre_usuario'];
            $this->nombre_cliente = $row['nombre_cliente'] ?? null;
        }
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (fecha, subtotal, descuento_total, total, estado, id_usuario, id_cliente) 
                  VALUES (:fecha, :subtotal, :descuento_total, :total, :estado, :id_usuario, :id_cliente)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':fecha', $this->fecha);
        $stmt->bindParam(':subtotal', $this->subtotal);
        $stmt->bindParam(':descuento_total', $this->descuento_total);
        $stmt->bindParam(':total', $this->total);
        $stmt->bindParam(':estado', $this->estado, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $this->id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':id_cliente', $this->id_cliente, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "UPDATE " . $this->table . " SET estado = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>