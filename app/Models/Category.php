<?php

namespace App\Models;

use app\Core\Database;
use PDO;

class Category{
    private $db;

    public function __construct(){
        $this->db = Database::getConnection();    
    }

    public function getAll(){
        $stmt = $this->db->query("SELECT * FROM categories ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id){
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($name){
        $check = $this->db->prepare("SELECT * FROM categories WHERE name = :name");
        $check->execute(['name' => $name]);

        if($check->fetch()){
            return ['status' => 'error', 'message' => 'Category already exists'];    
        }

        $stmt = $this->db->prepare("INSERT INTO categories (name) VALUES (:name)");
        $success = $stmt->execute([':name' => $name]);

        if($success){
            return [
                'status' => 'success',
                'message' => 'Category created successfully',
                'id' => $this->db->lastInsertId(),
                'name' => $name
            ];
        }

        return ['status' => 'error', 'message' => 'Failed to create category'];
    }

    public function update($id, $name){
        $stmt = $this->db->prepare("UPDATE categories SET name = :name WHERE id = :id");
        $success = $stmt->execute([':name' => $name, ':id' => $id]);

        if($success){
            return ['status' => 'success', 'message' => 'Category updated successfully'];
        }

        return ['status' => 'error', 'message' => 'Failed to update category'];
    }

    public function delete($id){
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = :id");
        $success = $stmt->execute([':id' => $id]);

        if($success){
            return ['status' => 'success', 'message' => 'Category deleted successfully'];
        }

        return ['status' => 'error', 'message' => 'Failed to delete category'];
    }

    public function getWithCarCount(){
        $stmt = $this->db->query("
            SELECT c.*, COUNT(car.id) AS car_count
            FROM categories c
            LEFT JOIN cars ON c.id = car.category_id
            GROUP BY c.id
            ORDER BY c.name ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}