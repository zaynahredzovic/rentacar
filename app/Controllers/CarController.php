<?php
namespace App\Controllers;

use App\Models\Car;
use App\Models\Category;
use App\Core\Session;

class CarController{
    private $carModel; 
    private $categoryModel;

    public function __construct(){
        $this->carModel = new Car();
        $this->categoryModel = new Category();
    }

    //get cars for logged in user
    public function list(){
        header('Content-Type: application/json');

        //check if user is logged in
        $user = Session::get('user');
        if(!$user){
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        //get filter from query params
        $filter = $_GET['filter'] ?? 'all';

        $cars = $this->carModel->getByUser($user['id'], $filter);

        echo json_encode([
            'status' => 'success',
            'data' => $cars,
            'count' => count($cars)
        ]);
    }

    //get a single car by id
    public function get($id){
        header('Content-Type: application/json');

        //check if user is logged in
        $user = Session::get('user');
        if(!$user){
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        $car = $this->carModel->getById($id);

        if(!$car){
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Car not found']);
            return;
        }

        //check if car belongs to user
        if($car['user_id'] != $user['id']){
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Forbidden']);
            return;
        }

        echo json_encode([
            'status' => 'success',
            'data' => $car
        ]);
    }

    //create new car
    public function create(){
        header('Content-Type: application/json');

        //check if user is logged in
        $user = Session::get('user');
        if(!$user){
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        //handle img upload
        $image_path = '';
        if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK){
            $uploadResult = $this->uploadImage($_FILES['image'], $user['id']);
            if($uploadResult['status'] === 'success'){
                $image_path = $uploadResult['path'];
            } else {
                echo json_encode($uploadResult);
                return;
            }
        }else{
            echo json_encode(['status' => 'error', 'message' => 'Image upload failed']);
            return;
        }

        //prepare car data
        $carData = [
            'user_id' => $user['id'],
            'category_id' => $_POST['categpory_id'],
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'price_per_day' => $_POST['price_per_day'],
            'image_path' => $image_path,
            'active' => isset($_POST['active']) ? 1 : 0
        ];

        //valiate request fields
        $requiredFields = ['category_id', 'title', 'description', 'price_per_day'];
        foreach($requiredFields as $field){
            if(empty($carData[$field])){
                echo json_encode(['status' => 'error', 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
                return;
            }
        }

        //create car
        $result = $this->carModel->create($carData);

        echo json_encode($result);
    }

    //update car
    public function update($id){
        header('Content-Type: application/json');

        //check if user is logged in
        $user = Session::get('user');
        if(!$user){
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        //check if car exists and belongs to user
        $car = $this->carModel->getById($id);
        if(!$car){
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Car not found']);
            return;
        }

        //get PUT data
        $input = json_decode(file_get_contents('php://input'), true);
        if(!$input){
            $input = $_POST;
        }

        //handle img upload if provided
        if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK){
            $uploadResult = $this->uploadImage($_FILES['image'], $user['id']);
            if($uploadResult['status'] === 'success'){
                //delete old image if exists
                if($car['image_path'] && file_exists($_SERVER['DOCUMENT_ROOT'] . $car['image_path'])){
                    unlink($_SERVER['DOCUMENT_ROOT'] . $car['image_path']);
                }

                $input['image_path'] = $uploadResult['path'];
            }else{
                echo json_encode($uploadResult);
                return;
            }
        }

        //prepare update data
        $updateData = [
            'category_id' => $input['category_id'] ?? $car['category_id'],
            'title' => $input['title'] ?? $car['title'],
            'description' => $input['description'] ?? $car['description'],
            'price_per_day' => $input['price_per_day'] ?? $car['price_per_day'],
            'active' => isset($input['active']) ? 1 : $car['active'],
            'image_path' => $input['image_path'] ?? $car['image_path']
        ];

        $result = $this->carModel->update($id, $updateData);

        echo json_encode($result);
    }

    //delete car
    public function delete($id){
        header('Content-Type: application/json');

        //check if user is logged in
        $user = Session::get('user');
        if(!$user){
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        //check if car exists and belongs to user
        $car = $this->carModel->getById($id);
        if(!$car){
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Car not found']);
            return;
        }

        if($car['user_id'] != $user['id']){
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Forbidden']);
            return;
        }

        //delete image if exists
        if($car['image_path'] && file_exists($_SERVER['DOCUMENT_ROOT'] . $car['image_path'])){
            unlink($_SERVER['DOCUMENT_ROOT'] . $car['image_path']);
        }

        $result = $this->carModel->delete($id);

        echo json_encode($result);
    }

    //toggle status
    public function toggleStatus($id){
        header('Content-Type: application/json');

        //check if user is logged in
        $user = Session::get('user');
        if(!$user){
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        //check if car exists and belongs to user
        $car = $this->carModel->getById($id);
        if(!$car){
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Car not found']);
            return;
        }

        if($car['user_id'] != $user['id']){
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Forbidden']);
            return;
        }

        $result = $this->carModel->toggleStatus($id);
        
        echo json_encode($result);
    }

    //helper uploadImage
    public function uploadImage($file, $userId){
        $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/rentacar/public/uploads/cars/";

        //create target dir if not exists
        if(!file_exists($targetDir)){
            mkdir($targetDir, 0755, true);
        }

        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'car_' . $userId . '_' . time() . '.' . $fileExtension;
        $targetFile = $targetDir . $fileName;

        //check file type
        $allowesTyper = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if(!in_array($file['type'], $allowesTyper)){
            return ['status' => 'error', 'message' => 'Invalid file type'];
        }

        //move uploaded file (max 5MG)
        if($file['size'] > 5 * 1024 * 1024){
            return ['status' => 'error', 'message' => 'File size exceeds 5MB'];
        }

        if(move_uploaded_file($file['tmp_name'], $targetFile)){
            return ['status' => 'success', 
            'path' => '/rentacar/public/uploads/cars/' . $fileName];
        }

        return ['status' => 'error', 'message' => 'Failed to upload image'];

    }
}