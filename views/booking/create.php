<div class="container py-4" style="max-width: 860px">
    <h1 class="h4 section-title mb-4">Confirm your appointment</h1>

    <div class="row g-4">
        <div class="col-md-5 order-md-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-3 align-items-center mb-3">
                        <?= avatar($doctor['image'], $doctor['name'], 56) ?>
                        <div>
                            <div class="fw-semibold"><?= e($doctor['name']) ?></div>
                            <div class="small text-muted"><?= e($doctor['qualification']) ?></div>
                        </div>
                    </div>
                    <ul class="list-unstyled small mb-0">
                        <li class="d-flex justify-content-between py-2 border-top"><span class="text-muted">Hospital</span><span class="text-end"><?= e($doctor['hospital_name']) ?></span></li>
                        <li class="d-flex justify-content-between py-2 border-top"><span class="text-muted">Date</span><strong><?= format_date($date) ?> (<?= date('l', strtotime($date)) ?>)</strong></li>
                        <li class="d-flex justify-content-between py-2 border-top"><span class="text-muted">Time</span><strong><?= format_time($time) ?></strong></li>
                        <li class="d-flex justify-content-between py-2 border-top"><span class="text-muted">Fee (pay at hospital)</span><strong><?= money($doctor['consultation_fee']) ?></strong></li>
                    </ul>
                    <a href="<?= url('doctors/' . $doctor['id'], ['date' => $date]) ?>" class="btn btn-link btn-sm px-0 mt-2"><i class="bi bi-arrow-left"></i> Change time</a>
                </div>
            </div>
        </div>

        <div class="col-md-7 order-md-1">
            <form method="post" action="<?= url('doctors/' . $doctor['id'] . '/book') ?>" class="card">
                <div class="card-body">
                    <?= csrf_field() ?>
                    <input type="hidden" name="date" value="<?= e($date) ?>">
                    <input type="hidden" name="time" value="<?= e($time) ?>">

                    <h2 class="h6 mb-3">Patient details</h2>
                    <p class="small text-muted">Booking for someone else? Just change the name and phone.</p>

                    <div class="mb-3">
                        <label class="form-label" for="patient_name">Patient name</label>
                        <input type="text" class="form-control<?= invalid('patient_name') ?>" id="patient_name" name="patient_name"
                               value="<?= e(old('patient_name', $user['name'])) ?>" required maxlength="100">
                        <?= field_error('patient_name') ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="patient_phone">Phone number</label>
                        <input type="tel" class="form-control<?= invalid('patient_phone') ?>" id="patient_phone" name="patient_phone"
                               value="<?= e(old('patient_phone', $user['phone'])) ?>" required>
                        <?= field_error('patient_phone') ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="note">Problem / note <span class="text-muted fw-normal">(optional)</span></label>
                        <textarea class="form-control<?= invalid('note') ?>" id="note" name="note" rows="3" maxlength="500"><?= e(old('note')) ?></textarea>
                        <?= field_error('note') ?>
                    </div>

                    <?php if (error('date') || error('time')): ?>
                        <div class="alert alert-danger small"><?= e(error('date') ?: error('time')) ?></div>
                    <?php endif; ?>

                    <button class="btn btn-primary w-100"><i class="bi bi-check2-circle"></i> Confirm appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>
