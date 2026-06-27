<?php
require_once __DIR__ . '/../../../Negocio/helpers/session.php';
Session::init();
if (!Session::isLoggedIn()) { header("Location: ../../../index.php?page=login"); exit(); }

require_once __DIR__ . '/../../../Datos/config/database.php';
$database = new Database();
$db = $database->getConnection();

$descuento_id = $_GET['id'] ?? 0;
$base_path = "/PUNTO_VENTA";
$rol = Session::get('user_rol');

// Obtener información del descuento
$query = "SELECT * FROM descuentos WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $descuento_id, PDO::PARAM_INT);
$stmt->execute();
$descuento_info = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener productos con este descuento
$query = "SELECT p.*, pd.id as asignacion_id
          FROM productos p
          INNER JOIN producto_descuento pd ON p.id = pd.id_producto
          WHERE pd.id_descuento = :id_descuento
          ORDER BY p.nombre";
$stmt = $db->prepare($query);
$stmt->bindParam(':id_descuento', $descuento_id, PDO::PARAM_INT);
$stmt->execute();
$productos = $stmt;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos con Descuento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo $base_path; ?>/index.php?page=dashboard">
                <i class="bi bi-cart-check"></i> Sistema de Ventas
            </a>
            <div class="d-flex text-white align-items-center">
                <span class="me-3">
                    <i class="bi bi-person-circle"></i> 
                    <?php echo Session::get('user_nombre'); ?> 
                    (<?php echo Session::get('user_rol'); ?>)
                </span>
                <a href="<?php echo $base_path; ?>/logout.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </a>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid" style="margin-top: 56px;">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block bg-light" style="min-height: 100vh;">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=dashboard"><i class="bi bi-speedometer2"></i> Inicio</a></li>
                        <?php if($rol === 'ADMINISTRADOR'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=usuarios"><i class="bi bi-people"></i> Usuarios</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=clientes"><i class="bi bi-person"></i> Clientes</a></li>
                        <?php if(in_array($rol, ['ADMINISTRADOR', 'SUPERVISOR'])): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=categorias"><i class="bi bi-tags"></i> Categorías</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=marcas"><i class="bi bi-bookmark"></i> Marcas</a></li>
                        <li class="nav-item"><a class="nav-link active" href="<?php echo $base_path; ?>/index.php?page=descuentos"><i class="bi bi-percent"></i> Descuentos</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=productos"><i class="bi bi-box"></i> Productos</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=ventas"><i class="bi bi-cart"></i> Ventas</a></li>
                    </ul>
                </div>
            </nav>
            
            <main class="col-md-10 ms-sm-auto px-4 py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>
                        <i class="bi bi-box-seam"></i> 
                        Productos con Descuento: <?php echo htmlspecialchars($descuento_info['nombre']); ?>
                    </h2>
                    <div>
                        <a href="<?php echo $base_path; ?>/index.php?page=descuentoController&accion=asignar" class="btn btn-success me-2">
                            <i class="bi bi-plus-circle"></i> Asignar Producto
                        </a>
                        <a href="<?php echo $base_path; ?>/index.php?page=descuentos" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                
                <?php if(isset($_GET['mensaje'])): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['mensaje']); ?></div>
                <?php endif; ?>
                
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Información del Descuento</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php $tipo_desc = strtolower($descuento_info['tipo']); ?>
                            <div class="col-md-3">
                                <strong>Nombre:</strong><br>
                                <?php echo htmlspecialchars($descuento_info['nombre']); ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Tipo:</strong><br>
                                <span class="badge bg-<?php echo $tipo_desc === 'porcentaje' ? 'success' : 'info'; ?>">
                                    <?php echo strtoupper($descuento_info['tipo']); ?>
                                </span>
                            </div>
                            <div class="col-md-3">
                                <strong>Valor:</strong><br>
                                <h4 class="text-primary">
                                    <?php echo $descuento_info['valor']; ?><?php echo $tipo_desc === 'porcentaje' ? '%' : '$'; ?>
                                </h4>
                            </div>
                            <div class="col-md-3">
                                <strong>Vigencia:</strong><br>
                                <?php echo date('d/m/Y', strtotime($descuento_info['fecha_inicio'])); ?> - <?php echo date('d/m/Y', strtotime($descuento_info['fecha_fin'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-box"></i> Productos Asignados</h5>
                    </div>
                    <div class="card-body">
                        <?php if($productos->rowCount() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Código</th>
                                        <th>Producto</th>
                                        <th>Precio Original</th>
                                        <th>Precio con Descuento</th>
                                        <th>Stock</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($prod = $productos->fetch(PDO::FETCH_ASSOC)): 
                                        $precio_original = $prod['precio'];
                                        $descuento_valor = $descuento_info['valor'];
                                        
                                        if ($tipo_desc === 'porcentaje') {
                                            $precio_con_descuento = $precio_original - ($precio_original * ($descuento_valor / 100));
                                        } else {
                                            $precio_con_descuento = $precio_original - $descuento_valor;
                                            if ($precio_con_descuento < 0) $precio_con_descuento = 0;
                                        }
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($prod['codigo']); ?></td>
                                        <td><strong><?php echo htmlspecialchars($prod['nombre']); ?></strong></td>
                                        <td>$<?php echo number_format($precio_original, 2); ?></td>
                                        <td>
                                            <strong class="text-success">
                                                $<?php echo number_format($precio_con_descuento, 2); ?>
                                            </strong>
                                            <br>
                                            <small class="text-muted">
                                                Ahorro: $<?php echo number_format($precio_original - $precio_con_descuento, 2); ?>
                                            </small>
                                        </td>
                                        <td><?php echo $prod['stock']; ?></td>
                                        <td>
                                            <a href="<?php echo $base_path; ?>/index.php?page=descuentoController&accion=quitar&producto=<?php echo $prod['id']; ?>&descuento=<?php echo $descuento_id; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('¿Quitar descuento de este producto?')">
                                                <i class="bi bi-x-circle"></i> Quitar
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle"></i> 
                            No hay productos asignados a este descuento. 
                            <a href="<?php echo $base_path; ?>/index.php?page=descuentoController&accion=asignar">Asignar producto</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>