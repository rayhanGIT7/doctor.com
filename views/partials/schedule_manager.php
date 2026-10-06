<?php
// Shared by the admin panel and the doctor panel.
// Needs: $schedules, $baseUrl ("admin/schedules" or "doctor/schedule"), optional $doctorId (admin only)
use App\Models\Schedule;
?>
<div class="row g-4">
    <div class="col-lg-4">
        <form method="post" action="<?= url($baseUrl) ?>" class="card">
            <div class="card-body">
                <?= csrf_field() ?>
                <?php if (!empty($doctorId)): ?>
                    <input type="hidden" name="doctor_id" value="<?= (int) $doctorId ?>">
                <?php endif; ?>

                <h2 class="h6 mb-3">Add available time</h2>

                <div class="mb-3">
                    <label class="form-label" for="day_of_week">Day</label>
                    <select class="form-select<?= invalid('day_of_week') ?>" id="day_of_week" name="day_of_week" required>
                        <?php foreach ([6, 0, 1, 2, 3, 4, 5] as $day): ?>
                            <option value="<?= $day ?>" <?= selected($day, old('day_of_week')) ?>><?= day_name($day) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?= field_error('day_of_week') ?>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label" for="start_time">From</label>
                        <input type="time" class="form-control<?= invalid('start_time') ?>" id="start_time" name="start_time" value="<?= e(old('start_time', '17:00')) ?>" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="end_time">To</label>
                        <input type="time" class="form-control<?= invalid('end_time') ?>" id="end_time" name="end_time" value="<?= e(old('end_time', '21:00')) ?>" required>
                    </div>
                    <?php if (error('start_time') || error('end_time')): ?>
                        <div class="col-12 text-danger small"><?= e(error('start_time') ?: error('end_time')) ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="slot_minutes">Time per patient</label>
                    <select class="form-select<?= invalid('slot_minutes') ?>" id="slot_minutes" name="slot_minutes">
                        <?php foreach (Schedule::SLOT_OPTIONS as $minutes): ?>
                            <option value="<?= $minutes ?>" <?= selected($minutes, old('slot_minutes', '15')) ?>><?= $minutes ?> minutes</option>
                        <?php endforeach; ?>
                    </select>
                    <?= field_error('slot_minutes') ?>
                </div>

                <button class="btn btn-primary w-100"><i class="bi bi-plus-lg"></i> Add schedule</button>
            </div>
        </form>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <?php if (!$schedules): ?>
                <div class="empty-state"><i class="bi bi-clock"></i> No schedule yet. Patients cannot book until a schedule is added.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Day</th><th>Time</th><th>Per patient</th><th>Slots</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                        <tbody>
                        <?php foreach ($schedules as $schedule): ?>
                            <?php $slotCount = intdiv(strtotime($schedule['end_time']) - strtotime($schedule['start_time']), $schedule['slot_minutes'] * 60); ?>
                            <tr>
                                <td class="fw-semibold"><?= day_name((int) $schedule['day_of_week']) ?></td>
                                <td><?= format_time($schedule['start_time']) ?> - <?= format_time($schedule['end_time']) ?></td>
                                <td><?= (int) $schedule['slot_minutes'] ?> min</td>
                                <td><?= $slotCount ?></td>
                                <td><?= status_badge($schedule['status']) ?></td>
                                <td class="text-end text-nowrap">
                                    <form method="post" action="<?= url($baseUrl . '/' . $schedule['id'] . '/status') ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-sm btn-light border" title="<?= $schedule['status'] === 'active' ? 'Pause' : 'Activate' ?>">
                                            <i class="bi <?= $schedule['status'] === 'active' ? 'bi-pause-circle' : 'bi-play-circle' ?>"></i>
                                        </button>
                                    </form>
                                    <form method="post" action="<?= url($baseUrl . '/' . $schedule['id'] . '/delete') ?>" class="d-inline" data-confirm="Remove this schedule?">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-sm btn-light border text-danger" title="Remove"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <p class="small text-muted mt-2 mb-0"><i class="bi bi-info-circle"></i> Changing a schedule does not affect appointments that are already booked.</p>
    </div>
</div>
