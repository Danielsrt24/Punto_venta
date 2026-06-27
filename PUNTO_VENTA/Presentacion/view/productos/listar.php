<?php
require_once __DIR__ . '/../../../Negocio/helpers/session.php';
Session::init();
if (!Session::isLoggedIn()) { header("Location: ../../../index.php?page=login"); exit(); }

$filtro = $_GET['buscar'] ?? '';
$productos = $producto->readAll($filtro);
$base_path = "/PUNTO_VENTA";
$rol = Session::get('user_rol');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo $base_path; ?>/index.php?page=dashboard">Sistema de Ventas</a>
            <div class="d-flex text-white">
                <span class="me-3"><?php echo Session::get('user_nombre'); ?></span>
                <a href="<?php echo $base_path; ?>/logout.php" class="btn btn-outline-light btn-sm">Salir</a>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid" style="margin-top: 56px;">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block bg-light sidebar" style="min-height: 100vh;">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=dashboard">Inicio</a></li>
                        <?php if($rol === 'ADMINISTRADOR'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=usuarios">Usuarios</a></li>
                        <?php endif; ?>
                        <?php if(in_array($rol, ['ADMINISTRADOR', 'SUPERVISOR', 'CAJERO'])): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=clientes">Clientes</a></li>
                        <?php endif; ?>
                        <?php if(in_array($rol, ['ADMINISTRADOR', 'SUPERVISOR'])): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=categorias">Categorías</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="nav-link active" href="<?php echo $base_path; ?>/index.php?page=productos">Productos</a></li>
                        <?php if(in_array($rol, ['ADMINISTRADOR', 'SUPERVISOR', 'CAJERO'])): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=ventas">Ventas</a></li>
                        <?php endif; ?>
                        <?php if(in_array($rol, ['ADMINISTRADOR', 'SUPERVISOR'])): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=reportes">Reportes</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>
            
            <main class="col-md-10 ms-sm-auto px-4 py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-box"></i> Gestión de Productos</h2>
                    <?php if($rol !== 'CAJERO'): ?>
                    <a href="<?php echo $base_path; ?>/index.php?page=productosController&accion=crear" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Nuevo Producto
                    </a>
                    <?php endif; ?>
                </div>
                
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <form method="GET" action="<?php echo $base_path; ?>/index.php" class="row g-2">
                            <input type="hidden" name="page" value="productos">
                            <div class="col-md-10">
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                    <input type="text" name="buscar" class="form-control" 
                                           placeholder="Buscar por nombre, código, marca o categoría..." 
                                           value="<?php echo htmlspecialchars($filtro); ?>" autofocus>
                                </div>
                            </div>
                            <div class="col-md-2 d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Buscar</button>
                                <?php if(!empty($filtro)): ?>
                                <a href="<?php echo $base_path; ?>/index.php?page=productos" class="btn btn-secondary btn-sm">Limpiar</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
                
                <?php if(isset($_GET['mensaje'])): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['mensaje']); ?></div>
                <?php endif; ?>
                
                <?php if(isset($_GET['error'])): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                <?php endif; ?>
                
                <?php if(!empty($filtro)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Resultados para: <strong>"<?php echo htmlspecialchars($filtro); ?>"</strong>
                    </div>
                <?php endif; ?>
                
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Categoría</th>
                                        <th>Marca</th>
                                        <th>Precio</th>
                                        <th>Stock</th>
                                        <?php if($rol !== 'CAJERO'): ?>
                                        <th>Acciones</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $hay_resultados = false;
                                    if ($productos) {
                                        while($row = $productos->fetch(PDO::FETCH_ASSOC)): 
                                            $hay_resultados = true;
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['codigo']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($row['categoria_nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($row['marca_nombre'] ?? 'Sin marca'); ?></td>
                                        <td>$<?php echo number_format($row['precio'], 2); ?></td>
                                        <td><?php echo $row['stock']; ?></td>
                                        <?php if($rol !== 'CAJERO'): ?>
                                        <td>
                                            <a href="<?php echo $base_path; ?>/index.php?page=productosController&accion=editar&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                                            <a href="<?php echo $base_path; ?>/index.php?page=productosController&accion=eliminar&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar producto?')">Eliminar</a>
                                        </td>
                                        <?php endif; ?>
                                    </tr>
                                    <?php 
                                        endwhile;
                                    }
                                    if (!$hay_resultados) {
                                        $colspan = ($rol !== 'CAJERO') ? 7 : 6;
                                        echo "<tr><td colspan='$colspan' class='text-center'>No hay productos registrados</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>