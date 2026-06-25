<?php

declare(strict_types=1);

/**
 * Router
 *
 * Maps an incoming request (HTTP method + path) to a controller action.
 *
 * NOTE: This is the Week 1 skeleton. Route registration is sketched out, but
 * dispatching is implemented in a later week once the controllers exist.
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
     * Match the current request to a registered route and run its handler.
     */
    public function dispatch(string $method, string $uri): void
    {
        // Week 4+: find the matching route and invoke its controller action.
        throw new RuntimeException('Router::dispatch() is implemented in a later week.');
    }
}
