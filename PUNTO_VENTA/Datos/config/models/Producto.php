<?php
class Producto {
    private $conn;
    private $table = 'productos';
    
    public $id, $codigo, $nombre, $descripcion, $precio, $stock;
    public $id_categoria, $id_marca, $IVA, $estado;
    public $categoria_nombre, $marca_nombre;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll($filtro = '') {
        $query = "SELECT p.*, c.nombre as categoria_nombre, m.nombre as marca_nombre 
                  FROM " . $this->table . " p
                  LEFT JOIN categorias c ON p.id_categoria = c.id
                  LEFT JOIN marcas m ON p.id_marca = m.id
                  WHERE p.estado = 1";
        
        if (!empty($filtro)) {
            $query .= " AND (p.nombre LIKE :filtro OR p.codigo LIKE :filtro2 OR c.nombre LIKE :filtro3 OR m.nombre LIKE :filtro4)";
        }
        
        $query .= " ORDER BY p.id DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($filtro)) {
            $filtro_like = "%{$filtro}%";
            $stmt->bindParam(':filtro', $filtro_like);
            $stmt->bindParam(':filtro2', $filtro_like);
            $stmt->bindParam(':filtro3', $filtro_like);
            $stmt->bindParam(':filtro4', $filtro_like);
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
            $this->codigo = $row['codigo'];
            $this->nombre = $row['nombre'];
            $this->descripcion = $row['descripcion'];
            $this->precio = $row['precio'];
            $this->stock = $row['stock'];
            $this->id_categoria = $row['id_categoria'];
            $this->id_marca = $row['id_marca'];
            $this->IVA = $row['IVA'];
            $this->estado = $row['estado'];
        }
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (codigo, nombre, descripcion, precio, stock, id_categoria, id_marca, IVA, estado) 
                  VALUES (:codigo, :nombre, :descripcion, :precio, :stock, :id_categoria, :id_marca, :IVA, :estado)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':codigo', $this->codigo);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':precio', $this->precio);
        $stmt->bindParam(':stock', $this->stock, PDO::PARAM_INT);
        $stmt->bindParam(':id_categoria', $this->id_categoria, PDO::PARAM_INT);
        
        if ($this->id_marca) {
            $stmt->bindParam(':id_marca', $this->id_marca, PDO::PARAM_INT);
        } else {
            $stmt->bindValue(':id_marca', null, PDO::PARAM_NULL);
        }
        
        $stmt->bindParam(':IVA', $this->IVA);
        $stmt->bindParam(':estado', $this->estado, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET codigo = :codigo, nombre = :nombre, descripcion = :descripcion, 
                      precio = :precio, stock = :stock, id_categoria = :id_categoria, 
                      id_marca = :id_marca, IVA = :IVA, estado = :estado 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':codigo', $this->codigo);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':precio', $this->precio);
        $stmt->bindParam(':stock', $this->stock, PDO::PARAM_INT);
        $stmt->bindParam(':id_categoria', $this->id_categoria, PDO::PARAM_INT);
        
        if ($this->id_marca) {
            $stmt->bindParam(':id_marca', $this->id_marca, PDO::PARAM_INT);
        } else {
            $stmt->bindValue(':id_marca', null, PDO::PARAM_NULL);
        }
        
        $stmt->bindParam(':IVA', $this->IVA);
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