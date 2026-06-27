<?php
require_once __DIR__ . '/../../../Negocio/helpers/session.php';
Session::init();
if (!Session::isLoggedIn()) { header("Location: ../../../index.php?page=login"); exit(); }

$filtro = $_GET['buscar'] ?? '';
$descuentos = $descuento_model->readAll($filtro);
$base_path = "/PUNTO_VENTA";
$rol = Session::get('user_rol');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descuentos - Sistema de Ventas</title>
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-percent"></i> Gestión de Descuentos</h2>
                    <?php if($rol !== 'CAJERO'): ?>
                    <a href="<?php echo $base_path; ?>/index.php?page=descuentoController&accion=crear" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Nuevo Descuento
                    </a>
                    <?php endif; ?>
                </div>
                
                <?php if(isset($_GET['mensaje'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($_GET['mensaje']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if(isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <form method="GET" action="<?php echo $base_path; ?>/index.php" class="row g-2">
                            <input type="hidden" name="page" value="descuentos">
                            <div class="col-md-10">
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                    <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre o descripción..." value="<?php echo htmlspecialchars($filtro); ?>">
                                </div>
                            </div>
                            <div class="col-md-2 d-grid gap-2">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Buscar</button>
                                <?php if(!empty($filtro)): ?>
                                <a href="<?php echo $base_path; ?>/index.php?page=descuentos" class="btn btn-secondary btn-sm"><i class="bi bi-x-circle"></i> Limpiar</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Tipo</th>
                                        <th>Valor</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $hay_resultados = false;
                                    if ($descuentos) {
                                        while($row = $descuentos->fetch(PDO::FETCH_ASSOC)): 
                                            $hay_resultados = true;
                                    ?>
                                    <tr class="<?php echo $row['estado'] == 0 ? 'table-secondary' : ''; ?>">
                                        <td><strong><?php echo htmlspecialchars($row['nombre']); ?></strong></td>
                                        <td>
                                            <span class="badge bg-<?php echo $row['tipo'] === 'PORCENTAJE' ? 'success' : 'info'; ?>">
                                                <?php echo htmlspecialchars($row['tipo']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['valor']); ?> <?php echo $row['tipo'] === 'PORCENTAJE' ? '%' : '$'; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['fecha_inicio'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['fecha_fin'])); ?></td>
                                        <td>
                                            <?php if($row['estado'] == 1): ?>
                                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> Activo</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><i class="bi bi-eye-slash"></i> Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo $base_path; ?>/index.php?page=descuentoController&accion=editar&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="<?php echo $base_path; ?>/index.php?page=descuentoController&accion=eliminar&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar descuento?')" title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php 
                                        endwhile;
                                    }
                                    if (!$hay_resultados): 
                                    ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                            <p class="mt-2">No hay descuentos registrados</p>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>