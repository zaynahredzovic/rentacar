<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';

use FastRoute\RouteCollector;
use App\Controllers\AuthController;

// Prevent direct access to PHP files
$requestedFile = $_SERVER['SCRIPT_NAME'];
if (strpos($requestedFile, '.php') !== false && $requestedFile !== '/index.php') {
    // Redirect to the appropriate route
    $path = str_replace('.php', '', basename($requestedFile));
    if ($path === 'login') {
        header('Location: /rentacar/public/');
        exit;
    } elseif ($path === 'signup') {
        header('Location: /rentacar/public/signup');
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

//Remove the base
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

// Dispatch the route
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // You can remove this debug line after testing
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