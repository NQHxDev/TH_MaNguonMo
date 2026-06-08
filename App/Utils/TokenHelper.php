<?php

class TokenHelper {

   private static string $secret = 'zeion_store_jwt_secret_key_123456789_!@#'; // A strong secret key

   private static function base64UrlEncode(string $data): string {
      return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
   }

   private static function base64UrlDecode(string $data): string {
      $remainder = strlen($data) % 4;
      if ($remainder) {
         $data .= str_repeat('=', 4 - $remainder);
      }
      return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
   }

   /**
    * Tạo JWT tự chứa payload (username, role) và chữ ký (HMAC-SHA256).
    * Hạn sử dụng mặc định là 24 giờ (86400 giây).
    */
   public static function generateToken(string $username, string $role, int $expiry = 86400): string {
      $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
      $payload = [
         'username' => $username,
         'role'     => $role,
         'iat'      => time(),
         'exp'      => time() + $expiry
      ];

      $base64UrlHeader = self::base64UrlEncode($header);
      $base64UrlPayload = self::base64UrlEncode(json_encode($payload));

      $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, self::$secret, true);
      $base64UrlSignature = self::base64UrlEncode($signature);

      return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
   }

   /**
    * Xác thực chữ ký và hạn dùng của JWT, trả về payload nếu hợp lệ.
    */
   public static function verifyToken(string $token): ?array {
      if (empty($token)) {
         return null;
      }

      $parts = explode('.', $token);
      if (count($parts) !== 3) {
         return null;
      }

      list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $parts;

      // Xác thực chữ ký
      $signature = self::base64UrlDecode($base64UrlSignature);
      $expectedSignature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, self::$secret, true);

      if (!hash_equals($signature, $expectedSignature)) {
         return null;
      }

      $payload = json_decode(self::base64UrlDecode($base64UrlPayload), true);
      if (!$payload) {
         return null;
      }

      // Kiểm tra hạn sử dụng (exp)
      if (isset($payload['exp']) && $payload['exp'] < time()) {
         return null;
      }

      return $payload;
   }

   /**
    * Không cần xóa token phía máy chủ đối với JWT (vì stateless).
    * Phương thức được giữ lại để tương thích với cấu trúc gọi cũ.
    */
   public static function deleteToken(string $token): bool {
      return true;
   }
}
?>
