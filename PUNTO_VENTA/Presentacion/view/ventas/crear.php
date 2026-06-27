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
    <title>Nueva Venta</title>
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
                        <li class="nav-item"><a class="nav-link active" href="<?php echo $base_path; ?>/index.php?page=ventas">Ventas</a></li>
                    </ul>
                </div>
            </nav>
            
            <main class="col-md-10 ms-sm-auto px-4 py-4">
                <h2><i class="bi bi-cart-plus"></i> Nueva Venta</h2>
                
                <?php if(isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo $base_path; ?>/index.php?page=ventasController&accion=crear">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-person"></i> Cliente</label>
                            <select name="id_cliente" class="form-select">
                                <option value="">Consumidor Final</option>
                                <?php while($cli = $clientes->fetch(PDO::FETCH_ASSOC)): ?>
                                    <option value="<?php echo $cli['id']; ?>"><?php echo htmlspecialchars($cli['nombre']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    
                    <hr>
                    <h5>Agregar Productos</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Producto</label>
                            <select name="producto_id[]" class="form-select" required>
                                <option value="">Seleccione un producto</option>
                                <?php foreach($productos_con_descuentos as $prod): ?>
                                    <option value="<?php echo $prod['id']; ?>" data-precio="<?php echo $prod['precio']; ?>" data-stock="<?php echo $prod['stock']; ?>">
                                        <?php echo htmlspecialchars($prod['nombre']); ?> - Stock: <?php echo $prod['stock']; ?> - $<?php echo number_format($prod['precio'], 2); ?>
                                        <?php if(!empty($prod['descuentos'])): ?>
                                            [<?php echo count($prod['descuentos']); ?> descuentos]
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Cantidad</label>
                            <input type="number" name="cantidad[]" class="form-control" value="1" min="1" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" name="agregar" class="btn btn-success w-100">
                                <i class="bi bi-plus-circle"></i> Agregar
                            </button>
                        </div>
                    </div>
                    
                    <?php if(isset($_POST['producto_id']) && is_array($_POST['producto_id'])): ?>
                    <table class="table table-bordered mt-3">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Precio Unit.</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>Descuentos</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $subtotal_general = 0;
                            $total_descuentos = 0;
                            
                            foreach($_POST['producto_id'] as $i => $id_producto):
                                $cantidad = intval($_POST['cantidad'][$i]);
                                
                                // OBTENER PRECIO DESDE LA BD (no desde POST)
                                $query_precio = "SELECT nombre, precio FROM productos WHERE id = :id";
                                $stmt_precio = $db->prepare($query_precio);
                                $stmt_precio->bindParam(':id', $id_producto, PDO::PARAM_INT);
                                $stmt_precio->execute();
                                $prod = $stmt_precio->fetch(PDO::FETCH_ASSOC);
                                
                                $precio = floatval($prod['precio']);
                                $subtotal = $precio * $cantidad;
                                $subtotal_general += $subtotal;
                                
                                $descuentos_prod = obtenerDescuentosProducto($db, $id_producto);
                                $descuento_producto = 0;
                                $descuentos_aplicados = [];
                                
                                foreach($descuentos_prod as $desc):
                                    $tipo_desc = strtolower($desc['tipo']);
                                    
                                    if ($tipo_desc === 'porcentaje') {
                                        $descuento_calc = $subtotal * ($desc['valor'] / 100);
                                        $simbolo = '%';
                                    } else {
                                        $descuento_calc = floatval($desc['valor']);
                                        $simbolo = '$';
                                    }
                                    $descuento_producto += $descuento_calc;
                                    $descuentos_aplicados[] = $desc['nombre'] . ' (' . $desc['valor'] . $simbolo . ')';
                                endforeach;
                                
                                $total_descuentos += $descuento_producto;
                                $total_producto = $subtotal - $descuento_producto;
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($prod['nombre']); ?></td>
                                <td>$<?php echo number_format($precio, 2); ?></td>
                                <td><?php echo $cantidad; ?></td>
                                <td>$<?php echo number_format($subtotal, 2); ?></td>
                                <td>
                                    <?php if(!empty($descuentos_aplicados)): ?>
                                        <ul class="mb-0 ps-3">
                                            <?php foreach($descuentos_aplicados as $desc_nombre): ?>
                                                <li><small><?php echo $desc_nombre; ?></small></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <small class="text-muted">Sin descuentos</small>
                                    <?php endif; ?>
                                </td>
                                <td><strong>$<?php echo number_format($total_producto, 2); ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                <td colspan="3"><strong>$<?php echo number_format($subtotal_general, 2); ?></strong></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Descuentos Productos:</strong></td>
                                <td colspan="3"><strong class="text-danger">- $<?php echo number_format($total_descuentos, 2); ?></strong></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Base Gravada:</strong></td>
                                <td colspan="3"><strong>$<?php echo number_format($subtotal_general - $total_descuentos, 2); ?></strong></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end"><strong>IVA (13%):</strong></td>
                                <td colspan="3"><strong>$<?php echo number_format(($subtotal_general - $total_descuentos) * 0.13, 2); ?></strong></td>
                            </tr>
                            <tr class="table-success">
                                <td colspan="3" class="text-end"><h5 class="mb-0"><strong>TOTAL:</strong></h5></td>
                                <td colspan="3"><h5 class="mb-0"><strong>$<?php echo number_format(($subtotal_general - $total_descuentos) * 1.13, 2); ?></strong></h5></td>
                            </tr>
                        </tfoot>
                    </table>
                    
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-circle"></i> Registrar Venta
                    </button>
                    <?php endif; ?>
                    
                    <a href="<?php echo $base_path; ?>/index.php?page=ventas" class="btn btn-secondary btn-lg">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                </form>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>