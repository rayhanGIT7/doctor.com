<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3"><?= partial('partials/stat_card', ['icon' => 'bi-calendar-day', 'value' => $stats['today'], 'label' => "Today's appointments"]) ?></div>
    <div class="col-6 col-xl-3"><?= partial('partials/stat_card', ['icon' => 'bi-calendar-event', 'value' => $stats['upcoming'], 'label' => 'Upcoming']) ?></div>
    <div class="col-6 col-xl-3"><?= partial('partials/stat_card', ['icon' => 'bi-check2-circle', 'value' => $stats['completed'], 'label' => 'Completed']) ?></div>
    <div class="col-6 col-xl-3"><?= partial('partials/stat_card', ['icon' => 'bi-journal-medical', 'value' => $stats['total'], 'label' => 'All appointments']) ?></div>
    <div class="col-6 col-xl-3"><?= partial('partials/stat_card', ['icon' => 'bi-person-badge', 'value' => $doctors, 'label' => 'Active doctors']) ?></div>
    <div class="col-6 col-xl-3"><?= partial('partials/stat_card', ['icon' => 'bi-hospital', 'value' => $hospitals, 'label' => 'Active hospitals']) ?></div>
    <div class="col-6 col-xl-3"><?= partial('partials/stat_card', ['icon' => 'bi-tags', 'value' => $categories, 'label' => 'Categories']) ?></div>
    <div class="col-6 col-xl-3"><?= partial('partials/stat_card', ['icon' => 'bi-people', 'value' => $patients, 'label' => 'Patients']) ?></div>
</div>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong>Today's appointments</strong>
        <a href="<?= url('admin/appointments', ['period' => 'today']) ?>" class="small">View all</a>
    </div>
    <?php if (!$today): ?>
        <div class="empty-state"><i class="bi bi-calendar2-x"></i> No appointments today.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Time</th><th>Patient</th><th>Doctor</th><th>Hospital</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($today as $appointment): ?>
                    <tr>
                        <td class="fw-semibold"><?= format_time($appointment['appointment_time']) ?></td>
                        <td><?= e($appointment['patient_name']) ?><div class="small text-muted"><?= e($appointment['patient_phone']) ?></div></td>
                        <td><?= e($appointment['doctor_name']) ?></td>
                        <td><?= e($appointment['hospital_name']) ?></td>
                        <td><?= status_badge($appointment['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
