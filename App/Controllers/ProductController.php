<?php

require_once 'App/Config/Database.php';
require_once 'App/Models/ProductModel.php';
require_once 'App/Models/CategoryModel.php';

class ProductController {

   private PDO $db;

   public function __construct() {
      if (session_status() === PHP_SESSION_NONE) {
         session_start();
      }
      if (!SessionHelper::isAdmin()) {
         $_SESSION['error'] = "Bạn không có quyền truy cập!";
         header("Location: /");
         exit();
      }
      $this->db = Database::getConnection();
   }

   public function index() {
      $this->list();
   }

   public function list(){
      $products = ProductModel::getAll($this->db);
      $data = ['products' => $products];
      extract($data);
      include 'App/Views/Product/List.php';
   }

   public function create() {
      $errors = [];
      $categories = CategoryModel::getAll($this->db);

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

         if (empty($errors)) {
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
               $_SESSION['success'] = "Thêm sản phẩm thành công!";
               header("Location: /product/list");
               exit();
            } else {
               $errors[] = "Lỗi khi thêm sản phẩm vào cơ sở dữ liệu.";
            }
         }
      }

      include 'App/Views/Product/Create.php';
   }

   public function edit(int $id) {
      $product = ProductModel::getById($this->db, $id);
      if (!$product) {
         die('Không tìm thấy sản phẩm!');
      }

      $categories = CategoryModel::getAll($this->db);
      $errors = [];

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

         if (empty($errors)) {
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

            if (empty($errors)) {
               if ($product->update($this->db)) {
                  $_SESSION['success'] = "Cập nhật sản phẩm thành công!";
                  header("Location: /product/list");
                  exit();
               } else {
                  $errors[] = "Lỗi khi cập nhật sản phẩm vào cơ sở dữ liệu.";
               }
            }
         }
      }

      include 'App/Views/Product/Edit.php';
   }

   public function delete(int $id) {
      if (ProductModel::delete($this->db, $id)) {
         $_SESSION['success'] = "Xóa sản phẩm thành công!";
      } else {
         $_SESSION['error'] = "Lỗi khi xóa sản phẩm.";
      }

      header("Location: /product/list");
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
