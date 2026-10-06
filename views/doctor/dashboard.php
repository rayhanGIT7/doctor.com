<div class="row g-3 mb-4">
    <div class="col-6 col-xl"><?= partial('partials/stat_card', ['icon' => 'bi-calendar-day', 'value' => $stats['today'], 'label' => 'Today']) ?></div>
    <div class="col-6 col-xl"><?= partial('partials/stat_card', ['icon' => 'bi-calendar-event', 'value' => $stats['upcoming'], 'label' => 'Upcoming']) ?></div>
    <div class="col-6 col-xl"><?= partial('partials/stat_card', ['icon' => 'bi-check2-circle', 'value' => $stats['completed'], 'label' => 'Completed']) ?></div>
    <div class="col-6 col-xl"><?= partial('partials/stat_card', ['icon' => 'bi-x-circle', 'value' => $stats['cancelled'], 'label' => 'Cancelled']) ?></div>
    <div class="col-12 col-xl"><?= partial('partials/stat_card', ['icon' => 'bi-journal-medical', 'value' => $stats['total'], 'label' => 'Total']) ?></div>
</div>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong>Today's appointments &middot; <?= format_date(today()) ?></strong>
        <a href="<?= url('doctor/appointments') ?>" class="small">All appointments</a>
    </div>
    <?php if (!$today): ?>
        <div class="empty-state"><i class="bi bi-cup-hot"></i> No appointments today.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Time</th><th>Patient</th><th>Note</th><th>Status</th><th class="text-end">Action</th></tr></thead>
                <tbody>
                <?php foreach ($today as $appointment): ?>
                    <tr>
                        <td class="fw-semibold"><?= format_time($appointment['appointment_time']) ?></td>
                        <td><?= e($appointment['patient_name']) ?><div class="small text-muted"><?= e($appointment['patient_phone']) ?> &middot; <?= e($appointment['appointment_no']) ?></div></td>
                        <td class="small text-muted"><?= e($appointment['note']) ?></td>
                        <td><?= status_badge($appointment['status']) ?></td>
                        <td class="text-end"><?= partial('doctor/_status_actions', ['appointment' => $appointment]) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
