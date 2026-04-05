<?php
declare(strict_types=1);

abstract class Controller
{
    // ── Rendering ─────────────────────────────────────────────────────────

    protected function render(string $view, array $data = []): void
    {
        header('Content-Type: text/html; charset=utf-8');
        extract($data);
        $csrf  = $this->csrfToken();
        $flash = $this->popFlash();

        $viewPath = ROOT_PATH . '/app/views/' . $view . '.php';
        if (!file_exists($viewPath)) {
            http_response_code(404);
            die('View not found: ' . htmlspecialchars($view, ENT_QUOTES, 'UTF-8'));
        }

        require $viewPath;
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . BASE_URL . $path);
        exit;
    }

    protected function json(mixed $data, int $status = 200): never
    {
        // Discard any buffered output (e.g. PHP warnings with display_errors=On)
        // so the response is guaranteed to be pure JSON.
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    // ── Auth ──────────────────────────────────────────────────────────────

    protected function requireAuth(): void
    {
        if (empty($_SESSION['user_id'])) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            $this->redirect('/login');
        }
    }

    protected function requireAdmin(): void
    {
        $this->requireAuth();
        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            $pageTitle = '403 Forbidden';
            require ROOT_PATH . '/app/views/403.php';
            exit;
        }
    }

    // ── CSRF ──────────────────────────────────────────────────────────────

    protected function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    protected function verifyCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403);
            die('Invalid security token. Go back and try again.');
        }
    }

    // ── Flash messages ────────────────────────────────────────────────────

    protected function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    private function popFlash(): ?array
    {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }

        return null;
    }
}
