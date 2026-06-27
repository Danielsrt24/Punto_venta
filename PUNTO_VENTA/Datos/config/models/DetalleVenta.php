<?php
class DetalleVenta {
    private $conn;
    private $table = 'detalle_venta';
    
    public $id, $cantidad, $precio_unitario, $precio_total;
    public $descuento, $subtotal, $id_venta, $id_producto;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT dv.*, p.nombre AS nombre_producto 
                  FROM " . $this->table . " dv
                  INNER JOIN productos p ON dv.id_producto = p.id
                  ORDER BY dv.id DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readByVenta($id_venta) {
        $query = "SELECT dv.*, p.nombre AS nombre_producto, p.codigo
                  FROM " . $this->table . " dv
                  INNER JOIN productos p ON dv.id_producto = p.id
                  WHERE dv.id_venta = :id_venta
                  ORDER BY dv.id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_venta', $id_venta, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (cantidad, precio_unitario, precio_total, descuento, subtotal, id_venta, id_producto) 
                  VALUES (:cantidad, :precio_unitario, :precio_total, :descuento, :subtotal, :id_venta, :id_producto)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':cantidad', $this->cantidad, PDO::PARAM_INT);
        $stmt->bindParam(':precio_unitario', $this->precio_unitario);
        $stmt->bindParam(':precio_total', $this->precio_total);
        $stmt->bindParam(':descuento', $this->descuento);
        $stmt->bindParam(':subtotal', $this->subtotal);
        $stmt->bindParam(':id_venta', $this->id_venta, PDO::PARAM_INT);
        $stmt->bindParam(':id_producto', $this->id_producto, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}
?>