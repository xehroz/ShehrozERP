<?php
/**
 * Procurement ERP - Router
 * 
 * Handles URL routing and dispatching to controllers
 */

namespace App\Core;

class Router {
    /**
     * Dispatch the request to the appropriate controller and action
     */
    public function dispatch() {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Get the URI and remove query string
        $uri = $_SERVER['REQUEST_URI'];
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        
        // Remove trailing slash if not root
        $uri = rtrim($uri, '/');
        if (empty($uri)) {
            $uri = '/';
        }
        
        // Default routes
        $routes = [
            '/' => ['controller' => 'Auth', 'action' => 'login'],
            '/auth/login' => ['controller' => 'Auth', 'action' => 'login'],
            '/auth/authenticate' => ['controller' => 'Auth', 'action' => 'authenticate'],
            '/auth/logout' => ['controller' => 'Auth', 'action' => 'logout', 'auth' => true],
            '/auth/forgot-password' => ['controller' => 'Auth', 'action' => 'forgotPassword'],
            '/auth/reset-password' => ['controller' => 'Auth', 'action' => 'resetPassword'],
            '/dashboard' => ['controller' => 'Dashboard', 'action' => 'index', 'auth' => true],
            '/error/404' => ['controller' => 'Error', 'action' => 'notFound'],
            '/error/403' => ['controller' => 'Error', 'action' => 'forbidden'],
            '/error/500' => ['controller' => 'Error', 'action' => 'serverError'],
        ];
        
        // Check if route exists
        if (array_key_exists($uri, $routes)) {
            $route = $routes[$uri];
        } else {
            // Try to match a pattern (for routes with parameters)
            $matched = false;
            foreach ($routes as $pattern => $routeData) {
                // Convert route pattern to regex
                if (strpos($pattern, ':') !== false) {
                    $regexPattern = preg_replace('/\/:([^\/]+)/', '/([^/]+)', $pattern);
                    $regexPattern = str_replace('/', '\/', $regexPattern);
                    $regexPattern = '/^' . $regexPattern . '$/';
                    
                    if (preg_match($regexPattern, $uri, $matches)) {
                        $matched = true;
                        $route = $routeData;
                        
                        // Extract parameters
                        $paramNames = [];
                        preg_match_all('/:([^\/]+)/', $pattern, $paramNames);
                        
                        if (!empty($paramNames[1])) {
                            $params = [];
                            foreach ($paramNames[1] as $index => $name) {
                                $params[$name] = $matches[$index + 1];
                            }
                            $route['params'] = $params;
                        }
                        
                        break;
                    }
                }
            }
            
            // If no match found, show 404
            if (!$matched) {
                $route = $routes['/error/404'];
            }
        }
        
        // Check if authentication required
        if (isset($route['auth']) && $route['auth'] === true) {
            if (!$this->isAuthenticated()) {
                // Store intended URL for redirect after login
                $_SESSION['redirect_after_login'] = $uri;
                header('Location: /auth/login');
                exit;
            }
        }
        
        // Get controller and action
        $controllerName = $route['controller'] . 'Controller';
        $actionName = $route['action'];
        $params = $route['params'] ?? [];
        
        // Instantiate controller
        $controllerClass = 'App\\Controllers\\' . $controllerName;
        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();
            
            // Call action with parameters
            if (method_exists($controller, $actionName)) {
                call_user_func_array([$controller, $actionName], $params);
            } else {
                // Method not found, show 404
                $errorController = new \App\Controllers\ErrorController();
                $errorController->notFound("Action '$actionName' not found in controller '$controllerName'");
            }
        } else {
            // Controller not found, show 404
            $errorController = new \App\Controllers\ErrorController();
            $errorController->notFound("Controller '$controllerName' not found");
        }
    }
    
    /**
     * Check if user is authenticated
     */
    private function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }
}