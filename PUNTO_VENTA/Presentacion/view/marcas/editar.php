<?php
require_once __DIR__ . '/../../../Negocio/helpers/session.php';
require_once __DIR__ . '/../../../Negocio/helpers/permisos.php';
Session::init();
if (!Session::isLoggedIn()) { header("Location: ../../../index.php?page=login"); exit(); }
$base_path = "/PUNTO_VENTA";
$rol = Session::get('user_rol');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Marca</title>
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
                        <li class="nav-item"><a class="nav-link active" href="<?php echo $base_path; ?>/index.php?page=marcas">Marcas</a></li>
                        <?php if(tienePermiso($rol, 'productos')): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=productos">Productos</a></li>
                        <?php endif; ?>
                        <?php if(tienePermiso($rol, 'ventas')): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=ventas">Ventas</a></li>
                        <?php endif; ?>
                        <?php if(tienePermiso($rol, 'reportes')): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=reportes">Reportes</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>
            
            <main class="col-md-10 ms-sm-auto px-4 py-4">
                <h2><i class="bi bi-pencil"></i> Editar Marca</h2>
                <?php if(isset($error)): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
                
                <form method="POST" action="<?php echo $base_path; ?>/index.php?page=marcaController&accion=editar" class="mt-4" style="max-width: 600px;">
                    <input type="hidden" name="id" value="<?php echo $marca->id; ?>">
                    <div class="mb-3">
                        <label>Nombre *</label>
                        <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($marca->nombre); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3"><?php echo htmlspecialchars($marca->descripcion); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Estado</label>
                        <select name="estado" class="form-select">
                            <option value="1" <?php echo $marca->estado ? 'selected' : ''; ?>>Activo</option>
                            <option value="0" <?php echo !$marca->estado ? 'selected' : ''; ?>>Inactivo</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="<?php echo $base_path; ?>/index.php?page=marcas" class="btn btn-secondary">Cancelar</a>
                </form>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>