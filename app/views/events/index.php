<?php require ROOT_PATH . '/app/views/partials/header.php'; ?>

<!-- Hero -->
<section aria-label="Welcome banner">
<div class="hero">
    <h1>Find your next game.<br><em>Or create it.</em></h1>
    <p>Organise sports meetups, fill your team, and never miss a session again.</p>
    <div class="d-flex flex-wrap gap-3">
        <a href="#events" class="btn btn-primary btn-lg">Browse Events</a>
        <?php if (!empty($_SESSION['user_id'])): ?>
        <a href="<?= url('/events/create') ?>" class="btn btn-outline-light btn-lg"
           aria-label="Create a new event">+ Create Event</a>
        <?php else: ?>
        <a href="<?= url('/register') ?>" class="btn btn-outline-light btn-lg">Get Started Free</a>
        <?php endif; ?>
    </div>
</div>
</section>

<!-- Filter Bar -->
<?php require ROOT_PATH . '/app/views/partials/filter_bar.php'; ?>

<!-- Section heading -->
<section aria-labelledby="events-heading">
<div class="d-flex justify-content-between align-items-center mb-3" id="events">
    <h2 class="section-title mb-0" id="events-heading">Upcoming Events</h2>
    <?php if (!empty($_SESSION['user_id'])): ?>
    <a href="<?= url('/events/create') ?>" class="btn btn-primary btn-sm"
       aria-label="Create a new event">+ Create Event</a>
    <?php endif; ?>
</div>

<!-- No results state (shown by JS when filtering returns nothing) -->
<div id="noResults" class="text-center py-5 text-muted d-none">
    <div class="fs-1 mb-2">&#x1F50D;</div>
    <h5>No matches</h5>
    <p class="small mb-0">Try a different search or sport filter.</p>
</div>

<?php if (empty($cards)): ?>
<div class="text-center py-5 text-muted">
    <div class="fs-1 mb-2">&#x1F3C3;</div>
    <h5>No events yet</h5>
    <p class="small mb-0"><a href="<?= url('/events/create') ?>">Create the first one &rarr;</a></p>
</div>
<?php else: ?>
<div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4" id="eventsGrid">
    <?php foreach ($cards as $card): ?>
    <?php require ROOT_PATH . '/app/views/partials/event_card.php'; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>

</section>

<?php require ROOT_PATH . '/app/views/partials/footer.php'; ?>