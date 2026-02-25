<?php 

namespace App\Models;

use App\Core\Database;
use PDO;

class User{
    private $db;

    public function __construct(){
        $this->db = Database::getConnection();
        
        if ($this->db === null) {
            error_log("Database connection is null in User model");
        } else {
            error_log("Database connection successful in User model");
        }
    }

    public function register($fullName, $email, $password){
        if($this->findByEmail($email)){
            return ["status" => "error", "message"=> "Email already exists"];
        }

        $hashedPwd = password_hash($password, PASSWORD_BCRYPT);

        try {
            $stmt = $this->db->prepare("INSERT INTO users (name, email, pwd) VALUES (:name, :email, :pwd)");
            $stmt->execute([
                ':name' => $fullName,
                ':email' => $email,
                ':pwd' => $hashedPwd
            ]);
            
            return ["status" => "success", "message" => "Account created successfully"];
        } catch (\PDOException $e) {
            error_log("Register error: " . $e->getMessage());
            return ["status" => "error", "message" => "Signup failed"];
        }
    }

    public function login($email, $password) {
        $user = $this->findByEmail($email);

        if(!$user) {
            return ["status" => "error", "message" => "Invalid credentials"];
        }

        if(password_verify($password, $user['pwd'])){
            unset($user['pwd']);
            return ["status" => "success", "message" => "Login successful", "user" => $user];
        }

        return ["status" => "error", "message" => "Invalid credentials"];
    }

    public function findByEmail($email) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("FindByEmail error: " . $e->getMessage());
            return null;
        }
    }
}