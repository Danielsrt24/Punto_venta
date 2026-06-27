<?php if(in_array($rol, ['ADMINISTRADOR', 'SUPERVISOR'])): ?>
<li class="nav-item">
    <a class="nav-link" href="<?php echo $base_path; ?>/index.php?page=descuentos">
        <i class="bi bi-percent"></i> Descuentos
    </a>
</li>
<?php endif; ?>