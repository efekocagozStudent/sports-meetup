/* ── SportsMeet — app.js ────────────────────────────────────────────────── */

document.addEventListener('DOMContentLoaded', () => {

    /* ── 0. ARIA live announcer ─────────────────────────────────────────── */
    // Updates the visually-hidden live region so screen readers announce
    // dynamic changes (filter results, join status) without a page reload.
    function announceLive(message) {
        const el = document.getElementById('a11y-announce');
        if (!el) return;
        el.textContent = '';                          // reset to force re-announce
        requestAnimationFrame(() => { el.textContent = message; });
    }

    /* ── 1. Live filtering (search + sport) ────────────────────────────── */
    const searchInput = document.getElementById('search');
    const sportSelect = document.getElementById('sport');
    const eventsGrid  = document.getElementById('eventsGrid');
    const noResults   = document.getElementById('noResults');

    function applyFilters() {
        if (!eventsGrid) return;
        const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
        const sport = sportSelect ? sportSelect.value : '';
        let visible = 0;

        eventsGrid.querySelectorAll('.event-card').forEach(card => {
            const text  = (card.dataset.title || '') + ' ' + (card.dataset.location || '');
            const show  = (!query || text.includes(query)) && (!sport || card.dataset.sport === sport);
            card.classList.toggle('hidden', !show);
            if (show) visible++;
        });

        if (noResults) noResults.classList.toggle('d-none', visible > 0);
        announceLive(visible === 0 ? 'No events match your filters.' : visible + ' event' + (visible === 1 ? '' : 's') + ' shown.');
    }

    if (searchInput) {
        let t;
        searchInput.addEventListener('input', () => { clearTimeout(t); t = setTimeout(applyFilters, 220); });
    }
    if (sportSelect) sportSelect.addEventListener('change', applyFilters);

    /* ── 2. Join button animation ───────────────────────────────────────── */
    document.querySelectorAll('.join-form').forEach(form => {
        form.addEventListener('submit', function () {
            const btn = this.querySelector('button[type=submit]');
            if (!btn || btn.disabled) return;
            btn.classList.add('btn-joined');
            btn.innerHTML = '✓ Joining…';
            btn.disabled = true;
            announceLive('Joining event, please wait…');
        });
    });

    /* ── 3. Password strength + match (register) ────────────────────────── */
    const pwInput    = document.getElementById('password');
    const pwStrength = document.getElementById('passwordStrength');
    const pwConfirm  = document.getElementById('confirm_password');
    const pwMatch    = document.getElementById('passwordMatch');

    if (pwInput && pwStrength) {
        pwInput.addEventListener('input', () => {
            const v = pwInput.value;
            if (!v) { pwStrength.textContent = ''; return; }
            const strong = v.length >= 12 && /[^a-zA-Z0-9]/.test(v);
            const ok     = v.length >= 8;
            pwStrength.textContent = strong ? '✓ Strong' : ok ? '~ Moderate' : '✗ Too short';
            pwStrength.className   = 'form-text fw-semibold ' + (strong ? 'text-success' : ok ? 'text-warning' : 'text-danger');
        });
    }

    if (pwConfirm && pwMatch && pwInput) {
        const check = () => {
            if (!pwConfirm.value) { pwMatch.textContent = ''; return; }
            const ok = pwConfirm.value === pwInput.value;
            pwMatch.textContent = ok ? '✓ Passwords match' : '✗ Passwords do not match';
            pwMatch.className   = 'form-text fw-semibold ' + (ok ? 'text-success' : 'text-danger');
        };
        pwConfirm.addEventListener('input', check);
        pwInput.addEventListener('input', check);
    }

    /* ── 4. Auto-dismiss flash alerts after 5s ──────────────────────────── */
    document.querySelectorAll('.alert-dismissible').forEach(el => {
        setTimeout(() => bootstrap.Alert.getOrCreateInstance(el)?.close(), 5000);
    });

});


    /* ── 1. Live client-side filtering (search + sport) ───────────────────── */
    const searchInput = document.getElementById('search');
    const sportSelect = document.getElementById('sport');
    const eventsGrid  = document.getElementById('eventsGrid');

    function applyFilters() {
        if (!eventsGrid) return;
        const query = (searchInput ? searchInput.value.trim().toLowerCase() : '');
        const sport = (sportSelect ? sportSelect.value : '');
        const cards = eventsGrid.querySelectorAll('.event-card');

        cards.forEach(card => {
            const title    = card.dataset.title    || '';
            const location = card.dataset.location || '';
            const cardSport = card.dataset.sport   || '';

            const textMatch  = !query || title.includes(query) || location.includes(query);
            const sportMatch = !sport || cardSport === sport;

            card.classList.toggle('hidden', !(textMatch && sportMatch));
        });
    }

    if (searchInput) {
        let debounceTimer;
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(applyFilters, 250);
        });
    }

    if (sportSelect) {
        sportSelect.addEventListener('change', applyFilters);
    }

    /* ── 2. Register page — password strength indicator ──────────────────── */
    const passwordInput    = document.getElementById('password');
    const strengthDisplay  = document.getElementById('passwordStrength');
    const confirmInput     = document.getElementById('confirm_password');
    const matchDisplay     = document.getElementById('passwordMatch');

    if (passwordInput && strengthDisplay) {
        passwordInput.addEventListener('input', () => {
            const val = passwordInput.value;
            let strength = '';
            let cls      = '';

            if (val.length === 0) {
                strength = '';
            } else if (val.length < 8) {
                strength = 'Too short';
                cls      = 'text-danger';
            } else if (val.length < 12 || !/[^a-zA-Z0-9]/.test(val)) {
                strength = 'Moderate';
                cls      = 'text-warning';
            } else {
                strength = 'Strong';
                cls      = 'text-success';
            }

            strengthDisplay.textContent  = strength;
            strengthDisplay.className    = 'form-text ' + cls;
        });
    }

    if (confirmInput && matchDisplay && passwordInput) {
        const checkMatch = () => {
            if (!confirmInput.value) {
                matchDisplay.textContent = '';
                return;
            }
            if (confirmInput.value === passwordInput.value) {
                matchDisplay.textContent = '✓ Passwords match';
                matchDisplay.className   = 'form-text text-success';
            } else {
                matchDisplay.textContent = '✗ Passwords do not match';
                matchDisplay.className   = 'form-text text-danger';
            }
        };
        confirmInput.addEventListener('input', checkMatch);
        passwordInput.addEventListener('input', checkMatch);
    }

    /* ── 3. AJAX join/leave button ───────────────────────────────────────── */
    const joinBtn = document.getElementById('joinBtn');

    if (joinBtn) {
        joinBtn.closest('form').addEventListener('submit', function (e) {
            joinBtn.disabled    = true;
            joinBtn.textContent = 'Joining…';
            // Form submits normally; button just gives immediate feedback
        });
    }

    /* ── 4. Confirm cancel-event forms (extra guard) ─────────────────────── */
    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', e => {
            if (!confirm(form.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });

    /* ── 5. Auto-dismiss flash alerts after 5 s ──────────────────────────── */
    const flash = document.querySelector('.alert-dismissible');
    if (flash) {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(flash);
            bsAlert.close();
        }, 5000);
    }

;
