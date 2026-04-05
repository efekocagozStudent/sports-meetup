<?php require ROOT_PATH . '/app/views/partials/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Notifications</h1>
        <p class="text-muted small mb-0"><?= count($notifications) ?> total</p>
    </div>
    <?php if (!empty($notifications)): ?>
    <form method="POST" action="<?= url('/notifications/clear') ?>">
        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
        <button class="btn btn-outline-secondary btn-sm"
                onclick="return confirm('Clear all notifications?')">Clear all</button>
    </form>
    <?php endif; ?>
</div>

<?php if (empty($notifications)): ?>
<div class="text-center py-5 text-muted">
    <div class="fs-1 mb-2">&#x1F514;</div>
    <h5>All caught up!</h5>
    <p class="small">No notifications yet.</p>
</div>
<?php else: ?>
<div class="list-group shadow-sm">
    <?php foreach ($notifications as $n): ?>
    <?php require ROOT_PATH . '/app/views/partials/notification_item.php'; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php require ROOT_PATH . '/app/views/partials/footer.php'; ?>