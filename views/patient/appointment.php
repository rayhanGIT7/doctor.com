<?php
$startsAt  = strtotime($appointment['appointment_date'] . ' ' . $appointment['appointment_time']);
$canCancel = $appointment['status'] === 'confirmed' && $startsAt > time();
?>
<div class="container py-4" style="max-width: 720px">
    <a href="<?= url('my/appointments') ?>" class="small no-print"><i class="bi bi-arrow-left"></i> My appointments</a>

    <div class="card mt-3">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <?php if ($appointment['status'] === 'confirmed'): ?>
                    <span class="icon-circle mb-2" style="width:64px;height:64px;font-size:2rem"><i class="bi bi-check2"></i></span>
                    <h1 class="h4 mb-1">Appointment confirmed</h1>
                <?php else: ?>
                    <h1 class="h4 mb-1">Appointment details</h1>
                <?php endif; ?>
                <div class="text-muted">Appointment No.</div>
                <div class="fs-4 fw-bold text-primary"><?= e($appointment['appointment_no']) ?></div>
                <div class="mt-1"><?= status_badge($appointment['status']) ?></div>
            </div>

            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="small text-muted">Doctor</div>
                    <div class="fw-semibold"><?= e($appointment['doctor_name']) ?></div>
                </div>
                <div class="col-sm-6">
                    <div class="small text-muted">Hospital</div>
                    <div class="fw-semibold"><?= e($appointment['hospital_name']) ?></div>
                    <div class="small text-muted"><?= e($appointment['chamber_info']) ?> <?= e($appointment['hospital_address']) ?>, <?= e($appointment['city']) ?></div>
                </div>
                <div class="col-sm-6">
                    <div class="small text-muted">Date &amp; time</div>
                    <div class="fw-semibold"><?= format_date($appointment['appointment_date']) ?>, <?= format_time($appointment['appointment_time']) ?></div>
                </div>
                <div class="col-sm-6">
                    <div class="small text-muted">Consultation fee</div>
                    <div class="fw-semibold"><?= money($appointment['fee']) ?> <span class="small text-muted fw-normal">(pay at hospital)</span></div>
                </div>
                <div class="col-sm-6">
                    <div class="small text-muted">Patient</div>
                    <div class="fw-semibold"><?= e($appointment['patient_name']) ?></div>
                    <div class="small text-muted"><?= e($appointment['patient_phone']) ?></div>
                </div>
                <?php if ($appointment['note']): ?>
                    <div class="col-sm-6">
                        <div class="small text-muted">Note</div>
                        <div><?= e($appointment['note']) ?></div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($appointment['status'] === 'confirmed'): ?>
                <div class="alert alert-info small mt-4 mb-0">
                    Please arrive 10 minutes early and show this appointment number at the reception.
                </div>
            <?php endif; ?>
        </div>

        <div class="card-footer bg-white d-flex flex-wrap gap-2 justify-content-between no-print">
            <button type="button" class="btn btn-light" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
            <?php if ($canCancel): ?>
                <form method="post" action="<?= url('my/appointments/' . $appointment['id'] . '/cancel') ?>"
                      data-confirm="Cancel this appointment? The slot will be released for other patients.">
                    <?= csrf_field() ?>
                    <button class="btn btn-outline-danger"><i class="bi bi-x-circle"></i> Cancel appointment</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
