<?php
// Layout for the admin panel and the doctor panel. The menu depends on the role.
$user = auth();

$menus = [
    'admin' => [
        ['admin', 'Dashboard', 'bi-speedometer2', true],
        ['admin/appointments', 'Appointments', 'bi-calendar2-check', false],
        ['admin/doctors', 'Doctors', 'bi-person-badge', false],
        ['admin/schedules', 'Schedules', 'bi-clock', false],
        ['admin/hospitals', 'Hospitals', 'bi-hospital', false],
        ['admin/categories', 'Categories', 'bi-tags', false],
        ['admin/users', 'Patients', 'bi-people', false],
    ],
    'doctor' => [
        ['doctor', 'Dashboard', 'bi-speedometer2', true],
        ['doctor/appointments', 'My Appointments', 'bi-calendar2-check', false],
        ['doctor/schedule', 'Schedule', 'bi-clock', false],
        ['doctor/profile', 'Profile', 'bi-person', false],
    ],
];
$menu = $menus[$user['role']] ?? [];
?>
<!doctype html>
<html lang="en">
<head>
    <?= partial('partials/head', ['title' => $title ?? null]) ?>
</head>
<body>

<div class="d-flex min-vh-100">
    <aside class="panel-sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar">
        <div class="offcanvas-header">
            <span class="brand fw-bold"><i class="bi bi-heart-pulse-fill"></i> <?= e(config('app_name')) ?></span>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#sidebar"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column p-3">
            <a href="<?= url('/') ?>" class="brand fw-bold fs-5 text-decoration-none mb-1 d-none d-lg-block">
                <i class="bi bi-heart-pulse-fill"></i> <?= e(config('app_name')) ?>
            </a>
            <div class="small text-uppercase opacity-50 mb-3 d-none d-lg-block"><?= $user['role'] === 'admin' ? 'Admin Panel' : 'Doctor Panel' ?></div>

            <nav class="nav flex-column gap-1">
                <?php foreach ($menu as [$path, $label, $icon, $exact]): ?>
                    <a class="nav-link <?= nav_active($path, $exact) ?>" href="<?= url($path) ?>">
                        <i class="bi <?= $icon ?>"></i> <?= e($label) ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="mt-auto pt-4">
                <a class="nav-link" href="<?= url('/') ?>"><i class="bi bi-globe"></i> View Website</a>
            </div>
        </div>
    </aside>

    <div class="flex-grow-1 min-w-0 d-flex flex-column">
        <header class="panel-topbar px-3 px-lg-4 py-2 d-flex align-items-center gap-2">
            <button class="btn btn-light d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-label="Menu">
                <i class="bi bi-list"></i>
            </button>
            <h1 class="h5 mb-0 flex-grow-1 text-truncate"><?= e($title ?? '') ?></h1>

            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle"></i> <span class="d-none d-sm-inline"><?= e($user['name']) ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <?php if ($user['role'] === 'doctor'): ?>
                        <li><a class="dropdown-item" href="<?= url('doctor/profile') ?>"><i class="bi bi-person me-2"></i>Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                    <?php endif; ?>
                    <li>
                        <form method="post" action="<?= url('logout') ?>">
                            <?= csrf_field() ?>
                            <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>

        <main class="p-3 p-lg-4 flex-grow-1">
            <?= partial('partials/flash') ?>
            <?= $content ?>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
