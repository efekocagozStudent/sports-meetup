<?php require ROOT_PATH . '/app/views/partials/header.php'; ?>

<div class="container py-5">

  <!-- ── Page header ─────────────────────────────────────────── -->
  <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
    <div>
      <h1 class="mb-0 fw-bold" style="font-size:1.75rem;">Live Event Explorer</h1>
    </div>
    <div class="ms-auto d-flex gap-2 flex-wrap">
      <span id="ex-count" class="badge bg-secondary align-self-center" style="font-size:.9rem;"></span>
    </div>
  </div>

  <!-- ── Filter bar ──────────────────────────────────────────── -->
  <div class="card border-0 shadow-sm mb-4 p-3">
    <div class="row g-2">
      <div class="col-12 col-sm-5 col-md-4">
        <input id="ex-search" type="search" class="form-control"
               placeholder="Search title or location…" autocomplete="off">
      </div>
      <div class="col-6 col-sm-3 col-md-2">
        <select id="ex-sport" class="form-select">
          <option value="">All sports</option>
          <?php foreach ($sportTypes as $st): ?>
          <option value="<?= e(strtolower($st['name'])) ?>">
            <?= e($st['icon'] ?? '') ?> <?= e($st['name']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-6 col-sm-3 col-md-2">
        <select id="ex-skill" class="form-select">
          <option value="">Any level</option>
          <option value="beginner">Beginner</option>
          <option value="intermediate">Intermediate</option>
          <option value="advanced">Advanced</option>
        </select>
      </div>
      <div class="col-6 col-sm-3 col-md-2">
        <select id="ex-status" class="form-select">
          <option value="">Any status</option>
          <option value="open">Open</option>
          <option value="full">Full</option>
          <option value="closed">Closed</option>
        </select>
      </div>
      <div class="col-6 col-sm-auto">
        <button id="ex-upcoming" class="btn btn-outline-secondary w-100" type="button">
          Upcoming only
        </button>
      </div>
      <div class="col-6 col-sm-auto">
        <button id="ex-refresh" class="btn btn-outline-primary w-100" type="button">
          ↺ Refresh
        </button>
      </div>
    </div>
  </div>

  <!-- ── States ─────────────────────────────────────────────── -->
  <div id="ex-loading" class="text-center py-5 d-none">
    <div class="spinner-border text-primary mb-3" role="status" style="width:3rem;height:3rem;">
      <span class="visually-hidden">Loading…</span>
    </div>
    <p class="text-muted">Fetching events…</p>
  </div>

  <div id="ex-error" class="alert alert-danger d-none" role="alert"></div>

  <div id="ex-empty" class="text-center py-5 d-none">
    <p class="fs-1 mb-2">🏟️</p>
    <h4 class="text-muted">No events match your filters</h4>
    <button id="ex-reset" class="btn btn-sm btn-outline-secondary mt-2">Clear filters</button>
  </div>

  <!-- ── Results grid ───────────────────────────────────────── -->
  <div id="ex-grid" class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-4"></div>

</div>

<!-- ── Event detail modal ─────────────────────────────────────── -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-0 pb-0">
        <h2 class="modal-title fs-5 fw-bold" id="detailModalLabel"></h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-2" id="detailModalBody"></div>
      <div class="modal-footer border-0">
        <?php if (!empty($_SESSION['user_id'])): ?>
        <button id="detailModalJoin" class="btn btn-success" style="display:none;" onclick="apiJoin()">Join Event</button>
        <button id="detailModalLeave" class="btn btn-warning" style="display:none;" onclick="apiLeave()">Leave Event</button>
        <?php endif; ?>
        <a id="detailModalLink" href="#" class="btn btn-primary">View full page →</a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php require ROOT_PATH . '/app/views/partials/footer.php'; ?>

<script>
/*
 * How this page works:
 *
 * 1. PHP renders only the skeleton (filter bar, empty containers).
 *    No event data is embedded in the HTML at all.
 *
 * 2. On page load, fetchEvents() calls GET /api/events using the browser's
 *    fetch() API and stores the full response in `allEvents`.
 *
 * 3. applyFilters() runs over `allEvents` entirely in JS — no extra HTTP
 *    requests — and calls renderCards() with the matching subset.
 *
 * 4. renderCards() builds card HTML from template literals and injects it
 *    into #ex-grid. PHP is not involved at this step at all.
 *
 * 5. Clicking a card calls GET /api/events/{id} for the full detail and
 *    shows it inside a Bootstrap modal — again no page reload.
 */
(function () {
  'use strict';

  // Base URL for the events API endpoint (PHP writes this value at render time)
  const API_URL = '<?= url('/api/events') ?>';

  // allEvents holds the full unfiltered array returned by the API.
  // Filtering always works against this array so only one fetch is needed.
  let allEvents   = [];
  let upcomingOn  = false;  // tracks the Upcoming Only toggle state
  let fetchTimer  = null;   // used by the debounce helper

  // ── DOM refs ─────────────────────────────────────────────────────────
  const grid        = document.getElementById('ex-grid');
  const loading     = document.getElementById('ex-loading');
  const errorBox    = document.getElementById('ex-error');
  const emptyBox    = document.getElementById('ex-empty');
  const countBadge  = document.getElementById('ex-count');
  const searchInput = document.getElementById('ex-search');
  const sportSel    = document.getElementById('ex-sport');
  const skillSel    = document.getElementById('ex-skill');
  const statusSel   = document.getElementById('ex-status');
  const upcomingBtn = document.getElementById('ex-upcoming');
  const refreshBtn  = document.getElementById('ex-refresh');
  const resetBtn    = document.getElementById('ex-reset');

  // Skill badge colours
  const SKILL_CLASS = {
    beginner:     'bg-success',
    intermediate: 'bg-warning text-dark',
    advanced:     'bg-danger',
  };

  // Status badge colours
  const STATUS_CLASS = {
    open:   'bg-success',
    full:   'bg-warning text-dark',
    closed: 'bg-secondary',
  };

  // ── Fetch ─────────────────────────────────────────────────────────────
  // Calls GET /api/events, which returns JSON from ApiController::events().
  // The Accept header tells the server we want JSON, not an HTML page.
  // Once the response arrives we store it in allEvents and trigger filtering.
  async function fetchEvents() {
    setLoading(true);
    clearError();

    try {
      const res = await fetch(API_URL, { headers: { 'Accept': 'application/json' } });
      if (!res.ok) throw new Error(`Server returned ${res.status}`);
      const json = await res.json();         // parse the JSON response body
      if (!json.success) throw new Error(json.message ?? 'API error');
      allEvents = json.data ?? [];           // store the events array
    } catch (err) {
      showError('Could not load events: ' + err.message);
      allEvents = [];
    } finally {
      setLoading(false);
    }

    applyFilters();  // render whatever matches the current filter state
  }

  // ── Filtering ─────────────────────────────────────────────────────────
  // Runs entirely in the browser against the cached allEvents array.
  // No network request is made — the API was only called once on page load.
  function applyFilters() {
    const search = searchInput.value.trim().toLowerCase();
    const sport  = sportSel.value.toLowerCase();
    const skill  = skillSel.value.toLowerCase();
    const status = statusSel.value.toLowerCase();
    const now    = new Date();

    let filtered = allEvents.filter(e => {
      // Text search: match against title or location
      if (search && !e.title.toLowerCase().includes(search)
                 && !e.location.toLowerCase().includes(search)) return false;
      // Sport filter: compare against the sport name from the API
      if (sport  && (e.sport ?? '').toLowerCase() !== sport)   return false;
      // Skill level: beginner | intermediate | advanced
      if (skill  && (e.skill_level ?? '').toLowerCase() !== skill) return false;
      // Status: open | full | closed
      if (status && e.status !== status)                         return false;
      // Upcoming toggle: hide events whose date has already passed
      if (upcomingOn && new Date(e.event_date) <= now)           return false;
      return true;
    });

    renderCards(filtered);
  }

  // ── Render ────────────────────────────────────────────────────────────
  // Takes the filtered events array, builds HTML strings via cardHTML(),
  // and injects them into #ex-grid. PHP is not involved here at all —
  // this is the "JavaScript displays the data" part of the requirement.
  function renderCards(events) {
    grid.innerHTML = '';
    countBadge.textContent = events.length + (events.length === 1 ? ' event' : ' events');

    if (events.length === 0) {
      emptyBox.classList.remove('d-none');
      return;
    }
    emptyBox.classList.add('d-none');

    // Build all card HTML at once and set it in a single DOM write for performance
    grid.innerHTML = events.map(cardHTML).join('');

    // Attach a click listener to each card so clicking opens the detail modal
    grid.querySelectorAll('[data-event-id]').forEach(btn => {
      btn.addEventListener('click', () => openDetail(btn.dataset.eventId));
    });
  }

  function cardHTML(e) {
    const date       = formatDate(e.event_date);
    const spots      = e.spots_left ?? (e.max_participants - (e.participants_count ?? 0));
    const pct        = Math.min(100, Math.round(((e.participants_count ?? 0) / (e.max_participants || 1)) * 100));
    const fillClass  = pct >= 100 ? 'bg-danger' : pct >= 75 ? 'bg-warning' : 'bg-success';
    const skillBadge = e.skill_level
      ? `<span class="badge ${SKILL_CLASS[e.skill_level] ?? 'bg-secondary'}">${cap(e.skill_level)}</span>`
      : '';
    const approvalIcon = e.requires_approval
      ? `<span title="Requires approval" class="badge bg-info text-dark">Approval needed</span>`
      : '';
    const statusBadge  = `<span class="badge ${STATUS_CLASS[e.status] ?? 'bg-secondary'}">${cap(e.status)}</span>`;

    return `
    <div class="col">
      <div class="card h-100 border-0 shadow-sm event-card" style="cursor:pointer;"
           data-event-id="${e.id}">
        <div class="card-header d-flex align-items-center gap-2 border-0 pb-1"
             style="background:var(--bs-body-bg)">
          <span class="fs-4">${e.sport_icon ?? ''}</span>
          <span class="small text-muted">${e.sport ?? ''}</span>
          <div class="ms-auto d-flex gap-1 flex-wrap justify-content-end">
            ${statusBadge} ${skillBadge} ${approvalIcon}
          </div>
        </div>
        <div class="card-body pt-1">
          <h5 class="card-title fw-semibold mb-1">${esc(e.title)}</h5>
          <p class="text-muted small mb-2">
            📅 ${date} &nbsp;·&nbsp; 📍 ${esc(e.location)}
          </p>
          <div class="progress mb-1" style="height:6px;" title="${pct}% full">
            <div class="progress-bar ${fillClass}" style="width:${pct}%"></div>
          </div>
          <p class="text-muted small mb-0">
            ${Math.max(0, spots)} spot${spots !== 1 ? 's' : ''} left
            &nbsp;·&nbsp; by ${esc(e.organizer ?? '')}
          </p>
        </div>
      </div>
    </div>`;
  }

  // ── Detail modal ──────────────────────────────────────────────────────
  // Called when the user clicks a card.
  // Shows the modal with a spinner immediately, then calls GET /api/events/{id}
  // to fetch the full event detail and replaces the spinner with the content.
  async function openDetail(id) {
    _currentDetailId = id;
    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
    const body  = document.getElementById('detailModalBody');
    const title = document.getElementById('detailModalLabel');
    const link  = document.getElementById('detailModalLink');
    const joinBtn  = document.getElementById('detailModalJoin');
    const leaveBtn = document.getElementById('detailModalLeave');

    // Reset join/leave buttons
    if (joinBtn)  { joinBtn.style.display = '';  joinBtn.disabled = false; joinBtn.textContent = 'Join Event'; }
    if (leaveBtn) { leaveBtn.style.display = 'none'; }

    // Show spinner and open modal before the request completes
    body.innerHTML  = '<div class="text-center py-3"><div class="spinner-border text-primary"></div></div>';
    title.textContent = 'Loading…';
    link.href = '<?= url('/events/') ?>' + id;  // "View full page" button links to the PHP show page
    modal.show();

    try {
      // Second API call: GET /api/events/{id} — returns full detail for one event
      const res  = await fetch('<?= url('/api/events/') ?>' + id, { headers: { 'Accept': 'application/json' } });
      const json = await res.json();
      if (!json.success) throw new Error(json.message ?? 'Not found');
      const e = json.data;

      title.textContent = e.title;
      const date   = formatDate(e.event_date);
      const pct    = Math.min(100, Math.round(((e.max_participants - (e.max_participants || 1)) / (e.max_participants || 1)) * 100));

      body.innerHTML = `
        <div class="row g-3">
          <div class="col-12">
            <p class="text-muted small mb-1">
              <strong>${e.sport_icon ?? ''} ${esc(e.sport ?? '')}</strong>
              &nbsp;·&nbsp; 📅 ${date}
              &nbsp;·&nbsp; 📍 ${esc(e.location)}
            </p>
            <p class="mb-2">${esc(e.description ?? 'No description provided.')}</p>
          </div>
          <div class="col-sm-6">
            <dl class="row small g-0 mb-0">
              <dt class="col-5 text-muted">Organiser</dt>
              <dd class="col-7">${esc(e.organizer ?? '')}</dd>
              <dt class="col-5 text-muted">Skill level</dt>
              <dd class="col-7">${cap(e.skill_level ?? 'Any')}</dd>
              <dt class="col-5 text-muted">Status</dt>
              <dd class="col-7"><span class="badge ${STATUS_CLASS[e.status] ?? 'bg-secondary'}">${cap(e.status)}</span></dd>
              <dt class="col-5 text-muted">Approval</dt>
              <dd class="col-7">${e.requires_approval ? 'Required' : 'Instant join'}</dd>
            </dl>
          </div>
          <div class="col-sm-6">
            <dl class="row small g-0 mb-0">
              <dt class="col-5 text-muted">Capacity</dt>
              <dd class="col-7">${e.max_participants} players</dd>
            </dl>
          </div>
        </div>`;
    } catch (err) {
      body.innerHTML = `<p class="text-danger">${err.message}</p>`;
    }
  }

  // ── UI helpers ────────────────────────────────────────────────────────
  function setLoading(on) {
    loading.classList.toggle('d-none', !on);
    if (on) grid.innerHTML = '';
  }

  function showError(msg) {
    errorBox.textContent = msg;
    errorBox.classList.remove('d-none');
  }

  function clearError() {
    errorBox.classList.add('d-none');
    errorBox.textContent = '';
  }

  function formatDate(str) {
    if (!str) return '–';
    const d = new Date(str);
    return isNaN(d) ? str : d.toLocaleDateString('en-GB', { day:'numeric', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' });
  }

  function cap(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }

  function esc(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  // ── Debounced live-filter ──────────────────────────────────────────────
  // Wraps a function so it only runs after `ms` milliseconds of inactivity.
  // Used on the search input so we don't re-filter on every single keystroke.
  function debounce(fn, ms) {
    return (...args) => { clearTimeout(fetchTimer); fetchTimer = setTimeout(() => fn(...args), ms); };
  }

  const debouncedFilter = debounce(applyFilters, 200);  // 200 ms delay on search input

  // ── Event listeners ───────────────────────────────────────────────────
  // Wire up controls to filtering. Dropdowns filter instantly; search is debounced.
  // Refresh re-fetches from the API in case new events were created.
  searchInput.addEventListener('input',  debouncedFilter);
  sportSel.addEventListener('change',    applyFilters);
  skillSel.addEventListener('change',    applyFilters);
  statusSel.addEventListener('change',   applyFilters);
  refreshBtn.addEventListener('click',   fetchEvents);   // only button that hits the network again

  upcomingBtn.addEventListener('click', () => {
    upcomingOn = !upcomingOn;
    upcomingBtn.classList.toggle('btn-outline-secondary', !upcomingOn);
    upcomingBtn.classList.toggle('btn-primary',            upcomingOn);
    applyFilters();
  });

  resetBtn.addEventListener('click', () => {
    searchInput.value = '';
    sportSel.value    = '';
    skillSel.value    = '';
    statusSel.value   = '';
    upcomingOn        = false;
    upcomingBtn.classList.remove('btn-primary');
    upcomingBtn.classList.add('btn-outline-secondary');
    applyFilters();
  });

  // ── Bootstrap ─────────────────────────────────────────────────────────
  // Kick off the initial API call as soon as the script runs.
  fetchEvents();
})();

// ── Join / Leave via API (POST) ───────────────────────────────────────
// These functions live outside the IIFE so the inline onclick handlers
// in the modal footer can reach them.
let _currentDetailId = null;

async function apiJoin() {
  if (!_currentDetailId) return;
  const btn = document.getElementById('detailModalJoin');
  btn.disabled = true;
  btn.textContent = 'Joining…';

  try {
    const res  = await fetch('<?= url('/api/events/') ?>' + _currentDetailId + '/join', {
      method: 'POST',
      headers: { 'Accept': 'application/json' },
    });
    const json = await res.json();
    if (!json.success) throw new Error(json.message ?? 'Could not join.');

    btn.textContent = '✓ Joined!';
    btn.classList.replace('btn-success', 'btn-outline-success');
    // Show the leave button
    const leaveBtn = document.getElementById('detailModalLeave');
    if (leaveBtn) { leaveBtn.style.display = ''; }
    btn.style.display = 'none';
  } catch (err) {
    btn.disabled = false;
    btn.textContent = 'Join Event';
    alert(err.message);
  }
}

async function apiLeave() {
  if (!_currentDetailId) return;
  const btn = document.getElementById('detailModalLeave');
  btn.disabled = true;
  btn.textContent = 'Leaving…';

  try {
    const res  = await fetch('<?= url('/api/events/') ?>' + _currentDetailId + '/leave', {
      method: 'POST',
      headers: { 'Accept': 'application/json' },
    });
    const json = await res.json();
    if (!json.success) throw new Error(json.message ?? 'Could not leave.');

    btn.style.display = 'none';
    // Show join button again
    const joinBtn = document.getElementById('detailModalJoin');
    if (joinBtn) { joinBtn.style.display = ''; joinBtn.disabled = false; joinBtn.textContent = 'Join Event'; joinBtn.classList.replace('btn-outline-success','btn-success'); }
  } catch (err) {
    btn.disabled = false;
    btn.textContent = 'Leave Event';
    alert(err.message);
  }
}
</script>
