<?php

require_once 'App/Config/Database.php';
require_once 'App/Config/Redis.php';
require_once 'App/Models/ProductModel.php';

class DefaultController {

   private PDO $db;

   public function __construct() {
      if (session_status() === PHP_SESSION_NONE) {
         session_start();
      }
      $this->db = Database::getConnection();
   }

   public function index(){
      $products = ProductModel::getAll($this->db);

      $cartCount = 0;
      try {
         $redis = new RedisClient();
         $cartJson = $redis->get('cart:' . session_id());
         $cart = $cartJson ? json_decode($cartJson, true) : [];
         if (is_array($cart)) {
            foreach ($cart as $item) {
               $cartCount += $item['quantity'];
            }
         }
      } catch (Exception $e) {
         error_log($e->getMessage());
      }

      $data = [
         'products' => $products,
         'cartCount' => $cartCount
      ];
      extract($data);
      include 'App/Views/Home.php';
   }
}
?>
