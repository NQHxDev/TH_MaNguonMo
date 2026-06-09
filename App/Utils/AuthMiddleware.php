<?php

require_once __DIR__ . '/TokenHelper.php';

class AuthMiddleware {

   /**
    * Trích xuất thông tin người dùng từ Access Token.
    * 
    * Thứ tự ưu tiên:
    * 1. Cookie access_token (HttpOnly — browser tự gửi)
    * 2. Header Authorization: Bearer <token> (cho API client/Postman)
    * 
    * Không hỗ trợ truyền token qua query string (không an toàn).
    */
   public static function getUserFromHeaders(): ?array {
      // 1. Ưu tiên đọc từ HttpOnly cookie (browser tự gửi)
      if (isset($_COOKIE['access_token']) && !empty($_COOKIE['access_token'])) {
         return TokenHelper::verifyAccessToken($_COOKIE['access_token']);
      }

      // 2. Fallback: đọc từ Authorization header (cho API client như Postman, cURL)
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

      if ($authHeader && preg_match('/Bearer\s(\S+)/i', $authHeader, $matches)) {
         return TokenHelper::verifyAccessToken($matches[1]);
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
