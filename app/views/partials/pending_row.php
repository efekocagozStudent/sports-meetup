<?php
/**
 * Partial: Pending approval table row (Dashboard Pending Approvals tab).
 * Expects: $pa (pending approval array), $csrf (CSRF token string)
 */
?>
<tr>
    <td class="fw-semibold"><?= e($pa['username']) ?></td>
    <td>
        <a href="<?= url('/events/' . $pa['event_id']) ?>" style="color:var(--text);">
            <?= e($pa['event_title']) ?>
        </a>
    </td>
    <td class="small" style="color:var(--muted);"><?= formatDate($pa['joined_at'], 'd M Y') ?></td>
    <td>
        <div class="d-flex gap-2">
            <form method="POST" action="<?= url('/events/' . $pa['event_id'] . '/approve/' . $pa['user_id']) ?>">
                <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                <button type="submit" class="btn btn-sm btn-primary">Approve</button>
            </form>
            <form method="POST" action="<?= url('/events/' . $pa['event_id'] . '/reject/' . $pa['user_id']) ?>">
                <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger">Reject</button>
            </form>
        </div>
    </td>
</tr>
