<?php

namespace Chinook\Utils;

class Router {
    private $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => []
    ];

    /**
     * Register a GET route
     * 
     * @param string $path Route path with regex pattern
     * @param string $handler Controller and method in format 'Controller@method'
     * @return void
     */
    public function get($path, $handler) {
        $this->routes['GET'][$path] = $handler;
    }

    /**
     * Register a POST route
     * 
     * @param string $path Route path with regex pattern
     * @param string $handler Controller and method in format 'Controller@method'
     * @return void
     */
    public function post($path, $handler) {
        $this->routes['POST'][$path] = $handler;
    }

    /**
     * Register a PUT route
     * 
     * @param string $path Route path with regex pattern
     * @param string $handler Controller and method in format 'Controller@method'
     * @return void
     */
    public function put($path, $handler) {
        $this->routes['PUT'][$path] = $handler;
    }

    /**
     * Register a DELETE route
     * 
     * @param string $path Route path with regex pattern
     * @param string $handler Controller and method in format 'Controller@method'
     * @return void
     */
    public function delete($path, $handler) {
        $this->routes['DELETE'][$path] = $handler;
    }

    /**
     * Handle the incoming request and route to the appropriate controller
     * 
     * @return void
     */
    public function handleRequest() {
        // Get request method and URI
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove base URL from URI if needed
        $uri = str_replace(BASE_URL, '', $uri);
        $uri = '/' . trim($uri, '/');

        // Handle PUT requests via POST with _method parameter
        if ($method === 'POST' && isset($_POST['_method']) && $_POST['_method'] === 'PUT') {
            $method = 'PUT';
        }

        // Check if the method is supported
        if (!isset($this->routes[$method])) {
            $this->sendResponse(['error' => 'Method not allowed'], 405);
            return;
        }

        // Find matching route
        $matchedRoute = false;
        $params = [];
        
        foreach ($this->routes[$method] as $route => $handler) {
            // Convert route to regex pattern
            $pattern = '#^' . $route . '$#';
            
            if (preg_match($pattern, $uri, $matches)) {
                $matchedRoute = true;
                
                // Extract parameters from matches
                array_shift($matches); // Remove the full match
                $params = $matches;
                
                // Parse handler
                list($controller, $action) = explode('@', $handler);
                
                // Add namespace to controller
                $controllerClass = "Chinook\\Controllers\\" . $controller;
                
                // Create controller instance and call the action
                $controllerInstance = new $controllerClass();
                call_user_func_array([$controllerInstance, $action], $params);
                
                break;
            }
        }
        
        // If no route matched, return 404
        if (!$matchedRoute) {
            $this->sendResponse(['error' => 'Not found'], 404);
        }
    }

    /**
     * Send JSON response with appropriate headers
     * 
     * @param mixed $data Data to be encoded as JSON
     * @param int $statusCode HTTP status code
     * @return void
     */
    public function sendResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
} 