<?php
/** @var EventViewModel $vm */
/** @var string $csrf */
require ROOT_PATH . '/app/views/partials/header.php';
?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= url('/events') ?>">Explore</a></li>
        <li class="breadcrumb-item active"><?= e($vm->event['title']) ?></li>
    </ol>
</nav>

<section aria-label="Event details">
<div class="row g-4">
    <!-- Main -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body p-4">

                <!-- Badges row -->
                <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                    <span class="sport-badge"><?= e($vm->event['sport_icon']) ?> <?= e($vm->event['sport_name']) ?></span>
                    <?php if (!empty($vm->event['skill_level'])): ?>
                    <span class="skill-badge skill-<?= e($vm->event['skill_level']) ?>"><?= ucfirst(e($vm->event['skill_level'])) ?></span>
                    <?php endif; ?>
                    <span class="status-badge status-<?= e($vm->event['status']) ?>"><?= ucfirst(e($vm->event['status'])) ?></span>
                    <?php if ($vm->event['requires_approval']): ?>
                    <span class="skill-badge" style="background:#FEF3C7;color:#B45309;">&#x26A0; Approval required</span>
                    <?php endif; ?>

                    <?php if ($vm->isOrganizer): ?>
                    <div class="ms-auto d-flex gap-2">
                        <a href="<?= url('/events/' . $vm->event['id'] . '/edit') ?>"
                           class="btn btn-sm btn-outline-secondary"
                           aria-label="Edit <?= e($vm->event['title']) ?>">Edit</a>
                        <form method="POST" action="<?= url('/events/' . $vm->event['id'] . '/delete') ?>"
                              onsubmit="return confirm('Cancel this event? This cannot be undone.')">
                            <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                            <button class="btn btn-sm btn-danger"
                                    aria-label="Cancel event: <?= e($vm->event['title']) ?>">Cancel Event</button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>

                <h1 class="fw-bold mb-4" style="font-size:1.75rem;"><?= e($vm->event['title']) ?></h1>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3" style="background:var(--bg);">
                            <div class="small fw-semibold mb-1" style="color:var(--muted);">DATE &amp; TIME</div>
                            <div class="fw-semibold">&#x1F4C5; <?= formatDate($vm->event['event_date']) ?></div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3" style="background:var(--bg);">
                            <div class="small fw-semibold mb-1" style="color:var(--muted);">LOCATION</div>
                            <div class="fw-semibold">&#x1F4CD; <?= e($vm->event['location']) ?></div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3" style="background:var(--bg);">
                            <div class="small fw-semibold mb-1" style="color:var(--muted);">CAPACITY</div>
                            <div class="fw-semibold">&#x1F465; <?= $vm->approvedCount ?> / <?= (int) $vm->event['max_participants'] ?> participants</div>
                            <div class="spots-bar mt-2"
                                 role="progressbar"
                                 aria-label="<?= $vm->approvedCount ?> of <?= (int)$vm->event['max_participants'] ?> spots filled"
                                 aria-valuenow="<?= $vm->approvedCount ?>"
                                 aria-valuemin="0"
                                 aria-valuemax="<?= (int)$vm->event['max_participants'] ?>">
                                <div class="spots-bar-fill <?= $vm->fillClass ?>" style="width:<?= $vm->fillPercent ?>%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3" style="background:var(--bg);">
                            <div class="small fw-semibold mb-1" style="color:var(--muted);">ORGANISER</div>
                            <div class="fw-semibold">&#x1F464; <?= e($vm->event['organizer_name']) ?></div>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bold mb-2">About this event</h5>
                <p style="color:var(--muted);line-height:1.8;"><?= nl2br(e($vm->event['description'])) ?></p>
            </div>
        </div>

        <!-- Participant list (organiser only) -->
        <?php if ($vm->isOrganizer && !empty($vm->participants)): ?>
        <div class="card">
            <div class="card-header bg-white fw-semibold py-3">
                Participants (<?= count($vm->participants) ?>)
            </div>
            <ul class="list-group list-group-flush">
                <?php foreach ($vm->participants as $p): ?>
                <?php require ROOT_PATH . '/app/views/partials/participant_row.php'; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <?php require ROOT_PATH . '/app/views/partials/join_sidebar.php'; ?>
    </div>
</div>
</section>

<?php require ROOT_PATH . '/app/views/partials/footer.php'; ?>