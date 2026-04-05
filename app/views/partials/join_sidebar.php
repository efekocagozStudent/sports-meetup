<?php
/**
 * Partial: Join/leave/status sidebar card (event show page).
 * Expects: $vm   (EventViewModel)
 *          $csrf (CSRF token string)
 */
$event = $vm->event;
?>
<div class="card" style="position:sticky;top:1.5rem;">
    <div class="card-body p-4">

        <?php if (!empty($_SESSION['user_id'])): ?>

            <?php if ($vm->isOrganizer): ?>
            <p class="small mb-0" style="color:var(--muted);">You are the organiser of this event.</p>

            <?php elseif ($vm->userStatus === 'approved'): ?>
            <div class="text-center mb-3">
                <div class="fs-2 mb-1">✅</div>
                <div class="fw-bold">You're going!</div>
                <small style="color:var(--muted);">See you on the field</small>
            </div>
            <form method="POST" action="<?= url('/events/' . $event['id'] . '/leave') ?>"
                  onsubmit="return confirm('Leave this event?')">
                <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                <button class="btn btn-outline-secondary w-100"
                        aria-label="Leave <?= e($event['title']) ?>">Leave Event</button>
            </form>

            <?php elseif ($vm->userStatus === 'pending'): ?>
            <div class="text-center mb-3">
                <div class="fs-2 mb-1">⏳</div>
                <div class="fw-bold">Request pending</div>
                <small style="color:var(--muted);">Waiting for organiser approval</small>
            </div>
            <form method="POST" action="<?= url('/events/' . $event['id'] . '/leave') ?>"
                  onsubmit="return confirm('Cancel your request?')">
                <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                <button class="btn btn-outline-secondary w-100"
                        aria-label="Cancel join request for <?= e($event['title']) ?>">Cancel Request</button>
            </form>

            <?php elseif ($vm->userStatus === 'rejected'): ?>
            <p class="small text-center" style="color:#B91C1C;">Your request was declined.</p>

            <?php elseif ($event['status'] === 'open' && $vm->spotsLeft > 0): ?>
            <div class="text-center mb-3">
                <div class="fw-bold fs-5"><?= $vm->spotsLeft ?> spots left</div>
                <small style="color:var(--muted);">out of <?= (int) $event['max_participants'] ?> total</small>
            </div>
            <form method="POST" action="<?= url('/events/' . $event['id'] . '/join') ?>" class="join-form">
                <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                <button type="submit" class="btn btn-primary w-100 btn-lg"
                        aria-label="<?= $event['requires_approval'] ? 'Request to join ' : 'Join ' ?><?= e($event['title']) ?>">
                    <?= $event['requires_approval'] ? '&#x1F4CB; Request to Join' : '&#x1F3AE; Join Event' ?>
                </button>
            </form>

            <?php elseif ($vm->spotsLeft <= 0): ?>
            <div class="text-center">
                <div class="fs-2 mb-1">🚫</div>
                <div class="fw-bold" style="color:#B91C1C;">Event is full</div>
            </div>

            <?php else: ?>
            <p class="small text-center" style="color:var(--muted);">This event is no longer open.</p>
            <?php endif; ?>

        <?php else: ?>
        <div class="text-center mb-3">
            <div class="fs-2 mb-2">🎮</div>
            <div class="fw-bold mb-1">Want to join?</div>
            <small style="color:var(--muted);">Sign in to participate</small>
        </div>
        <a href="<?= url('/login') ?>" class="btn btn-primary w-100">Sign In to Join</a>
        <a href="<?= url('/register') ?>" class="btn btn-outline-secondary w-100 mt-2">Create Account</a>
        <?php endif; ?>

        <hr class="my-3" style="border-color:var(--border);">
        <div class="small" style="color:var(--muted);">
            <div class="d-flex justify-content-between mb-1">
                <span>Sport</span>
                <span class="fw-semibold" style="color:var(--text);"><?= e($event['sport_icon']) ?> <?= e($event['sport_name']) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <span>Spots</span>
                <span class="fw-semibold" style="color:var(--text);"><?= $vm->approvedCount ?> / <?= (int) $event['max_participants'] ?></span>
            </div>
            <?php if (!empty($event['skill_level'])): ?>
            <div class="d-flex justify-content-between">
                <span>Level</span>
                <span class="fw-semibold" style="color:var(--text);"><?= ucfirst(e($event['skill_level'])) ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
