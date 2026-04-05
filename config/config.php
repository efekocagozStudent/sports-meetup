<?php
declare(strict_types=1);

// ── Database ───────────────────────────────────────────────────────────────
// Values are read from environment variables so the app works on Railway,
// Render, or any other host. Local Docker defaults are used as fallback.
define('DB_HOST',    $_ENV['DB_HOST']    ?? getenv('DB_HOST')    ?: 'mysql');
define('DB_NAME',    $_ENV['DB_NAME']    ?? getenv('DB_NAME')    ?: 'sports_meetup');
define('DB_USER',    $_ENV['DB_USER']    ?? getenv('DB_USER')    ?: 'root');
define('DB_PASS',    $_ENV['DB_PASS']    ?? getenv('DB_PASS')    ?: 'secret123');
define('DB_CHARSET', 'utf8mb4');

// ── Application ────────────────────────────────────────────────────────────
define('BASE_URL',  $_ENV['BASE_URL'] ?? getenv('BASE_URL') ?: '');
define('APP_NAME',  'SportsMeet');
