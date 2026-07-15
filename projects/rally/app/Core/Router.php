<?php

declare(strict_types=1);

namespace Rally\Core;

/**
 * Method plus path router. Routes are literal segments or {param}
 * placeholders; a placeholder matches one non-slash segment and is
 * passed to the controller action as a string argument in declaration
 * order.
 */
final class Router
{
    /** @var list<array{method: string, regex: string, handler: array{class-string, string}}> */
    private array $routes = [];

    /** @param array{class-string, string} $handler */
    public function get(string $pattern, array $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    /** @param array{class-string, string} $handler */
    public function post(string $pattern, array $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    /** @param array{class-string, string} $handler */
    private function add(string $method, string $pattern, array $handler): void
    {
        $regex = preg_replace('/\{[a-zA-Z_][a-zA-Z0-9_]*\}/', '([^/]+)', $pattern);
        $this->routes[] = [
            'method'  => $method,
            'regex'   => '#^' . $regex . '$#',
            'handler' => $handler,
        ];
    }

    /**
     * Resolve and invoke the matching controller action. Emits 404 for
     * no path match and 405 for a method mismatch.
     */
    public function dispatch(string $method, string $path): void
    {
        $path = '/' . trim($path, '/');
        $pathMatched = false;

        foreach ($this->routes as $route) {
            if (!preg_match($route['regex'], $path, $matches)) {
                continue;
            }
            $pathMatched = true;
            if ($route['method'] !== $method) {
                continue;
            }
            [$class, $action] = $route['handler'];
            $controller = new $class();
            $controller->$action(...array_map('urldecode', array_slice($matches, 1)));
            return;
        }

        if ($pathMatched) {
            http_response_code(405);
            header('Allow: GET, POST');
            View::render('errors/404', ['title' => 'Method not allowed']);
            return;
        }

        http_response_code(404);
        View::render('errors/404', ['title' => 'Page not found']);
    }
}
