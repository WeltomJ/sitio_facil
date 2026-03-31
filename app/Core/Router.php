<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array $action): void
    {
        $this->addRoute('GET', $path, $action);
    }

    public function post(string $path, array $action): void
    {
        $this->addRoute('POST', $path, $action);
    }

    private function addRoute(string $method, string $path, array $action): void
    {
        $this->routes[] = ['method' => $method, 'path' => $path, 'action' => $action];
    }

    public function dispatch(string $method, string $uri): void
    {
        foreach ($this->routes as $route) {
            $pattern = '#^' . preg_replace('#\{[^/]+\}#', '([^/]+)', $route['path']) . '$#';

            if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                [$controllerClass, $action] = $route['action'];

                $fullClass  = "App\\Controllers\\{$controllerClass}";
                $controller = new $fullClass();
                $controller->$action(...$matches);
                return;
            }
        }

        http_response_code(404);
        require ROOT_PATH . '/app/Views/errors/404.php';
    }
}
