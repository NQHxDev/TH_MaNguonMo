<?php

require_once __DIR__ . '/TokenHelper.php';

class AuthMiddleware {

   /**
    * Trích xuất thông tin người dùng từ Header Authorization (Bearer Token) hoặc query string.
    */
   public static function getUserFromHeaders(): ?array {
      $authHeader = null;

      if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
         $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
      } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
         $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
      } elseif (function_exists('getallheaders')) {
         $headers = getallheaders();
         foreach ($headers as $key => $value) {
               if (strcasecmp($key, 'Authorization') === 0) {
                  $authHeader = $value;
                  break;
               }
         }
      }

      // Hỗ trợ truyền qua query string để test hoặc callback tiện lợi
      if (!$authHeader && isset($_GET['token'])) {
         $authHeader = 'Bearer ' . $_GET['token'];
      }

      if ($authHeader && preg_match('/Bearer\s(\S+)/i', $authHeader, $matches)) {
         $token = $matches[1];
         return TokenHelper::verifyToken($token);
      }

      return null;
   }

   /**
    * Yêu cầu người dùng phải đăng nhập, nếu không trả về lỗi 401 Unauthorized.
    */
   public static function requireAuth(): array {
      $user = self::getUserFromHeaders();
      if (!$user) {
         http_response_code(401);
         echo json_encode([
               'success' => false,
               'message' => 'Unauthorized. Vui lòng đăng nhập!'
         ]);
         exit();
      }
      return $user;
   }

   /**
    * Yêu cầu người dùng phải có quyền Admin, nếu không trả về lỗi 403 Forbidden.
    */
   public static function requireAdmin(): array {
      $user = self::requireAuth();
      if (($user['role'] ?? '') !== 'admin') {
         http_response_code(403);
         echo json_encode([
               'success' => false,
               'message' => 'Forbidden. Bạn không có quyền quản trị!'
         ]);
         exit();
      }
      return $user;
   }
}
?>
