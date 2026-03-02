<?php
session_start();

// First, include the autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Then you can use Dotenv
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once __DIR__ . '/app/core/Database.php';
use FastRoute\RouteCollector;
use App\Controllers\AuthController;

// Initialize database connection
new App\Core\Database(); // This establishes the connection

error_log("DB_NAME: " . App\Core\Env::get('DB_NAME'));

// Prevent direct access to PHP files
$requestedFile = $_SERVER['SCRIPT_NAME'];
if (strpos($requestedFile, '.php') !== false && $requestedFile !== '/index.php') {
    // Redirect to the appropriate route
    $path = str_replace('.php', '', basename($requestedFile));
    if ($path === 'login') {
        header('Location: /rentacar/');
        exit;
    } elseif ($path === 'signup') {
        header('Location: /rentacar/signup');
        exit;
    }
}

// Initialize Dispatcher
$dispatcher = FastRoute\simpleDispatcher(function(RouteCollector $r) {
    
    // Pages
    $r->addRoute('GET', '/', 'App\Controllers\AuthController@login');
    $r->addRoute('GET', '/signup', 'App\Controllers\AuthController@signup');

    // API
    $r->addRoute('POST', '/api/login', 'App\Controllers\AuthController@loginPost');
    $r->addRoute('POST', '/api/signup', 'App\Controllers\AuthController@signupPost');
});

// Fetch method and URI
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// DEBUG: Log the incoming request
error_log("=== INCOMING REQUEST ===");
error_log("Method: " . $httpMethod);
error_log("Original URI: " . $uri);
error_log("POST data: " . print_r($_POST, true));

// Remove query string (?foo=bar) if present
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}

// Remove the base path
$basePath = '/rentacar';
if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}

// If URI is empty after removing base path, set it to '/'
if (empty($uri)) {
    $uri = '/';
}

// Decode URI
$uri = rawurldecode($uri);

// DEBUG: Log the processed URI
error_log("Processed URI: " . $uri);

// Dispatch the route
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// DEBUG: Log the route info
error_log("Route info: " . print_r($routeInfo, true));

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        error_log("404 - Page not found for URI: " . $uri);
        echo "404 - Page not found for URI: " . $uri;
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        error_log("405 - Method not allowed. Allowed: " . implode(', ', $allowedMethods));
        echo "405 - Method not allowed";
        break;

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1]; 
        $vars = $routeInfo[2]; 

        error_log("Route found! Handler: " . $handler);
        
        // Split controller and method
        list($class, $method) = explode('@', $handler);

        error_log("Calling controller: " . $class . "::" . $method);

        // Instantiate controller and call method
        if(class_exists($class)) {
            $controller = new $class();
            call_user_func_array([$controller, $method], $vars);
        } else {
            error_log("Controller $class not found");
            echo "Controller $class not found";
        }
        break;
}