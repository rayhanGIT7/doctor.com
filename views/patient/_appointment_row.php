<a href="<?= url('my/appointments/' . $appointment['id']) ?>" class="list-group-item list-group-item-action py-3">
    <div class="d-flex gap-3 align-items-center">
        <?= avatar($appointment['doctor_image'], $appointment['doctor_name'], 48) ?>
        <div class="flex-grow-1 min-w-0">
            <div class="fw-semibold"><?= e($appointment['doctor_name']) ?></div>
            <div class="small text-muted text-truncate"><?= e($appointment['hospital_name']) ?> &middot; <?= e($appointment['appointment_no']) ?></div>
        </div>
        <div class="text-end">
            <div class="small fw-semibold"><?= format_date($appointment['appointment_date']) ?></div>
            <div class="small text-muted"><?= format_time($appointment['appointment_time']) ?></div>
            <?= status_badge($appointment['status']) ?>
        </div>
    </div>
</a>
