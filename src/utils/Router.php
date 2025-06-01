<?php

namespace Chinook\Utils;

class Router {
    private $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => []
    ];

    // GET route
    public function get($path, $handler) {
        $this->routes['GET'][$path] = $handler;
    }

    // POST route
    public function post($path, $handler) {
        $this->routes['POST'][$path] = $handler;
    }

    // PUT route
    public function put($path, $handler) {
        $this->routes['PUT'][$path] = $handler;
    }

    // DELETE route
    public function delete($path, $handler) {
        $this->routes['DELETE'][$path] = $handler;
    }

    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        $uri = str_replace(BASE_URL, '', $uri);
        $uri = '/' . trim($uri, '/');

        if ($method === 'POST' && isset($_POST['_method']) && $_POST['_method'] === 'PUT') {
            $method = 'PUT';
        }

        if (!isset($this->routes[$method])) {
            $this->sendResponse(['error' => 'Method not allowed'], 405);
            return;
        }

        $matchedRoute = false;
        $params = [];
        
        foreach ($this->routes[$method] as $route => $handler) {
            $pattern = '#^' . $route . '$#';
            
            if (preg_match($pattern, $uri, $matches)) {
                $matchedRoute = true;
                
                array_shift($matches);
                $params = $matches;
                
                list($controller, $action) = explode('@', $handler);
                
                $controllerClass = "Chinook\\Controllers\\" . $controller;
                
                $controllerInstance = new $controllerClass();
                call_user_func_array([$controllerInstance, $action], $params);
                
                break;
            }
        }
        
        if (!$matchedRoute) {
            $this->sendResponse(['error' => 'Not found'], 404);
        }
    }

    // Send JSON response
    public function sendResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
} 