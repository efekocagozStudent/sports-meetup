
</main>

<!-- ARIA live region: screen readers announce dynamic updates (join, filter) without a page reload -->
<div id="a11y-announce" role="status" aria-live="polite" aria-atomic="true" class="sr-only"></div>

<footer style="background:var(--navbar-bg);color:rgba(255,255,255,.45);" class="py-4 mt-5">
    <div class="container text-center">
        <small>© <?= date('Y') ?> Sports<span style="color:var(--accent);">Meet</span> — Find your next game</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= url('/assets/js/app.js') ?>"></script>
</body>
</html>
