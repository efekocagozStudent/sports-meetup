<?php require ROOT_PATH . '/app/views/partials/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Admin Panel</h1>
        <p class="small mb-0" style="color:var(--muted);">Signed in as <strong><?= e($_SESSION['username']) ?></strong> &mdash; administrator</p>
    </div>
    <a href="<?= url('/admin/events/create') ?>" class="btn btn-primary btn-sm">+ Create Event</a>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-0" id="adminTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab-users" data-bs-toggle="tab" data-bs-target="#tab-users-pane"
                type="button" role="tab" aria-controls="tab-users-pane" aria-selected="true">
            Users <span class="badge rounded-pill ms-1" style="background:var(--accent);font-size:.7rem;"><?= count($users) ?></span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-events" data-bs-toggle="tab" data-bs-target="#tab-events-pane"
                type="button" role="tab" aria-controls="tab-events-pane" aria-selected="false">
            Events <span class="badge rounded-pill ms-1" style="background:var(--blue);font-size:.7rem;"><?= count($events) ?></span>
        </button>
    </li>
</ul>

<div class="tab-content" id="adminTabContent">

    <!-- ── Users tab ─────────────────────────────────────────────────── -->
    <div class="tab-pane fade show active" id="tab-users-pane" role="tabpanel" aria-labelledby="tab-users">
        <div class="table-responsive mt-1">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td class="text-muted small"><?= (int)$u['id'] ?></td>
                    <td><strong><?= e($u['username']) ?></strong></td>
                    <td class="small"><?= e($u['email']) ?></td>
                    <td>
                        <?php if ($u['role'] === 'admin'): ?>
                        <span class="badge" style="background:var(--accent);">Admin</span>
                        <?php else: ?>
                        <span class="badge bg-secondary">User</span>
                        <?php endif; ?>
                    </td>
                    <td class="small text-muted"><?= formatDate($u['created_at'], 'd M Y') ?></td>
                    <td class="text-end">
                        <a href="<?= url('/admin/users/' . (int)$u['id'] . '/edit') ?>"
                           class="btn btn-sm btn-outline-secondary me-1">Edit</a>
                        <?php if ($u['role'] !== 'admin'): ?>
                        <form method="POST" action="<?= url('/admin/users/' . (int)$u['id'] . '/delete') ?>"
                              onsubmit="return confirm('Delete user <?= e(addslashes($u['username'])) ?>? This will also remove all their events and participations.');"
                              class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                            <button type="submit" class="btn btn-sm btn-danger"
                                    aria-label="Delete user <?= e($u['username']) ?>">Delete</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ── Events tab ────────────────────────────────────────────────── -->
    <div class="tab-pane fade" id="tab-events-pane" role="tabpanel" aria-labelledby="tab-events">
        <div class="table-responsive mt-1">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Organiser</th>
                        <th>Sport</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($events as $ev): ?>
                <tr>
                    <td class="text-muted small"><?= (int)$ev['id'] ?></td>
                    <td>
                        <a href="<?= url('/events/' . (int)$ev['id']) ?>"><?= e($ev['title']) ?></a>
                    </td>
                    <td class="small"><?= e($ev['organizer_name']) ?></td>
                    <td class="small"><?= e($ev['sport_icon']) ?> <?= e($ev['sport_name']) ?></td>
                    <td class="small text-muted"><?= formatDate($ev['event_date'], 'd M Y H:i') ?></td>
                    <td>
                        <?php
                        $badge = match($ev['status']) {
                            'open'      => 'bg-success',
                            'closed'    => 'bg-secondary',
                            'cancelled' => 'bg-danger',
                            default     => 'bg-secondary',
                        };
                        ?>
                        <span class="badge <?= $badge ?>"><?= ucfirst(e($ev['status'])) ?></span>
                    </td>
                    <td class="text-end">
                        <a href="<?= url('/admin/events/' . (int)$ev['id'] . '/edit') ?>"
                           class="btn btn-sm btn-outline-secondary me-1">Edit</a>
                        <form method="POST" action="<?= url('/admin/events/' . (int)$ev['id'] . '/delete') ?>"
                              onsubmit="return confirm('Permanently delete this event? This cannot be undone.');"
                              class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                            <button type="submit" class="btn btn-sm btn-danger"
                                    aria-label="Delete event <?= e($ev['title']) ?>">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php require ROOT_PATH . '/app/views/partials/footer.php'; ?>
