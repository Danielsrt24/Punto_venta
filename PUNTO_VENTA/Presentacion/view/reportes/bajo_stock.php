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
    <title>Productos con Bajo Stock</title>
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
                        <li class="nav-item"><a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=reportes">Reportes</a></li>
                    </ul>
                </div>
            </nav>
            
            <main class="col-md-10 ms-sm-auto px-4 py-4">
                <h2><i class="bi bi-exclamation-triangle"></i> Productos con Bajo Stock</h2>
                
                <div class="mb-3">
                    <a href="<?php echo $base_path; ?>/index.php?page=reportes" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver a Reportes
                    </a>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead class="table-warning">
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>Stock Actual</th>
                                    <th>Precio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if ($productos->rowCount() > 0) {
                                    while($row = $productos->fetch(PDO::FETCH_ASSOC)): 
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['codigo']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nombre_categoria']); ?></td>
                                    <td>
                                        <span class="badge bg-danger"><?php echo $row['stock']; ?></span>
                                    </td>
                                    <td>$<?php echo number_format($row['precio'], 2); ?></td>
                                </tr>
                                <?php 
                                    endwhile;
                                } else {
                                    echo "<tr><td colspan='5' class='text-center'>No hay productos con bajo stock</td></tr>";
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