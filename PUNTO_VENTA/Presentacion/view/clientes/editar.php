<?php
require_once __DIR__ . '/../../../Negocio/helpers/session.php';
Session::init();
if (!Session::isLoggedIn()) { header("Location: ../../../index.php?page=login"); exit(); }
$base_path = "/PUNTO_VENTA";
$rol = Session::get('user_rol');

if (!isset($cliente_model) || !isset($cliente_model->id) || $cliente_model->id == 0) {
    header("Location: " . $base_path . "/index.php?page=clientes&error=Cliente+no+encontrado");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
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
                        <li class="nav-item"><a class="nav-link active" href="<?php echo $base_path; ?>/index.php?page=clientes"><i class="bi bi-person"></i> Clientes</a></li>
                    </ul>
                </div>
            </nav>
            
            <main class="col-md-10 ms-sm-auto px-4 py-4">
                <h2><i class="bi bi-pencil"></i> Editar Cliente</h2>
                
                <?php if(isset($error)): ?>
                <div class="alert alert-danger mt-3">
                    <i class="bi bi-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo $base_path; ?>/index.php?page=clienteController&accion=editar" class="mt-4" style="max-width: 800px;">
                    <input type="hidden" name="id" value="<?php echo $cliente_model->id; ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre Completo *</label>
                            <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($cliente_model->nombre); ?>" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($cliente_model->telefono ?? ''); ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($cliente_model->email ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">DUI</label>
                            <input type="text" name="DUI" class="form-control" value="<?php echo htmlspecialchars($cliente_model->DUI ?? ''); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">NIT</label>
                            <input type="text" name="NIT" class="form-control" value="<?php echo htmlspecialchars($cliente_model->NIT ?? ''); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tipo de Persona</label>
                            <select name="tipo_cliente" class="form-select">
                                <option value="NATURAL" <?php echo ($cliente_model->TipoPersona ?? 'NATURAL') === 'NATURAL' ? 'selected' : ''; ?>>NATURAL</option>
                                <option value="JURIDICA" <?php echo ($cliente_model->TipoPersona ?? '') === 'JURIDICA' ? 'selected' : ''; ?>>JURÍDICA</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <textarea name="direccion" class="form-control" rows="3"><?php echo htmlspecialchars($cliente_model->direccion ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="1" <?php echo ($cliente_model->estado ?? 1) == 1 ? 'selected' : ''; ?>>Activo</option>
                            <option value="0" <?php echo ($cliente_model->estado ?? 0) == 0 ? 'selected' : ''; ?>>Inactivo</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Actualizar
                    </button>
                    <a href="<?php echo $base_path; ?>/index.php?page=clientes" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                </form>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>