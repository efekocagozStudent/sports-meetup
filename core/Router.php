<?php
declare(strict_types=1);

class Router
{
    private array $routes = [];

    public function get(string $pattern, string $controller, string $action): void
    {
        $this->routes[] = ['GET', $pattern, $controller, $action];
    }

    public function post(string $pattern, string $controller, string $action): void
    {
        $this->routes[] = ['POST', $pattern, $controller, $action];
    }

    public function dispatch(string $method, string $uri): void
    {
        foreach ($this->routes as [$routeMethod, $pattern, $controller, $action]) {
            if ($routeMethod !== $method) {
                continue;
            }

            // Convert {param} placeholders to named capture groups
            $regex = '#^' . preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern) . '$#';

            if (preg_match($regex, $uri, $matches)) {
                // Extract only named string keys (the captured params)
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                $file = ROOT_PATH . '/app/controllers/' . $controller . '.php';
                require_once $file;
                (new $controller())->$action(...array_values($params));
                return;
            }
        }

        http_response_code(404);
        require ROOT_PATH . '/app/views/404.php';
    }
}
