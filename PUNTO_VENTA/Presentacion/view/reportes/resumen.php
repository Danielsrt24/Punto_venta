<?php
require_once __DIR__ . '/../../../Negocio/helpers/session.php';
Session::init();
if (!Session::isLoggedIn()) { header("Location: ../../../index.php?page=login"); exit(); }
$base_path = "/PUNTO_VENTA";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes - Sistema de Ventas</title>
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
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=usuarios">Usuarios</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=clientes">Clientes</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=categorias">Categorías</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=productos">Productos</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=ventas">Ventas</a></li>
                        <li class="nav-item"><a class="nav-link active" href="<?php echo $base_path; ?>/index.php?page=reportes">Reportes</a></li>
                    </ul>
                </div>
            </nav>
            
            <main class="col-md-10 ms-sm-auto px-4 py-4">
                <h2><i class="bi bi-graph-up"></i> Reportes Administrativos</h2>
                
                <!-- Tarjetas de Resumen -->
                <div class="row mt-4">
                    <div class="col-md-3 mb-3">
                        <div class="card text-white bg-primary h-100">
                            <div class="card-body">
                                <h5 class="card-title">Total Productos</h5>
                                <h2><?php echo $resumen['total_productos']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-white bg-success h-100">
                            <div class="card-body">
                                <h5 class="card-title">Total Clientes</h5>
                                <h2><?php echo $resumen['total_clientes']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-white bg-info h-100">
                            <div class="card-body">
                                <h5 class="card-title">Total Ventas</h5>
                                <h2><?php echo $resumen['total_ventas']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-white bg-warning h-100">
                            <div class="card-body">
                                <h5 class="card-title">Total Vendido</h5>
                                <h2>$<?php echo number_format($resumen['total_vendido'], 2); ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Alerta de Bajo Stock -->
                <?php if($resumen['bajo_stock'] > 0): ?>
                <div class="alert alert-warning mt-4">
                    <i class="bi bi-exclamation-triangle"></i> 
                    <strong>¡Atención!</strong> Hay <?php echo $resumen['bajo_stock']; ?> productos con stock bajo (≤10 unidades)
                    <a href="<?php echo $base_path; ?>/index.php?page=reportes&accion=bajo_stock" class="btn btn-sm btn-warning ms-3">Ver Reporte</a>
                </div>
                <?php endif; ?>
                
                <!-- Enlaces a Reportes -->
                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-exclamation-triangle"></i> Productos con Bajo Stock</h5>
                                <p class="card-text">Ver productos con stock menor o igual a 10 unidades</p>
                                <a href="<?php echo $base_path; ?>/index.php?page=reportes&accion=bajo_stock" class="btn btn-warning">Ver Reporte</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-calendar"></i> Ventas por Fecha</h5>
                                <p class="card-text">Consultar ventas realizadas en un rango de fechas</p>
                                <a href="<?php echo $base_path; ?>/index.php?page=reportes&accion=ventas_fecha" class="btn btn-info">Ver Reporte</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-trophy"></i> Productos Más Vendidos</h5>
                                <p class="card-text">Ver los 10 productos con mayor cantidad vendida</p>
                                <a href="<?php echo $base_path; ?>/index.php?page=reportes&accion=mas_vendidos" class="btn btn-success">Ver Reporte</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-arrow-counterclockwise"></i> Volver al Resumen</h5>
                                <p class="card-text">Regresar a la vista general de reportes</p>
                                <a href="<?php echo $base_path; ?>/index.php?page=reportes" class="btn btn-secondary">Resumen</a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>