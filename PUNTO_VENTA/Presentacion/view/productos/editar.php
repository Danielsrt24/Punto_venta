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
    <title>Editar Producto</title>
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
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=dashboard">Inicio</a></li>
                        <?php if(Session::get('user_rol') === 'ADMINISTRADOR'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=usuarios">Usuarios</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=clientes">Clientes</a></li>
                        <?php if(in_array(Session::get('user_rol'), ['ADMINISTRADOR', 'SUPERVISOR'])): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=categorias">Categorías</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=marcas">Marcas</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=descuentos">Descuentos</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="nav-link active" href="<?php echo $base_path; ?>/index.php?page=productos">Productos</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=ventas">Ventas</a></li>
                    </ul>
                </div>
            </nav>
            
            <main class="col-md-10 ms-sm-auto px-4 py-4">
                <h2><i class="bi bi-pencil"></i> Editar Producto</h2>
                <?php if(isset($error)): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
                
                <form method="POST" action="<?php echo $base_path; ?>/index.php?page=productosController&accion=editar" class="mt-4" style="max-width: 900px;">
                    <input type="hidden" name="id" value="<?php echo $producto->id; ?>">
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Código *</label>
                            <input type="text" name="codigo" class="form-control" value="<?php echo htmlspecialchars($producto->codigo); ?>" required>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Nombre *</label>
                            <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($producto->nombre); ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2"><?php echo htmlspecialchars($producto->descripcion); ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Precio *</label>
                            <input type="number" step="0.01" name="precio" class="form-control" value="<?php echo $producto->precio; ?>" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Stock *</label>
                            <input type="number" name="stock" class="form-control" min="0" value="<?php echo $producto->stock; ?>" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Estado</label>
                            <select name="estado" class="form-select">
                                <option value="1" <?php echo $producto->estado ? 'selected' : ''; ?>>Activo</option>
                                <option value="0" <?php echo !$producto->estado ? 'selected' : ''; ?>>Inactivo</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Categoría *</label>
                            <select name="id_categoria" class="form-select" required>
                                <option value="">Seleccione una categoría</option>
                                <?php 
                                $categorias->execute();
                                while($cat = $categorias->fetch(PDO::FETCH_ASSOC)): 
                                ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $producto->id_categoria ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['nombre']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Marca</label>
                            <select name="id_marca" class="form-select">
                                <option value="">-- Sin marca --</option>
                                <?php 
                                $marcas->execute();
                                while($m = $marcas->fetch(PDO::FETCH_ASSOC)): 
                                ?>
                                    <option value="<?php echo $m['id']; ?>" <?php echo $m['id'] == $producto->id_marca ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($m['nombre']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- SECCIÓN DE DESCUENTOS -->
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="bi bi-percent"></i> Descuentos Disponibles</h5>
                        </div>
                        <div class="card-body">
                            <?php 
                            $hay_descuentos = false;
                            $descuentos->execute();
                            while($desc = $descuentos->fetch(PDO::FETCH_ASSOC)): 
                                $hay_descuentos = true;
                                $checked = in_array($desc['id'], $ids_descuentos ?? []) ? 'checked' : '';
                            ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="descuentos[]" 
                                       value="<?php echo $desc['id']; ?>" id="descuento_<?php echo $desc['id']; ?>"
                                       <?php echo $checked; ?>>
                                <label class="form-check-label" for="descuento_<?php echo $desc['id']; ?>">
                                    <strong><?php echo htmlspecialchars($desc['nombre']); ?></strong>
                                    <span class="badge bg-<?php echo $desc['tipo'] === 'porcentaje' ? 'success' : 'primary'; ?>">
                                        <?php echo $desc['valor']; ?><?php echo $desc['tipo'] === 'porcentaje' ? '%' : '$'; ?>
                                    </span>
                                    <small class="text-muted">
                                        (<?php echo date('d/m/Y', strtotime($desc['fecha_inicio'])); ?> - 
                                        <?php echo date('d/m/Y', strtotime($desc['fecha_fin'])); ?>)
                                    </small>
                                </label>
                            </div>
                            <?php endwhile; ?>
                            
                            <?php if(!$hay_descuentos): ?>
                            <p class="text-muted mb-0">
                                <i class="bi bi-info-circle"></i> No hay descuentos activos disponibles
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-circle"></i> Actualizar Producto
                    </button>
                    <a href="<?php echo $base_path; ?>/index.php?page=productos" class="btn btn-secondary btn-lg">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                </form>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>