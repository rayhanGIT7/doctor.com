<div class="container py-4">
    <h1 class="h4 section-title mb-3">My Profile</h1>

    <div class="row g-4">
        <div class="col-lg-7">
            <form method="post" action="<?= url('my/profile') ?>" class="card">
                <div class="card-body">
                    <?= csrf_field() ?>
                    <h2 class="h6 mb-3">Personal information</h2>

                    <div class="mb-3">
                        <label class="form-label" for="name">Full name</label>
                        <input type="text" class="form-control<?= invalid('name') ?>" id="name" name="name" value="<?= e(old('name', $user['name'])) ?>" required maxlength="100">
                        <?= field_error('name') ?>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" class="form-control<?= invalid('email') ?>" id="email" name="email" value="<?= e(old('email', $user['email'])) ?>" required>
                            <?= field_error('email') ?>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="phone">Phone</label>
                            <input type="tel" class="form-control<?= invalid('phone') ?>" id="phone" name="phone" value="<?= e(old('phone', $user['phone'])) ?>" required>
                            <?= field_error('phone') ?>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label" for="gender">Gender</label>
                            <select class="form-select<?= invalid('gender') ?>" id="gender" name="gender">
                                <option value="">Prefer not to say</option>
                                <?php foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= selected($value, old('gender', $user['gender'])) ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?= field_error('gender') ?>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="date_of_birth">Date of birth</label>
                            <input type="date" class="form-control<?= invalid('date_of_birth') ?>" id="date_of_birth" name="date_of_birth" value="<?= e(old('date_of_birth', $user['date_of_birth'])) ?>" max="<?= today() ?>">
                            <?= field_error('date_of_birth') ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="address">Address</label>
                        <input type="text" class="form-control<?= invalid('address') ?>" id="address" name="address" value="<?= e(old('address', $user['address'])) ?>" maxlength="255">
                        <?= field_error('address') ?>
                    </div>

                    <button class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>

        <div class="col-lg-5">
            <?= partial('partials/password_form') ?>
        </div>
    </div>
</div>
