<?php

namespace App\Core;

class Route {
    private static $routes = [];
    private static $appRoot = '/entry-tracking-api/public/';

    public static function get ($pattern, $action, $middleware = []) {
        self::$routes['GET'][] = compact('pattern', 'action', 'middleware');
    }

    public static function post ($pattern, $action, $middleware = []) {
        self::$routes['POST'][] = compact('pattern', 'action', 'middleware');
    }

    public static function executeAction ($action, $args = [], $request = null) {
        if (is_array($action)) {
            list ($class, $method) = $action;
        }

        if (is_string($action)) {
            list ($class, $method) = explode('@', $action);
            $class = "App\\Controller\\$class";
        }

        $reflection = new \ReflectionMethod($class, $method);
        $callArgs = [];
        

        foreach ($reflection->getParameters() as $i => $param) {
            $type = $param->getType();

            if ($type && $type->getName() === "App\\Core\\Request") {
                $callArgs[] = $request;
            } else {
                $callArgs[$i] = $args[$i] ?? null;
            }
        }

        $controller = new $class();

        return $controller->$method(...$callArgs);
    } 

    public static function dispatch ($method, $uri) {
        $routes = self::$routes[$method];
        $url = trim(preg_replace('/\/entry-tracking-api\/public\//', '', $uri), '/');

        foreach ($routes as $route) {
            $regex = "#^". preg_replace('/\{[0-9a-zA-z]+\}/', '([^/]+)', $route['pattern']) ."$#";

            if (preg_match($regex, $url, $matched)) {
                array_shift($matched);
                
                $request = new Request();

                $next  = function ($req) use ($route, $matched) {
                    return self::executeAction($route['action'], $matched ?? null, $req);
                };

                foreach (array_reverse($route['middleware']) as $key => $middlewareConfig) {                    
                    if (is_int($key) && is_string($middlewareConfig)) {
                        $middlewareClass = $middlewareConfig;
                        $params = null;
                    }
                    
                    if (is_string($key)) {
                        $middlewareClass = $key;
                        $params = $middlewareConfig;
                    }

                    $middleware = new $middlewareClass();

                    $next = function ($req) use ($middleware, $next, $params) {
                        return $middleware->handle($req, $next, $params);
                    };
                }
                
                return $next($request);
            }
        }

        return view('views.errors.404');
    }
}


