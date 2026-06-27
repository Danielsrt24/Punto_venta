<?php
require_once __DIR__ . '/../../../Negocio/helpers/session.php';
Session::init();
if (!Session::isLoggedIn()) { header("Location: ../../../index.php?page=login"); exit(); }

$base_path = "/PUNTO_VENTA";
$rol = Session::get('user_rol');

if (!isset($descuento_model) || !isset($descuento_model->id) || $descuento_model->id == 0) {
    header("Location: " . $base_path . "/index.php?page=descuentos&error=No+se+pudo+cargar+el+descuento");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Descuento</title>
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
                        <li class="nav-item"><a class="nav-link active" href="<?php echo $base_path; ?>/index.php?page=descuentos">Descuentos</a></li>
                    </ul>
                </div>
            </nav>
            
            <main class="col-md-10 ms-sm-auto px-4 py-4">
                <h2><i class="bi bi-pencil"></i> Editar Descuento</h2>
                
                <?php if(isset($error)): ?>
                <div class="alert alert-danger mt-3"><i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?></div>
                <?php endif; ?>
                
                <!-- Formulario de edición del descuento -->
                <form method="POST" action="<?php echo $base_path; ?>/index.php?page=descuentoController&accion=editar" class="mt-4">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($descuento_model->id); ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre *</label>
                            <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($descuento_model->nombre); ?>" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tipo *</label>
                            <select name="tipo" class="form-select" required>
                                <option value="PORCENTAJE" <?php echo ($descuento_model->tipo ?? '') === 'PORCENTAJE' ? 'selected' : ''; ?>>PORCENTAJE</option>
                                <option value="FIJO" <?php echo ($descuento_model->tipo ?? '') === 'FIJO' ? 'selected' : ''; ?>>FIJO</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Valor *</label>
                            <input type="number" step="0.01" name="valor" class="form-control" value="<?php echo htmlspecialchars($descuento_model->valor); ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2"><?php echo htmlspecialchars($descuento_model->descripcion ?? ''); ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fecha Inicio *</label>
                            <input type="date" name="fecha_inicio" class="form-control" value="<?php echo htmlspecialchars($descuento_model->fecha_inicio ?? date('Y-m-d')); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fecha Fin *</label>
                            <input type="date" name="fecha_fin" class="form-control" value="<?php echo htmlspecialchars($descuento_model->fecha_fin ?? date('Y-m-d')); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Estado</label>
                            <select name="estado" class="form-select">
                                <option value="1" <?php echo ($descuento_model->estado ?? 1) == 1 ? 'selected' : ''; ?>>Activo</option>
                                <option value="0" <?php echo ($descuento_model->estado ?? 0) == 0 ? 'selected' : ''; ?>>Inactivo</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Actualizar Descuento</button>
                    <a href="<?php echo $base_path; ?>/index.php?page=descuentos" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Cancelar</a>
                </form>
                
                <hr class="my-5">
                
                <!-- Sección de asignación de productos -->
                <h3><i class="bi bi-box"></i> Asignar Productos al Descuento</h3>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> 
                    Selecciona los productos que tendrán aplicado este descuento 
                    (<strong><?php echo $descuento_model->tipo; ?>: <?php echo $descuento_model->valor; ?><?php echo $descuento_model->tipo === 'PORCENTAJE' ? '%' : '$'; ?></strong>)
                </div>
                
                <form method="POST" action="<?php echo $base_path; ?>/index.php?page=descuentoController&accion=asignar_productos">
                    <input type="hidden" name="id_descuento" value="<?php echo $descuento_model->id; ?>">
                    
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50px;">
                                                <input type="checkbox" id="seleccionar_todos" class="form-check-input">
                                            </th>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Categoría</th>
                                            <th>Precio</th>
                                            <th>Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if (isset($todos_productos) && $todos_productos) {
                                            while($prod = $todos_productos->fetch(PDO::FETCH_ASSOC)): 
                                                $checked = in_array($prod['id'], $ids_asignados) ? 'checked' : '';
                                        ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="productos[]" value="<?php echo $prod['id']; ?>" class="form-check-input producto-check" <?php echo $checked; ?>>
                                            </td>
                                            <td><strong><?php echo htmlspecialchars($prod['codigo']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($prod['nombre']); ?></td>
                                            <td><?php echo htmlspecialchars($prod['categoria_nombre'] ?? 'Sin categoría'); ?></td>
                                            <td>$<?php echo number_format($prod['precio'], 2); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo ($prod['stock'] ?? 0) > 0 ? 'success' : 'danger'; ?>">
                                                    <?php echo $prod['stock'] ?? 0; ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php 
                                            endwhile;
                                        } else {
                                        ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No hay productos disponibles</td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-check-circle"></i> Guardar Asignación de Productos
                    </button>
                </form>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Seleccionar/Deseleccionar todos
        document.getElementById('seleccionar_todos').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.producto-check');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>
</body>
</html>