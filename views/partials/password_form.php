<form method="post" action="<?= url('account/password') ?>" class="card">
    <div class="card-body">
        <?= csrf_field() ?>
        <h2 class="h6 mb-3">Change password</h2>

        <div class="mb-3">
            <label class="form-label" for="current_password">Current password</label>
            <input type="password" class="form-control<?= invalid('current_password') ?>" id="current_password" name="current_password" required>
            <?= field_error('current_password') ?>
        </div>
        <div class="mb-3">
            <label class="form-label" for="password">New password</label>
            <input type="password" class="form-control<?= invalid('password') ?>" id="password" name="password" required minlength="6">
            <?= field_error('password') ?>
        </div>
        <div class="mb-3">
            <label class="form-label" for="password_confirmation">Confirm new password</label>
            <input type="password" class="form-control<?= invalid('password_confirmation') ?>" id="password_confirmation" name="password_confirmation" required>
            <?= field_error('password_confirmation') ?>
        </div>

        <button class="btn btn-outline-primary">Update password</button>
    </div>
</form>
