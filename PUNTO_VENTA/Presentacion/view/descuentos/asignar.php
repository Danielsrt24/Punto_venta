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
    <title>Asignar Descuento a Productos</title>
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
                <span class="me-3">
                    <?php echo Session::get('user_nombre'); ?>
                </span>
                <a href="<?php echo $base_path; ?>/logout.php" class="btn btn-outline-light btn-sm">Salir</a>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid" style="margin-top: 56px;">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block bg-light" style="min-height: 100vh;">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=dashboard"><i class="bi bi-speedometer2"></i> Inicio</a></li>
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
                    </ul>
                </div>
            </nav>
            
            <main class="col-md-10 ms-sm-auto px-4 py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-link-45deg"></i> Asignar Descuento a Productos</h2>
                    <a href="<?php echo $base_path; ?>/index.php?page=descuentos" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>
                
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if(isset($_GET['mensaje'])): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['mensaje']); ?></div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="bi bi-box"></i> Asignar Descuento</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="<?php echo $base_path; ?>/index.php?page=descuentoController&accion=asignar" id="formAsignar">
                                    <div class="mb-3">
                                        <label class="form-label">Seleccionar Producto *</label>
                                        <select name="id_producto" id="id_producto" class="form-select" required onchange="this.form.submit()">
                                            <option value="">-- Seleccione un producto --</option>
                                            <?php 
                                            $productos->execute();
                                            $id_producto_seleccionado = isset($_GET['producto']) ? intval($_GET['producto']) : (isset($_POST['id_producto']) ? intval($_POST['id_producto']) : null);
                                            while($prod = $productos->fetch(PDO::FETCH_ASSOC)): 
                                            ?>
                                                <option value="<?php echo $prod['id']; ?>" <?php echo $prod['id'] == $id_producto_seleccionado ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($prod['nombre']); ?> - $<?php echo number_format($prod['precio'], 2); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Seleccionar Descuento *</label>
                                        <select name="id_descuento" class="form-select" required <?php echo !$id_producto_seleccionado ? 'disabled' : ''; ?>>
                                            <option value="">-- Seleccione un descuento --</option>
                                            <?php 
                                            if ($descuentos && $id_producto_seleccionado) {
                                                $hay_descuentos = false;
                                                while($desc = $descuentos->fetch(PDO::FETCH_ASSOC)): 
                                                    $hay_descuentos = true;
                                                    $tipo_desc = strtolower($desc['tipo']);
                                            ?>
                                                <option value="<?php echo $desc['id']; ?>">
                                                    <?php echo htmlspecialchars($desc['nombre']); ?> 
                                                    (<?php echo $desc['valor']; ?><?php echo $tipo_desc === 'porcentaje' ? '%' : '$'; ?>)
                                                </option>
                                            <?php 
                                                endwhile;
                                                if (!$hay_descuentos) {
                                                    echo '<option value="" disabled selected>No hay descuentos disponibles para este producto</option>';
                                                }
                                            } else {
                                                echo '<option value="" disabled selected>Primero seleccione un producto</option>';
                                            }
                                            ?>
                                        </select>
                                        <?php if($id_producto_seleccionado && !$hay_descuentos): ?>
                                            <small class="text-success mt-2 d-block">
                                                <i class="bi bi-check-circle"></i> Este producto ya tiene todos los descuentos disponibles asignados
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-success w-100" <?php echo (!$id_producto_seleccionado || (isset($hay_descuentos) && !$hay_descuentos)) ? 'disabled' : ''; ?>>
                                        <i class="bi bi-check-circle"></i> Asignar Descuento
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="bi bi-percent"></i> Descuentos Activos</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <?php 
                                    $descuentos_todos = $descuento->readActivos();
                                    while($desc = $descuentos_todos->fetch(PDO::FETCH_ASSOC)): 
                                        $tipo_desc = strtolower($desc['tipo']);
                                    ?>
                                        <div class="list-group-item">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($desc['nombre']); ?></h6>
                                                <small class="badge bg-<?php echo $tipo_desc === 'porcentaje' ? 'success' : 'info'; ?>">
                                                    <?php echo $desc['valor']; ?><?php echo $tipo_desc === 'porcentaje' ? '%' : '$'; ?>
                                                </small>
                                            </div>
                                            <p class="mb-1 small"><?php echo htmlspecialchars($desc['descripcion']); ?></p>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> <?php echo date('d/m/Y', strtotime($desc['fecha_inicio'])); ?> - <?php echo date('d/m/Y', strtotime($desc['fecha_fin'])); ?>
                                            </small>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>