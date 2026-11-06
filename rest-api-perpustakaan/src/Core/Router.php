<?php

namespace App\Core;

class Router
{
    /** @var array<string, array<string, callable>> */
    private array $routes = [];

    public function get(string $path, callable $h): void    { $this->routes['GET'][$path] = $h; }
    public function post(string $path, callable $h): void   { $this->routes['POST'][$path] = $h; }
    public function put(string $path, callable $h): void    { $this->routes['PUT'][$path] = $h; }
    public function delete(string $path, callable $h): void { $this->routes['DELETE'][$path] = $h; }

    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $uri    = rtrim($request->uri(), '/') ?: '/';

        // 1) Cari handler untuk method yang diminta
        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            $params = $this->match($route, $uri);
            if ($params !== null) {
                call_user_func_array($handler, array_merge([$request], $params));
                return;
            }
        }

        // 2) Path dikenal, tetapi method tidak cocok, maka 405
        foreach ($this->routes as $otherMethod => $list) {
            if ($otherMethod === $method) {
                continue;
            }
            foreach (array_keys($list) as $route) {
                if ($this->match($route, $uri) !== null) {
                    Response::error("Method $method tidak diizinkan untuk $uri", 405);
                }
            }
        }

        // 3) Path tidak dikenal, maka 404
        Response::error("Endpoint tidak ditemukan: $uri", 404);
    }

    /** @return string[]|null parameter hasil cocok, atau null bila tidak cocok */
    private function match(string $route, string $uri): ?array
    {
        // {id} hanya cocok dengan angka, sehingga /api/books/abc menjadi 404
        $pattern = preg_replace('#\{[^}]+\}#', '(\d+)', $route);

        if (preg_match("#^$pattern$#", $uri, $matches)) {
            array_shift($matches);
            return $matches;
        }
        return null;
    }
}