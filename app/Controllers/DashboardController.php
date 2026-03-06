<?php
namespace App\Controllers;

use App\Core\Session;

class DashboardController {
    
    public function index() {
        // Check if user is logged in
        $user = Session::get('user');
        
        
        if (!$user) {
            // Not logged in, redirect to login
            header('Location: /rentacar/');
            exit;
        }
        
        if (!is_array($user)) {
            error_log("Dashboard - User is not an array: " . gettype($user));
            Session::destroy();
            header('Location: /rentacar/');
            exit;
        }
        
        // Load dashboard view with user data
        require __DIR__ . '/../Views/dashboard.php';
    }
}