<?php
class Marca {
    private $conn;
    private $table = 'marcas';
    
    public $id, $nombre, $descripcion, $estado;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll($filtro = '') {
        $query = "SELECT id, nombre, descripcion, estado 
                  FROM " . $this->table . " 
                  WHERE estado = 1";
        
        if (!empty($filtro)) {
            $query .= " AND (nombre LIKE :filtro OR descripcion LIKE :filtro2)";
        }
        
        $query .= " ORDER BY nombre ASC";
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($filtro)) {
            $filtro_like = "%{$filtro}%";
            $stmt->bindParam(':filtro', $filtro_like);
            $stmt->bindParam(':filtro2', $filtro_like);
        }
        
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
            $this->descripcion = $row['descripcion'];
            $this->estado = $row['estado'];
        }
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, descripcion, estado) 
                  VALUES (:nombre, :descripcion, :estado)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':estado', $this->estado, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET nombre = :nombre, descripcion = :descripcion, estado = :estado 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':estado', $this->estado, PDO::PARAM_INT);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function delete() {
        $query = "UPDATE " . $this->table . " SET estado = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>