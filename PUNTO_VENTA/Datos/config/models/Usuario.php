<?php
class Usuario {
    private $conn;
    private $table = 'usuarios';
    
    public $id, $nombre, $usuario, $password, $rol, $estado;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll($filtro = '') {
        $query = "SELECT * FROM " . $this->table . " WHERE 1=1";
        
        if (!empty($filtro)) {
            $query .= " AND (nombre LIKE :filtro OR usuario LIKE :filtro2)";
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

    public function readOne() {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $this->nombre = $row['nombre'];
            $this->usuario = $row['usuario'];
            $this->password = $row['password'];
            $this->rol = $row['rol'];
            $this->estado = $row['estado'];
            return true;
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, usuario, password, rol, estado) 
                  VALUES (:nombre, :usuario, :password, :rol, :estado)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':usuario', $this->usuario);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':rol', $this->rol);
        $stmt->bindParam(':estado', $this->estado, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function update() {
        if (!empty($this->password)) {
            $query = "UPDATE " . $this->table . " 
                      SET nombre = :nombre, usuario = :usuario, password = :password, 
                          rol = :rol, estado = :estado 
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':password', $this->password);
        } else {
            $query = "UPDATE " . $this->table . " 
                      SET nombre = :nombre, usuario = :usuario, rol = :rol, estado = :estado 
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
        }
        
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':usuario', $this->usuario);
        $stmt->bindParam(':rol', $this->rol);
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

    // Método para autenticar usuario
    public function login($usuario, $password) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE usuario = :usuario AND estado = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar contraseña
            if (password_verify($password, $row['password'])) {
                $this->id = $row['id'];
                $this->nombre = $row['nombre'];
                $this->usuario = $row['usuario'];
                $this->rol = $row['rol'];
                $this->estado = $row['estado'];
                return true;
            }
        }
        
        return false;
    }

    // Método para obtener usuario por ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>