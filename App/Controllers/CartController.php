<?php

require_once 'App/Config/Database.php';
require_once 'App/Config/Redis.php';
require_once 'App/Config/VNPay.php';
require_once 'App/Config/AppConfig.php';
require_once 'App/Models/ProductModel.php';
require_once 'App/Utils/AuthMiddleware.php';
require_once 'App/Utils/TokenHelper.php';

class CartController {

   private PDO $db;
   private RedisClient $redis;
   private string $cartKey;
   private ?string $username = null;

   public function __construct() {
      $this->db = Database::getConnection();
      $this->redis = new RedisClient();
      
      // Xác định thông tin giỏ hàng qua Token hoặc X-Guest-Id (Client gửi lên)
      $user = AuthMiddleware::getUserFromHeaders();
      if ($user) {
         $this->username = $user['username'];
         $this->cartKey = 'cart:user:' . $this->username;
      } else {
         $guestId = null;
         if (function_exists('getallheaders')) {
            $headers = getallheaders();
            foreach ($headers as $key => $value) {
               if (strcasecmp($key, 'X-Guest-Id') === 0) {
                  $guestId = $value;
                  break;
               }
            }
         }
         if (!$guestId && isset($_GET['guest_id'])) {
            $guestId = $_GET['guest_id'];
         }
         if (!$guestId && isset($_POST['guest_id'])) {
            $guestId = $_POST['guest_id'];
         }
         // Fallback về địa chỉ IP của Client
         if (!$guestId) {
            $guestId = md5($_SERVER['REMOTE_ADDR']);
         }
         $this->cartKey = 'cart:guest:' . $guestId;
      }
   }

   /**
    * GET /api/cart
    */
   public function index() {
      $cart = $this->getCart();
      echo json_encode([
         'success' => true,
         'cart'    => $cart
      ], JSON_UNESCAPED_UNICODE);
      exit();
   }

   /**
    * POST /api/cart/add
    */
   public function add(int $id) {
      $product = ProductModel::getById($this->db, $id);
      if (!$product) {
         http_response_code(404);
         echo json_encode([
            'success' => false,
            'message' => "Không tìm thấy sản phẩm!"
         ], JSON_UNESCAPED_UNICODE);
         exit();
      }

      $cart = $this->getCart();

      if (isset($cart[$id])) {
         $cart[$id]['quantity']++;
      } else {
         $cart[$id] = [
            'id'       => $product->getID(),
            'name'     => $product->getName(),
            'price'    => $product->getPrice(),
            'image'    => $product->getImage(),
            'quantity' => 1
         ];
      }

      $this->saveCart($cart);

      echo json_encode([
         'success' => true,
         'message' => "Đã thêm \"" . $product->getName() . "\" vào giỏ hàng thành công!"
      ], JSON_UNESCAPED_UNICODE);
      exit();
   }

   /**
    * POST /api/cart/remove
    */
   public function remove(int $id) {
      $cart = $this->getCart();
      if (isset($cart[$id])) {
         unset($cart[$id]);
         $this->saveCart($cart);
         echo json_encode([
            'success' => true,
            'message' => "Đã xóa sản phẩm khỏi giỏ hàng."
         ], JSON_UNESCAPED_UNICODE);
      } else {
         http_response_code(404);
         echo json_encode([
            'success' => false,
            'message' => "Không tìm thấy sản phẩm trong giỏ hàng."
         ], JSON_UNESCAPED_UNICODE);
      }
      exit();
   }

   /**
    * PUT /api/cart/update
    */
   public function update() {
      $quantities = $_POST['quantities'] ?? [];
      if (empty($quantities) && isset($_POST['cart_items'])) {
         // Hỗ trợ truyền mảng items trực tiếp
         $quantities = $_POST['cart_items'];
      }

      if (empty($quantities)) {
         http_response_code(400);
         echo json_encode([
            'success' => false,
            'message' => "Dữ liệu cập nhật giỏ hàng không hợp lệ!"
         ], JSON_UNESCAPED_UNICODE);
         exit();
      }

      $cart = $this->getCart();
      foreach ($quantities as $id => $qty) {
         $qty = (int)$qty;
         if ($qty <= 0) {
            unset($cart[$id]);
         } else if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $qty;
         }
      }
      $this->saveCart($cart);

