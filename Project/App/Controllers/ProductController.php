<?php

require_once 'App/Models/ProductModel.php';

class ProductController {

   private $products = [];

   public function __construct() {
      session_start();

      if (isset($_SESSION['products'])) {
         $this->products = $_SESSION['products'];
      } else {
         $this->products = [];
         $_SESSION['products'] = $this->products;
      }
   }

   public function index() {
      $this->list();
   }

   public function list(){
      $data = ['products' => $this->products];
      extract($data);
      include 'App/Views/Product/List.php';
   }

   public function create() {
      $errors = [];

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
         $name = $_POST['name'] ?? '';
         $description = $_POST['description'] ?? '';
         $price = $_POST['price'] ?? '';

         if (empty($name)) {
            $errors[] = "Name is required";
         } else if (strlen($name) < 10 || strlen($name) > 100){
            $errors[] = "Name must be between 10 and 100 characters";
         }

         if (empty($price)) {
            $errors[] = "Price is required";
         } else if (!is_numeric($price)) {
            $errors[] = "Price must be a number";
         } else if ($price <= 0) {
            $errors[] = "Price must be greater than 0";
         }

         if (empty($errors)) {
            $id = count($this->products) + 1;

            $product = new ProductModel($id, $name, $description, $price);
            $this->products[] = $product;
            $_SESSION['products'] = $this->products;
            $_SESSION['success'] = "Product created successfully!";

            header("Location: /project/product/list");
            exit();
         }
      }

      include 'App/Views/Product/Create.php';
   }

   public function edit(int $id) {
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
         foreach($this->products as $key => $product) {
            if ($product->getID() == $id) {
               $this->products[$key]->setName($_POST['name']);
               $this->products[$key]->setDescription($_POST['description']);
               $this->products[$key]->setPrice($_POST['price']);
               break;
            }
         }

         $_SESSION['products'] = $this->products;

         header("Location: /project/product/list");
         exit();
      }

      foreach($this->products as $product) {
         if ($product->getID() == $id) {
            include 'App/Views/Product/Edit.php';
            return;
         }
      }

      die('Product Not Found!');
   }

   public function delete(int $id) {
      foreach($this->products as $key => $product) {
         if ($product->getID() == $id) {
            unset($this->products[$key]);
            break;
         }
      }

      $this->products = array_values($this->products);
      $_SESSION['products'] = $this->products;

      header("Location: /project/product/list");
      exit();
   }

}

?>
