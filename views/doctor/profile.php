<div class="row g-4">
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-body d-flex flex-column flex-sm-row gap-4">
                <?= avatar($doctor['image'], $doctor['name'], 96) ?>
                <div class="flex-grow-1">
                    <h2 class="h5 mb-1"><?= e($doctor['name']) ?></h2>
                    <div class="small text-muted mb-2"><?= e(implode(', ', array_column($categories, 'name'))) ?></div>
                    <div class="row small g-2">
                        <div class="col-sm-6"><span class="text-muted">Email:</span> <?= e($doctor['email']) ?></div>
                        <div class="col-sm-6"><span class="text-muted">Phone:</span> <?= e($doctor['phone']) ?></div>
                        <div class="col-sm-6"><span class="text-muted">Hospital:</span> <?= e($doctor['hospital_name']) ?></div>
                        <div class="col-sm-6"><span class="text-muted">Fee:</span> <?= money($doctor['consultation_fee']) ?></div>
                        <div class="col-sm-6"><span class="text-muted">Experience:</span> <?= (int) $doctor['experience_years'] ?> years</div>
                    </div>
                    <p class="small text-muted mt-2 mb-0"><i class="bi bi-info-circle"></i> Contact the admin to change name, hospital, fee or specialization.</p>
                </div>
            </div>
        </div>

        <form method="post" action="<?= url('doctor/profile') ?>" enctype="multipart/form-data" class="card">
            <div class="card-body">
                <?= csrf_field() ?>
                <h2 class="h6 mb-3">Public profile</h2>

                <div class="mb-3">
                    <label class="form-label" for="qualification">Qualification</label>
                    <input type="text" class="form-control<?= invalid('qualification') ?>" id="qualification" name="qualification" value="<?= e(old('qualification', $doctor['qualification'])) ?>" required maxlength="255">
                    <?= field_error('qualification') ?>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="chamber_info">Chamber information</label>
                    <input type="text" class="form-control<?= invalid('chamber_info') ?>" id="chamber_info" name="chamber_info" value="<?= e(old('chamber_info', $doctor['chamber_info'])) ?>" maxlength="255">
                    <?= field_error('chamber_info') ?>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="bio">About me</label>
                    <textarea class="form-control<?= invalid('bio') ?>" id="bio" name="bio" rows="4" maxlength="2000"><?= e(old('bio', $doctor['bio'])) ?></textarea>
                    <?= field_error('bio') ?>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="image">Profile photo <span class="text-muted fw-normal">(JPG/PNG/WEBP, max 2 MB)</span></label>
                    <input type="file" class="form-control<?= invalid('image') ?>" id="image" name="image" accept="image/jpeg,image/png,image/webp">
                    <?= field_error('image') ?>
                </div>

                <button class="btn btn-primary">Save profile</button>
                <a href="<?= url('doctors/' . $doctor['id']) ?>" class="btn btn-light" target="_blank">View public page</a>
            </div>
        </form>
    </div>

    <div class="col-lg-5">
        <?= partial('partials/password_form') ?>
    </div>
</div>
