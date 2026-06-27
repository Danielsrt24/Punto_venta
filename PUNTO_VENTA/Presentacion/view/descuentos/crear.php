<?php
require_once __DIR__ . '/../../../Negocio/helpers/session.php';
Session::init();
if (!Session::isLoggedIn()) { header("Location: ../../../index.php?page=login"); exit(); }
$base_path = "/PUNTO_VENTA";
$rol = Session::get('user_rol');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Descuento</title>
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
                <span class="me-3"><?php echo Session::get('user_nombre'); ?> (<?php echo Session::get('user_rol'); ?>)</span>
                <a href="<?php echo $base_path; ?>/logout.php" class="btn btn-outline-light btn-sm">Salir</a>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid" style="margin-top: 56px;">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block bg-light" style="min-height: 100vh;">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
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
                        <?php if(in_array($rol, ['ADMINISTRADOR', 'SUPERVISOR'])): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=reportes"><i class="bi bi-graph-up"></i> Reportes</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>
            
            <main class="col-md-10 ms-sm-auto px-4 py-4">
                <h2><i class="bi bi-plus-circle"></i> Crear Descuento</h2>
                
                <?php if(isset($error)): ?>
                <div class="alert alert-danger mt-3">
                    <i class="bi bi-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo $base_path; ?>/index.php?page=descuentoController&accion=crear" class="mt-4" style="max-width: 800px;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre *</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tipo *</label>
                            <select name="tipo" class="form-select" required>
                                <option value="PORCENTAJE">PORCENTAJE</option>
                                <option value="FIJO">FIJO ($)</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Valor *</label>
                            <input type="number" step="0.01" name="valor" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fecha Inicio *</label>
                            <input type="date" name="fecha_inicio" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fecha Fin *</label>
                            <input type="date" name="fecha_fin" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Estado</label>
                            <select name="estado" class="form-select">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Guardar Descuento
                    </button>
                    <a href="<?php echo $base_path; ?>/index.php?page=descuentos" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                </form>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>