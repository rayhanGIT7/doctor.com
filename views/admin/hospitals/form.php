<?php
// $hospital is null when adding a new one
$action = $hospital ? url('admin/hospitals/' . $hospital['id']) : url('admin/hospitals');
?>
<form method="post" action="<?= $action ?>" class="card" style="max-width: 760px">
    <div class="card-body">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label class="form-label" for="name">Hospital name</label>
            <input type="text" class="form-control<?= invalid('name') ?>" id="name" name="name" value="<?= e(old('name', $hospital['name'] ?? '')) ?>" required maxlength="150">
            <?= field_error('name') ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="address">Address</label>
            <input type="text" class="form-control<?= invalid('address') ?>" id="address" name="address" value="<?= e(old('address', $hospital['address'] ?? '')) ?>" required maxlength="255">
            <?= field_error('address') ?>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-sm-6">
                <label class="form-label" for="city">City</label>
                <input type="text" class="form-control<?= invalid('city') ?>" id="city" name="city" value="<?= e(old('city', $hospital['city'] ?? '')) ?>" required maxlength="80">
                <?= field_error('city') ?>
            </div>
            <div class="col-sm-6">
                <label class="form-label" for="country">Country</label>
                <input type="text" class="form-control<?= invalid('country') ?>" id="country" name="country" value="<?= e(old('country', $hospital['country'] ?? 'Bangladesh')) ?>" required maxlength="80">
                <?= field_error('country') ?>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-sm-6">
                <label class="form-label" for="phone">Contact number</label>
                <input type="tel" class="form-control<?= invalid('phone') ?>" id="phone" name="phone" value="<?= e(old('phone', $hospital['phone'] ?? '')) ?>">
                <?= field_error('phone') ?>
            </div>
            <div class="col-sm-6">
                <label class="form-label" for="email">Email</label>
                <input type="email" class="form-control<?= invalid('email') ?>" id="email" name="email" value="<?= e(old('email', $hospital['email'] ?? '')) ?>">
                <?= field_error('email') ?>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label" for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?= e(old('description', $hospital['description'] ?? '')) ?></textarea>
        </div>

        <div class="mb-4">
            <label class="form-label" for="status">Status</label>
            <select class="form-select<?= invalid('status') ?>" id="status" name="status" style="max-width: 200px">
                <option value="active" <?= selected('active', old('status', $hospital['status'] ?? 'active')) ?>>Active</option>
                <option value="inactive" <?= selected('inactive', old('status', $hospital['status'] ?? 'active')) ?>>Inactive</option>
            </select>
            <?= field_error('status') ?>
        </div>

        <button class="btn btn-primary"><?= $hospital ? 'Save changes' : 'Add hospital' ?></button>
        <a href="<?= url('admin/hospitals') ?>" class="btn btn-light">Cancel</a>
    </div>
</form>
