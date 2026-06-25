<?php

declare(strict_types=1);

/**
 * Router
 *
 * Maps an incoming request (HTTP method + path) to a controller action.
 * Matching is a simple exact match on the path, which is all StreamHive needs:
 * controllers read any extra input (ids, form fields) from $_GET / $_POST
 * themselves, so the router stays small.
 */
class Router
{
    /**
     * Registered routes.
     *
     * @var array<int, array{method: string, path: string, handler: callable|array}>
     */
    private array $routes = [];

    /**
     * Register a route for a given HTTP method and path.
     *
     * @param callable|array $handler A callable or a [ControllerClass, 'method'] pair.
     */
    public function add(string $method, string $path, callable|array $handler): void
    {
        $this->routes[] = [
            'method'  => strtoupper($method),
            'path'    => $path,
            'handler' => $handler,
        ];
    }

    /**
     * Register a GET route.
     *
     * @param callable|array $handler
     */
    public function get(string $path, callable|array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    /**
     * Register a POST route.
     *
     * @param callable|array $handler
     */
    public function post(string $path, callable|array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    /**
     * Match the current request to a registered route and run its handler.
     * Sends a 404 response when no route matches.
     */
    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        $path = $this->normalisePath($uri);

        foreach ($this->routes as $route)
        {
            if ($route['method'] === $method && $route['path'] === $path)
            {
                $this->invoke($route['handler']);
                return;
            }
        }

        http_response_code(404);
        echo 'Page not found.';
    }

    /**
     * Strip the query string and any trailing slash from the request URI.
     */
    private function normalisePath(string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH);

        if (!is_string($path) || $path === '')
        {
            return '/';
        }

        $path = rtrim($path, '/');

        return $path === '' ? '/' : $path;
    }

    /**
     * Run a route handler, instantiating the controller when needed.
     *
     * @param callable|array $handler
     */
    private function invoke(callable|array $handler): void
    {
        if (is_array($handler))
        {
            [$class, $method] = $handler;
            $controller = new $class();
            $controller->$method();
            return;
        }

        $handler();
    }
}
