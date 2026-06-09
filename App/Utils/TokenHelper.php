<?php

require_once __DIR__ . '/../Config/JWTConfig.php';
require_once __DIR__ . '/../Config/Redis.php';

/**
 * TokenHelper — Quản lý JWT (Access Token + Refresh Token)
 * 
 * Kiến trúc:
 * - Access Token:  Ngắn hạn (15 phút), dùng cho mọi API request
 * - Refresh Token: Dài hạn (7 ngày), chỉ dùng để lấy Access Token mới
 * 
 * Redis keys:
 * - refresh:{jti}           → JSON{username, role}  TTL: 7 ngày (whitelist)
 * - blacklist:access:{jti}  → "1"                   TTL: remaining exp (blacklist)
 * - user_refresh:{username} → SET{jti1, jti2, ...}  (quản lý session theo user)
 */
class TokenHelper {

   // ===================================================================
   // BASE64 URL ENCODE/DECODE
   // ===================================================================

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
    * Tạo UUID v4 làm JWT ID (jti).
    */
   private static function generateJti(): string {
      $bytes = random_bytes(16);
      // Set version (4) và variant (RFC 4122)
      $bytes[6] = chr(ord($bytes[6]) & 0x0f | 0x40);
      $bytes[8] = chr(ord($bytes[8]) & 0x3f | 0x80);
      return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
   }

   // ===================================================================
   // GENERATE TOKENS
   // ===================================================================

   /**
    * Tạo Access Token (JWT ngắn hạn).
    * 
    * @param string $username
    * @param string $role
    * @return string JWT access token
    */
   public static function generateAccessToken(string $username, string $role): string {
      return self::createJwt([
         'username' => $username,
         'role'     => $role,
         'type'     => 'access',
         'jti'      => self::generateJti(),
         'iat'      => time(),
         'exp'      => time() + JWTConfig::$accessTokenTTL
      ]);
   }

   /**
    * Tạo Refresh Token (JWT dài hạn) và lưu vào Redis whitelist.
    * 
    * @param string $username
    * @param string $role
    * @return string JWT refresh token
    */
   public static function generateRefreshToken(string $username, string $role): string {
      $jti = self::generateJti();
      $payload = [
         'username' => $username,
         'role'     => $role,
         'type'     => 'refresh',
         'jti'      => $jti,
         'iat'      => time(),
         'exp'      => time() + JWTConfig::$refreshTokenTTL
      ];

      $token = self::createJwt($payload);

      // Lưu vào Redis whitelist
      try {
         $redis = new RedisClient();
         // Lưu refresh token metadata
         $redis->setex(
            'refresh:' . $jti,
            JWTConfig::$refreshTokenTTL,
            json_encode(['username' => $username, 'role' => $role])
         );
         // Thêm vào danh sách session của user (để hỗ trợ logout-all)
         $redis->sadd('user_refresh:' . $username, $jti);
      } catch (Exception $e) {
         error_log('TokenHelper: Lỗi lưu refresh token vào Redis: ' . $e->getMessage());
      }

      return $token;
   }

   /**
    * Tạo JWT từ payload.
    */
   private static function createJwt(array $payload): string {
      $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);

      $base64UrlHeader = self::base64UrlEncode($header);
      $base64UrlPayload = self::base64UrlEncode(json_encode($payload));

