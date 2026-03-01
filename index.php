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

// Get the base path dynamically
$scriptPath = $_SERVER['SCRIPT_NAME'];
$basePath = rtrim(dirname($scriptPath), '/\\');

// Remove 'public' from the path if present (since index.php might be in public folder)
$basePath = str_replace('/public', '', $basePath);

// If basePath is empty, set it to empty string (for root installations)
if ($basePath == '.' || $basePath == '\\') {
    $basePath = '';
}

// Prevent direct access to PHP files
$requestedFile = $_SERVER['SCRIPT_NAME'];
if (strpos($requestedFile, '.php') !== false && $requestedFile !== '/index.php') {
    // Redirect to the appropriate route
    $path = str_replace('.php', '', basename($requestedFile));
    if ($path === 'login') {
        header('Location: ' . $basePath . '/');
        exit;
    } elseif ($path === 'signup') {
        header('Location: ' . $basePath . '/signup');
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

// Remove query string (?foo=bar) if present
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}

// Remove the base path dynamically
if (!empty($basePath) && strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}

// If URI is empty after removing base path, set it to '/'
if (empty($uri)) {
    $uri = '/';
}

// Decode URI
$uri = rawurldecode($uri);

// Dispatch the route
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        echo "404 - Page not found for URI: " . $uri;
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        echo "405 - Method not allowed";
        break;

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1]; 
        $vars = $routeInfo[2]; 

        // Split controller and method
        list($class, $method) = explode('@', $handler);

        // Instantiate controller and call method
        if(class_exists($class)) {
            $controller = new $class();
            call_user_func_array([$controller, $method], $vars);
        } else {
            echo "Controller $class not found";
        }
        break;
}