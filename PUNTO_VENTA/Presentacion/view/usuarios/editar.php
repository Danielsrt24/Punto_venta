<?php
require_once __DIR__ . '/../../../Negocio/helpers/session.php';
Session::init();
if (!Session::isLoggedIn()) { header("Location: ../../../index.php?page=login"); exit(); }

$base_path = "/PUNTO_VENTA";
$rol = Session::get('user_rol');

if (!isset($usuario_model) || !isset($usuario_model->id) || $usuario_model->id == 0) {
    header("Location: " . $base_path . "/index.php?page=usuarios&error=Usuario+no+encontrado");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
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
                        <li class="nav-item"><a class="nav-link active" href="<?php echo $base_path; ?>/index.php?page=usuarios"><i class="bi bi-people"></i> Usuarios</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=clientes"><i class="bi bi-person"></i> Clientes</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=productos"><i class="bi bi-box"></i> Productos</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=ventas"><i class="bi bi-cart"></i> Ventas</a></li>
                    </ul>
                </div>
            </nav>
            
            <main class="col-md-10 ms-sm-auto px-4 py-4">
                <h2><i class="bi bi-pencil"></i> Editar Usuario</h2>
                
                <?php if(isset($error)): ?>
                <div class="alert alert-danger mt-3">
                    <i class="bi bi-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo $base_path; ?>/index.php?page=usuarioController&accion=editar" class="mt-4" style="max-width: 600px;">
                    <input type="hidden" name="id" value="<?php echo $usuario_model->id; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Nombre Completo *</label>
                        <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($usuario_model->nombre); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Usuario *</label>
                        <input type="text" name="usuario" class="form-control" value="<?php echo htmlspecialchars($usuario_model->usuario); ?>" required>
                        <small class="text-muted">El nombre de usuario puede ser modificado</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nueva Contraseña</label>
                        <input type="password" name="password" class="form-control">
                        <small class="text-muted">Dejar en blanco para mantener la contraseña actual</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Rol *</label>
                        <select name="rol" class="form-select" required>
                            <option value="ADMINISTRADOR" <?php echo ($usuario_model->rol ?? '') === 'ADMINISTRADOR' ? 'selected' : ''; ?>>ADMINISTRADOR</option>
                            <option value="SUPERVISOR" <?php echo ($usuario_model->rol ?? '') === 'SUPERVISOR' ? 'selected' : ''; ?>>SUPERVISOR</option>
                            <option value="CAJERO" <?php echo ($usuario_model->rol ?? '') === 'CAJERO' ? 'selected' : ''; ?>>CAJERO</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="1" <?php echo ($usuario_model->estado ?? 1) == 1 ? 'selected' : ''; ?>>Activo</option>
                            <option value="0" <?php echo ($usuario_model->estado ?? 0) == 0 ? 'selected' : ''; ?>>Inactivo</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Actualizar Usuario
                    </button>
                    <a href="<?php echo $base_path; ?>/index.php?page=usuarios" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                </form>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>