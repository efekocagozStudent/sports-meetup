<?php require ROOT_PATH . '/app/views/partials/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card auth-card">
            <div class="card-body p-4">
                <h1 class="h4 mb-1 fw-bold">Reset Password</h1>
                <p class="text-muted mb-4 small">Enter your email and we'll generate a reset link.</p>

                <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        <?php foreach ($errors as $err): ?>
                        <li><?= e($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?= $success /* intentionally unescaped — contains a safe demo link built with htmlspecialchars */ ?></div>
                <?php endif; ?>

                <form method="POST" action="<?= url('/forgot-password') ?>" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email address</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= e($old['email'] ?? '') ?>"
                               placeholder="you@example.com" required autofocus>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">Send Reset Link</button>
                    </div>
                </form>

                <hr class="my-4">
                <p class="text-center text-muted small mb-0">
                    Remembered it? <a href="<?= url('/login') ?>" class="fw-semibold">Back to sign in</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/app/views/partials/footer.php'; ?>
