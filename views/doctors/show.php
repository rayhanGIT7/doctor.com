<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="<?= url('doctors') ?>">Doctors</a></li>
            <li class="breadcrumb-item active"><?= e($doctor['name']) ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Doctor information -->
        <div class="col-lg-7">
            <div class="card mb-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-sm-row gap-4">
                        <?= avatar($doctor['image'], $doctor['name'], 120) ?>
                        <div>
                            <h1 class="h3 mb-1"><?= e($doctor['name']) ?></h1>
                            <div class="mb-2">
                                <?php foreach ($categories as $category): ?>
                                    <a href="<?= url('doctors', ['category' => $category['id']]) ?>" class="badge rounded-pill text-bg-light border text-decoration-none"><?= e($category['name']) ?></a>
                                <?php endforeach; ?>
                            </div>
                            <div class="text-muted"><?= e($doctor['qualification']) ?></div>
                            <div class="mt-3 d-flex flex-wrap gap-4">
                                <div><div class="small text-muted">Experience</div><strong><?= (int) $doctor['experience_years'] ?> years</strong></div>
                                <div><div class="small text-muted">Consultation fee</div><strong><?= money($doctor['consultation_fee']) ?></strong></div>
                                <?php if ($doctor['gender']): ?>
                                    <div><div class="small text-muted">Gender</div><strong><?= e(ucfirst($doctor['gender'])) ?></strong></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($doctor['bio']): ?>
                        <hr>
                        <h2 class="h6">About</h2>
                        <p class="mb-0 text-muted"><?= nl2br(e($doctor['bio'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body p-4">
                    <h2 class="h6 mb-3"><i class="bi bi-hospital text-primary"></i> Chamber</h2>
                    <div class="fw-semibold"><?= e($doctor['hospital_name']) ?></div>
                    <?php if ($doctor['chamber_info']): ?>
                        <div><?= e($doctor['chamber_info']) ?></div>
                    <?php endif; ?>
                    <div class="text-muted small"><?= e($doctor['hospital_address']) ?>, <?= e($doctor['city']) ?>, <?= e($doctor['country']) ?></div>
                    <?php if ($doctor['hospital_phone']): ?>
                        <div class="small mt-1"><i class="bi bi-telephone"></i> <?= e($doctor['hospital_phone']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-4">
                    <h2 class="h6 mb-3"><i class="bi bi-clock text-primary"></i> Weekly schedule</h2>
                    <?php if (!$schedules): ?>
                        <p class="text-muted mb-0">No schedule published yet.</p>
                    <?php else: ?>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($schedules as $schedule): ?>
                                <li class="d-flex justify-content-between border-bottom py-2">
                                    <span><?= day_name((int) $schedule['day_of_week']) ?></span>
                                    <span class="text-muted"><?= format_time($schedule['start_time']) ?> - <?= format_time($schedule['end_time']) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Booking -->
        <div class="col-lg-5">
            <div class="card position-sticky" style="top: 80px">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">Book an appointment</h2>

                    <?php if (!$days): ?>
                        <div class="empty-state py-4">
                            <i class="bi bi-calendar-x"></i>
                            No available days in the next two weeks.
                        </div>
                    <?php else: ?>
                        <div class="small text-muted mb-2">1. Select a day</div>
                        <div class="day-tabs mb-4">
                            <?php foreach ($days as $day): ?>
                                <a href="<?= url('doctors/' . $doctor['id'], ['date' => $day['date']]) ?>"
                                   class="day-tab <?= $day['date'] === $date ? 'active' : '' ?> <?= $day['free'] === 0 ? 'full' : '' ?>">
                                    <small><?= $day['date'] === today() ? 'Today' : date('D', strtotime($day['date'])) ?></small>
                                    <strong><?= date('d M', strtotime($day['date'])) ?></strong>
                                    <small><?= $day['free'] ? $day['free'] . ' free' : 'Full' ?></small>
                                </a>
                            <?php endforeach; ?>
                        </div>

                        <div class="small text-muted mb-2">2. Select a time on <?= format_date($date) ?></div>
                        <?php if (!$slots): ?>
                            <p class="text-muted">No free slots on this day. Please pick another day.</p>
                        <?php else: ?>
                            <div class="slot-grid">
                                <?php foreach ($slots as $slot): ?>
                                    <a href="<?= url('doctors/' . $doctor['id'] . '/book', ['date' => $date, 'time' => $slot]) ?>"
                                       class="btn btn-outline-primary"><?= format_time($slot) ?></a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!auth()): ?>
                            <p class="small text-muted mt-3 mb-0"><i class="bi bi-info-circle"></i> You will be asked to log in or register before confirming.</p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
