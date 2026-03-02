<?php

namespace App\Controllers;

use App\Models\User;
use App\Core\Session;

class AuthController {

    public function login() {
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function signup() {
        require __DIR__ . '/../Views/auth/signup.php';
    }

    // SIGNUP API
    public function signupPost() {
        header('Content-Type: application/json');
        
        $userModel = new User();

        if(!isset($_POST['fullName']) || !isset($_POST['email']) || !isset($_POST['password'])){
            echo json_encode(['status' => 'error', 'message' => 'Fill in all the fields']);
            return;
        }

        $result = $userModel->register(
            $_POST['fullName'], 
            $_POST['email'],
            $_POST['password']
        );

        echo json_encode($result);
    }

    // LOGIN API
    public function loginPost() {
        header('Content-Type: application/json');

        if(!isset($_POST['email']) || !isset($_POST['password'])){
            echo json_encode(['status' => 'error', 'message' => 'Email and password required']);
            return;
        }

        $userModel = new User();

        $result = $userModel->login(
            $_POST['email'],
            $_POST['password']
        );

        if($result['status'] === 'success') {
            Session::set('user', $result['user']);
        }

        echo json_encode([
            "status" => $result['status'],
            "message" => $result['message']
        ]);
    }
}