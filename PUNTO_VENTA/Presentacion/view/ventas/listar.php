<?php
require_once __DIR__ . '/../../../Negocio/helpers/session.php';
require_once __DIR__ . '/../../../Negocio/helpers/permisos.php';
Session::init();
if (!Session::isLoggedIn()) { header("Location: ../../../index.php?page=login"); exit(); }
$ventas = $venta->readAll();
$base_path = "/PUNTO_VENTA";
$rol = Session::get('user_rol');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ventas</title>
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
                        <?php if(tienePermiso($rol, 'usuarios')): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=usuarios">Usuarios</a></li>
                        <?php endif; ?>
                        <?php if(tienePermiso($rol, 'clientes')): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=clientes">Clientes</a></li>
                        <?php endif; ?>
                        <?php if(tienePermiso($rol, 'categorias')): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=categorias">Categorías</a></li>
                        <?php endif; ?>
                        <?php if(tienePermiso($rol, 'marcas')): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=marcas">Marcas</a></li>
                        <?php endif; ?>
                        <?php if(tienePermiso($rol, 'productos')): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=productos">Productos</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="nav-link active" href="<?php echo $base_path; ?>/index.php?page=ventas">Ventas</a></li>
                        <?php if(tienePermiso($rol, 'reportes')): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=reportes">Reportes</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>
            
            <main class="col-md-10 ms-sm-auto px-4 py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-cart"></i> Gestión de Ventas</h2>
                    <a href="<?php echo $base_path; ?>/index.php?page=ventasController&accion=crear" class="btn btn-success btn-lg">
                        <i class="bi bi-plus-circle"></i> Nueva Venta
                    </a>
                </div>
                
                <?php if(isset($_GET['mensaje'])): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['mensaje']); ?></div>
                <?php endif; ?>
                <?php if(isset($_GET['error'])): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                <?php endif; ?>
                
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Atendió</th>
                                        <th>Subtotal</th>
                                        <th>Total</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if ($ventas) {
                                        while($row = $ventas->fetch(PDO::FETCH_ASSOC)): 
                                    ?>
                                    <tr>
                                        <td><strong><?php echo $row['id']; ?></strong></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['fecha'])); ?></td>
                                        <td><?php echo htmlspecialchars($row['nombre_cliente'] ?? 'Consumidor Final'); ?></td>
                                        <td><?php echo htmlspecialchars($row['nombre_usuario']); ?></td>
                                        <td>$<?php echo number_format($row['subtotal'], 2); ?></td>
                                        <td><strong>$<?php echo number_format($row['total'], 2); ?></strong></td>
                                        <td>
                                            <a href="<?php echo $base_path; ?>/index.php?page=ventasController&accion=ver&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">Ver</a>
                                            <?php if(puedeEliminar($rol, 'ventas')): ?>
                                            <a href="<?php echo $base_path; ?>/index.php?page=ventasController&accion=eliminar&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta venta?')">Eliminar</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php 
                                        endwhile;
                                    } else {
                                        echo "<tr><td colspan='7' class='text-center'>No hay ventas registradas</td></tr>";
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