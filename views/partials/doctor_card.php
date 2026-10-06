<div class="card doctor-card h-100">
    <div class="card-body d-flex gap-3">
        <?= avatar($doctor['image'], $doctor['name'], 72) ?>
        <div class="min-w-0">
            <h3 class="h6 mb-1">
                <a href="<?= url('doctors/' . $doctor['id']) ?>" class="stretched-link text-reset text-decoration-none"><?= e($doctor['name']) ?></a>
            </h3>
            <div class="small fw-semibold text-primary"><?= e($doctor['categories'] ?? '') ?></div>
            <div class="small text-muted text-truncate"><?= e($doctor['qualification']) ?></div>
            <div class="small mt-2"><i class="bi bi-hospital text-muted"></i> <?= e($doctor['hospital_name']) ?>, <?= e($doctor['city']) ?></div>
            <div class="small"><i class="bi bi-briefcase text-muted"></i> <?= (int) $doctor['experience_years'] ?> years experience</div>
        </div>
    </div>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
        <span><span class="small text-muted">Fee</span> <strong><?= money($doctor['consultation_fee']) ?></strong></span>
        <span class="btn btn-sm btn-primary">Book Now</span>
    </div>
</div>
