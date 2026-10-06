<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <h1 class="h4 section-title mb-0">Hello, <?= e(auth()['name']) ?></h1>
        <a href="<?= url('doctors') ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Book an appointment</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3"><?= partial('partials/stat_card', ['icon' => 'bi-calendar-event', 'value' => $stats['upcoming'], 'label' => 'Upcoming']) ?></div>
        <div class="col-6 col-lg-3"><?= partial('partials/stat_card', ['icon' => 'bi-check2-circle', 'value' => $stats['completed'], 'label' => 'Completed']) ?></div>
        <div class="col-6 col-lg-3"><?= partial('partials/stat_card', ['icon' => 'bi-x-circle', 'value' => $stats['cancelled'], 'label' => 'Cancelled']) ?></div>
        <div class="col-6 col-lg-3"><?= partial('partials/stat_card', ['icon' => 'bi-journal-medical', 'value' => $stats['total'], 'label' => 'Total']) ?></div>
    </div>

    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>Upcoming appointments</strong>
            <a href="<?= url('my/appointments') ?>" class="small">View all</a>
        </div>
        <?php if (!$upcoming): ?>
            <div class="empty-state">
                <i class="bi bi-calendar2-x"></i>
                You have no upcoming appointments.
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($upcoming as $appointment): ?>
                    <?= partial('patient/_appointment_row', ['appointment' => $appointment]) ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-md-6">
            <a href="<?= url('my/profile') ?>" class="card category-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="icon-circle"><i class="bi bi-person"></i></span>
                    <div><div class="fw-semibold">My profile</div><div class="small text-muted">Update your details and password</div></div>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="<?= url('my/appointments', ['period' => 'past']) ?>" class="card category-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="icon-circle"><i class="bi bi-clock-history"></i></span>
                    <div><div class="fw-semibold">Appointment history</div><div class="small text-muted">See your past visits</div></div>
                </div>
            </a>
        </div>
    </div>
</div>
