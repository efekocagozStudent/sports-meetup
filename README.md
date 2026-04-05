# SportsMeet

A PHP 8 sports event management application built with a custom MVC framework running on Docker (nginx + PHP-FPM + MariaDB).

---

## Running the app

```bash
docker-compose up --build
```
## GitHub Repo
https://github.com/efekocagozStudent/sports-meetup.git

Visit `http://localhost:8080`. The database schema is in `sql/schema.sql`.

---

## Test accounts

Run `sql/seed.sql` after `sql/schema.sql` to populate the database with the accounts below.

**All accounts use the password: `Password1!`**

phpMyAdmin: `http://localhost:8081` — username `root`, password `secret123`

| Username | Email | Role |
|---|---|---|
| `admin` | admin@sportsmeet.local | Admin |
| `alex_smith` | alex@example.com | User |
| `jordan_fc` | jordan@example.com | User (organises football events) |
| `sam_hoops` | sam@example.com | User (organises basketball events) |
| `riley_runner` | riley@example.com | User (organises running events) |
| `morgan_net` | morgan@example.com | User (organises volleyball events) |

The `admin` account can access `/admin` — a panel with full CRUD over users and events:
- **Users**: edit username, email, and role; delete any non-admin account
- **Events**: create events, edit all fields (title, sport, date, location, status, etc.), delete any event
- All events including cancelled ones are visible
- Regular users cannot access any `/admin` route; they receive a 403 page

---

## Project structure

```
sports-meetup/
├── app/
│   ├── controllers/       # HTTP layer — maps requests to services and renders views
│   ├── services/          # Business logic layer (implements I*Service interfaces)
│   ├── repositories/      # Data access layer (implements I*Repository interfaces)
│   ├── interfaces/        # Contracts between layers (IEventService, IEventRepository, etc.)
│   ├── viewmodels/        # Pre-computed display data passed to views
│   └── views/
│       ├── partials/      # Reusable view fragments (header, footer, cards, rows)
│       ├── auth/
│       ├── events/
│       ├── dashboard/
│       └── notifications/
├── core/
│   ├── Controller.php     # Abstract base controller (render, redirect, json, CSRF, auth)
│   ├── Router.php         # Front-controller router
│   └── Database.php       # PDO singleton
├── config/
│   └── config.php         # DB credentials and app constants
├── public/
│   ├── index.php          # Single entry point — bootstraps the app
│   ├── .htaccess          # Rewrites all requests to index.php
│   └── assets/css/app.css
└── sql/
    └── schema.sql
```

---

## Architecture patterns

### 1. Service and Repository layers with Interfaces

Every data access operation is encapsulated behind an interface:

| Interface | Implementation | Purpose |
|---|---|---|
| `app/interfaces/IEventRepository.php` | `app/repositories/EventRepository.php` | Event CRUD and queries |
| `app/interfaces/IUserRepository.php` | `app/repositories/UserRepository.php` | User lookup and creation |
| `app/interfaces/IParticipantRepository.php` | `app/repositories/ParticipantRepository.php` | Join/leave/approve |
| `app/interfaces/INotificationRepository.php` | `app/repositories/NotificationRepository.php` | Notification persistence |
| `app/interfaces/ISportTypeRepository.php` | `app/repositories/SportTypeRepository.php` | Sport type listing |
| `app/interfaces/IEventService.php` | `app/services/EventService.php` | Event business logic |
| `app/interfaces/IUserService.php` | `app/services/UserService.php` | Auth and registration logic |
| `app/interfaces/INotificationService.php` | `app/services/NotificationService.php` | Notification orchestration |

Controllers hold typed interface references, not concrete classes:

```php
// app/controllers/EventController.php
private IEventService        $eventService;
private INotificationService $notifService;
```

This is the **Dependency Inversion Principle** — high-level modules (controllers) depend on abstractions (interfaces), not on concrete implementations. Swapping an implementation (e.g. replacing `EventRepository` with a cached version) requires no changes to the controller or service.

---

### 2. Automatic view mapping

The base `Controller::render()` method (`core/Controller.php`) resolves a view name to a file path automatically — no explicit `require` or path construction needed in controllers:

