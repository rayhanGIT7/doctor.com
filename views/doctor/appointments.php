<?php use App\Models\Appointment; ?>
<ul class="nav nav-pills mb-3">
    <?php foreach (['today' => 'Today', 'upcoming' => 'Upcoming', 'past' => 'Past', 'all' => 'All'] as $key => $label): ?>
        <li class="nav-item">
            <a class="nav-link <?= $period === $key ? 'active' : '' ?>" href="<?= url('doctor/appointments', ['period' => $key]) ?>"><?= $label ?></a>
        </li>
    <?php endforeach; ?>
</ul>

<form method="get" class="d-flex flex-wrap gap-2 mb-3">
    <input type="hidden" name="period" value="<?= e($period) ?>">
    <input type="text" name="q" value="<?= e($filters['q']) ?>" class="form-control" placeholder="Patient name, phone or appointment no." style="max-width: 300px">
    <input type="date" name="date" value="<?= e($filters['date']) ?>" class="form-control" style="max-width: 170px">
    <select name="status" class="form-select" style="max-width: 150px">
        <option value="">All status</option>
        <?php foreach (Appointment::STATUSES as $status): ?>
            <option value="<?= $status ?>" <?= selected($status, $filters['status']) ?>><?= status_label($status) ?></option>
        <?php endforeach; ?>
    </select>
    <button class="btn btn-light border">Search</button>
</form>

<div class="card">
    <?php if (!$appointments): ?>
        <div class="empty-state"><i class="bi bi-calendar2-x"></i> No appointments found.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Patient</th><th>Date</th><th>Time</th><th>Status</th><th class="text-end">Action</th></tr></thead>
                <tbody>
                <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td>
                            <span class="fw-semibold"><?= e($appointment['patient_name']) ?></span>
                            <div class="small text-muted"><?= e($appointment['patient_phone']) ?> &middot; <?= e($appointment['appointment_no']) ?></div>
                            <?php if ($appointment['note']): ?><div class="small text-muted fst-italic"><?= e($appointment['note']) ?></div><?php endif; ?>
                        </td>
                        <td><?= format_date($appointment['appointment_date']) ?></td>
                        <td><?= format_time($appointment['appointment_time']) ?></td>
                        <td><?= status_badge($appointment['status']) ?></td>
                        <td class="text-end"><?= partial('doctor/_status_actions', ['appointment' => $appointment]) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?= partial('partials/pagination', ['total' => $total, 'perPage' => $perPage]) ?>
