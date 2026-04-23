<?php

class Router {

    private array $routes = [];
    private array $middlewares = [];

    public function addMiddleware(string $method, string $path, array $middlewares) {
        $this->middlewares[$method][$path] = $middlewares;
    }

    public function get(string $path, callable $handler) {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable $handler) {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $uri, string $method) {
        //var_dump($method, $uri);
        $uri = trim($uri, '/');

        // Static Routes
        if ( isset( $this->routes[$method][$uri] ) ) {
            foreach ( $this->middlewares[$method][$uri] ?? [] as $middleware ) {
                call_user_func($middleware);
            }

            return call_user_func( $this->routes[$method][$uri] );
        }

        // Dynamic routes
        foreach ( $this->routes[$method] ?? [] as $route => $handler ) {
            $pattern = preg_replace( '#:id#', '([a-f0-9]{24})', $route );
            $pattern = "#^" . $pattern . "$#i";

            if ( preg_match( $pattern, $uri, $matches ) ) {
                $_GET['id'] = $matches[1] ?? null;

                foreach ( $this->middlewares[$method][$route] ?? [] as $middleware ) {
                    call_user_func($middleware);
                }

                return call_user_func($handler);
            }
        }

        http_response_code(404);
        echo "Not found";
    }
}

?>