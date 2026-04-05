<?php require ROOT_PATH . '/app/views/partials/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-lg-7">

        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('/admin') ?>">Admin Panel</a></li>
                <li class="breadcrumb-item active">Edit Event</li>
            </ol>
        </nav>

        <div class="card auth-card">
            <div class="card-body p-4">
                <h1 class="h4 fw-bold mb-1">Edit Event</h1>
                <p class="small mb-4" style="color:var(--muted);">Admin edit — ownership check bypassed.</p>

                <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?= url('/admin/events/' . $event['id'] . '/update') ?>" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">

                    <div class="mb-3">
                        <label for="title" class="form-label">Event title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title"
                               value="<?= e($event['title']) ?>" minlength="3" maxlength="100" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="sport_type_id" class="form-label">Sport type <span class="text-danger">*</span></label>
                            <select class="form-select" id="sport_type_id" name="sport_type_id" required>
                                <option value="">— Select —</option>
                                <?php foreach ($sportTypes as $st): ?>
                                <option value="<?= e($st['id']) ?>" <?= $event['sport_type_id'] == $st['id'] ? 'selected' : '' ?>>
                                    <?= e($st['icon']) ?> <?= e($st['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="skill_level" class="form-label">Skill level</label>
                            <select class="form-select" id="skill_level" name="skill_level">
                                <option value="beginner"     <?= ($event['skill_level'] ?? '') === 'beginner'     ? 'selected' : '' ?>>🟢 Beginner</option>
                                <option value="intermediate" <?= ($event['skill_level'] ?? '') === 'intermediate' ? 'selected' : '' ?>>🟡 Intermediate</option>
                                <option value="advanced"     <?= ($event['skill_level'] ?? '') === 'advanced'     ? 'selected' : '' ?>>🔴 Advanced</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="event_date" class="form-label">Date &amp; time <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="event_date" name="event_date"
                                   value="<?= e(date('Y-m-d\TH:i', strtotime($event['event_date']))) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="max_participants" class="form-label">Max participants <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="max_participants" name="max_participants"
                                   value="<?= e($event['max_participants']) ?>" min="2" max="1000" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="location" name="location"
                               value="<?= e($event['location']) ?>" maxlength="255" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="4" minlength="10" required><?= e($event['description']) ?></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="open"      <?= $event['status'] === 'open'      ? 'selected' : '' ?>>Open</option>
                                <option value="closed"    <?= $event['status'] === 'closed'    ? 'selected' : '' ?>>Closed</option>
                                <option value="cancelled" <?= $event['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="requires_approval" name="requires_approval" value="1"
                               <?= $event['requires_approval'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="requires_approval">Require organiser approval to join</label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                        <a href="<?= url('/admin') ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/app/views/partials/footer.php'; ?>
