<?php
require_once __DIR__ . '/../../Negocio/helpers/session.php';
Session::init();

if (!Session::isLoggedIn()) {
    header("Location: ../../index.php?page=login");
    exit();
}

$base_path = "/PUNTO_VENTA";
$rol = Session::get('user_rol');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio- Sistema de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>/Presentacion/assets/css/estilos.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo $base_path; ?>/index.php?page=dashboard">
                <i class="bi bi-cart-check"></i> Sistema de Ventas
            </a>
            <div class="d-flex text-white">
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
            <nav class="col-md-2 d-none d-md-block bg-light sidebar" style="min-height: 100vh;">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="<?php echo $base_path; ?>/index.php?page=dashboard">
                                <i class="bi bi-speedometer2"></i> Inicio
                            </a>
                        </li>
                        <?php if($rol === 'ADMINISTRADOR'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=usuarios">
                                <i class="bi bi-people"></i> Usuarios
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array($rol, ['ADMINISTRADOR', 'SUPERVISOR', 'CAJERO'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=clientes">
                                <i class="bi bi-person"></i> Clientes
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array($rol, ['ADMINISTRADOR', 'SUPERVISOR'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=categorias">
                                <i class="bi bi-tags"></i> Categorías
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=marcas">
                                <i class="bi bi-bookmark"></i> Marcas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=descuentos">
                                <i class="bi bi-percent"></i> Descuentos
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array($rol, ['ADMINISTRADOR', 'SUPERVISOR', 'CAJERO'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=productos">
                                <i class="bi bi-box"></i> Productos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=ventas">
                                <i class="bi bi-cart"></i> Ventas
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array($rol, ['ADMINISTRADOR', 'SUPERVISOR'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=reportes">
                                <i class="bi bi-graph-up"></i> Reportes
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>
            
            <main class="col-md-10 ms-sm-auto px-4 py-4">
                <div class="d-flex justify-content-between align-items-center mb-4 fade-in">
                    <h2><i class="bi bi-speedometer2"></i> Inicio</h2>
                    <div class="text-muted">
                    </div>
                </div>
                
                <div class="alert alert-light shadow-lg fade-in">
                    <h4 class="alert-heading"><i class="bi bi-hand-thumbs-up"></i> ¡Bienvenido!</h4>
                    <p class="mb-0">Hola, <strong><?php echo Session::get('user_nombre'); ?></strong>. 
                    </p>
                </div>
                
                <div class="row mt-4">
                    <?php if($rol === 'ADMINISTRADOR'): ?>
                    <div class="col-md-4 mb-3 fade-in">
                        <div class="card text-white bg-primary h-100 shadow-lg">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-people" style="font-size: 3rem; margin-bottom: 15px;"></i>
                                <h5 class="card-title">Usuarios</h5>
                                <p class="card-text">Gestionar usuarios del sistema</p>
                                <a href="<?php echo $base_path; ?>/index.php?page=usuarios" class="btn btn-light btn-lg mt-2">
                                    <i class="bi bi-arrow-right"></i> Ir a Usuarios
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(in_array($rol, ['ADMINISTRADOR', 'SUPERVISOR', 'CAJERO'])): ?>
                    <div class="col-md-4 mb-3 fade-in">
                        <div class="card text-white bg-success h-100 shadow-lg">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-person" style="font-size: 3rem; margin-bottom: 15px;"></i>
                                <h5 class="card-title">Clientes</h5>
                                <p class="card-text">Gestionar clientes</p>
                                <a href="<?php echo $base_path; ?>/index.php?page=clientes" class="btn btn-light btn-lg mt-2">
                                    <i class="bi bi-arrow-right"></i> Ir a Clientes
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(in_array($rol, ['ADMINISTRADOR', 'SUPERVISOR'])): ?>
                    <div class="col-md-4 mb-3 fade-in">
                        <div class="card text-white bg-secondary h-100 shadow-lg">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-tags" style="font-size: 3rem; margin-bottom: 15px;"></i>
                                <h5 class="card-title">Categorías</h5>
                                <p class="card-text">Organizar productos</p>
                                <a href="<?php echo $base_path; ?>/index.php?page=categorias" class="btn btn-light btn-lg mt-2">
                                    <i class="bi bi-arrow-right"></i> Ir a Categorías
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3 fade-in">
                        <div class="card text-white bg-dark h-100 shadow-lg">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-bookmark" style="font-size: 3rem; margin-bottom: 15px;"></i>
                                <h5 class="card-title">Marcas</h5>
                                <p class="card-text">Gestionar marcas</p>
                                <a href="<?php echo $base_path; ?>/index.php?page=marcas" class="btn btn-light btn-lg mt-2">
                                    <i class="bi bi-arrow-right"></i> Ir a Marcas
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3 fade-in">
                        <div class="card text-white bg-info h-100 shadow-lg">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-percent" style="font-size: 3rem; margin-bottom: 15px;"></i>
                                <h5 class="card-title">Descuentos</h5>
                                <p class="card-text">Gestionar descuentos y promociones</p>
                                <a href="<?php echo $base_path; ?>/index.php?page=descuentos" class="btn btn-light btn-lg mt-2">
                                    <i class="bi bi-arrow-right"></i> Ir a Descuentos
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(in_array($rol, ['ADMINISTRADOR', 'SUPERVISOR', 'CAJERO'])): ?>
                    <div class="col-md-4 mb-3 fade-in">
                        <div class="card text-white bg-primary h-100 shadow-lg" style="background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%) !important;">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-box" style="font-size: 3rem; margin-bottom: 15px;"></i>
                                <h5 class="card-title">Productos</h5>
                                <p class="card-text">Gestionar inventario</p>
                                <a href="<?php echo $base_path; ?>/index.php?page=productos" class="btn btn-light btn-lg mt-2">
                                    <i class="bi bi-arrow-right"></i> Ir a Productos
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3 fade-in">
                        <div class="card text-white bg-warning h-100 shadow-lg">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-cart" style="font-size: 3rem; margin-bottom: 15px;"></i>
                                <h5 class="card-title">Ventas</h5>
                                <p class="card-text">Registrar nuevas ventas</p>
                                <a href="<?php echo $base_path; ?>/index.php?page=ventas" class="btn btn-light btn-lg mt-2">
                                    <i class="bi bi-arrow-right"></i> Ir a Ventas
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(in_array($rol, ['ADMINISTRADOR', 'SUPERVISOR'])): ?>
                    <div class="col-md-4 mb-3 fade-in">
                        <div class="card text-white bg-danger h-100 shadow-lg">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-graph-up" style="font-size: 3rem; margin-bottom: 15px;"></i>
                                <h5 class="card-title">Reportes</h5>
                                <p class="card-text">Ver estadísticas del sistema</p>
                                <a href="<?php echo $base_path; ?>/index.php?page=reportes" class="btn btn-light btn-lg mt-2">
                                    <i class="bi bi-arrow-right"></i> Ir a Reportes
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function actualizarReloj() {
        const ahora = new Date();
        const horas = String(ahora.getHours()).padStart(2, '0');
        const minutos = String(ahora.getMinutes()).padStart(2, '0');
        const segundos = String(ahora.getSeconds()).padStart(2, '0');
        document.getElementById('reloj').textContent = `${horas}:${minutos}:${segundos}`;
    }
    setInterval(actualizarReloj, 1000);
    actualizarReloj();
    </script>
</body>
</html>