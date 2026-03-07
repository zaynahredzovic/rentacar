<?php
namespace App\Controllers;

use App\Models\Category;
use App\Core\Session;

class CategoryController{
    private $categoryModel;

    public function __construct(){
        $this->categoryModel = new Category();
    }

    public function list(){
        header('Content-Type: application/json');

        if (!Session::get('user')) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        $categories = $this->categoryModel->getAll();

        echo json_encode([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    public function listWithCount(){
        header('Content-Type: application/json');

        if (!Session::get('user')) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        $categories = $this->categoryModel->getWithCarCount();

        echo json_encode([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    public function create(){
        header('Content-Type: application/json');

        if (!Session::get('user')) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if(!$input){
            $input = $_POST;
        }

        if(!isset($input['name']) || empty(trim($input['name']))){
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Name is required']);
            return;
        }

        $name = trim($input['name']);

        $result = $this->categoryModel->create($name);

        echo json_encode($result);
    }

    public function update($id){
        header('Content-Type: application/json');

        if (!Session::get('user')) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if(!$input){
            $input = $_POST;
        }

        if(!isset($input['name']) || empty(trim($input['name']))){
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Name is required']);
            return;
        }

        $name = trim($input['name']);
        $result = $this->categoryModel->update($id, $name);

        echo json_encode($result);
    }

    public function delete($id){
        header('Content-Type: application/json');

        if (!Session::get('user')) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        $result = $this->categoryModel->delete($id);

        echo json_encode($result);
    }
}