<form method="get" class="card mb-4">
    <div class="card-body d-flex flex-wrap align-items-center gap-2">
        <label class="form-label mb-0" for="doctor_id">Doctor</label>
        <select class="form-select" id="doctor_id" name="doctor_id" style="max-width: 360px" onchange="this.form.submit()">
            <option value="">Select a doctor</option>
            <?php foreach ($doctors as $item): ?>
                <option value="<?= $item['id'] ?>" <?= selected($item['id'], $doctor['id'] ?? '') ?>>
                    <?= e($item['name']) ?> - <?= e($item['hospital_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <noscript><button class="btn btn-light border">Show</button></noscript>
    </div>
</form>

<?php if (!$doctor): ?>
    <div class="card">
        <div class="empty-state"><i class="bi bi-person-badge"></i> Select a doctor to manage their weekly schedule.</div>
    </div>
<?php else: ?>
    <div class="d-flex align-items-center gap-3 mb-3">
        <?= avatar($doctor['image'], $doctor['name'], 48) ?>
        <div>
            <div class="fw-semibold"><?= e($doctor['name']) ?></div>
            <div class="small text-muted"><?= e($doctor['hospital_name']) ?></div>
        </div>
    </div>
    <?= partial('partials/schedule_manager', ['schedules' => $schedules, 'baseUrl' => 'admin/schedules', 'doctorId' => $doctor['id']]) ?>
<?php endif; ?>
