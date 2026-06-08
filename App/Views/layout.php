<?php
require_once 'App/Utils/AuthMiddleware.php';
require_once 'App/Models/ProductModel.php';
require_once 'App/Models/CategoryModel.php';
require_once 'App/Config/Database.php';

$db = Database::getConnection();
$currentUser = AuthMiddleware::getUserFromHeaders();
$isAdmin = ($currentUser && isset($currentUser['role']) && $currentUser['role'] === 'admin');

$productsRaw = ProductModel::getAll($db);
$productsJsonData = [];
foreach ($productsRaw as $p) {
   $productsJsonData[] = [
      'id' => $p->getID(),
      'name' => $p->getName(),
      'description' => $p->getDescription(),
      'price' => $p->getPrice(),
      'image' => $p->getImage(),
      'category_id' => $p->getCategoryID(),
      'category_name' => $p->getCategoryName(),
      'sub_images' => $p->getSubImages()
   ];
}

$categoriesRaw = CategoryModel::getAll($db);
$categoriesJsonData = [];
foreach ($categoriesRaw as $c) {
   $categoriesJsonData[] = [
      'id' => $c->getId(),
      'name' => $c->getName(),
      'description' => $c->getDescription()
   ];
}
?>
<!doctype html>
<html lang="vi">
   <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>ZeionStore - Cửa hàng Công nghệ Hiện đại</title>
      <link
         rel="stylesheet"
         href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      />
      <link rel="stylesheet" href="/Project/Public/CSS/variables.css" />
      <link rel="stylesheet" href="/Project/Public/CSS/components.css" />
      <link rel="stylesheet" href="/Project/Public/CSS/layout.css" />
      <link rel="stylesheet" href="/Project/Public/CSS/shop.css" />
      <link rel="stylesheet" href="/Project/Public/CSS/admin.css" />
      <script>
         window.initialProducts = <?php echo json_encode($productsJsonData, JSON_UNESCAPED_UNICODE); ?>;
         window.initialCategories = <?php echo json_encode($categoriesJsonData, JSON_UNESCAPED_UNICODE); ?>;
      </script>
   </head>
   <body>
      <?php include 'Partials/header.php'; ?>

      <main class="container">
         <?php include 'Pages/Shop/home.php'; ?>
         <?php include 'Pages/Auth/login.php'; ?>
         <?php include 'Pages/Auth/register.php'; ?>
         <?php include 'Pages/Shop/cart.php'; ?>
         <?php include 'Pages/Shop/checkout.php'; ?>
         <?php include 'Pages/Order/orders.php'; ?>
         
         <?php if ($isAdmin): ?>
            <?php include 'Pages/Admin/admin.php'; ?>
            <?php include 'Pages/Admin/manager-role.php'; ?>
         <?php endif; ?>

         <?php include 'Pages/Auth/profile.php'; ?>
         <?php include 'Pages/Shop/payment-result.php'; ?>
      </main>

      <?php include 'Partials/cart-drawer.php'; ?>
      <?php include 'Partials/modals.php'; ?>
      <?php include 'Partials/toast.php'; ?>

      <script src="/Project/Public/JS/core.js"></script>
      <script src="/Project/Public/JS/auth.js"></script>
      <script src="/Project/Public/JS/shop.js"></script>
      <script src="/Project/Public/JS/cart.js"></script>
      <script src="/Project/Public/JS/admin.js"></script>
   </body>
</html>
