<?php

class ProductModel {

   private ?int $ID;
   private string $Name;
   private ?string $Description;
   private float $Price;
   private ?string $Image;
   private ?int $CategoryID;
   private ?string $CategoryName;
   private array $SubImages = [];

   public function __construct(
       ?int $id, 
       string $name, 
       ?string $description, 
       float $price, 
       ?string $image = null, 
       ?int $categoryID = null, 
       ?string $categoryName = null,
       array $subImages = []
   ) {
      $this->ID = $id;
      $this->Name = $name;
      $this->Description = $description;
      $this->Price = $price;
      $this->Image = $image;
      $this->CategoryID = $categoryID;
      $this->CategoryName = $categoryName;
      $this->SubImages = $subImages;
   }

   public function getID() {
      return $this->ID;
   }

   public function getName() {
      return $this->Name;
   }

   public function getDescription() {
      return $this->Description;
   }

   public function getPrice() {
      return $this->Price;
   }

   public function getImage() {
      return $this->Image;
   }

   public function getCategoryID() {
      return $this->CategoryID;
   }

   public function getCategoryName() {
      return $this->CategoryName;
   }

   public function getSubImages(): array {
      return $this->SubImages;
   }

   public function setID(?int $id) {
      $this->ID = $id;
   }

   public function setName(string $name) {
      $this->Name = $name;
   }

   public function setDescription(?string $description) {
      $this->Description = $description;
   }

   public function setPrice(float $price) {
      $this->Price = $price;
   }

   public function setImage(?string $image) {
      $this->Image = $image;
   }

   public function setCategoryID(?int $categoryID) {
      $this->CategoryID = $categoryID;
   }

   public function setCategoryName(?string $categoryName) {
      $this->CategoryName = $categoryName;
   }

   public function setSubImages(array $subImages) {
      $this->SubImages = $subImages;
   }

   public static function getAll(PDO $db): array {
      $sql = "SELECT p.*, c.name as category_name 
              FROM product p 
              LEFT JOIN category c ON p.category_id = c.id 
              ORDER BY p.id DESC";
      $stmt = $db->query($sql);
      $products = [];
      $productIds = [];
      
      while ($row = $stmt->fetch()) {
         $id = (int)$row['id'];
         $products[$id] = new ProductModel(
            $id,
            $row['name'],
            $row['description'],
            (float)$row['price'],
            $row['image'],
            $row['category_id'] !== null ? (int)$row['category_id'] : null,
            $row['category_name'],
            []
         );
         $productIds[] = $id;
      }

      if (!empty($productIds)) {
         $inQuery = implode(',', $productIds);
         $imgSql = "SELECT * FROM product_image WHERE product_id IN ($inQuery)";
         $imgStmt = $db->query($imgSql);
         while ($imgRow = $imgStmt->fetch()) {
            $prodId = (int)$imgRow['product_id'];
            if (isset($products[$prodId])) {
               $subImgs = $products[$prodId]->getSubImages();
               $subImgs[] = $imgRow['image_path'];
               $products[$prodId]->setSubImages($subImgs);
            }
         }
      }
      return array_values($products);
   }

   public static function getById(PDO $db, int $id): ?ProductModel {
      $sql = "SELECT p.*, c.name as category_name 
              FROM product p 
              LEFT JOIN category c ON p.category_id = c.id 
              WHERE p.id = ?";
      $stmt = $db->prepare($sql);
      $stmt->execute([$id]);
      $row = $stmt->fetch();
      if ($row) {
         $imgStmt = $db->prepare("SELECT image_path FROM product_image WHERE product_id = ?");
         $imgStmt->execute([$id]);
         $subImages = $imgStmt->fetchAll(PDO::FETCH_COLUMN);

         return new ProductModel(
            (int)$row['id'],
            $row['name'],
            $row['description'],
            (float)$row['price'],
            $row['image'],
            $row['category_id'] !== null ? (int)$row['category_id'] : null,
            $row['category_name'],
            $subImages
         );
      }
      return null;
   }

   public function create(PDO $db): bool {
      $db->beginTransaction();
      try {
         $sql = "INSERT INTO product (name, description, price, image, category_id) 
                 VALUES (:name, :description, :price, :image, :category_id)";
         $stmt = $db->prepare($sql);
         $stmt->execute([
            'name'        => $this->Name,
            'description' => $this->Description,
            'price'       => $this->Price,
            'image'       => $this->Image,
            'category_id' => $this->CategoryID
         ]);
         
         $productId = (int)$db->lastInsertId();
         $this->ID = $productId;

         if (!empty($this->SubImages)) {
            $insertImg = $db->prepare("INSERT INTO product_image (product_id, image_path) VALUES (?, ?)");
            foreach ($this->SubImages as $subImg) {
               $insertImg->execute([$productId, $subImg]);
            }
         }

         $db->commit();
         return true;
      } catch (Exception $e) {
         $db->rollBack();
         return false;
      }
   }

   public function update(PDO $db): bool {
      $db->beginTransaction();
      try {
         $sql = "UPDATE product 
                 SET name = :name, description = :description, price = :price, image = :image, category_id = :category_id 
                 WHERE id = :id";
         $stmt = $db->prepare($sql);
         $stmt->execute([
            'name'        => $this->Name,
            'description' => $this->Description,
            'price'       => $this->Price,
            'image'       => $this->Image,
            'category_id' => $this->CategoryID,
            'id'          => $this->ID
         ]);

         // Luôn đồng bộ danh sách ảnh phụ trong cơ sở dữ liệu
         $delStmt = $db->prepare("DELETE FROM product_image WHERE product_id = ?");
         $delStmt->execute([$this->ID]);

         if ($this->SubImages !== null && !empty($this->SubImages)) {
            $insertImg = $db->prepare("INSERT INTO product_image (product_id, image_path) VALUES (?, ?)");
            foreach ($this->SubImages as $subImg) {
               $insertImg->execute([$this->ID, $subImg]);
            }
         }

         $db->commit();
         return true;
      } catch (Exception $e) {
         $db->rollBack();
         return false;
      }
   }

   public static function delete(PDO $db, int $id): bool {
      $product = self::getById($db, $id);
      if ($product) {
         if ($product->getImage()) {
            $localImg = ltrim($product->getImage(), '/');
            if (file_exists($localImg)) {
               @unlink($localImg);
            }
         }
         foreach ($product->getSubImages() as $subImg) {
            $localImg = ltrim($subImg, '/');
            if (file_exists($localImg)) {
               @unlink($localImg);
            }
         }
      }

      $stmt = $db->prepare("DELETE FROM product WHERE id = ?");
      return $stmt->execute([$id]);
   }
}
?>
