<?php

class Router {

    private array $routes = [];

    public function get(string $path, callable $handler) {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable $handler) {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $uri, string $method) {
        $uri = trim($uri, '/');

        if (isset($this->routes[$method][$uri])) {
            return call_user_func($this->routes[$method][$uri]);
        }

        if (preg_match('/^[a-f0-9]{24}$/i', $uri)) {
            $_GET['id'] = $uri;
            return call_user_func($this->routes[$method][':id']);
        }

        http_response_code(404);
        echo "Not found";
    }
}

?>