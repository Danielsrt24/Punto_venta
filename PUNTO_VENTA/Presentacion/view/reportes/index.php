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
    <title>Reportes - Sistema de Ventas</title>
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
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                        <?php if($rol === 'ADMINISTRADOR'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=usuarios"><i class="bi bi-people"></i> Usuarios</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=clientes"><i class="bi bi-person"></i> Clientes</a></li>
                        <?php if(in_array($rol, ['ADMINISTRADOR', 'SUPERVISOR'])): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=categorias"><i class="bi bi-tags"></i> Categorías</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=marcas"><i class="bi bi-bookmark"></i> Marcas</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=descuentos"><i class="bi bi-percent"></i> Descuentos</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=productos"><i class="bi bi-box"></i> Productos</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=ventas"><i class="bi bi-cart"></i> Ventas</a></li>
                        <?php if(in_array($rol, ['ADMINISTRADOR', 'SUPERVISOR'])): ?>
                        <li class="nav-item"><a class="nav-link active" href="<?php echo $base_path; ?>/index.php?page=reportes"><i class="bi bi-graph-up"></i> Reportes</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>
            
            <main class="col-md-10 ms-sm-auto px-4 py-4">
                <h2><i class="bi bi-graph-up"></i> Reportes de Ventas</h2>
                
                <!-- Filtros -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-funnel"></i> Filtros</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="<?php echo $base_path; ?>/index.php" class="row g-3">
                            <input type="hidden" name="page" value="reportes">
                            <div class="col-md-4">
                                <label class="form-label">Fecha Inicio</label>
                                <input type="date" name="fecha_inicio" class="form-control" value="<?php echo $fecha_inicio; ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Fecha Fin</label>
                                <input type="date" name="fecha_fin" class="form-control" value="<?php echo $fecha_fin; ?>">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Consultar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Detalle de Ventas -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-receipt"></i> Detalle de Ventas</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Atendió</th>
                                        <th class="text-end">Subtotal</th>
                                        <th class="text-end">Descuento</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $contador = 1;
                                    $gran_total = 0;
                                    while($v = $ventas_detalle->fetch(PDO::FETCH_ASSOC)): 
                                        $gran_total += $v['total'];
                                    ?>
                                    <tr>
                                        <td><?php echo $contador++; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($v['fecha'])); ?></td>
                                        <td><?php echo htmlspecialchars($v['cliente_nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($v['usuario_nombre']); ?></td>
                                        <td class="text-end">$<?php echo number_format($v['subtotal'], 2); ?></td>
                                        <td class="text-end">$<?php echo number_format($v['descuento_total'], 2); ?></td>
                                        <td class="text-end"><strong>$<?php echo number_format($v['total'], 2); ?></strong></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="table-dark">
                                        <td colspan="6" class="text-end"><strong>GRAN TOTAL:</strong></td>
                                        <td class="text-end"><strong>$<?php echo number_format($gran_total, 2); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Ventas por Cajero/Usuario -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-people"></i> Ventas por Usuario</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Usuario</th>
                                        <th>Cantidad de Ventas</th>
                                        <th class="text-end">Total Vendido</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $contador = 1;
                                    $gran_total_cajero = 0;
                                    while($vc = $ventas_cajero->fetch(PDO::FETCH_ASSOC)): 
                                        $gran_total_cajero += $vc['total_vendido'];
                                    ?>
                                    <tr>
                                        <td><?php echo $contador++; ?></td>
                                        <td><strong><?php echo htmlspecialchars($vc['cajero_nombre']); ?></strong> <small class="text-muted">(<?php echo htmlspecialchars($vc['usuario']); ?>)</small></td>
                                        <td><?php echo $vc['cantidad_ventas']; ?></td>
                                        <td class="text-end"><strong>$<?php echo number_format($vc['total_vendido'], 2); ?></strong></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="table-dark">
                                        <td colspan="3" class="text-end"><strong>GRAN TOTAL:</strong></td>
                                        <td class="text-end"><strong>$<?php echo number_format($gran_total_cajero, 2); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Resumen por Fecha -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-calendar"></i> Resumen por Fecha</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Cantidad de Ventas</th>
                                        <th class="text-end">Subtotal</th>
                                        <th class="text-end">Descuentos</th>
                                        <th class="text-end">Total Vendido</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $gran_total_fecha = 0;
                                    while($vf = $ventas_fecha->fetch(PDO::FETCH_ASSOC)): 
                                        $gran_total_fecha += $vf['total_vendido'];
                                    ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($vf['fecha_venta'])); ?></td>
                                        <td><?php echo $vf['cantidad_ventas']; ?></td>
                                        <td class="text-end">$<?php echo number_format($vf['subtotal'], 2); ?></td>
                                        <td class="text-end">$<?php echo number_format($vf['descuentos'], 2); ?></td>
                                        <td class="text-end"><strong>$<?php echo number_format($vf['total_vendido'], 2); ?></strong></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="table-dark">
                                        <td colspan="4" class="text-end"><strong>GRAN TOTAL:</strong></td>
                                        <td class="text-end"><strong>$<?php echo number_format($gran_total_fecha, 2); ?></strong></td>
                                    </tr>
                                </tfoot>
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