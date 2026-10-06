<?php
$titles = [403 => 'Access denied', 404 => 'Page not found', 419 => 'Page expired', 500 => 'Server error'];
?>
<div class="container text-center py-5">
    <div class="display-1 fw-bold text-primary"><?= (int) $code ?></div>
    <h1 class="h3 mb-3"><?= e($titles[$code] ?? 'Error') ?></h1>
    <p class="text-muted mb-4"><?= e($message) ?></p>
    <a href="<?= url('/') ?>" class="btn btn-primary"><i class="bi bi-house"></i> Go to Home</a>
</div>
