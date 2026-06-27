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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #<?php echo $venta->id; ?></title>
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
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=dashboard">Inicio</a></li>
                        <li class="nav-item"><a class="nav-link active" href="<?php echo $base_path; ?>/index.php?page=ventas">Ventas</a></li>
                    </ul>
                </div>
            </nav>
            
            <main class="col-md-10 ms-sm-auto px-4 py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-receipt"></i> Factura #<?php echo $venta->id; ?></h2>
                    <div>
                        <button onclick="window.print()" class="btn btn-info btn-lg me-2">
                            <i class="bi bi-printer"></i> Imprimir
                        </button>
                        <a href="<?php echo $base_path; ?>/index.php?page=ventas" class="btn btn-secondary btn-lg">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Información de la Venta</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p><strong><i class="bi bi-calendar"></i> Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($venta->fecha)); ?></p>
                                <p><strong><i class="bi bi-person"></i> Cliente:</strong> <?php echo htmlspecialchars($venta->nombre_cliente ?? 'Consumidor Final'); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong><i class="bi bi-person-badge"></i> Atendió:</strong> <?php echo htmlspecialchars($venta->nombre_usuario); ?></p>
                                <p><strong><i class="bi bi-check-circle"></i> Estado:</strong> <span class="badge bg-success">Activa</span></p>
                            </div>
                        </div>
                        
                        <h5 class="mt-4 mb-3"><i class="bi bi-box"></i> Productos</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Código</th>
                                        <th>Producto</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-end">Precio Unit.</th>
                                        <th class="text-end">Subtotal</th>
                                        <th class="text-end">Descuentos</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total_descuentos = 0;
                                    while($det = $detalles->fetch(PDO::FETCH_ASSOC)): 
                                        $descuento_producto = floatval($det['descuento'] ?? 0);
                                        $total_producto = $det['subtotal'] - $descuento_producto;
                                        $total_descuentos += $descuento_producto;
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($det['codigo']); ?></td>
                                        <td><?php echo htmlspecialchars($det['nombre_producto']); ?></td>
                                        <td class="text-center"><?php echo $det['cantidad']; ?></td>
                                        <td class="text-end">$<?php echo number_format($det['precio_unitario'], 2); ?></td>
                                        <td class="text-end">$<?php echo number_format($det['subtotal'], 2); ?></td>
                                        <td class="text-end text-danger">
                                            <?php if($descuento_producto > 0): ?>
                                                -$<?php echo number_format($descuento_producto, 2); ?>
                                            <?php else: ?>
                                                $0.00
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end"><strong>$<?php echo number_format($total_producto, 2); ?></strong></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="row justify-content-end mt-4">
                            <div class="col-md-5">
                                <table class="table table-bordered">
                                    <tr>
                                        <td><strong>Subtotal:</strong></td>
                                        <td class="text-end">$<?php echo number_format($venta->subtotal, 2); ?></td>
                                    </tr>
                                    <?php if($total_descuentos > 0): ?>
                                    <tr class="table-warning">
                                        <td><strong><i class="bi bi-percent"></i> Descuentos:</strong></td>
                                        <td class="text-end text-danger"><strong>-$<?php echo number_format($total_descuentos, 2); ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Base Gravada:</strong></td>
                                        <td class="text-end">$<?php echo number_format($venta->subtotal - $total_descuentos, 2); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td><strong>IVA (13%):</strong></td>
                                        <td class="text-end">$<?php echo number_format(($venta->subtotal - $total_descuentos) * 0.13, 2); ?></td>
                                    </tr>
                                    <tr class="table-success">
                                        <td><h5 class="mb-0"><strong><i class="bi bi-cash-stack"></i> TOTAL:</strong></h5></td>
                                        <td class="text-end"><h5 class="mb-0"><strong>$<?php echo number_format($venta->total, 2); ?></strong></h5></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        @media print {
            .navbar, .sidebar, .btn { display: none !important; }
            .main-content { margin-left: 0 !important; }
            .card { box-shadow: none !important; border: 1px solid #ddd; }
        }
    </style>
</body>
</html>