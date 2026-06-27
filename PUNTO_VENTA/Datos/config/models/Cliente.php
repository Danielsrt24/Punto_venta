<?php
class Cliente {
    private $conn;
    private $table = 'clientes';
    
    public $id, $nombre, $DUI, $telefono, $email, $nit, $tipo_cliente, $direccion, $estado;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll($filtro = '') {
        $query = "SELECT * FROM " . $this->table . " WHERE estado = 1";
        
        if (!empty($filtro)) {
            $query .= " AND (nombre LIKE :filtro OR telefono LIKE :filtro2 OR email LIKE :filtro3 OR DUI LIKE :filtro4 OR nit LIKE :filtro5)";
        }
        
        $query .= " ORDER BY id DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($filtro)) {
            $filtro_like = "%{$filtro}%";
            $stmt->bindParam(':filtro', $filtro_like);
            $stmt->bindParam(':filtro2', $filtro_like);
            $stmt->bindParam(':filtro3', $filtro_like);
            $stmt->bindParam(':filtro4', $filtro_like);
            $stmt->bindParam(':filtro5', $filtro_like);
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
            $this->DUI = $row['DUI'];
            $this->telefono = $row['telefono'];
            $this->email = $row['email'];
            $this->NIT = $row['NIT'];
            $this->tipo_cliente = $row['TipoPerson'];
            $this->direccion = $row['direccion'];
            $this->estado = $row['estado'];
            return true;
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, DUI, telefono, email, NIT, TipoPerson, direccion, estado) 
                  VALUES (:nombre, :DUI, :telefono, :email, :NIT, :TipoPerson, :direccion, :estado)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':DUI', $this->DUI);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':NIT', $this->NIT);
        $stmt->bindParam(':TipoPerson', $this->tipo_cliente);
        $stmt->bindParam(':direccion', $this->direccion);
        $stmt->bindParam(':estado', $this->estado, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET nombre = :nombre, DUI = :DUI, telefono = :telefono, 
                      email = :email, NIT = :NIT, TipoPerson = :TipoPerson, 
                      direccion = :direccion, estado = :estado 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':DUI', $this->DUI);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':NIT', $this->NIT);
        $stmt->bindParam(':TipoPerson', $this->tipo_cliente);
        $stmt->bindParam(':direccion', $this->direccion);
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