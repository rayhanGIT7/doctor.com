<div class="container py-4">
    <h1 class="h4 section-title mb-3">My Appointments</h1>

    <ul class="nav nav-pills mb-3">
        <?php foreach (['upcoming' => 'Upcoming', 'past' => 'History', 'all' => 'All'] as $key => $label): ?>
            <li class="nav-item">
                <a class="nav-link <?= $period === $key ? 'active' : '' ?>" href="<?= url('my/appointments', ['period' => $key]) ?>"><?= $label ?></a>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="card">
        <?php if (!$appointments): ?>
            <div class="empty-state">
                <i class="bi bi-calendar2-x"></i>
                No appointments here yet.
                <div class="mt-3"><a href="<?= url('doctors') ?>" class="btn btn-primary btn-sm">Find a doctor</a></div>
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($appointments as $appointment): ?>
                    <?= partial('patient/_appointment_row', ['appointment' => $appointment]) ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?= partial('partials/pagination', ['total' => $total, 'perPage' => $perPage]) ?>
</div>
