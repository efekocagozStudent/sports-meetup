<?php
// Expects: $e (event row array)
?>
<tr>
    <td>
        <a href="<?= url('/events/' . $e['id']) ?>" class="fw-semibold" style="color:var(--text);">
            <?= e($e['title']) ?>
        </a>
    </td>
    <td class="small"><?= e($e['sport_icon']) ?> <?= e($e['sport_name']) ?></td>
    <td class="small text-nowrap" style="color:var(--muted);"><?= formatDate($e['event_date'], 'd M Y') ?></td>
    <td class="small"><?= (int) $e['participant_count'] ?> / <?= (int) $e['max_participants'] ?></td>
    <td><span class="status-badge status-<?= e($e['status']) ?>"><?= ucfirst(e($e['status'])) ?></span></td>
    <td>
        <a href="<?= url('/events/' . $e['id'] . '/edit') ?>"
           class="btn btn-sm btn-outline-secondary"
           aria-label="Edit <?= e($e['title']) ?>">Edit</a>
    </td>
</tr>
