<?php
class Descuento {
    private $conn;
    private $table = 'descuentos';
    
    public $id, $nombre, $tipo, $valor, $descripcion;
    public $fecha_inicio, $fecha_fin, $estado;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll($filtro = '') {
        $query = "SELECT * FROM " . $this->table . " WHERE 1=1";
        
        if (!empty($filtro)) {
            $query .= " AND (nombre LIKE :filtro OR descripcion LIKE :filtro2)";
        }
        
        $query .= " ORDER BY id DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($filtro)) {
            $filtro_like = "%{$filtro}%";
            $stmt->bindParam(':filtro', $filtro_like);
            $stmt->bindParam(':filtro2', $filtro_like);
        }
        
        $stmt->execute();
        return $stmt;
    }

    public function readActivos() {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE estado = 1 
                  AND fecha_inicio <= CURDATE() 
                  AND fecha_fin >= CURDATE()
                  ORDER BY id DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $this->nombre = $row['nombre'];
            $this->tipo = $row['tipo'];
            $this->valor = $row['valor'];
            $this->descripcion = $row['descripcion'];
            $this->fecha_inicio = $row['fecha_inicio'];
            $this->fecha_fin = $row['fecha_fin'];
            $this->estado = $row['estado'];
            return true;
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, tipo, valor, descripcion, fecha_inicio, fecha_fin, estado) 
                  VALUES (:nombre, :tipo, :valor, :descripcion, :fecha_inicio, :fecha_fin, :estado)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':tipo', $this->tipo);
        $stmt->bindParam(':valor', $this->valor);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':fecha_inicio', $this->fecha_inicio);
        $stmt->bindParam(':fecha_fin', $this->fecha_fin);
        $stmt->bindParam(':estado', $this->estado, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET nombre = :nombre, tipo = :tipo, valor = :valor, 
                      descripcion = :descripcion, fecha_inicio = :fecha_inicio, 
                      fecha_fin = :fecha_fin, estado = :estado 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':tipo', $this->tipo);
        $stmt->bindParam(':valor', $this->valor);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':fecha_inicio', $this->fecha_inicio);
        $stmt->bindParam(':fecha_fin', $this->fecha_fin);
        $stmt->bindParam(':estado', $this->estado, PDO::PARAM_INT);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function delete() {
        // Borrado lógico: solo cambia estado a 0
        $query = "UPDATE " . $this->table . " SET estado = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Asignar descuento a un producto
    public function asignarAProducto($id_producto, $id_descuento) {
        // Verificar si ya está asignado
        $query_check = "SELECT id FROM producto_descuento 
                        WHERE id_producto = :id_producto AND id_descuento = :id_descuento";
        $stmt_check = $this->conn->prepare($query_check);
        $stmt_check->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
        $stmt_check->bindParam(':id_descuento', $id_descuento, PDO::PARAM_INT);
        $stmt_check->execute();
        
        if ($stmt_check->rowCount() > 0) {
            return true; // Ya está asignado
        }
        
        $query = "INSERT INTO producto_descuento (id_producto, id_descuento) 
                  VALUES (:id_producto, :id_descuento)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
        $stmt->bindParam(':id_descuento', $id_descuento, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Obtener descuentos asignados a un producto
    public function getDescuentosByProducto($id_producto) {
        $query = "SELECT d.* FROM descuentos d
                  INNER JOIN producto_descuento pd ON d.id = pd.id_descuento
                  WHERE pd.id_producto = :id_producto
                  AND d.estado = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Eliminar descuentos asignados a un producto
    public function eliminarDescuentosDeProducto($id_producto) {
        $query = "DELETE FROM producto_descuento WHERE id_producto = :id_producto";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>