<?php
/**
 * Partial: Single participant list row (used on the event show page).
 * Expects: $p    (participant array: user_id, username, joined_at, status)
 *          $vm   (EventViewModel)
 *          $csrf (CSRF token string)
 */
?>
<li class="list-group-item d-flex justify-content-between align-items-center py-3">
    <div class="d-flex align-items-center gap-3">
        <span class="avatar-circle"><?= e(strtoupper(substr($p['username'], 0, 1))) ?></span>
        <div>
            <div class="fw-semibold"><?= e($p['username']) ?></div>
            <small style="color:var(--muted);">Joined <?= formatDate($p['joined_at'], 'd M Y') ?></small>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <?php
            $badgeClass = match($p['status']) {
                'approved' => 'skill-beginner',
                'pending'  => 'skill-intermediate',
                default    => 'skill-advanced',
            };
        ?>
        <span class="skill-badge <?= $badgeClass ?>"><?= ucfirst(e($p['status'])) ?></span>

        <?php if ($p['status'] === 'pending'): ?>
        <form method="POST"
              action="<?= url('/events/' . $vm->event['id'] . '/approve/' . $p['user_id']) ?>"
              class="d-inline">
            <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
            <button class="btn btn-sm btn-primary"
                    aria-label="Approve <?= e($p['username']) ?>">Approve</button>
        </form>
        <form method="POST"
              action="<?= url('/events/' . $vm->event['id'] . '/reject/' . $p['user_id']) ?>"
              class="d-inline">
            <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
            <button class="btn btn-sm btn-outline-secondary"
                    aria-label="Reject <?= e($p['username']) ?>">Reject</button>
        </form>
        <?php endif; ?>
    </div>
</li>
