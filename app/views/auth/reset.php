<?php require ROOT_PATH . '/app/views/partials/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card auth-card">
            <div class="card-body p-4">
                <h1 class="h4 mb-1 fw-bold">Set New Password</h1>
                <p class="text-muted mb-4 small">Choose a new password for your account.</p>

                <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        <?php foreach ($errors as $err): ?>
                        <li><?= e($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?= url('/reset-password/' . e($token)) ?>" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">New password</label>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Min. 8 characters" required autofocus>
                        <div id="passwordStrength" class="form-text"></div>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label fw-semibold">Confirm password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                               placeholder="Repeat password" required>
                        <div id="passwordMatch" class="form-text"></div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/app/views/partials/footer.php'; ?>
