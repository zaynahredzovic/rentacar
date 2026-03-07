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
    $r->addRoute('GET', '/logout', 'App\Controllers\AuthController@logout');

    // Dashboard (protected)
    $r->addRoute('GET', '/dashboard', 'App\Controllers\DashboardController@index');

    // API for auth
    $r->addRoute('POST', '/api/login', 'App\Controllers\AuthController@loginPost');
    $r->addRoute('POST', '/api/signup', 'App\Controllers\AuthController@signupPost');

    // API for categories
    $r->addRoute('GET', '/api/categories', 'App\Controllers\CategoryController@list');
    $r->addRoute('GET', '/api/categories/count', 'App\Controllers\CategoryController@listWithCount');

    $r->addRoute('POST', '/api/categories', 'App\Controllers\CategoryController@create');
    $r->addRoute('PUT', '/api/categories/{id}', 'App\Controllers\CategoryController@update');

    $r->addRoute('DELETE', '/api/categories/{id}', 'App\Controllers\CategoryController@delete');
});

// Fetch method and URI
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];


// Remove query string (?foo=bar) if present
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}

// Remove the base path
$basePath = '/rentacar';
while (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
    error_log("Removed base path, now URI: " . $uri);
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