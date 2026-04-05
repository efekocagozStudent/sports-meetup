<?php
declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/config/config.php';

// Buffer all output so stray PHP warnings/notices never corrupt JSON API responses.
// Controller::json() discards this buffer before sending its response.
ob_start();

// ── Secure session configuration ──────────────────────────────────────────
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => false,   // Set to true when serving over HTTPS
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// ── Autoloader ────────────────────────────────────────────────────────────
spl_autoload_register(static function (string $class): void {
    foreach (['/core/', '/app/interfaces/', '/app/repositories/', '/app/services/', '/app/viewmodels/', '/app/controllers/'] as $dir) {
        $path = ROOT_PATH . $dir . $class . '.php';
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// ── Global helpers ────────────────────────────────────────────────────────

/**
 * Escape a value for safe HTML output.
 */
function e(mixed $val): string
{
    return htmlspecialchars((string) $val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Return a URL prefixed with BASE_URL.
 */
function url(string $path = ''): string
{
    return BASE_URL . $path;
}

/**
 * Format a datetime string for display.
 */
function formatDate(string $date, string $format = 'D, d M Y \a\t H:i'): string
{
    try {
        return (new DateTimeImmutable($date))->format($format);
    } catch (Exception) {
        return e($date);
    }
}

// ── Routing ───────────────────────────────────────────────────────────────
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$uri = '/' . ltrim($uri, '/');

// Strip BASE_URL prefix when deployed in a subdirectory
$base = rtrim(BASE_URL, '/');
if ($base !== '' && str_starts_with($uri, $base)) {
    $uri = substr($uri, strlen($base));
}
$uri    = '/' . ltrim($uri, '/');
$method = strtoupper($_SERVER['REQUEST_METHOD']);

$router = new Router();

// Auth
$router->get('/login',             'AuthController', 'loginForm');
$router->post('/login',            'AuthController', 'login');
$router->get('/register',          'AuthController', 'registerForm');
$router->post('/register',         'AuthController', 'register');
$router->get('/logout',            'AuthController', 'logout');
$router->get('/forgot-password',   'AuthController', 'forgotForm');
$router->post('/forgot-password',  'AuthController', 'forgotSubmit');
$router->get('/reset-password/{token}',  'AuthController', 'resetForm');
$router->post('/reset-password/{token}', 'AuthController', 'resetSubmit');

// Static pages
$router->get('/privacy', 'PageController', 'privacy');

// Events
$router->get('/',                              'EventController', 'index');
$router->get('/events',                        'EventController', 'index');
$router->get('/events/create',                 'EventController', 'create');
$router->post('/events/store',                 'EventController', 'store');
$router->get('/events/{id}',                   'EventController', 'show');
$router->get('/events/{id}/edit',              'EventController', 'edit');
$router->post('/events/{id}/update',           'EventController', 'update');
$router->post('/events/{id}/delete',           'EventController', 'delete');
$router->post('/events/{id}/join',             'EventController', 'join');
$router->post('/events/{id}/leave',            'EventController', 'leave');
$router->post('/events/{id}/approve/{userId}', 'EventController', 'approve');
$router->post('/events/{id}/reject/{userId}',  'EventController', 'reject');

// Dashboard
$router->get('/dashboard', 'DashboardController', 'index');

// Admin
$router->get('/admin',                               'AdminController', 'dashboard');
$router->get('/admin/users/{id}/edit',               'AdminController', 'editUser');
$router->post('/admin/users/{id}/update',            'AdminController', 'updateUser');
$router->post('/admin/users/{id}/delete',            'AdminController', 'deleteUser');
$router->get('/admin/events/create',                 'AdminController', 'createEvent');
$router->post('/admin/events/store',                 'AdminController', 'storeEvent');
$router->get('/admin/events/{id}/edit',              'AdminController', 'editEvent');
$router->post('/admin/events/{id}/update',           'AdminController', 'updateEvent');
$router->post('/admin/events/{id}/delete',           'AdminController', 'deleteEvent');

// Notifications
$router->get('/notifications',          'NotificationController', 'index');
$router->post('/notifications/read-all','NotificationController', 'readAll');
$router->post('/notifications/clear',   'NotificationController', 'clear');

// Live explorer (JS-rendered)
$router->get('/explore', 'EventController', 'explore');

// JSON API
$router->get('/api/events',             'ApiController', 'events');
$router->get('/api/events/{id}',        'ApiController', 'event');
$router->post('/api/events/{id}/join',  'ApiController', 'joinEvent');
$router->post('/api/events/{id}/leave', 'ApiController', 'leaveEvent');
$router->get('/api/sports',             'ApiController', 'sports');

$router->dispatch($method, $uri);