      echo json_encode([
         'success' => true,
         'message' => "Cập nhật giỏ hàng thành công!",
         'cart'    => $cart
      ], JSON_UNESCAPED_UNICODE);
      exit();
   }

   /**
    * POST /api/orders (Đặt hàng)
    */
   public function placeOrder() {
      // Đặt hàng yêu cầu đăng nhập
      $user = AuthMiddleware::requireAuth();

      $name = $_POST['name'] ?? '';
      $phone = $_POST['phone'] ?? '';
      $address = $_POST['address'] ?? '';
      $payment_method = $_POST['payment_method'] ?? 'cod';

      if (empty($name) || empty($phone) || empty($address)) {
         http_response_code(400);
         echo json_encode([
            'success' => false,
            'message' => "Vui lòng nhập đầy đủ thông tin giao hàng!"
         ], JSON_UNESCAPED_UNICODE);
         exit();
      }

      $cart = $this->getCart();
      if (empty($cart)) {
         http_response_code(400);
         echo json_encode([
            'success' => false,
            'message' => "Giỏ hàng của bạn đang trống."
         ], JSON_UNESCAPED_UNICODE);
         exit();
      }

      $total_usd = 0;
      foreach ($cart as $item) {
         $total_usd += $item['price'] * $item['quantity'];
      }

      if ($payment_method === 'vnpay') {
         $exchange_rate = VNPayConfig::$config['exchange_rate'];
         $total_vnd = (int)($total_usd * $exchange_rate);

         $vnp_TxnRef = date("YmdHis") . "_" . uniqid();
         $vnp_OrderInfo = "Thanh toan don hang #" . $vnp_TxnRef;

         $pendingOrder = [
            'username'   => $user['username'],
            'name'       => $name,
            'phone'      => $phone,
            'address'    => $address,
            'amount_usd' => $total_usd,
            'amount_vnd' => $total_vnd,
            'cart_items' => $cart
         ];

         // Lưu pending order vào Redis để xử lý sau khi VNPay callback (thay vì Session)
         try {
            $this->redis->setex('pending_order:' . $vnp_TxnRef, 1800, json_encode($pendingOrder));
         } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
               'success' => false,
               'message' => "Lỗi lưu trữ Redis: " . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
            exit();
         }

         $vnp_TmnCode = VNPayConfig::$config['vnp_TmnCode'];
         $vnp_HashSecret = VNPayConfig::$config['vnp_HashSecret'];
         $vnp_Url = VNPayConfig::$config['vnp_Url'];
         $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
         
         // Callback API VNPay nhận phản hồi thanh toán
         $vnp_ReturnUrl = $protocol . $_SERVER['HTTP_HOST'] . '/api/orders/vnpay-return';

         $vnp_OrderType = "billpayment";
         $vnp_Amount = $total_vnd * 100;
         $vnp_Locale = "vn";
         $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

         $vnp_Params = [
            "vnp_Version"    => "2.1.0",
            "vnp_Command"    => "pay",
            "vnp_TmnCode"    => $vnp_TmnCode,
            "vnp_Amount"     => $vnp_Amount,
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode"   => "VND",
            "vnp_IpAddr"     => $vnp_IpAddr,
            "vnp_Locale"     => $vnp_Locale,
            "vnp_OrderInfo"  => $vnp_OrderInfo,
            "vnp_OrderType"  => $vnp_OrderType,
            "vnp_ReturnUrl"  => $vnp_ReturnUrl,
            "vnp_TxnRef"     => $vnp_TxnRef
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

         echo json_encode([
            'success'    => true,
            'paymentUrl' => $vnp_Url
         ], JSON_UNESCAPED_UNICODE);
         exit();
      } else if ($payment_method === 'cod') {
         http_response_code(400);
         echo json_encode([
            'success' => false,
            'message' => "Phương thức thanh toán COD hiện đang tạm khóa!"
         ], JSON_UNESCAPED_UNICODE);
         exit();
         try {
            $this->db->beginTransaction();

            $stmtOrder = $this->db->prepare(
               "INSERT INTO orders (vnpay_txn_ref, customer_name, customer_phone, customer_address, total_usd, total_vnd, bank_code, payment_status, username) 
                VALUES (:txn_ref, :name, :phone, :address, :total_usd, :total_vnd, 'COD', 'unpaid', :username)"
            );
            $txnRef = 'COD_' . date("YmdHis") . "_" . uniqid();
            $totalVnd = 0; // COD không yêu cầu quy đổi VNPay
            $stmtOrder->execute([
               'txn_ref'  => $txnRef,
               'name'     => $name,
               'phone'    => $phone,
               'address'  => $address,
               'total_usd'=> $total_usd,
               'total_vnd'=> $totalVnd,
               'username' => $user['username']
            ]);

            $orderId = (int)$this->db->lastInsertId();

            $stmtDetail = $this->db->prepare(
               "INSERT INTO order_details (order_id, product_id, product_name, quantity, price) 
                VALUES (:order_id, :product_id, :product_name, :quantity, :price)"
            );
            foreach ($cart as $productId => $item) {
               $stmtDetail->execute([
                  'order_id'     => $orderId,
                  'product_id'   => $productId,
                  'product_name' => $item['name'],
                  'quantity'     => $item['quantity'],
                  'price'        => $item['price']
               ]);
            }

            $this->db->commit();
            $this->clearCart();

            echo json_encode([
               'success'  => true,
               'message'  => "Đặt hàng COD thành công!",
               'order_id' => $orderId
            ], JSON_UNESCAPED_UNICODE);
            exit();
         } catch (Exception $e) {
            $this->db->rollBack();
            http_response_code(500);
            echo json_encode([
               'success' => false,
               'message' => "Lỗi khi đặt hàng: " . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
            exit();
         }
      } else {
         http_response_code(400);
         echo json_encode([
            'success' => false,
            'message' => "Phương thức thanh toán không hợp lệ!"
         ], JSON_UNESCAPED_UNICODE);
         exit();
      }
   }

   /**
    * GET /api/orders/vnpay-return
    */
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
      $txnRef = $_GET['vnp_TxnRef'] ?? '';

      // Lấy thông tin pending order từ Redis
      $pending = [];
      try {
         $pendingJson = $this->redis->get('pending_order:' . $txnRef);
         if ($pendingJson) {
            $pending = json_decode($pendingJson, true);
         }
      } catch (Exception $e) {
         error_log("Failed to fetch pending order from Redis: " . $e->getMessage());
      }

      if ($isValid && $responseCode === '00') {
         // Xóa giỏ hàng của người dùng (vì thanh toán thành công)
         if (!empty($pending['username'])) {
            $this->cartKey = 'cart:user:' . $pending['username'];
            $this->clearCart();
         }

         try {
            $this->db->beginTransaction();

            $stmtOrder = $this->db->prepare(
               "INSERT INTO orders (vnpay_txn_ref, customer_name, customer_phone, customer_address, total_usd, total_vnd, bank_code, payment_status, username) 
                VALUES (:txn_ref, :name, :phone, :address, :total_usd, :total_vnd, :bank_code, 'paid', :username)"
            );
            $bankCode = $_GET['vnp_BankCode'] ?? '';
            $totalUsd = $pending['amount_usd'] ?? 0;
            $totalVnd = $pending['amount_vnd'] ?? 0;
            $custName = $pending['name'] ?? '';
            $custPhone = $pending['phone'] ?? '';
            $custAddress = $pending['address'] ?? '';
            $usernameVal = $pending['username'] ?? null;
            
            $stmtOrder->execute([
               'txn_ref'  => $txnRef,
               'name'     => $custName,
               'phone'    => $custPhone,
               'address'  => $custAddress,
               'total_usd'=> $totalUsd,
               'total_vnd'=> $totalVnd,
               'bank_code'=> $bankCode,
               'username' => $usernameVal
            ]);

            $orderId = (int)$this->db->lastInsertId();

            $cartItems = $pending['cart_items'] ?? [];
            $stmtDetail = $this->db->prepare(
               "INSERT INTO order_details (order_id, product_id, product_name, quantity, price) 
                VALUES (:order_id, :product_id, :product_name, :quantity, :price)"
            );
            foreach ($cartItems as $productId => $item) {
               $stmtDetail->execute([
                  'order_id'     => $orderId,
                  'product_id'   => $productId,
                  'product_name' => $item['name'],
                  'quantity'     => $item['quantity'],
                  'price'        => $item['price']
               ]);
            }

            $this->db->commit();
            
            // Xóa pending order
            try {
               $this->redis->del('pending_order:' . $txnRef);
            } catch (Exception $e) {
               // Ignore
            }
         } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Failed to insert VNPay order details: " . $e->getMessage());
         }

         // Redirect về Frontend kèm trạng thái thành công
         $redirectUrl = AppConfig::$frontendUrl . '/payment-result?success=true&txnRef=' . urlencode($txnRef) . '&amount=' . urlencode($_GET['vnp_Amount'] ?? 0) . '&bankCode=' . urlencode($_GET['vnp_BankCode'] ?? '');
         header("Location: " . $redirectUrl);
         exit();
      } else {
         // Redirect về Frontend kèm trạng thái thất bại
         $redirectUrl = AppConfig::$frontendUrl . '/payment-result?success=false&txnRef=' . urlencode($txnRef) . '&responseCode=' . urlencode($responseCode);
         header("Location: " . $redirectUrl);
         exit();
      }
   }

   /**
    * GET /api/orders
    */
   public function orders() {
      $user = AuthMiddleware::requireAuth();

      $isAdmin = (($user['role'] ?? '') === 'admin');

      if ($isAdmin) {
         $stmt = $this->db->prepare("SELECT * FROM orders ORDER BY created_at DESC");
         $stmt->execute();
      } else {
         $stmt = $this->db->prepare("SELECT * FROM orders WHERE username = :username ORDER BY created_at DESC");
         $stmt->execute(['username' => $user['username']]);
      }
      $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

      foreach ($orders as &$order) {
         $stmtDetail = $this->db->prepare(
            "SELECT od.*, p.image FROM order_details od LEFT JOIN product p ON od.product_id = p.id WHERE od.order_id = :order_id"
         );
         $stmtDetail->execute(['order_id' => $order['id']]);
         $order['details'] = $stmtDetail->fetchAll(PDO::FETCH_ASSOC);
      }

      echo json_encode([
         'success' => true,
         'orders'  => $orders
      ], JSON_UNESCAPED_UNICODE);
      exit();
   }

   private function getCart(): array {
      try {
         $cartJson = $this->redis->get($this->cartKey);
         return $cartJson ? json_decode($cartJson, true) : [];
      } catch (Exception $e) {
         return [];
      }
   }

   private function saveCart(array $cart) {
      try {
         $this->redis->setex($this->cartKey, 604800, json_encode($cart));
      } catch (Exception $e) {
         error_log("Failed to save cart to Redis: " . $e->getMessage());
      }
   }

   private function clearCart() {
      try {
         $this->redis->del($this->cartKey);
      } catch (Exception $e) {
         error_log("Failed to clear cart in Redis: " . $e->getMessage());
      }
   }
}
?>
