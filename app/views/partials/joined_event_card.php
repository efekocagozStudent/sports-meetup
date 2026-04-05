<?php
/**
 * Partial: Joined event card (Dashboard "Joined" tab).
 * Shows participant status rather than event status.
 * Expects: $joinedEvent (row from getJoinedEvents:
 *          event_id, title, location, event_date, sport_name, sport_icon, status)
 */
$statusMap = [
    'approved' => 'open',
    'pending'  => 'closed',
    'rejected' => 'cancelled',
];
$badgeStatus = $statusMap[$joinedEvent['status']] ?? 'closed';
?>
<div class="col">
    <div class="card h-100">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <span class="sport-badge"><?= e($joinedEvent['sport_icon']) ?> <?= e($joinedEvent['sport_name']) ?></span>
                <span class="status-badge status-<?= $badgeStatus ?>"><?= ucfirst(e($joinedEvent['status'])) ?></span>
            </div>
            <h6 class="fw-bold mb-1">
                <a href="<?= url('/events/' . $joinedEvent['event_id']) ?>" style="color:var(--text);">
                    <?= e($joinedEvent['title']) ?>
                </a>
            </h6>
            <p class="small mb-1" style="color:var(--muted);">&#x1F4CD; <?= e($joinedEvent['location']) ?></p>
            <p class="small mb-0" style="color:var(--muted);">&#x1F4C5; <?= formatDate($joinedEvent['event_date'], 'd M Y') ?></p>
        </div>
    </div>
</div>
