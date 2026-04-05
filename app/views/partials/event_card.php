<?php
// Expects: $card (EventCardViewModel)
?>
<article class="col event-card"
     aria-label="<?= e($card->title) ?> — <?= e($card->sportName) ?> event"
     data-title="<?= e(strtolower($card->title)) ?>"
     data-location="<?= e(strtolower($card->location)) ?>"
     data-sport="<?= e($card->sportTypeId) ?>">
    <div class="card h-100">
        <div class="card-body p-4 d-flex flex-column">

            <!-- Top: sport + skill + status -->
            <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
                <span class="sport-badge"><?= e($card->sportIcon) ?> <?= e($card->sportName) ?></span>
                <div class="d-flex gap-1 flex-wrap">
                    <?php if ($card->skillLevel !== ''): ?>
                    <span class="skill-badge skill-<?= e($card->skillLevel) ?>"><?= ucfirst(e($card->skillLevel)) ?></span>
                    <?php endif; ?>
                    <span class="status-badge status-<?= e($card->status) ?>"><?= ucfirst(e($card->status)) ?></span>
                </div>
            </div>

            <!-- Title -->
            <h5 class="fw-bold mb-2 lh-sm" style="font-size:1.05rem;">
                <a href="<?= url('/events/' . $card->id) ?>"
                   class="text-decoration-none stretched-link" style="color:var(--text);">
                    <?= e($card->title) ?>
                </a>
            </h5>

            <!-- Location + date -->
            <p class="mb-1 small" style="color:var(--muted);">📍 <?= e($card->location) ?></p>
            <p class="mb-0 small" style="color:var(--muted);">📅 <?= formatDate($card->eventDate) ?></p>

            <!-- Progress bar + meta -->
            <div class="mt-auto pt-3">
                <div class="d-flex justify-content-between small mb-1" style="color:var(--muted);">
                    <span>Spots</span>
                    <span><strong style="color:var(--text);"><?= $card->spotsLeft ?></strong> left of <?= $card->maxParticipants ?></span>
                </div>
                <div class="spots-bar"
                     role="progressbar"
                     aria-label="<?= $card->spotsLeft ?> of <?= $card->maxParticipants ?> spots remaining"
                     aria-valuenow="<?= $card->approvedCount ?>"
                     aria-valuemin="0"
                     aria-valuemax="<?= $card->maxParticipants ?>">
                    <div class="spots-bar-fill <?= $card->fillClass ?>" style="width:<?= $card->fillPercent ?>%"></div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2 small" style="color:var(--muted);">
                    <span>by <?= e($card->organizerName) ?></span>
                    <?php if ($card->requiresApproval): ?>
                    <span style="color:#B45309;">&#x26A0; Approval required</span>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</article>
