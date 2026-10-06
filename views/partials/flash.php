<?php foreach (App\Core\Session::flashes() as $type => $message): ?>
    <div class="alert alert-<?= e($type) ?> alert-dismissible fade show" role="alert">
        <?= e($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endforeach; ?>
