# SportsMeet

A PHP 8 sports event management app using a custom MVC framework, fully Dockerized (nginx + PHP-FPM + MariaDB).

---

## GitHub

<https://github.com/efekocagozStudent/sports-meetup.git>

---

## Run locally

```bash
docker-compose up --build
```

Visit **<http://localhost:8080>**.

Database schema + seed data are in `sql/schema.sql` and auto-load on first run.

> **Fresh database:**
> ```bash
> docker-compose down -v
> docker-compose up --build
> ```

---

## Public URL via ngrok

1. Install ngrok: `winget install Ngrok.Ngrok`
2. Add your token: `ngrok config add-authtoken YOUR_TOKEN`
3. Run Docker, then: `ngrok http 8080`

> Free URL changes on restart. First public URL: `https://holli-proscholastic-businesslike.ngrok-free.dev/`

---

## Test accounts

**All passwords:** `Password1!`

**phpMyAdmin:** <http://localhost:8081> — username `root`, password `secret123`

| Username | Role |
|---|---|
| `admin` | Admin |
| `alex_smith` | User |
| `jordan_fc` | User (football) |
| `sam_hoops` | User (basketball) |
| `riley_runner` | User (running) |
| `morgan_net` | User (volleyball) |

The `/admin` panel is for admins only: manage users and events. Regular users get a 403.

---

## Project structure

```
sports-meetup/
├── app/
│   ├── controllers/
│   ├── services/
│   ├── repositories/
│   ├── interfaces/
│   ├── viewmodels/
│   └── views/ (partials + pages)
├── core/ (Controller, Router, Database)
├── config/ (config.php)
├── public/ (index.php, .htaccess, CSS)
└── sql/ (schema.sql)
```

---

## Architecture / Patterns

- **MVC** — controllers handle requests, services contain business logic, repositories access data.
- **Interfaces** decouple layers — easy to swap implementations.
- **ViewModels** precompute display data for views (no business logic in views).
- **Partials** for reusable headers, footers, cards.
- **Routes** declaratively map URLs to controller methods.
- **JSON API + JS** — client-side filtering and modals without reloading the page.

---

## Security

- PDO prepared statements (no string concatenation)
- Passwords hashed with bcrypt (cost 12)
- CSRF tokens on all POST forms
- `session_regenerate_id(true)` on login
- `httponly` + `samesite=Lax` cookies
- Output escaped with `htmlspecialchars` via `e()` helper

---

## Accessibility (WCAG 2.1 AA)

- Keyboard navigation + skip-to-content link
- Visible `:focus-visible` outlines
- ARIA labels for icons/buttons
- Alt text on images
- Semantic HTML (`<nav>`, `<main>`, `<article>`, `role="tablist"`)
- Color is never the only cue
- Screen reader live updates via `aria-live="polite"`

---

## GDPR

- Only session cookie used (`PHPSESSID`)
- Cookie consent banner on first visit (Accept / Decline)
- Users can view, update, and delete their data
- Privacy policy at `/privacy`

