<?php require ROOT_PATH . '/app/views/partials/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card auth-card">
            <div class="card-body p-4">
                <h1 class="h4 mb-1 fw-bold">Create Account</h1>
                <p class="text-muted mb-4 small">Join SportsMeet and start organising activities.</p>

                <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        <?php foreach ($errors as $err): ?>
                        <li><?= e($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?= url('/register') ?>" novalidate id="registerForm">
                    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">

                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">Username</label>
                        <input type="text" class="form-control" id="username" name="username"
                               value="<?= e($old['username'] ?? '') ?>"
                               placeholder="e.g. sporty_efe" minlength="3" maxlength="50" required autofocus>
                        <div class="form-text">3–50 characters, letters, numbers, _ and - only.</div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email address</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= e($old['email'] ?? '') ?>"
                               placeholder="you@example.com" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Min. 8 characters" minlength="8" required>
                        <div id="passwordStrength" class="form-text"></div>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label fw-semibold">Confirm password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                               placeholder="Repeat your password" required>
                        <div id="passwordMatch" class="form-text"></div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">Create Account</button>
                    </div>
                </form>

                <hr class="my-4">
                <p class="text-center text-muted small mb-0">
                    Already have an account?
                    <a href="<?= url('/login') ?>" class="fw-semibold">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/app/views/partials/footer.php'; ?>
