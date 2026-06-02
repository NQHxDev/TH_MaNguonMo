<?php

require_once 'App/Config/Database.php';
require_once 'App/Config/Redis.php';
require_once 'App/Config/VNPay.php';
require_once 'App/Models/ProductModel.php';

class CartController {

   private PDO $db;

   private RedisClient $redis;

   private string $cartKey;

   public function __construct() {
      if (session_status() === PHP_SESSION_NONE) {
         session_start();
      }
      $this->db = Database::getConnection();
      $this->redis = new RedisClient();
      $username = SessionHelper::getUsername();
      $this->cartKey = $username ? 'cart:user:' . $username : 'cart:guest:' . session_id();
   }

   public function index() {
      if (!SessionHelper::isLoggedIn()) {
         header("Location: /account/login");
         exit();
      }
      $cart = $this->getCart();
      include 'App/Views/Cart/List.php';
   }

   public function add(int $id) {
      if (!SessionHelper::isLoggedIn()) {
         $_SESSION['error'] = "Bạn cần đăng nhập!";
         $referer = $_SERVER['HTTP_REFERER'] ?? '/';
         header("Location: " . $referer);
         exit();
      }

      $product = ProductModel::getById($this->db, $id);
      if (!$product) {
         $_SESSION['error'] = "Không tìm thấy sản phẩm!";
         header("Location: /");
         exit();
      }

      $cart = $this->getCart();

      if (isset($cart[$id])) {
         $cart[$id]['quantity']++;
      } else {
         $cart[$id] = [
            'name'     => $product->getName(),
            'price'    => $product->getPrice(),
            'image'    => $product->getImage(),
            'quantity' => 1
         ];
      }

      $this->saveCart($cart);

      $_SESSION['success'] = "Đã thêm \"" . htmlspecialchars($product->getName()) . "\" vào giỏ hàng thành công!";
      
      $referer = $_SERVER['HTTP_REFERER'] ?? '/';
      header("Location: " . $referer);
      exit();
   }

   public function buyNow(int $id) {
      if (!SessionHelper::isLoggedIn()) {
         $_SESSION['error'] = "Bạn cần đăng nhập để mua sản phẩm!";
         $referer = $_SERVER['HTTP_REFERER'] ?? '/';
         header("Location: " . $referer);
         exit();
      }

      $product = ProductModel::getById($this->db, $id);
      if (!$product) {
         $_SESSION['error'] = "Không tìm thấy sản phẩm!";
         header("Location: /");
         exit();
      }

      $cart = $this->getCart();

      if (!isset($cart[$id])) {
         $cart[$id] = [
            'name'     => $product->getName(),
            'price'    => $product->getPrice(),
            'image'    => $product->getImage(),
            'quantity' => 1
         ];
         $this->saveCart($cart);
      }

      header("Location: /cart/checkout");
      exit();
   }

   public function remove(int $id) {
      if (!SessionHelper::isLoggedIn()) {
         $_SESSION['error'] = "Bạn cần đăng nhập!";
         header("Location: /account/login");
         exit();
      }
      $cart = $this->getCart();
      if (isset($cart[$id])) {
         unset($cart[$id]);
         $this->saveCart($cart);
         $_SESSION['success'] = "Đã xóa sản phẩm khỏi giỏ hàng.";
      }
      header("Location: /cart");
      exit();
   }

