<?php
// Expects: $sportTypes, $filters (search, sport_type_id, upcoming)
?>
<form method="GET" action="<?= url('/events') ?>" id="filterForm" class="mb-4">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label for="search" class="form-label">Search</label>
            <input type="text" class="form-control" id="search" name="search"
                   value="<?= e($filters['search']) ?>"
                   placeholder="Title or location…" autocomplete="off">
        </div>
        <div class="col-md-3">
            <label for="sport" class="form-label">Sport</label>
            <select class="form-select" id="sport" name="sport">
                <option value="">All sports</option>
                <?php foreach ($sportTypes as $st): ?>
                <option value="<?= e($st['id']) ?>" <?= $filters['sport_type_id'] == $st['id'] ? 'selected' : '' ?>>
                    <?= e($st['icon']) ?> <?= e($st['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <div class="form-check" style="margin-top:1.8rem;">
                <input class="form-check-input" type="checkbox" id="upcoming" name="upcoming" value="1"
                    <?= $filters['upcoming'] ? 'checked' : '' ?>>
                <label class="form-check-label small fw-semibold" for="upcoming">Upcoming only</label>
            </div>
        </div>
        <div class="col-md-3 d-flex gap-2" style="margin-top:1.5rem;">
            <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
            <a href="<?= url('/events') ?>" class="btn btn-outline-secondary">Clear</a>
        </div>
    </div>
</form>