```php
// core/Controller.php
protected function render(string $view, array $data = []): void
{
    extract($data);               // unpack data as local variables
    $csrf  = $this->csrfToken();  // always available in every view
    $flash = $this->popFlash();   // flash message available in every view

    $viewPath = ROOT_PATH . '/app/views/' . $view . '.php';
    require $viewPath;
}
```

A controller simply calls:
```php
$this->render('events/index', ['cards' => $cards, 'sportTypes' => $sportTypes]);
```

The framework resolves this to `app/views/events/index.php` and makes all `$data` keys available as local variables — equivalent to ASP.NET MVC's automatic `ViewBag` / strongly-typed `@model` mapping.

---

### 3. Route and controller method binding

Routes are registered in `public/index.php` as declarative `$router->get()` / `$router->post()` calls that bind a URL pattern directly to a controller class and method name:

```php
$router->get('/events',          'EventController', 'index');
$router->get('/events/{id}',     'EventController', 'show');
$router->post('/events/store',   'EventController', 'store');
$router->get('/api/events',      'ApiController',   'events');
```

The router (`core/Router.php`) converts `{param}` placeholders into named regex capture groups, instantiates the controller, and calls the method — passing URL parameters as typed arguments:

```php
// core/Router.php
$regex = '#^' . preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern) . '$#';
if (preg_match($regex, $uri, $matches)) {
    (new $controller())->$action(...array_values($params));
}
```

This mirrors ASP.NET attribute routing (`[Route("/events/{id}")]`, `[HttpGet]`).

---

### 4. Dependency Inversion — automatic class dependency configuration

Controllers declare typed interface properties, and each constructs its own dependencies in `__construct()`. Because all dependencies are typed as interfaces, a concrete implementation can be swapped without touching the controller:

```php
// app/controllers/EventController.php
public function __construct()
{
    $this->eventService  = new EventService();   // implements IEventService
    $this->notifService  = new NotificationService(); // implements INotificationService
}
```

The autoloader (`public/index.php`) scans all layer directories in dependency order so no manual `require` chains are needed:

```php
spl_autoload_register(static function (string $class): void {
    foreach ([
        '/core/', '/app/interfaces/', '/app/repositories/',
        '/app/services/', '/app/viewmodels/', '/app/controllers/'
    ] as $dir) {
        $path = ROOT_PATH . $dir . $class . '.php';
        if (file_exists($path)) { require_once $path; return; }
    }
});
```

---

### 5. ViewModels

`app/viewmodels/EventCardViewModel.php` and `app/viewmodels/EventViewModel.php` pre-compute all display values (fill percentage, badge CSS class, spots left, user participation state) before the view renders. Views receive a typed object and contain zero business logic — equivalent to ASP.NET MVC ViewModel classes.

---

### 6. View templating with partials

Views are split into reusable partials under `app/views/partials/`:

| Partial | Used by |
|---|---|
| `header.php` / `footer.php` | Every page |
| `event_card.php` | `events/index.php` |
| `filter_bar.php` | `events/index.php` |
| `join_sidebar.php` | `events/show.php` |
| `participant_row.php` | `events/show.php` |
| `event_row.php` | `dashboard/index.php` |
| `joined_event_card.php` | `dashboard/index.php` |
| `pending_row.php` | `dashboard/index.php` |
| `notification_item.php` | `notifications/index.php` |

---

### 7. JSON API and JavaScript rendering

`app/controllers/ApiController.php` exposes three endpoints that return JSON instead of rendering a view:

| Endpoint | Method | Description |
|---|---|---|
| `/api/events` | GET | All non-cancelled events with optional `?search`, `?sport`, `?status`, `?upcoming` filters |
| `/api/events/{id}` | GET | Single event detail |
| `/api/sports` | GET | All sport types |

`app/views/events/explore.php` is the Live Explorer page. PHP renders only the static page shell (header, filter bar, empty card grid) — no event data is embedded in the HTML. The full data lifecycle is:

1. **Initial load** — on `DOMContentLoaded`, the page calls `fetch('/api/events')`. The response array is stored in a module-level `allEvents` variable.
2. **Client-side filtering** — every change to the search input, sport dropdown, skill-level dropdown, status dropdown and "upcoming only" toggle calls a shared `applyFilters()` function that filters `allEvents` in memory and re-renders the card grid. No further network requests are made.
3. **Card rendering** — `renderCards(events)` clears the grid and uses a JavaScript template literal to build one Bootstrap card per event, including the sport icon, skill-level badge, participant count and status badge.
4. **Detail modal** — clicking "View Details" on any card fires a second `fetch('/api/events/{id}')` call to retrieve the full event record (including description and organiser name) and populates a Bootstrap modal. This avoids sending all event descriptions in the initial bulk response.
5. **Error handling** — if either fetch fails (non-2xx status or network error), an inline error message is rendered inside the grid rather than breaking the page.

