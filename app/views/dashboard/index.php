<?php require ROOT_PATH . '/app/views/partials/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Dashboard</h1>
        <p class="small mb-0" style="color:var(--muted);">Welcome back, <strong><?= e($_SESSION['username']) ?></strong> &#x1F44B;</p>
    </div>
    <a href="<?= url('/events/create') ?>" class="btn btn-primary"
       aria-label="Create a new event">+ Create Event</a>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-0" id="dashTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab-organised" data-bs-toggle="tab" data-bs-target="#organised" type="button" role="tab">
            My Events <span class="badge rounded-pill ms-1" style="background:var(--accent);font-size:.7rem;"><?= count($organizedEvents) ?></span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-joined" data-bs-toggle="tab" data-bs-target="#joined" type="button" role="tab">
            Joined <span class="badge rounded-pill ms-1" style="background:var(--blue);font-size:.7rem;"><?= count($joinedEvents) ?></span>
        </button>
    </li>
    <?php if (!empty($pendingApprovals)): ?>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-pending" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
            Pending Approvals <span class="badge rounded-pill ms-1" style="background:#F59E0B;font-size:.7rem;"><?= count($pendingApprovals) ?></span>
        </button>
    </li>
    <?php endif; ?>
</ul>

<div class="tab-content" id="dashTabContent">

    <!-- Tab: My Events -->
    <div class="tab-pane fade show active" id="organised" role="tabpanel" aria-labelledby="tab-organised">
        <?php if (empty($organizedEvents)): ?>
        <div class="p-4 text-center" style="color:var(--muted);">
            You haven't created any events yet. <a href="<?= url('/events/create') ?>">Create your first one &rarr;</a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr><th>Event</th><th>Sport</th><th>Date</th><th>Spots</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                <?php foreach ($organizedEvents as $e): ?>
                <?php require ROOT_PATH . '/app/views/partials/event_row.php'; ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Tab: Joined -->
    <div class="tab-pane fade" id="joined" role="tabpanel" aria-labelledby="tab-joined">
        <?php if (empty($joinedEvents)): ?>
        <div class="p-4 text-center" style="color:var(--muted);">
            You haven't joined any events yet. <a href="<?= url('/events') ?>">Browse events &rarr;</a>
        </div>
        <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
            <?php foreach ($joinedEvents as $joinedEvent): ?>
            <?php require ROOT_PATH . '/app/views/partials/joined_event_card.php'; ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Tab: Pending Approvals -->
    <?php if (!empty($pendingApprovals)): ?>
    <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="tab-pending">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr><th>User</th><th>Event</th><th>Requested</th><th>Action</th></tr>
                </thead>
                <tbody>
                <?php foreach ($pendingApprovals as $pa): ?>
                <?php require ROOT_PATH . '/app/views/partials/pending_row.php'; ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

</div><!-- /tab-content -->

<?php require ROOT_PATH . '/app/views/partials/footer.php'; ?>