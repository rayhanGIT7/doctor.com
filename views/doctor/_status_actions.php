<?php
// Buttons a doctor can use on a confirmed appointment. Needs: $appointment
$isFuture = $appointment['appointment_date'] > today();
?>
<?php if ($appointment['status'] !== 'confirmed'): ?>
    <span class="text-muted small">-</span>
<?php else: ?>
    <form method="post" action="<?= url('doctor/appointments/' . $appointment['id'] . '/status') ?>" class="d-inline-flex flex-wrap gap-1 justify-content-end">
        <?= csrf_field() ?>
        <?php if (!$isFuture): ?>
            <button name="status" value="completed" class="btn btn-sm btn-outline-success" title="Completed"><i class="bi bi-check2"></i> Done</button>
            <button name="status" value="no_show" class="btn btn-sm btn-outline-warning" title="Patient did not come">No show</button>
        <?php endif; ?>
        <button name="status" value="cancelled" class="btn btn-sm btn-outline-danger" title="Cancel"
                onclick="return confirm('Cancel this appointment?')"><i class="bi bi-x"></i></button>
    </form>
<?php endif; ?>
