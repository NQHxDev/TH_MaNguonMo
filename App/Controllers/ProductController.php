<?php

require_once 'App/Config/Database.php';
require_once 'App/Models/ProductModel.php';
require_once 'App/Models/CategoryModel.php';
require_once 'App/Utils/AuthMiddleware.php';

class ProductController {

   private PDO $db;

   public function __construct() {
      $this->db = Database::getConnection();
   }

   /**
    * GET /api/products
    */
   public function list() {
      $products = ProductModel::getAll($this->db);
      $serializedProducts = [];
      
      foreach ($products as $p) {
         $serializedProducts[] = [
            'id'            => $p->getID(),
            'name'          => $p->getName(),
            'description'   => $p->getDescription(),
            'price'         => $p->getPrice(),
            'image'         => $p->getImage(),
            'category_id'   => $p->getCategoryID(),
            'category_name' => $p->getCategoryName(),
            'sub_images'    => $p->getSubImages()
         ];
      }

      echo json_encode([
         'success'  => true,
         'products' => $serializedProducts
      ], JSON_UNESCAPED_UNICODE);
      exit();
   }

   /**
    * GET /api/products/{id}
    */
   public function detail(int $id) {
      $product = ProductModel::getById($this->db, $id);
      if (!$product) {
         http_response_code(404);
         echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy sản phẩm!'
         ], JSON_UNESCAPED_UNICODE);
         exit();
      }

      echo json_encode([
         'success' => true,
         'product' => [
            'id'            => $product->getID(),
            'name'          => $product->getName(),
            'description'   => $product->getDescription(),
            'price'         => $product->getPrice(),
            'image'         => $product->getImage(),
            'category_id'   => $product->getCategoryID(),
            'category_name' => $product->getCategoryName(),
            'sub_images'    => $product->getSubImages()
         ]
      ], JSON_UNESCAPED_UNICODE);
      exit();
   }

   /**
    * GET /api/categories
    */
   public function categories() {
      $categories = CategoryModel::getAll($this->db);
      $serializedCategories = [];
      
      foreach ($categories as $cat) {
         $serializedCategories[] = [
            'id'          => $cat->getId(),
            'name'        => $cat->getName(),
            'description' => $cat->getDescription()
         ];
      }

      echo json_encode([
         'success'    => true,
         'categories' => $serializedCategories
      ], JSON_UNESCAPED_UNICODE);
      exit();
   }

   /**
    * POST /api/products (Requires Admin)
    */
   public function create() {
      AuthMiddleware::requireAdmin();

      $errors = [];
      $name = $_POST['name'] ?? '';
      $description = $_POST['description'] ?? '';
      $price = $_POST['price'] ?? '';
      $category_id = $_POST['category_id'] ?? null;

      if (empty($name)) {
         $errors[] = "Tên sản phẩm không được để trống";
      } else if (strlen($name) < 10 || strlen($name) > 100){
         $errors[] = "Tên sản phẩm phải từ 10 đến 100 ký tự";
      }

      if (empty($price)) {
         $errors[] = "Giá sản phẩm không được để trống";
      } else if (!is_numeric($price)) {
         $errors[] = "Giá sản phẩm phải là số";
      } else if ($price <= 0) {
         $errors[] = "Giá sản phẩm phải lớn hơn 0";
      }

      $mainImagePath = null;
      if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
         $mainImagePath = $this->handleFileUpload($_FILES['image']);
         if (!$mainImagePath) {
            $errors[] = "Lỗi tải ảnh chính (vui lòng chọn ảnh < 5MB và định dạng JPG/PNG/WEBP/GIF).";
         }
      }

      $subImagePaths = [];
      if (isset($_FILES['sub_images']) && !empty($_FILES['sub_images']['name'][0])) {
         $subImagePaths = $this->handleMultipleUploads($_FILES['sub_images']);
      }

      if (!empty($errors)) {
         http_response_code(400);
         echo json_encode([
            'success' => false,
            'errors'  => $errors
         ], JSON_UNESCAPED_UNICODE);
         exit();
      }

      $product = new ProductModel(
         null, 
         $name, 
         $description, 
         (float)$price, 
         $mainImagePath, 
         $category_id ? (int)$category_id : null,
         null,
         $subImagePaths
      );
      
      if ($product->create($this->db)) {
         http_response_code(201);
         echo json_encode([
            'success'    => true,
            'message'    => "Thêm sản phẩm thành công!",
            'product_id' => $product->getID()
         ], JSON_UNESCAPED_UNICODE);
         exit();
      } else {
         http_response_code(500);
         echo json_encode([
            'success' => false,
            'message' => "Lỗi khi thêm sản phẩm vào cơ sở dữ liệu."
         ], JSON_UNESCAPED_UNICODE);
         exit();
      }
   }

   /**
    * PUT /api/products/{id} (Requires Admin)
    */
   public function edit(int $id) {
      AuthMiddleware::requireAdmin();

      $product = ProductModel::getById($this->db, $id);
      if (!$product) {
         http_response_code(404);
         echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy sản phẩm!'
         ], JSON_UNESCAPED_UNICODE);
         exit();
      }

      $errors = [];
      $name = $_POST['name'] ?? '';
      $description = $_POST['description'] ?? '';
      $price = $_POST['price'] ?? '';
      $category_id = $_POST['category_id'] ?? null;

      if (empty($name)) {
         $errors[] = "Tên sản phẩm không được để trống";
      } else if (strlen($name) < 10 || strlen($name) > 100){
         $errors[] = "Tên sản phẩm phải từ 10 đến 100 ký tự";
      }

      if (empty($price)) {
         $errors[] = "Giá sản phẩm không được để trống";
      } else if (!is_numeric($price)) {
         $errors[] = "Giá sản phẩm phải là số";
      } else if ($price <= 0) {
         $errors[] = "Giá sản phẩm phải lớn hơn 0";
      }

      if (!empty($errors)) {
         http_response_code(400);
         echo json_encode([
            'success' => false,
            'errors'  => $errors
         ], JSON_UNESCAPED_UNICODE);
         exit();
      }

      $product->setName($name);
      $product->setDescription($description);
      $product->setPrice((float)$price);
      $product->setCategoryID($category_id ? (int)$category_id : null);

      if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
         $newMainImage = $this->handleFileUpload($_FILES['image']);
         if ($newMainImage) {
            $oldImage = $product->getImage();
            if ($oldImage) {
               $localOldImage = ltrim($oldImage, '/');
               if (file_exists($localOldImage)) {
                  @unlink($localOldImage);
               }
            }
            $product->setImage($newMainImage);
         } else {
            $errors[] = "Lỗi khi tải lên ảnh chính mới.";
         }
      }

      if (isset($_FILES['sub_images']) && !empty($_FILES['sub_images']['name'][0])) {
         $newSubImages = $this->handleMultipleUploads($_FILES['sub_images']);
         if (!empty($newSubImages)) {
            $product->setSubImages($newSubImages);
         }
      }

      if (!empty($errors)) {
         http_response_code(400);
         echo json_encode([
            'success' => false,
            'errors'  => $errors
         ], JSON_UNESCAPED_UNICODE);
         exit();
      }

      if ($product->update($this->db)) {
         echo json_encode([
            'success' => true,
            'message' => "Cập nhật sản phẩm thành công!"
         ], JSON_UNESCAPED_UNICODE);
         exit();
      } else {
         http_response_code(500);
         echo json_encode([
            'success' => false,
            'message' => "Lỗi khi cập nhật sản phẩm vào cơ sở dữ liệu."
         ], JSON_UNESCAPED_UNICODE);
         exit();
      }
   }

   /**
    * DELETE /api/products/{id} (Requires Admin)
    */
   public function delete(int $id) {
      AuthMiddleware::requireAdmin();

      if (ProductModel::delete($this->db, $id)) {
         echo json_encode([
            'success' => true,
            'message' => "Xóa sản phẩm thành công!"
         ], JSON_UNESCAPED_UNICODE);
      } else {
         http_response_code(500);
         echo json_encode([
            'success' => false,
            'message' => "Lỗi khi xóa sản phẩm."
         ], JSON_UNESCAPED_UNICODE);
      }
      exit();
   }

   private function handleFileUpload(array $file): ?string {
      if (!isset($file['error']) || is_array($file['error'])) {
         return null;
      }

      if ($file['error'] !== UPLOAD_ERR_OK) {
         return null;
      }

      if ($file['size'] > 5000000) {
         return null;
      }

      $finfo = new finfo(FILEINFO_MIME_TYPE);
      $mime = $finfo->file($file['tmp_name']);
      $allowedMimes = [
         'image/jpeg' => 'jpg',
         'image/png'  => 'png',
         'image/gif'  => 'gif',
         'image/webp' => 'webp'
      ];

      if (!isset($allowedMimes[$mime])) {
         return null;
      }

      $uploadDir = 'uploads';
      if (!is_dir($uploadDir)) {
         mkdir($uploadDir, 0755, true);
      }

      $ext = $allowedMimes[$mime];
      $filename = sprintf('%s.%s', sha1_file($file['tmp_name']) . '_' . uniqid(), $ext);
      $destPath = $uploadDir . '/' . $filename;

      if (move_uploaded_file($file['tmp_name'], $destPath)) {
         return '/' . $destPath;
      }

      return null;
   }

   private function handleMultipleUploads(array $files): array {
      $uploadedPaths = [];
      if (!isset($files['name']) || !is_array($files['name'])) {
         return [];
      }

      $fileCount = count($files['name']);
      for ($i = 0; $i < $fileCount; $i++) {
         if ($files['error'][$i] !== UPLOAD_ERR_OK) {
            continue;
         }

         $singleFile = [
            'name'     => $files['name'][$i],
            'type'     => $files['type'][$i],
            'tmp_name' => $files['tmp_name'][$i],
            'error'    => $files['error'][$i],
            'size'     => $files['size'][$i],
         ];

         $path = $this->handleFileUpload($singleFile);
         if ($path) {
            $uploadedPaths[] = $path;
         }
      }
      return $uploadedPaths;
   }
}
?>
