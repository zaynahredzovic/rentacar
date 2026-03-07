<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Car{
    private $db;

    public function __construct(){
        $this->db = Database::getConnection();    
    }

    //create new car
    public function create($data){
    try {
        $sql = 'INSERT INTO cars (user_id, category_id, title, description, image_path, price_per_day, active) VALUES (:user_id, :category_id, :title, :description, :image_path, :price_per_day, :active)';

        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            ':user_id' => $data['user_id'],
            ':category_id' => $data['category_id'],
            ':title' => $data['title'],
            ':description' => $data['description'],  // FIXED: removed backticks
            ':image_path' => $data['image_path'],
            ':price_per_day' => $data['price_per_day'],
            ':active' => 1
        ]);

        if($success){
            return [
                'status' => 'success',
                'message' => 'Car created successfully',
                'id' => $this->db->lastInsertId(),
                'title' => $data['title']
            ];
        }

        return ['status' => 'error', 'message' => 'Failed to add car'];

    } catch (\PDOException $e) {
        error_log("Car creation failed: " . $e->getMessage());
        return ['status' => 'error', 'message' => 'Failed to create car'];
    }
}

    //get cars by user id
    public function getByUser($user_id, $filter = 'all'){
        try {
            $sql = "SELECT c.*, cat.name as category_name FROM cars c
        LEFT JOIN categories cat ON c.category_id = cat.id
        WHERE c.user_id = :user_id";

        if($filter === 'active'){
            $sql .= " AND c.active = 1";
        } elseif($filter === 'inactive'){
            $sql .= " AND c.active = 0";
        }

        $sql .= " ORDER BY c.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Car getByUser: " . $e->getMessage());
            return [];
        }
    }

    //get single car
    public function getById($id){
        try {
            $stmt = $this->db->prepare("SELECT * FROM cars WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Car getById: " . $e->getMessage());
            return null;
        }
    }

    //update car
    public function update($id, $data){
        try {
            $sql = "UPDATE cars SET
                category_id = :category_id,
                title = :title,
                `description` = :description,
                price_per_day = :price_per_day,
                active = :active";

            $params = [
                ':id' => $id,
                ':category_id' => $data['category_id'],
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':price_per_day' => $data['price_per_day'],
                ':active' => $data['active'] ? 1 : 0,
            ];

            //only update img if provided
            if(isset($data['image_path'])){
                $sql .= ", image_path = :image_path";
                $params[':image_path'] = $data['image_path'];
            }

            $sql .= " WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute($params);

            if($success){
                return ['status' => 'success', 'message' => 'Car updated successfully'];
            }

            return ['status' => 'error', 'message' => 'Failed to update car'];
        } catch (\PDOException $e) {
            error_log("Car update: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Failed to update car'];
        }
    }

    //delete car
    public function delete($id){
        try {
            $stmt = $this->db->prepare("DELETE FROM cars WHERE id = :id");
            $success = $stmt->execute([':id' => $id]);

            if($success){
                return ['status' => 'success', 'message' => 'Car deleted successfully'];
            }

            return ['status' => 'error', 'message' => 'Failed to delete car'];
        } catch (\PDOException $e) {
            error_log("Car delete: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Failed to delete car'];
        }
    }

    //togle active status
    public function toggleStatus($id){
        try {
            $car = $this->getById($id);
            if(!$car){
                return ['status' => 'error', 'message' => 'Car not found'];
            }

            $newStatus = $car['active'] ? 0 : 1;

            $stmt = $this->db->prepare("UPDATE cars SET active = :active WHERE id = :id");
            $success = $stmt->execute([':active' => $newStatus, ':id' => $id]);

            if($success){
                return ['status' => 'success', 'message' => 'Car status toggled successfully', 'active' => $newStatus];
            }

            return ['status' => 'error', 'message' => 'Failed to toggle car status'];
        } catch (\PDOException $e) {
            error_log("Car toggleStatus: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Failed to toggle car status'];
        }
    }
}