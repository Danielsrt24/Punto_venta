<?php
require_once __DIR__ . '/../../../Negocio/helpers/session.php';
Session::init();
if (!Session::isLoggedIn()) { header("Location: ../../../index.php?page=login"); exit(); }

$base_path = "/PUNTO_VENTA";
$rol = Session::get('user_rol');

// Verificar que $categoria_model exista y tenga datos
if (!isset($categoria_model) || !isset($categoria_model->id) || $categoria_model->id == 0) {
    header("Location: " . $base_path . "/index.php?page=categorias&error=No+se+pudo+cargar+la+categoria");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Categoría</title>
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
            <nav class="col-md-2 d-none d-md-block bg-light" style="min-height: 100vh;">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=dashboard">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link active" href="<?php echo $base_path; ?>/index.php?page=categorias">Categorías</a></li>
                    </ul>
                </div>
            </nav>
            
            <main class="col-md-10 ms-sm-auto px-4 py-4">
                <h2><i class="bi bi-pencil"></i> Editar Categoría</h2>
                
                <?php if(isset($error)): ?>
                <div class="alert alert-danger mt-3"><i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo $base_path; ?>/index.php?page=categorias&accion=editar" class="mt-4" style="max-width: 600px;">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($categoria_model->id); ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Nombre *</label>
                        <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($categoria_model->nombre); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3"><?php echo htmlspecialchars($categoria_model->descripcion ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="1" <?php echo ($categoria_model->estado ?? 1) == 1 ? 'selected' : ''; ?>>Activo</option>
                            <option value="0" <?php echo ($categoria_model->estado ?? 0) == 0 ? 'selected' : ''; ?>>Inactivo</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Actualizar</button>
                    <a href="<?php echo $base_path; ?>/index.php?page=categorias" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Cancelar</a>
                </form>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>