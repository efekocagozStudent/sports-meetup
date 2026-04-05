<?php require ROOT_PATH . '/app/views/partials/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-lg-6">

        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('/admin') ?>">Admin Panel</a></li>
                <li class="breadcrumb-item active">Edit User</li>
            </ol>
        </nav>

        <div class="card auth-card">
            <div class="card-body p-4">
                <h1 class="h4 fw-bold mb-4">Edit User</h1>

                <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?= url('/admin/users/' . (int)$user['id'] . '/update') ?>" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">

                    <div class="mb-3">
                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="username" name="username"
                               value="<?= e($user['username']) ?>" minlength="3" maxlength="50" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= e($user['email']) ?>" required>
                    </div>

                    <div class="mb-4">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role">
                            <option value="user"  <?= ($user['role'] ?? 'user') === 'user'  ? 'selected' : '' ?>>User</option>
                            <option value="admin" <?= ($user['role'] ?? 'user') === 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="<?= url('/admin') ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<?php require ROOT_PATH . '/app/views/partials/footer.php'; ?>
