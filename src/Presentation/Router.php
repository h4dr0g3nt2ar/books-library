<?php
declare(strict_types=1);


namespace App\Presentation;

final class Router
{

    private array $routes = [];
    public function  get(string $path, callable $handler): void{
        $this->routes['GET'][$path] = $handler;
    }
    public function post(string $path, callable $handler): void{
        $this->routes['POST'][$path] = $handler;
    }
    public function run(string $uri, string $method = 'GET'): void{
        $path = parse_url($uri, PHP_URL_PATH);

        if(!isset($this->routes[$method])){
            $this->notFound();
            return;
        }
        foreach($this->routes[$method] as $route => $handler){
            $pattern = preg_replace('#\{[^}]+\}#', '([^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches);
                $handler(...$matches);
                return;
            }
        }

        $this->notFound();
    }
    private function notFound(): void{
        http_response_code(404);
        echo '<h1>404 Not Found</h1>';
        echo '<a href="/">На главную</a>';
    }

}