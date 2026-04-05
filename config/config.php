<?php
declare(strict_types=1);

// ── Database ───────────────────────────────────────────────────────────────
define('DB_HOST',    'mysql');
define('DB_NAME',    'sports_meetup');
define('DB_USER',    'root');
define('DB_PASS',    'secret123');
define('DB_CHARSET', 'utf8mb4');

// ── Application ────────────────────────────────────────────────────────────
// BASE_URL: leave empty when running with the PHP built-in server
//   php -S localhost:8000 -t public
// If hosting in a subdirectory under Apache (e.g. /sports-meetup/public),
//   set to '/sports-meetup/public'
define('BASE_URL',  '');
define('APP_NAME',  'SportsMeet');
