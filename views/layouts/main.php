<?php
use App\Core\Auth;

$user = auth();
?>
<!doctype html>
<html lang="en">
<head>
    <?= partial('partials/head', ['title' => $title ?? null]) ?>
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= url('/') ?>">
            <i class="bi bi-heart-pulse-fill text-primary"></i> <?= e(config('app_name')) ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link <?= nav_active('/', true) ?>" href="<?= url('/') ?>">Home</a></li>
                <li class="nav-item"><a class="nav-link <?= nav_active('doctors') ?>" href="<?= url('doctors') ?>">Find Doctors</a></li>
                <?php if ($user && $user['role'] === 'patient'): ?>
                    <li class="nav-item"><a class="nav-link <?= nav_active('my/appointments') ?>" href="<?= url('my/appointments') ?>">My Appointments</a></li>
                <?php endif; ?>
            </ul>

            <?php if (!$user): ?>
                <div class="d-flex gap-2">
                    <a href="<?= url('login') ?>" class="btn btn-outline-primary">Login</a>
                    <a href="<?= url('register') ?>" class="btn btn-primary">Register</a>
                </div>
            <?php else: ?>
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> <?= e($user['name']) ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= url(Auth::homePath()) ?>"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                        <?php if ($user['role'] === 'patient'): ?>
                            <li><a class="dropdown-item" href="<?= url('my/profile') ?>"><i class="bi bi-person me-2"></i>Profile</a></li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="post" action="<?= url('logout') ?>">
                                <?= csrf_field() ?>
                                <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main class="flex-grow-1">
    <?php if (App\Core\Session::flashes()): ?>
        <div class="container pt-3"><?= partial('partials/flash') ?></div>
    <?php endif; ?>

    <?= $content ?>
</main>

<footer class="bg-white border-top mt-5 py-4">
    <div class="container d-flex flex-column flex-md-row justify-content-between gap-2 small text-muted">
        <span><i class="bi bi-heart-pulse-fill text-primary"></i> <?= e(config('app_name')) ?> &copy; <?= date('Y') ?> &middot; Online doctor appointments in Bangladesh</span>
        <span>
            <a href="<?= url('doctors') ?>" class="text-muted me-3">Find Doctors</a>
            <a href="<?= url('register') ?>" class="text-muted">Create Account</a>
        </span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
