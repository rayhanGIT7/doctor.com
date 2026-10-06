<div class="container py-5" style="max-width: 520px">
    <div class="card">
        <div class="card-body p-4">
            <h1 class="h4 mb-1">Create your account</h1>
            <p class="text-muted small mb-4">Book and manage your doctor appointments.</p>

            <form method="post" action="<?= url('register') ?>">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label" for="name">Full name</label>
                    <input type="text" class="form-control<?= invalid('name') ?>" id="name" name="name" value="<?= e(old('name')) ?>" required maxlength="100">
                    <?= field_error('name') ?>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" class="form-control<?= invalid('email') ?>" id="email" name="email" value="<?= e(old('email')) ?>" required>
                        <?= field_error('email') ?>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label" for="phone">Phone</label>
                        <input type="tel" class="form-control<?= invalid('phone') ?>" id="phone" name="phone" value="<?= e(old('phone')) ?>" placeholder="01XXXXXXXXX" required>
                        <?= field_error('phone') ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="gender">Gender <span class="text-muted fw-normal">(optional)</span></label>
                    <select class="form-select<?= invalid('gender') ?>" id="gender" name="gender">
                        <option value="">Prefer not to say</option>
                        <option value="male" <?= selected('male', old('gender')) ?>>Male</option>
                        <option value="female" <?= selected('female', old('gender')) ?>>Female</option>
                        <option value="other" <?= selected('other', old('gender')) ?>>Other</option>
                    </select>
                    <?= field_error('gender') ?>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" class="form-control<?= invalid('password') ?>" id="password" name="password" required minlength="6">
                        <?= field_error('password') ?>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label" for="password_confirmation">Confirm password</label>
                        <input type="password" class="form-control<?= invalid('password_confirmation') ?>" id="password_confirmation" name="password_confirmation" required>
                        <?= field_error('password_confirmation') ?>
                    </div>
                </div>

                <button class="btn btn-primary w-100">Create account</button>
            </form>

            <p class="text-center small mt-4 mb-0">Already have an account? <a href="<?= url('login') ?>">Login</a></p>
        </div>
    </div>
</div>
