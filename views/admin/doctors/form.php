<?php
// $doctor is null when adding a new one
$action      = $doctor ? url('admin/doctors/' . $doctor['id']) : url('admin/doctors');
$selectedIds = array_map('strval', old_array('categories', $categoryIds));
?>
<form method="post" action="<?= $action ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h6 mb-3">Account</h2>

                    <div class="mb-3">
                        <label class="form-label" for="name">Full name</label>
                        <input type="text" class="form-control<?= invalid('name') ?>" id="name" name="name" value="<?= e(old('name', $doctor['name'] ?? '')) ?>" placeholder="Dr. ..." required maxlength="100">
                        <?= field_error('name') ?>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" class="form-control<?= invalid('email') ?>" id="email" name="email" value="<?= e(old('email', $doctor['email'] ?? '')) ?>" required>
                            <?= field_error('email') ?>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="phone">Phone</label>
                            <input type="tel" class="form-control<?= invalid('phone') ?>" id="phone" name="phone" value="<?= e(old('phone', $doctor['phone'] ?? '')) ?>" required>
                            <?= field_error('phone') ?>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label" for="gender">Gender</label>
                            <select class="form-select<?= invalid('gender') ?>" id="gender" name="gender" required>
                                <option value="">Select</option>
                                <?php foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= selected($value, old('gender', $doctor['gender'] ?? '')) ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?= field_error('gender') ?>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="password">
                                Password <?php if ($doctor): ?><span class="text-muted fw-normal">(leave empty to keep)</span><?php endif; ?>
                            </label>
                            <input type="password" class="form-control<?= invalid('password') ?>" id="password" name="password" <?= $doctor ? '' : 'required' ?> minlength="6" autocomplete="new-password">
                            <?= field_error('password') ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="image">Profile photo <span class="text-muted fw-normal">(JPG/PNG/WEBP, max 2 MB)</span></label>
                        <div class="d-flex align-items-center gap-3">
                            <?php if ($doctor): ?>
                                <?= avatar($doctor['image'], $doctor['name'], 56) ?>
                            <?php endif; ?>
                            <input type="file" class="form-control<?= invalid('image') ?>" id="image" name="image" accept="image/jpeg,image/png,image/webp">
                        </div>
                        <?php if (error('image')): ?><div class="text-danger small mt-1"><?= e(error('image')) ?></div><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h6 mb-3">Professional details</h2>

                    <div class="mb-3">
                        <label class="form-label" for="hospital_id">Hospital</label>
                        <select class="form-select<?= invalid('hospital_id') ?>" id="hospital_id" name="hospital_id" required>
                            <option value="">Select hospital</option>
                            <?php foreach ($hospitals as $hospital): ?>
                                <option value="<?= $hospital['id'] ?>" <?= selected($hospital['id'], old('hospital_id', $doctor['hospital_id'] ?? '')) ?>>
                                    <?= e($hospital['name']) ?> (<?= e($hospital['city']) ?>)<?= $hospital['status'] === 'inactive' ? ' - inactive' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= field_error('hospital_id') ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block">Specialization</label>
                        <div class="row row-cols-2 g-1">
                            <?php foreach ($categories as $category): ?>
                                <div class="col">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="categories[]" value="<?= $category['id'] ?>" id="cat<?= $category['id'] ?>"
                                            <?= checked(in_array((string) $category['id'], $selectedIds, true)) ?>>
                                        <label class="form-check-label small" for="cat<?= $category['id'] ?>"><?= e($category['name']) ?></label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (error('categories')): ?><div class="text-danger small mt-1"><?= e(error('categories')) ?></div><?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="qualification">Qualification</label>
                        <input type="text" class="form-control<?= invalid('qualification') ?>" id="qualification" name="qualification" value="<?= e(old('qualification', $doctor['qualification'] ?? '')) ?>" placeholder="MBBS, FCPS (Medicine)" required maxlength="255">
                        <?= field_error('qualification') ?>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label" for="experience_years">Experience (years)</label>
                            <input type="number" class="form-control<?= invalid('experience_years') ?>" id="experience_years" name="experience_years" value="<?= e(old('experience_years', $doctor['experience_years'] ?? '')) ?>" min="0" max="70" required>
                            <?= field_error('experience_years') ?>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="consultation_fee">Consultation fee (৳)</label>
                            <input type="number" class="form-control<?= invalid('consultation_fee') ?>" id="consultation_fee" name="consultation_fee" value="<?= e(old('consultation_fee', $doctor ? (int) $doctor['consultation_fee'] : '')) ?>" min="0" step="1" required>
                            <?= field_error('consultation_fee') ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="chamber_info">Chamber information</label>
                        <input type="text" class="form-control<?= invalid('chamber_info') ?>" id="chamber_info" name="chamber_info" value="<?= e(old('chamber_info', $doctor['chamber_info'] ?? '')) ?>" placeholder="Room 504, 5th floor" maxlength="255">
                        <?= field_error('chamber_info') ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="bio">About the doctor</label>
                        <textarea class="form-control" id="bio" name="bio" rows="3"><?= e(old('bio', $doctor['bio'] ?? '')) ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button class="btn btn-primary"><?= $doctor ? 'Save changes' : 'Add doctor' ?></button>
        <a href="<?= url('admin/doctors') ?>" class="btn btn-light">Cancel</a>
    </div>
</form>
