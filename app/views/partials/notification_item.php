<?php
/**
 * Partial: Single notification list item.
 * Expects: $n (notification row array: link, is_read, message, created_at)
 */
?>
<a href="<?= e($n['link'] ?: url('/events')) ?>"
   class="list-group-item list-group-item-action d-flex justify-content-between align-items-start gap-3 py-3
          <?= $n['is_read'] ? '' : 'fw-semibold bg-light' ?>">
    <div class="me-auto">
        <div class="mb-1"><?= e($n['message']) ?></div>
        <small class="text-muted fw-normal"><?= formatDate($n['created_at'], 'd M Y \a\t H:i') ?></small>
    </div>
    <?php if (!$n['is_read']): ?>
    <span class="badge bg-primary rounded-pill align-self-center">New</span>
    <?php endif; ?>
</a>
