<?php
class Router {
    private $routes = [];
    public function get($path, $action) { $this->routes['GET'][$path] = $action; }
    public function post($path, $action) { $this->routes['POST'][$path] = $action; }
    public function dispatch($method, $path) {
        $path = rtrim($path, '/') ?: '/';
        $action = $this->routes[$method][$path] ?? null;
        if (!$action) { http_response_code(404); require __DIR__ . '/../views/errors/404.php'; return; }
        [$controller, $methodName] = explode('@', $action);
        (new $controller())->$methodName();
    }
}
