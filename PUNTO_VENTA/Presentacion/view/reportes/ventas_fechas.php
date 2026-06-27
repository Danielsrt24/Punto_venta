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
    <title>Ventas por Fecha</title>
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
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=usuarios">Usuarios</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=clientes">Clientes</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=categorias">Categorías</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=productos">Productos</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=ventas">Ventas</a></li>
                        <li class="nav-item"><a class="nav-link active" href="<?php echo $base_path; ?>/index.php?page=reportes">Reportes</a></li>
                    </ul>
                </div>
            </nav>
            
            <main class="col-md-10 ms-sm-auto px-4 py-4">
                <h2><i class="bi bi-calendar"></i> Ventas por Fecha</h2>
                
                <div class="mb-3">
                    <a href="<?php echo $base_path; ?>/index.php?page=reportes" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver a Reportes
                    </a>
                </div>
                
                <!-- Formulario de Filtro -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-funnel"></i> Filtros de Búsqueda</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="<?php echo $base_path; ?>/index.php" class="row g-3">
                            <input type="hidden" name="page" value="reportes">
                            <input type="hidden" name="accion" value="ventas_fecha">
                            
                            <div class="col-md-3">
                                <label class="form-label">Fecha Inicio</label>
                                <input type="date" name="fecha_inicio" class="form-control" value="<?php echo $fecha_inicio; ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Fecha Fin</label>
                                <input type="date" name="fecha_fin" class="form-control" value="<?php echo $fecha_fin; ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Cajero (opcional)</label>
                                <select name="usuario_id" class="form-select">
                                    <option value="">-- Todos los cajeros --</option>
                                    <?php 
                                    $usuarios->execute();
                                    while($u = $usuarios->fetch(PDO::FETCH_ASSOC)): 
                                    ?>
                                        <option value="<?php echo $u['id']; ?>" <?php echo $usuario_id == $u['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($u['nombre']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Consultar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <?php if(!empty($usuario_id)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        Mostrando ventas filtradas por cajero. 
                        <a href="<?php echo $base_path; ?>/index.php?page=reportes&accion=ventas_fecha&fecha_inicio=<?php echo $fecha_inicio; ?>&fecha_fin=<?php echo $fecha_fin; ?>">
                            Ver todos los cajeros
                        </a>
                    </div>
                <?php endif; ?>
                
                <!-- Tabla de Ventas -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Detalle de Ventas</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Atendió</th>
                                    <th>Subtotal</th>
                                    <th>Descuento</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if ($ventas->rowCount() > 0) {
                                    while($row = $ventas->fetch(PDO::FETCH_ASSOC)): 
                                ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($row['fecha'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['nombre_clientes'] ?? 'Consumidor Final'); ?></td>
                                    <td><?php echo htmlspecialchars($row['nombre_usuario']); ?></td>
                                    <td>$<?php echo number_format($row['subtotal'], 2); ?></td>
                                    <td>$<?php echo number_format($row['descuento_total'], 2); ?></td>
                                    <td><strong>$<?php echo number_format($row['total'], 2); ?></strong></td>
                                </tr>
                                <?php 
                                    endwhile;
                                } else {
                                    echo "<tr><td colspan='7' class='text-center'>No hay ventas en este período</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Resumen por Cajero -->
                <div class="card mb-4">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="bi bi-people"></i> Ventas por Cajero</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Cajero</th>
                                    <th>Cantidad de Ventas</th>
                                    <th>Total Vendido</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $contador = 1;
                                $gran_total = 0;
                                while($row = $total_por_usuario->fetch(PDO::FETCH_ASSOC)): 
                                    $gran_total += $row['total_vendido'];
                                ?>
                                <tr class="<?php echo $usuario_id == $row['id'] ? 'table-primary' : ''; ?>">
                                    <td><?php echo $contador++; ?></td>
                                    <td><strong><?php echo htmlspecialchars($row['nombre']); ?></strong></td>
                                    <td><?php echo $row['cantidad_ventas']; ?></td>
                                    <td><strong>$<?php echo number_format($row['total_vendido'], 2); ?></strong></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot class="table-dark">
                                <tr>
                                    <td colspan="3" class="text-end"><strong>GRAN TOTAL:</strong></td>
                                    <td><strong>$<?php echo number_format($gran_total, 2); ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                
                <!-- Resumen por Fecha -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Resumen por Fecha</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cantidad de Ventas</th>
                                    <th>Subtotal</th>
                                    <th>Descuentos</th>
                                    <th>Total Vendido</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if ($total->rowCount() > 0) {
                                    while($row = $total->fetch(PDO::FETCH_ASSOC)): 
                                ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($row['fecha'])); ?></td>
                                    <td><?php echo $row['cantidad_ventas']; ?></td>
                                    <td>$<?php echo number_format($row['total_subtotal'], 2); ?></td>
                                    <td>$<?php echo number_format($row['descuento_total'], 2); ?></td>
                                    <td><strong>$<?php echo number_format($row['total_vendido'], 2); ?></strong></td>
                                </tr>
                                <?php 
                                    endwhile;
                                } else {
                                    echo "<tr><td colspan='5' class='text-center'>No hay datos</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>