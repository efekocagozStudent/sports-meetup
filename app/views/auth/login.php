<?php require ROOT_PATH . '/app/views/partials/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card auth-card">
            <div class="card-body p-4">
                <h1 class="h4 mb-1 fw-bold">Sign In</h1>
                <p class="text-muted mb-4 small">Welcome back! Enter your credentials.</p>

                <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        <?php foreach ($errors as $err): ?>
                        <li><?= e($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?= url('/login') ?>" novalidate id="loginForm">
                    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email address</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= e($old['email'] ?? '') ?>"
                               placeholder="you@example.com" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Your password" required>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">Sign In</button>
                    </div>
                </form>

                <hr class="my-4">
                <p class="text-center text-muted small mb-0">
                    Don't have an account?
                    <a href="<?= url('/register') ?>" class="fw-semibold">Create one</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/app/views/partials/footer.php'; ?>
