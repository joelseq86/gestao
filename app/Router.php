<?php

class Router
{
    private array $routes = [];

    public function add(string $method, string $pattern, string $controller, string $action): void
    {
        $this->routes[] = compact('method', 'pattern', 'controller', 'action');
    }

    public function dispatch(string $method, string $uri): void
    {
        // Strip query string
        $uri = strtok($uri, '?') ?: '/';

        foreach ($this->routes as $route) {
            if (strtoupper($route['method']) !== strtoupper($method) &&
                $route['method'] !== 'ANY') {
                continue;
            }

            // Convert pattern like /lancamentos/editar/{id} to regex
            $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route['pattern']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                $controllerFile = __DIR__ . '/controllers/' . $route['controller'] . '.php';
                if (!file_exists($controllerFile)) {
                    http_response_code(500);
                    die('Controller não encontrado: ' . h($route['controller']));
                }
                require_once $controllerFile;

                $controller = new $route['controller']();
                $action = $route['action'];
                $controller->$action($params);
                return;
            }
        }

        http_response_code(404);
        echo '<h1>404 — Página não encontrada</h1>';
    }
}