      $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, JWTConfig::$secret, true);
      $base64UrlSignature = self::base64UrlEncode($signature);

      return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
   }

   // ===================================================================
   // VERIFY TOKENS
   // ===================================================================

   /**
    * Xác thực Access Token.
    * Kiểm tra: chữ ký → hạn dùng → type = access → không nằm trong blacklist.
    * 
    * @return array|null Payload nếu hợp lệ, null nếu không
    */
   public static function verifyAccessToken(string $token): ?array {
      $payload = self::verifyJwt($token);
      if (!$payload) return null;

      // Phải là access token
      if (($payload['type'] ?? '') !== 'access') return null;

      // Kiểm tra blacklist trong Redis
      $jti = $payload['jti'] ?? '';
      if (!empty($jti)) {
         try {
            $redis = new RedisClient();
            if ($redis->exists('blacklist:access:' . $jti)) {
               return null; // Token đã bị revoke
            }
         } catch (Exception $e) {
            error_log('TokenHelper: Lỗi kiểm tra blacklist Redis: ' . $e->getMessage());
            // Nếu Redis lỗi, cho phép pass (graceful degradation)
         }
      }

      return $payload;
   }

   /**
    * Xác thực Refresh Token.
    * Kiểm tra: chữ ký → hạn dùng → type = refresh → tồn tại trong Redis whitelist.
    * 
    * @return array|null Payload nếu hợp lệ, null nếu không
    */
   public static function verifyRefreshToken(string $token): ?array {
      $payload = self::verifyJwt($token);
      if (!$payload) return null;

      // Phải là refresh token
      if (($payload['type'] ?? '') !== 'refresh') return null;

      // Kiểm tra whitelist trong Redis (refresh token PHẢI tồn tại)
      $jti = $payload['jti'] ?? '';
      if (empty($jti)) return null;

      try {
         $redis = new RedisClient();
         if (!$redis->exists('refresh:' . $jti)) {
            return null; // Token không tồn tại hoặc đã bị revoke
         }
      } catch (Exception $e) {
         error_log('TokenHelper: Lỗi kiểm tra whitelist Redis: ' . $e->getMessage());
         return null; // Refresh token BẮT BUỘC phải check Redis
      }

      return $payload;
   }

   /**
    * Xác thực JWT cơ bản (chữ ký + hạn dùng).
    */
   private static function verifyJwt(string $token): ?array {
      if (empty($token)) return null;

      $parts = explode('.', $token);
      if (count($parts) !== 3) return null;

      list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $parts;

      // Xác thực chữ ký
      $signature = self::base64UrlDecode($base64UrlSignature);
      $expectedSignature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, JWTConfig::$secret, true);

      if (!hash_equals($signature, $expectedSignature)) {
         return null;
      }

      $payload = json_decode(self::base64UrlDecode($base64UrlPayload), true);
      if (!$payload) return null;

      // Kiểm tra hạn sử dụng (exp)
      if (isset($payload['exp']) && $payload['exp'] < time()) {
         return null;
      }

      return $payload;
   }

   // ===================================================================
   // REVOKE TOKENS
   // ===================================================================

   /**
    * Revoke Access Token — Thêm vào Redis blacklist.
    * TTL blacklist = thời gian còn lại của token (sau đó tự xóa vì token hết hạn).
    */
   public static function revokeAccessToken(string $token): bool {
      $payload = self::verifyJwt($token);
      if (!$payload) return false;
      if (($payload['type'] ?? '') !== 'access') return false;

      $jti = $payload['jti'] ?? '';
      $exp = $payload['exp'] ?? 0;
      if (empty($jti)) return false;

      $remainingTTL = $exp - time();
      if ($remainingTTL <= 0) return true; // Đã hết hạn, không cần blacklist

      try {
         $redis = new RedisClient();
         $redis->setex('blacklist:access:' . $jti, $remainingTTL, '1');
         return true;
      } catch (Exception $e) {
         error_log('TokenHelper: Lỗi blacklist access token: ' . $e->getMessage());
         return false;
      }
   }

   /**
    * Revoke Refresh Token — Xóa khỏi Redis whitelist.
    */
   public static function revokeRefreshToken(string $token): bool {
      $payload = self::verifyJwt($token);
      if (!$payload) return false;

      $jti = $payload['jti'] ?? '';
      $username = $payload['username'] ?? '';
      if (empty($jti)) return false;

      try {
         $redis = new RedisClient();
         $redis->del('refresh:' . $jti);
         if (!empty($username)) {
            $redis->srem('user_refresh:' . $username, $jti);
         }
         return true;
      } catch (Exception $e) {
         error_log('TokenHelper: Lỗi revoke refresh token: ' . $e->getMessage());
         return false;
      }
   }

   /**
    * Force Logout — Xóa TẤT CẢ refresh tokens của user.
    */
   public static function revokeAllUserTokens(string $username): bool {
      try {
         $redis = new RedisClient();
         $jtis = $redis->smembers('user_refresh:' . $username);

         foreach ($jtis as $jti) {
            $redis->del('refresh:' . $jti);
         }
         $redis->del('user_refresh:' . $username);
         return true;
      } catch (Exception $e) {
         error_log('TokenHelper: Lỗi revoke all tokens cho ' . $username . ': ' . $e->getMessage());
         return false;
      }
   }

   // ===================================================================
   // COOKIE MANAGEMENT
   // ===================================================================

   /**
    * Set cả Access Token và Refresh Token vào HttpOnly cookies.
    */
   public static function setTokenCookies(string $accessToken, string $refreshToken): void {
      setcookie('access_token', $accessToken, [
         'expires'  => time() + JWTConfig::$accessTokenTTL,
         'path'     => '/',
         'httponly'  => true,
         'secure'   => JWTConfig::$secureCookie,
         'samesite' => 'Strict'
      ]);

      setcookie('refresh_token', $refreshToken, [
         'expires'  => time() + JWTConfig::$refreshTokenTTL,
         'path'     => '/api/auth/',
         'httponly'  => true,
         'secure'   => JWTConfig::$secureCookie,
         'samesite' => 'Strict'
      ]);
   }

   /**
    * Xóa cả Access Token và Refresh Token cookies.
    */
   public static function clearTokenCookies(): void {
      setcookie('access_token', '', [
         'expires'  => time() - 3600,
         'path'     => '/',
         'httponly'  => true,
         'secure'   => JWTConfig::$secureCookie,
         'samesite' => 'Strict'
      ]);

      setcookie('refresh_token', '', [
         'expires'  => time() - 3600,
         'path'     => '/api/auth/',
         'httponly'  => true,
         'secure'   => JWTConfig::$secureCookie,
         'samesite' => 'Strict'
      ]);
   }
}
?>
