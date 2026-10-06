<?php
$action = $category ? url('admin/categories/' . $category['id']) : url('admin/categories');
?>
<form method="post" action="<?= $action ?>" class="card" style="max-width: 600px">
    <div class="card-body">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label class="form-label" for="name">Category name</label>
            <input type="text" class="form-control<?= invalid('name') ?>" id="name" name="name" value="<?= e(old('name', $category['name'] ?? '')) ?>" placeholder="e.g. Heart Specialist" required maxlength="100">
            <?= field_error('name') ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="description">Description</label>
            <input type="text" class="form-control<?= invalid('description') ?>" id="description" name="description" value="<?= e(old('description', $category['description'] ?? '')) ?>" maxlength="255">
            <?= field_error('description') ?>
        </div>

        <div class="mb-4">
            <label class="form-label" for="status">Status</label>
            <select class="form-select" id="status" name="status" style="max-width: 200px">
                <option value="active" <?= selected('active', old('status', $category['status'] ?? 'active')) ?>>Active</option>
                <option value="inactive" <?= selected('inactive', old('status', $category['status'] ?? 'active')) ?>>Inactive</option>
            </select>
        </div>

        <button class="btn btn-primary"><?= $category ? 'Save changes' : 'Add category' ?></button>
        <a href="<?= url('admin/categories') ?>" class="btn btn-light">Cancel</a>
    </div>
</form>
