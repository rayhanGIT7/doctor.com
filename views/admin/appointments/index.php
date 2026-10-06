<?php use App\Models\Appointment; ?>
<form method="get" class="card mb-3">
    <div class="card-body d-flex flex-wrap gap-2">
        <input type="text" name="q" value="<?= e($filters['q']) ?>" class="form-control" placeholder="Patient, phone or appointment no." style="max-width: 260px">
        <select name="doctor_id" class="form-select" style="max-width: 220px">
            <option value="">All doctors</option>
            <?php foreach ($doctors as $doctor): ?>
                <option value="<?= $doctor['id'] ?>" <?= selected($doctor['id'], $filters['doctor_id']) ?>><?= e($doctor['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="period" class="form-select" style="max-width: 150px">
            <option value="">Any time</option>
            <option value="today" <?= selected('today', $filters['period']) ?>>Today</option>
            <option value="upcoming" <?= selected('upcoming', $filters['period']) ?>>Upcoming</option>
            <option value="past" <?= selected('past', $filters['period']) ?>>Past</option>
        </select>
        <input type="date" name="date" value="<?= e($filters['date']) ?>" class="form-control" style="max-width: 170px">
        <select name="status" class="form-select" style="max-width: 150px">
            <option value="">All status</option>
            <?php foreach (Appointment::STATUSES as $status): ?>
                <option value="<?= $status ?>" <?= selected($status, $filters['status']) ?>><?= status_label($status) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-primary">Filter</button>
        <a href="<?= url('admin/appointments') ?>" class="btn btn-light border">Reset</a>
    </div>
</form>

<div class="card">
    <?php if (!$appointments): ?>
        <div class="empty-state"><i class="bi bi-calendar2-x"></i> No appointments found.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Appointment</th><th>Patient</th><th>Doctor</th><th>Date &amp; time</th><th>Status</th><th class="text-end">Change status</th></tr></thead>
                <tbody>
                <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td class="small fw-semibold"><?= e($appointment['appointment_no']) ?></td>
                        <td><?= e($appointment['patient_name']) ?><div class="small text-muted"><?= e($appointment['patient_phone']) ?></div></td>
                        <td><?= e($appointment['doctor_name']) ?><div class="small text-muted"><?= e($appointment['hospital_name']) ?></div></td>
                        <td><?= format_date($appointment['appointment_date']) ?><div class="small text-muted"><?= format_time($appointment['appointment_time']) ?></div></td>
                        <td><?= status_badge($appointment['status']) ?></td>
                        <td class="text-end">
                            <form method="post" action="<?= url('admin/appointments/' . $appointment['id'] . '/status') ?>" class="d-inline-flex gap-1">
                                <?= csrf_field() ?>
                                <select name="status" class="form-select form-select-sm" style="width: 130px">
                                    <?php foreach (Appointment::STATUSES as $status): ?>
                                        <option value="<?= $status ?>" <?= selected($status, $appointment['status']) ?>><?= status_label($status) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn btn-sm btn-light border">Save</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?= partial('partials/pagination', ['total' => $total, 'perPage' => $perPage]) ?>