   public function update() {
      if (!SessionHelper::isLoggedIn()) {
         $_SESSION['error'] = "Bạn cần đăng nhập!";
         header("Location: /account/login");
         exit();
      }
      if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantities'])) {
         $cart = $this->getCart();
         foreach ($_POST['quantities'] as $id => $qty) {
            $qty = (int)$qty;
            if ($qty <= 0) {
               unset($cart[$id]);
            } else if (isset($cart[$id])) {
               $cart[$id]['quantity'] = $qty;
            }
         }
         $this->saveCart($cart);
         $_SESSION['success'] = "Cập nhật giỏ hàng thành công!";
      }
      header("Location: /cart");
      exit();
   }

   public function checkout() {
      if (!SessionHelper::isLoggedIn()) {
         $_SESSION['error'] = "Bạn cần đăng nhập để thanh toán!";
         header("Location: /account/login");
         exit();
      }
      $cart = $this->getCart();
      if (empty($cart)) {
         $_SESSION['error'] = "Giỏ hàng trống. Vui lòng thêm sản phẩm trước khi thanh toán!";
         header("Location: /");
         exit();
      }
      include 'App/Views/Cart/Checkout.php';
   }

   public function placeOrder() {
      if (!SessionHelper::isLoggedIn()) {
         $_SESSION['error'] = "Bạn cần đăng nhập để đặt hàng!";
         header("Location: /account/login");
         exit();
      }

      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
         $name = $_POST['name'] ?? '';
         $phone = $_POST['phone'] ?? '';
         $address = $_POST['address'] ?? '';
         $payment_method = $_POST['payment_method'] ?? 'cod';

         if (empty($name) || empty($phone) || empty($address)) {
            $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin giao hàng!";
            header("Location: /cart/checkout");
            exit();
         }

         $cart = $this->getCart();
         if (empty($cart)) {
            $_SESSION['error'] = "Giỏ hàng của bạn đang trống.";
            header("Location: /");
            exit();
         }

         $total_usd = 0;
         foreach ($cart as $item) {
            $total_usd += $item['price'] * $item['quantity'];
         }

         if ($payment_method === 'vnpay') {
            $exchange_rate = VNPayConfig::$config['exchange_rate'];
            $total_vnd = (int)($total_usd * $exchange_rate);

            $_SESSION['pending_order'] = [
               'username' => SessionHelper::getUsername(),
               'name' => $name,
               'phone' => $phone,
               'address' => $address,
               'amount_usd' => $total_usd,
               'amount_vnd' => $total_vnd,
               'cart_items' => $cart
            ];

            $vnp_TmnCode = VNPayConfig::$config['vnp_TmnCode'];
            $vnp_HashSecret = VNPayConfig::$config['vnp_HashSecret'];
            $vnp_Url = VNPayConfig::$config['vnp_Url'];
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $vnp_ReturnUrl = $protocol . $_SERVER['HTTP_HOST'] . '/cart/vnpayReturn';

            $vnp_TxnRef = date("YmdHis") . "_" . uniqid();
            $vnp_OrderInfo = "Thanh toan don hang #" . $vnp_TxnRef;
            $vnp_OrderType = "billpayment";
            $vnp_Amount = $total_vnd * 100;
            $vnp_Locale = "vn";
            $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

            $vnp_Params = [
               "vnp_Version" => "2.1.0",
               "vnp_Command" => "pay",
               "vnp_TmnCode" => $vnp_TmnCode,
               "vnp_Amount" => $vnp_Amount,
               "vnp_CreateDate" => date('YmdHis'),
               "vnp_CurrCode" => "VND",
               "vnp_IpAddr" => $vnp_IpAddr,
               "vnp_Locale" => $vnp_Locale,
               "vnp_OrderInfo" => $vnp_OrderInfo,
               "vnp_OrderType" => $vnp_OrderType,
               "vnp_ReturnUrl" => $vnp_ReturnUrl,
               "vnp_TxnRef" => $vnp_TxnRef
            ];

            ksort($vnp_Params);
            $query = "";
            $i = 0;
            $hashdata = "";
            foreach ($vnp_Params as $key => $value) {
               if ($i == 1) {
                  $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
               } else {
                  $hashdata .= urlencode($key) . "=" . urlencode($value);
                  $i = 1;
               }
               $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            $vnp_Url = $vnp_Url . "?" . $query;
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

            header("Location: " . $vnp_Url);
            exit();
          } else {
             $_SESSION['error'] = "Phương thức thanh toán không hợp lệ hoặc COD đã bị khóa!";
             header("Location: /cart/checkout");
             exit();
          }
      }
      header("Location: /");
      exit();
   }

   public function vnpayReturn() {
      $vnp_SecureHash = $_GET['vnp_SecureHash'] ?? '';
      $vnp_Params = [];
      foreach ($_GET as $key => $value) {
         if (substr($key, 0, 4) === 'vnp_') {
            if ($key !== 'vnp_SecureHash' && $key !== 'vnp_SecureHashType') {
               $vnp_Params[$key] = $value;
            }
         }
      }

      ksort($vnp_Params);
      $i = 0;
      $hashdata = "";
      foreach ($vnp_Params as $key => $value) {
         if ($i == 1) {
            $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
         } else {
            $hashdata .= urlencode($key) . "=" . urlencode($value);
            $i = 1;
         }
      }

      $vnp_HashSecret = VNPayConfig::$config['vnp_HashSecret'];
      $secureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);

      $isValid = ($secureHash === $vnp_SecureHash);
      $responseCode = $_GET['vnp_ResponseCode'] ?? '';

      if ($isValid && $responseCode === '00') {
         $this->clearCart();
         $pending = $_SESSION['pending_order'] ?? [];
         unset($_SESSION['pending_order']);

         try {
            $this->db->beginTransaction();

            $stmtOrder = $this->db->prepare(
               "INSERT INTO orders (vnpay_txn_ref, customer_name, customer_phone, customer_address, total_usd, total_vnd, bank_code, payment_status, username) VALUES (:txn_ref, :name, :phone, :address, :total_usd, :total_vnd, :bank_code, 'paid', :username)"
            );
            $txnRef = $_GET['vnp_TxnRef'] ?? '';
            $bankCode = $_GET['vnp_BankCode'] ?? '';
            $totalUsd = $pending['amount_usd'] ?? 0;
            $totalVnd = $pending['amount_vnd'] ?? 0;
            $custName = $pending['name'] ?? '';
            $custPhone = $pending['phone'] ?? '';
            $custAddress = $pending['address'] ?? '';
            $usernameVal = $pending['username'] ?? null;
            $stmtOrder->bindParam(':txn_ref', $txnRef);
            $stmtOrder->bindParam(':name', $custName);
            $stmtOrder->bindParam(':phone', $custPhone);
            $stmtOrder->bindParam(':address', $custAddress);
            $stmtOrder->bindParam(':total_usd', $totalUsd);
            $stmtOrder->bindParam(':total_vnd', $totalVnd);
            $stmtOrder->bindParam(':bank_code', $bankCode);
            $stmtOrder->bindParam(':username', $usernameVal);
            $stmtOrder->execute();

            $orderId = (int)$this->db->lastInsertId();

            $cartItems = $pending['cart_items'] ?? [];
            $stmtDetail = $this->db->prepare(
               "INSERT INTO order_details (order_id, product_id, product_name, quantity, price) VALUES (:order_id, :product_id, :product_name, :quantity, :price)"
            );
            foreach ($cartItems as $productId => $item) {
               $prodName = $item['name'] ?? '';
               $qty = $item['quantity'];
               $price = $item['price'];
               $stmtDetail->bindParam(':order_id', $orderId);
               $stmtDetail->bindParam(':product_id', $productId);
               $stmtDetail->bindParam(':product_name', $prodName);
               $stmtDetail->bindParam(':quantity', $qty);
               $stmtDetail->bindParam(':price', $price);
               $stmtDetail->execute();
            }

            $this->db->commit();
         } catch (Exception $e) {
            $this->db->rollBack();
         }

         $orderData = [
            'success' => true,
            'txnRef' => $_GET['vnp_TxnRef'] ?? '',
            'amount' => $_GET['vnp_Amount'] ?? 0,
            'bankCode' => $_GET['vnp_BankCode'] ?? '',
            'payDate' => $_GET['vnp_PayDate'] ?? '',
            'recipient' => $pending
         ];

         include 'App/Views/Cart/VNPayResult.php';
         
         exit();
      } else {
         $orderData = [
            'success' => false,
            'responseCode' => $responseCode,
            'txnRef' => $_GET['vnp_TxnRef'] ?? '',
            'amount' => $_GET['vnp_Amount'] ?? 0
         ];
         include 'App/Views/Cart/VNPayResult.php';
         exit();
      }
   }

   public function orders() {
      if (!SessionHelper::isLoggedIn()) {
         $_SESSION['error'] = "Bạn cần đăng nhập để xem lịch sử đơn hàng!";
         header("Location: /account/login");
         exit();
      }

      $username = SessionHelper::getUsername();
      $isAdmin = SessionHelper::isAdmin();

      if ($isAdmin) {
         $stmt = $this->db->prepare("SELECT * FROM orders ORDER BY created_at DESC");
         $stmt->execute();
      } else {
         $stmt = $this->db->prepare("SELECT * FROM orders WHERE username = :username ORDER BY created_at DESC");
         $stmt->execute(['username' => $username]);
      }
      $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

      foreach ($orders as &$order) {
         $stmtDetail = $this->db->prepare(
            "SELECT od.*, p.image FROM order_details od LEFT JOIN product p ON od.product_id = p.id WHERE od.order_id = :order_id"
         );
         $stmtDetail->execute(['order_id' => $order['id']]);
         $order['details'] = $stmtDetail->fetchAll(PDO::FETCH_ASSOC);
      }

      include 'App/Views/Cart/Orders.php';
   }

   private function getCart(): array {
      try {
         $cartJson = $this->redis->get($this->cartKey);
         return $cartJson ? json_decode($cartJson, true) : [];
      } catch (Exception $e) {
         $_SESSION['error'] = "Lỗi kết nối Redis. Không thể thao tác giỏ hàng: " . $e->getMessage();
         return [];
      }
   }

   private function saveCart(array $cart) {
      try {
         $this->redis->setex($this->cartKey, 604800, json_encode($cart));
      } catch (Exception $e) {
         $_SESSION['error'] = "Không thể lưu giỏ hàng vào Redis: " . $e->getMessage();
      }
   }

   private function clearCart() {
      try {
         $this->redis->del($this->cartKey);
      } catch (Exception $e) {
         $_SESSION['error'] = "Không thể xóa giỏ hàng trong Redis: " . $e->getMessage();
      }
   }

}

?>
