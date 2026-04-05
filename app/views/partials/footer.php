
</main>

<!-- ARIA live region: screen readers announce dynamic updates (join, filter) without a page reload -->
<div id="a11y-announce" role="status" aria-live="polite" aria-atomic="true" class="sr-only"></div>

<footer style="background:var(--navbar-bg);color:rgba(255,255,255,.45);" class="py-4 mt-5">
    <div class="container text-center">
        <small>© <?= date('Y') ?> Sports<span style="color:var(--accent);">Meet</span> — Find your next game</small>
        <span class="mx-2" aria-hidden="true">·</span>
        <small><a href="<?= url('/privacy') ?>" style="color:rgba(255,255,255,.45);">Privacy Policy</a></small>
    </div>
</footer>

<!-- Cookie consent banner (GDPR) — shown until user accepts or declines -->
<div id="cookieBanner" role="dialog" aria-label="Cookie consent"
     style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:1055;
            background:#1e1e2e;color:#cdd6f4;padding:1rem 1.5rem;
            box-shadow:0 -2px 12px rgba(0,0,0,.4);">
    <div class="container d-flex flex-column flex-sm-row align-items-sm-center gap-3">
        <p class="mb-0 small flex-grow-1">
            We use a session cookie (<code>PHPSESSID</code>) to keep you signed in. No tracking or advertising cookies are used.
            <a href="<?= url('/privacy') ?>" class="text-info">Learn more</a>
        </p>
        <div class="d-flex gap-2 flex-shrink-0">
            <button id="cookieAccept" class="btn btn-sm btn-primary">Accept</button>
            <button id="cookieDecline" class="btn btn-sm btn-outline-secondary">Decline</button>
        </div>
    </div>
</div>

<script>
(function () {
    var banner = document.getElementById('cookieBanner');
    if (!banner) return;
    var consent = localStorage.getItem('cookie_consent');
    if (!consent) banner.style.display = 'block';

    document.getElementById('cookieAccept').addEventListener('click', function () {
        localStorage.setItem('cookie_consent', 'accepted');
        banner.style.display = 'none';
    });
    document.getElementById('cookieDecline').addEventListener('click', function () {
        localStorage.setItem('cookie_consent', 'declined');
        banner.style.display = 'none';
    });
}());
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= url('/assets/js/app.js') ?>"></script>
</body>
</html>
