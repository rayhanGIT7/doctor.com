<div class="container py-5" style="max-width: 440px">
    <div class="card">
        <div class="card-body p-4">
            <h1 class="h4 mb-1">Welcome back</h1>
            <p class="text-muted small mb-4">Log in with your email or phone number.</p>

            <form method="post" action="<?= url('login') ?>">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label" for="login">Email or phone</label>
                    <input type="text" class="form-control<?= invalid('login') ?>" id="login" name="login" value="<?= e(old('login')) ?>" required autofocus>
                    <?= field_error('login') ?>
                </div>

                <div class="mb-4">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" class="form-control<?= invalid('password') ?>" id="password" name="password" required>
                    <?= field_error('password') ?>
                </div>

                <button class="btn btn-primary w-100">Login</button>
            </form>

            <p class="text-center small mt-4 mb-0">New here? <a href="<?= url('register') ?>">Create an account</a></p>
        </div>
    </div>
</div>
