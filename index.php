<?php

require_once 'App/Config/Database.php';
require_once 'App/Config/AppConfig.php';
require_once 'App/Utils/SessionHelper.php';
require_once 'App/Models/CategoryModel.php';
require_once 'App/Models/ProductModel.php';
require_once 'App/Utils/TokenHelper.php';
require_once 'App/Utils/AuthMiddleware.php';

// CORS — origin cụ thể (bắt buộc khi dùng credentials: include cho cookies)
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowedOrigins = [AppConfig::$frontendUrl];
if (in_array($origin, $allowedOrigins)) {
   header("Access-Control-Allow-Origin: " . $origin);
} else {
   // Same-origin requests (frontend served by same PHP server) không có HTTP_ORIGIN
   header("Access-Control-Allow-Origin: " . AppConfig::$frontendUrl);
}
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, X-Guest-Id");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
   http_response_code(204);
   exit();
}

$contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
if (stripos($contentType, 'application/json') !== false) {
   $rawBody = file_get_contents('php://input');
   $jsonData = json_decode($rawBody, true);
   if (is_array($jsonData)) {
      $_POST = array_merge($_POST, $jsonData);
   }
}

$method = $_SERVER['REQUEST_METHOD'];
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$segments = explode('/', $url);

// Kiểm tra xem có phải yêu cầu API hay không
$isApi = false;
if (isset($segments[0]) && $segments[0] === 'api') {
   $isApi = true;
   array_shift($segments);
}

if (!$isApi) {
   header("Content-Type: text/html; charset=UTF-8");
   if (file_exists('App/Views/layout.php')) {
      include 'App/Views/layout.php';
   } elseif (file_exists('index.html')) {
      include 'index.html';
   } else {
      http_response_code(404);
      echo "Giao diện layout.php không tồn tại!";
   }
   exit();
}

// Thiết lập định dạng JSON cho phản hồi API
header("Content-Type: application/json; charset=UTF-8");

$resource = $segments[0] ?? '';
$subResource = $segments[1] ?? '';
$id = isset($segments[1]) && is_numeric($segments[1]) ? (int)$segments[1] : null;

function dispatch(string $controllerClass, string $action, array $params = []) {
   $filePath = 'App/Controllers/' . $controllerClass . '.php';
   if (!file_exists($filePath)) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => "Controller file $controllerClass not found!"]);
      exit();
   }
   require_once $filePath;
   $controller = new $controllerClass();
   if (!method_exists($controller, $action)) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => "Method $action not found in $controllerClass!"]);
      exit();
   }
   call_user_func_array([$controller, $action], $params);
   exit();
}

// Router
switch ($resource) {
   case 'auth':
      if ($subResource === 'register' && $method === 'POST') {
         dispatch('AccountController', 'save');
      } elseif ($subResource === 'login' && $method === 'POST') {
         dispatch('AccountController', 'checkLogin');
      } elseif ($subResource === 'logout' && $method === 'POST') {
         dispatch('AccountController', 'logout');
      } elseif ($subResource === 'refresh' && $method === 'POST') {
         dispatch('AccountController', 'refresh');
      } elseif ($subResource === 'me' && $method === 'GET') {
         dispatch('AccountController', 'me');
      } elseif ($subResource === 'google') {
         $action = $segments[2] ?? '';
         if ($action === 'login') {
               dispatch('AccountController', 'googleLogin');
         } elseif ($action === 'callback') {
               dispatch('AccountController', 'googleCallback');
         }
      } elseif ($subResource === 'github') {
         $action = $segments[2] ?? '';
         if ($action === 'login') {
               dispatch('AccountController', 'githubLogin');
         } elseif ($action === 'callback') {
               dispatch('AccountController', 'githubCallback');
         }
      }
      break;

   case 'account':
      if ($subResource === 'profile' && $method === 'GET') {
         dispatch('AccountController', 'profile');
      } elseif ($subResource === 'password' && ($method === 'PUT' || $method === 'POST')) {
         dispatch('AccountController', 'setPassword');
      } elseif ($subResource === 'update' && ($method === 'PUT' || $method === 'POST')) {
         dispatch('AccountController', 'updateProfile');
      }
      break;

   case 'admin':
      if ($subResource === 'accounts') {
         $action = $segments[2] ?? '';
         if ($action === 'role' && ($method === 'PUT' || $method === 'POST')) {
               dispatch('AccountController', 'updateRole');
         } elseif ($method === 'GET') {
               dispatch('AccountController', 'role');
         }
      }
      break;

   case 'products':
      if ($id !== null) {
         if ($method === 'GET') {
               dispatch('ProductController', 'detail', [$id]);
         } elseif ($method === 'PUT' || $method === 'POST') {
               // Hỗ trợ cả POST (ví dụ tải ảnh) cho cập nhật
               dispatch('ProductController', 'edit', [$id]);
         } elseif ($method === 'DELETE') {
               dispatch('ProductController', 'delete', [$id]);
         }
      } else {
         if ($method === 'GET') {
               dispatch('ProductController', 'list');
         } elseif ($method === 'POST') {
               dispatch('ProductController', 'create');
         }
      }
      break;

   case 'categories':
      if ($method === 'GET') {
         dispatch('ProductController', 'categories');
      }
      break;

   case 'cart':
      if ($subResource === 'add' && $method === 'POST') {
         $productId = isset($segments[2]) ? (int)$segments[2] : (int)($_POST['product_id'] ?? 0);
         dispatch('CartController', 'add', [$productId]);
      } elseif ($subResource === 'update' && ($method === 'PUT' || $method === 'POST')) {
         dispatch('CartController', 'update');
      } elseif ($subResource === 'remove' && ($method === 'DELETE' || $method === 'POST')) {
         $productId = isset($segments[2]) ? (int)$segments[2] : (int)($_POST['product_id'] ?? 0);
         dispatch('CartController', 'remove', [$productId]);
      } elseif ($method === 'GET') {
         dispatch('CartController', 'index');
      }
      break;

   case 'orders':
      if ($subResource === 'vnpay-return' && $method === 'GET') {
         dispatch('CartController', 'vnpayReturn');
      } elseif ($method === 'GET') {
         dispatch('CartController', 'orders');
      } elseif ($method === 'POST') {
         dispatch('CartController', 'placeOrder');
      }
      break;

   case '':
      echo json_encode([
         'success' => true,
         'message' => 'ZeionStore RESTful API is running...',
         'version' => '1.0.0'
      ]);
      exit();
}

http_response_code(404);
echo json_encode([
   'success' => false,
   'message' => 'API not found!'
]);
exit();