---

## Security

- All SQL uses PDO prepared statements — no string concatenation into queries
- All output uses `e()` (`htmlspecialchars`) — no raw variable echo in views
- CSRF tokens on every POST form, verified in `Controller::verifyCsrf()`
- Passwords stored with `password_hash(..., PASSWORD_BCRYPT, ['cost' => 12])`
- `session_regenerate_id(true)` on login to prevent session fixation
- `httponly` and `samesite=Lax` session cookie flags
- Output buffering (`ob_start`) ensures stray PHP warnings never corrupt JSON API responses

---

## Accessibility (WCAG 2.1 AA)

### Keyboard navigation
Every interactive element (buttons, links, form inputs, dropdown items) is reachable by `Tab` key alone. A **skip-to-content** link (`<a href="#main-content" class="skip-link">`) is the first focusable element on every page — it becomes visible on keyboard focus and jumps the user past the navbar directly to `<main id="main-content">`.

### Visible focus outlines
Focus rings are never hidden. Custom `:focus-visible` styles in `app.css` provide:
- **Blue** (`#1D4ED8`) 2 px outline + shadow for generic buttons, links, form controls
- **Accent orange** ring for primary/CTA buttons
- **White** ring for navbar links on the dark background

The legacy `outline: none` on form inputs was replaced with a proper `outline: 2px solid var(--blue)` combined with the blue box-shadow.

### ARIA labels
All icon-only or ambiguous controls have descriptive `aria-label` attributes:
- Notification bell link — label includes unread count: `"Notifications, 3 unread"`
- User menu toggle — `"User menu for username"`
- Join/Leave/Cancel request buttons — include the event title: `"Join Sunday 5-a-side Kickabout"`
- Approve/Reject participant buttons — include the username: `"Approve sam_hoops"`
- Edit/Cancel event buttons — include the event title
- Create Event links — `"Create a new event"`

### Alt text and icon images
- Navbar logo image: `alt="SportsMeet logo"`
- Notification bell image: `alt="Notifications"` (decorative duplicate in dropdown has `alt=""`)
- Sport icons in badges are rendered as text emoji via `e()` — inherently readable by screen readers

### Semantic HTML
| Element | Used for |
|---|---|
| `<nav aria-label="Main navigation">` | Site-wide navbar |
| `<main id="main-content">` | Page content target for skip link |
| `<footer>` | Site footer |
| `<section aria-label="…">` / `<section aria-labelledby="…">` | Hero banner, events listing |
| `<article aria-label="…">` | Each event card in the grid |
| `<nav aria-label="breadcrumb">` | Breadcrumb on event detail page |
| `role="tablist"` / `role="tab"` / `role="tabpanel"` | Dashboard tabs, with `aria-labelledby` linking each panel to its tab button |
| `role="progressbar"` with `aria-valuenow/min/max` | Spots progress bar on cards and event detail |

### Color is never the only cue
Skill level and status badges always show **text** alongside color (e.g. "Beginner", "Open"). Approval-required events show ⚠ text. Spots remaining always has a numeric label (`"3 left of 10"`) alongside the coloured progress bar.

### Font sizes
Body text uses `0.95rem` (≈15 px at default zoom — closest to 16 px with Inter's generous x-height). Headings start at `1.05rem` for card titles and reach `clamp(2rem, 5vw, 3.2rem)` for the hero. Form labels are `0.84rem` bold — all with explicit `<label for="…">` associations.

### ARIA live region
A visually-hidden `<div id="a11y-announce" role="status" aria-live="polite">` is injected in `footer.php` and is present on every page. `app.js` updates it via `announceLive(message)`:
- After the join form is submitted: `"Joining event, please wait…"`
- After a client-side filter runs: `"5 events shown."` or `"No events match your filters."`

Screen readers (NVDA, JAWS, VoiceOver) announce these messages without a page reload.
