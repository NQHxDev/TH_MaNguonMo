<?php

require_once 'App/Config/Database.php';
require_once 'App/Config/Redis.php';
require_once 'App/Config/AppConfig.php';
require_once 'App/Models/AccountModel.php';
require_once 'App/Utils/TokenHelper.php';
require_once 'App/Utils/AuthMiddleware.php';

class AccountController {

   private PDO $db;
   private AccountModel $accountModel;

   public function __construct() {
      $this->db = Database::getConnection();
      $this->accountModel = new AccountModel($this->db);
   }

   /**
    * POST /api/auth/register
    */
   public function save() {
      $username = $_POST['username'] ?? '';
      $fullName = $_POST['fullname'] ?? '';
      $password = $_POST['password'] ?? '';
      $confirmPassword = $_POST['confirmpassword'] ?? '';

      $errors = [];
      if (empty($username)) $errors['username'] = "Vui lòng nhập username!";
      if (empty($fullName)) $errors['fullname'] = "Vui lòng nhập fullname!";
      if (empty($password)) $errors['password'] = "Vui lòng nhập password!";
      if ($password !== $confirmPassword) $errors['confirmPass'] = "Mật khẩu và xác nhận chưa khớp!";

      if ($this->accountModel->getAccountByUsername($username)) {
         $errors['account'] = "Tài khoản này đã được đăng ký!";
      }

      if (count($errors) > 0) {
         http_response_code(400);
         echo json_encode([
               'success' => false,
               'errors' => $errors
         ], JSON_UNESCAPED_UNICODE);
         exit();
      }

      $result = $this->accountModel->save($username, $fullName, $password, 'user');
      if ($result) {
         http_response_code(201);
         echo json_encode([
               'success' => true,
               'message' => "Đăng ký tài khoản thành công!"
         ], JSON_UNESCAPED_UNICODE);
         exit();
      } else {
         http_response_code(500);
         echo json_encode([
               'success' => false,
               'message' => "Lỗi khi lưu tài khoản vào cơ sở dữ liệu."
         ], JSON_UNESCAPED_UNICODE);
         exit();
      }
   }

   /**
    * POST /api/auth/login
    */
   public function checkLogin() {
      $username = $_POST['username'] ?? '';
      $password = $_POST['password'] ?? '';

      if (empty($username) || empty($password)) {
         http_response_code(400);
         echo json_encode([
               'success' => false,
               'message' => "Vui lòng nhập đầy đủ tài khoản và mật khẩu!"
         ], JSON_UNESCAPED_UNICODE);
         exit();
      }

      $account = $this->accountModel->getAccountByUsername($username);
      if (!$account) {
         http_response_code(401);
         echo json_encode([
               'success' => false,
               'message' => "Không tìm thấy tài khoản!"
         ], JSON_UNESCAPED_UNICODE);
         exit();
      }

      // Tài khoản chưa đặt mật khẩu (chỉ đăng nhập bằng social)
      if (empty($account->password)) {
         http_response_code(401);
         echo json_encode([
               'success' => false,
               'message' => "Tài khoản này chưa đặt mật khẩu. Vui lòng đăng nhập bằng Google/GitHub và đặt mật khẩu trong Thông tin tài khoản."
         ], JSON_UNESCAPED_UNICODE);
         exit();
      }

      if (password_verify($password, $account->password)) {
         // Tạo cặp Access + Refresh token
         $accessToken  = TokenHelper::generateAccessToken($account->username, $account->role);
         $refreshToken = TokenHelper::generateRefreshToken($account->username, $account->role);

         // Set HttpOnly cookies — browser tự gửi mỗi request
         TokenHelper::setTokenCookies($accessToken, $refreshToken);

         echo json_encode([
               'success' => true,
               'message' => "Đăng nhập thành công!",
               'user' => [
                  'username' => $account->username,
                  'fullname' => $account->fullname,
                  'role' => $account->role
               ]
         ], JSON_UNESCAPED_UNICODE);
         exit();
      } else {
         http_response_code(401);
         echo json_encode([
               'success' => false,
               'message' => "Mật khẩu không đúng!"
         ], JSON_UNESCAPED_UNICODE);
         exit();
      }
   }

   /**
    * POST /api/auth/logout
    */
   public function logout() {
      // Đọc tokens từ cookies (browser tự gửi)
      $accessToken  = $_COOKIE['access_token'] ?? '';
      $refreshToken = $_COOKIE['refresh_token'] ?? '';

      // Revoke access token (thêm vào blacklist)
      if (!empty($accessToken)) {
         TokenHelper::revokeAccessToken($accessToken);
      }

      // Revoke refresh token (xóa khỏi whitelist)
      if (!empty($refreshToken)) {
         TokenHelper::revokeRefreshToken($refreshToken);
      }

      // Fallback: đọc từ Authorization header (cho API client)
      $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
      if (empty($authHeader) && function_exists('getallheaders')) {
         $headers = getallheaders();
         foreach ($headers as $key => $value) {
               if (strcasecmp($key, 'Authorization') === 0) {
                  $authHeader = $value;
                  break;
               }
         }
      }
      if ($authHeader && preg_match('/Bearer\s(\S+)/i', $authHeader, $matches)) {
         TokenHelper::revokeAccessToken($matches[1]);
      }

      // Xóa cookies
      TokenHelper::clearTokenCookies();

      echo json_encode([
         'success' => true,
         'message' => "Đăng xuất thành công!"
      ], JSON_UNESCAPED_UNICODE);
      exit();
   }

   /**
    * POST /api/auth/refresh
    * 
    * Refresh Token Rotation:
    * 1. Đọc refresh_token từ HttpOnly cookie (browser tự gửi)
    * 2. Verify signature + exp + Redis whitelist
    * 3. Xóa refresh token cũ (chống replay attack)
    * 4. Tạo cặp token mới
    * 5. Set cookies mới
    */
   public function refresh() {
      $refreshToken = $_COOKIE['refresh_token'] ?? '';

      if (empty($refreshToken)) {
         http_response_code(401);
         echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy refresh token.'
         ], JSON_UNESCAPED_UNICODE);
         exit();
      }

      // Verify refresh token (signature + exp + Redis whitelist)
      $payload = TokenHelper::verifyRefreshToken($refreshToken);

      if (!$payload) {
         // Token không hợp lệ hoặc đã bị revoke → xóa cookie
         TokenHelper::clearTokenCookies();
         http_response_code(401);
         echo json_encode([
            'success' => false,
            'message' => 'Refresh token không hợp lệ hoặc đã hết hạn.'
         ], JSON_UNESCAPED_UNICODE);
         exit();
      }

      $username = $payload['username'];
      $role = $payload['role'];

      // Lấy role mới nhất từ DB (phòng trường hợp admin đã đổi role)
      $account = $this->accountModel->getAccountByUsername($username);
      if (!$account) {
         TokenHelper::clearTokenCookies();
         http_response_code(401);
         echo json_encode([
            'success' => false,
            'message' => 'Tài khoản không tồn tại.'
         ], JSON_UNESCAPED_UNICODE);
         exit();
      }

      // Revoke refresh token cũ (rotation)
      TokenHelper::revokeRefreshToken($refreshToken);

      // Tạo cặp token mới với role mới nhất từ DB
      $newAccessToken  = TokenHelper::generateAccessToken($account->username, $account->role);
      $newRefreshToken = TokenHelper::generateRefreshToken($account->username, $account->role);

      // Set cookies mới
      TokenHelper::setTokenCookies($newAccessToken, $newRefreshToken);

      echo json_encode([
         'success' => true,
         'message' => 'Token đã được làm mới.'
      ], JSON_UNESCAPED_UNICODE);
      exit();
   }

   /**
    * GET /api/auth/me
    */
   public function me() {
      $user = AuthMiddleware::requireAuth();
      $account = $this->accountModel->getAccountByUsername($user['username']);
      if ($account) {
         echo json_encode([
               'success' => true,
               'user' => [
                  'username' => $account->username,
                  'fullname' => $account->fullname,
                  'role' => $account->role,
                  'has_password' => !empty($account->password)
               ]
         ], JSON_UNESCAPED_UNICODE);
      } else {
         http_response_code(404);
         echo json_encode([
               'success' => false,
               'message' => "Không tìm thấy thông tin tài khoản."
         ], JSON_UNESCAPED_UNICODE);
      }
      exit();
   }

   /**
    * GET /api/admin/accounts (Admin only)
    */
   public function role() {
      // AuthMiddleware::requireAdmin();
      $accounts = $this->accountModel->getAllAccounts();
      echo json_encode([
         'success' => true,
         'accounts' => $accounts
      ], JSON_UNESCAPED_UNICODE);
      exit();
   }

   /**
    * PUT /api/admin/accounts/role (Admin only)
    */
   public function updateRole() {
      // AuthMiddleware::requireAdmin();
      $username = $_POST['username'] ?? '';
      $role = $_POST['role'] ?? 'user';

      if (!empty($username) && in_array($role, ['admin', 'user'])) {
         $result = $this->accountModel->updateRole($username, $role);
         if ($result) {
               $response = [
                  'success' => true,
                  'message' => "Cập nhật vai trò cho tài khoản " . $username . " thành công!"
               ];

               // Nếu tự thay đổi quyền của bản thân, tạo cặp token mới
               $currentUser = AuthMiddleware::getUserFromHeaders();
               if ($currentUser && $currentUser['username'] === $username) {
                  // Revoke tokens cũ
                  $oldAccessToken  = $_COOKIE['access_token'] ?? '';
                  $oldRefreshToken = $_COOKIE['refresh_token'] ?? '';
                  if (!empty($oldAccessToken))  TokenHelper::revokeAccessToken($oldAccessToken);
                  if (!empty($oldRefreshToken)) TokenHelper::revokeRefreshToken($oldRefreshToken);

                  // Tạo cặp token mới với role mới
                  $newAccessToken  = TokenHelper::generateAccessToken($username, $role);
                  $newRefreshToken = TokenHelper::generateRefreshToken($username, $role);
                  TokenHelper::setTokenCookies($newAccessToken, $newRefreshToken);
               }

               echo json_encode($response, JSON_UNESCAPED_UNICODE);
         } else {
               http_response_code(500);
               echo json_encode([
                  'success' => false,
                  'message' => "Không thể cập nhật vai trò!"
               ], JSON_UNESCAPED_UNICODE);
         }
      } else {
         http_response_code(400);
         echo json_encode([
               'success' => false,
               'message' => "Dữ liệu không hợp lệ!"
         ], JSON_UNESCAPED_UNICODE);
      }
      exit();
   }

   /**
    * GET /api/auth/google/login
    */
   public function googleLogin() {
      $oauthFile = 'App/Config/OAuth.php';
      if (!file_exists($oauthFile)) {
         http_response_code(500);
         echo json_encode(['success' => false, 'message' => "OAuth config file missing."]);
         exit();
      }
      require_once $oauthFile;
      $clientId = OAuthConfig::$google['client_id'];
      $redirectUri = urlencode(OAuthConfig::$google['redirect_uri']);
      $scope = urlencode('email profile');
      $state = bin2hex(random_bytes(16));

      // Lưu state vào Redis tạm thời thay vì Session
      try {
         $redis = new RedisClient();
         $redis->setex('oauth:state:google:' . $state, 600, '1');
      } catch (Exception $e) {
         error_log($e->getMessage());
      }

      $url = "https://accounts.google.com/o/oauth2/v2/auth?response_type=code&client_id={$clientId}&redirect_uri={$redirectUri}&scope={$scope}&state={$state}&prompt=select_account";
      header("Location: " . $url);
      exit;
   }

   /**
    * GET /api/auth/google/callback
    */
   public function googleCallback() {
      $oauthFile = 'App/Config/OAuth.php';
      if (!file_exists($oauthFile)) {
         $this->redirectFailure("OAuth config file missing.");
      }
      require_once $oauthFile;

      $state = $_GET['state'] ?? '';
      try {
         $redis = new RedisClient();
         $stateExists = $redis->get('oauth:state:google:' . $state);
         if (empty($state) || !$stateExists) {
               $this->redirectFailure("State validation failed. CSRF suspected.");
         }
         $redis->del('oauth:state:google:' . $state);
      } catch (Exception $e) {
         // Fallback nếu không có Redis kết nối
      }

      $code = $_GET['code'] ?? '';
      if (empty($code)) {
         $this->redirectFailure("Authorization code not found.");
      }

      $tokenUrl = 'https://oauth2.googleapis.com/token';
      $postData = [
         'code' => $code,
         'client_id' => OAuthConfig::$google['client_id'],
         'client_secret' => OAuthConfig::$google['client_secret'],
         'redirect_uri' => OAuthConfig::$google['redirect_uri'],
         'grant_type' => 'authorization_code'
      ];

      $ch = curl_init($tokenUrl);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      $response = curl_exec($ch);
      $curlError = curl_error($ch);
      curl_close($ch);

      $tokenData = json_decode($response, true);
      $accessToken = $tokenData['access_token'] ?? '';

      if (empty($accessToken)) {
         $errMsg = "Failed to obtain access token";
         if ($curlError) {
            $errMsg .= " Curl Error: " . $curlError;
         } elseif ($response) {
            $errMsg .= " Response: " . $response;
         }
         $this->redirectFailure($errMsg);
      }

      $userinfoUrl = 'https://www.googleapis.com/oauth2/v3/userinfo?access_token=' . $accessToken;
      $ch = curl_init($userinfoUrl);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      $response = curl_exec($ch);
      curl_close($ch);

      $profile = json_decode($response, true);
      $googleUserId = $profile['sub'] ?? '';
      $email = $profile['email'] ?? '';
      $name = $profile['name'] ?? '';

      if (empty($googleUserId)) {
         $this->redirectFailure("Unique ID not returned by Google.");
      }
      if (empty($email)) {
         $this->redirectFailure("Email not returned by Google.");
      }

      // 1. Tìm tài khoản liên kết social hiện tại
      $socialAcc = $this->accountModel->getSocialAccount('google', $googleUserId);
      $account = null;

      if ($socialAcc) {
         // Cập nhật access token mới nhất
         $this->accountModel->updateSocialAccessToken($socialAcc->id, $accessToken);
         $account = $this->accountModel->getAccountById($socialAcc->account_id);
      } else {
         // 2. Nếu chưa liên kết, kiểm tra tài khoản email đã tồn tại chưa
         $account = $this->accountModel->getAccountByUsername($email);
         if (!$account) {
            // Tạo tài khoản chính mới nếu chưa có
            $this->accountModel->save($email, $name, '', 'user');
            $account = $this->accountModel->getAccountByUsername($email);
         }
         
         if ($account) {
            // Liên kết tài khoản mạng xã hội với tài khoản chính và lưu access token
            $this->accountModel->linkSocialAccount($account->id, 'google', $googleUserId, $email, $accessToken);
         }
      }

      if ($account) {
         $this->redirectSuccess($account->username, $account->role);
      } else {
         $this->redirectFailure("Đăng nhập liên kết Google thất bại.");
      }
   }

   /**
    * GET /api/auth/github/login
    */
   public function githubLogin() {
      $oauthFile = 'App/Config/OAuth.php';
      if (!file_exists($oauthFile)) {
         http_response_code(500);
         echo json_encode(['success' => false, 'message' => "OAuth config file missing."]);
         exit();
      }
      require_once $oauthFile;
      $clientId = OAuthConfig::$github['client_id'];
      $redirectUri = urlencode(OAuthConfig::$github['redirect_uri']);
      $state = bin2hex(random_bytes(16));

      try {
         $redis = new RedisClient();
         $redis->setex('oauth:state:github:' . $state, 600, '1');
      } catch (Exception $e) {
         error_log($e->getMessage());
      }

      $url = "https://github.com/login/oauth/authorize?client_id={$clientId}&redirect_uri={$redirectUri}&state={$state}&scope=user:email&prompt=select_account";
      header("Location: " . $url);
      exit;
   }

   /**
    * GET /api/auth/github/callback
    */
   public function githubCallback() {
      $oauthFile = 'App/Config/OAuth.php';
      if (!file_exists($oauthFile)) {
         $this->redirectFailure("OAuth config file missing.");
      }
      require_once $oauthFile;

      $state = $_GET['state'] ?? '';
      try {
         $redis = new RedisClient();
         $stateExists = $redis->get('oauth:state:github:' . $state);
         if (empty($state) || !$stateExists) {
               $this->redirectFailure("State validation failed.");
         }
         $redis->del('oauth:state:github:' . $state);
      } catch (Exception $e) {
         // Fallback
      }

      $code = $_GET['code'] ?? '';
      if (empty($code)) {
         $this->redirectFailure("Authorization code not found.");
      }

      $tokenUrl = 'https://github.com/login/oauth/access_token';
      $postData = [
         'client_id' => OAuthConfig::$github['client_id'],
         'client_secret' => OAuthConfig::$github['client_secret'],
         'code' => $code,
         'redirect_uri' => OAuthConfig::$github['redirect_uri']
      ];

      $ch = curl_init($tokenUrl);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, [
         'Accept: application/json',
         'User-Agent: ZeionStore-OAuthApp'
      ]);
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      $response = curl_exec($ch);
      $curlError = curl_error($ch);
      curl_close($ch);

      $tokenData = json_decode($response, true);
      $accessToken = $tokenData['access_token'] ?? '';

      if (empty($accessToken)) {
         $errMsg = "Failed to obtain access token";
         if ($curlError) {
            $errMsg .= " Curl Error: " . $curlError;
         } elseif ($response) {
            $errMsg .= " Response: " . $response;
         }
         $this->redirectFailure($errMsg);
      }

      $userinfoUrl = 'https://api.github.com/user';
      $ch = curl_init($userinfoUrl);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      $checkboxHeaders = [
         'User-Agent: ZeionStore-OAuthApp',
         'Authorization: token ' . $accessToken
      ];
      curl_setopt($ch, CURLOPT_HTTPHEADER, $checkboxHeaders);
      $response = curl_exec($ch);
      curl_close($ch);

      $profile = json_decode($response, true);
      $githubUserId = (string)($profile['id'] ?? '');
      $username = $profile['login'] ?? '';
      $email = $profile['email'] ?? $username;
      $name = $profile['name'] ?? $username;

      if (empty($githubUserId)) {
         $this->redirectFailure("Unique ID not returned by GitHub.");
      }
      if (empty($username)) {
         $this->redirectFailure("Failed to retrieve GitHub profile.");
      }

      // 1. Tìm tài khoản liên kết social hiện tại
      $socialAcc = $this->accountModel->getSocialAccount('github', $githubUserId);
      $account = null;

      if ($socialAcc) {
         // Cập nhật access token mới nhất
         $this->accountModel->updateSocialAccessToken($socialAcc->id, $accessToken);
         $account = $this->accountModel->getAccountById($socialAcc->account_id);
      } else {
         // 2. Nếu chưa liên kết, kiểm tra tài khoản chính đã tồn tại chưa
         $account = $this->accountModel->getAccountByUsername($username);
         if (!$account && !empty($profile['email'])) {
            $account = $this->accountModel->getAccountByUsername($profile['email']);
         }
         
         if (!$account) {
            // Tạo tài khoản chính mới nếu chưa có
            $this->accountModel->save($username, $name, '', 'user');
            $account = $this->accountModel->getAccountByUsername($username);
         }
         
         if ($account) {
            // Liên kết tài khoản mạng xã hội với tài khoản chính và lưu access token
            $this->accountModel->linkSocialAccount($account->id, 'github', $githubUserId, $email, $accessToken);
         }
      }

      if ($account) {
         $this->redirectSuccess($account->username, $account->role);
      } else {
         $this->redirectFailure("Đăng nhập liên kết GitHub thất bại.");
      }
   }

   /**
    * Redirect sau OAuth thành công — Set cookies rồi redirect, KHÔNG truyền token qua URL.
    */
   private function redirectSuccess(string $username, string $role) {
      $accessToken  = TokenHelper::generateAccessToken($username, $role);
      $refreshToken = TokenHelper::generateRefreshToken($username, $role);
      TokenHelper::setTokenCookies($accessToken, $refreshToken);

      // Redirect về frontend KHÔNG kèm token trong URL
      $redirectUrl = AppConfig::$frontendUrl . '/oauth-success';
      header("Location: " . $redirectUrl);
      exit();
   }

   private function redirectFailure(string $message) {
      // Truncate error message if it's too long to prevent Apache 414 Request-URI Too Long
      if (strlen($message) > 150) {
         $message = substr($message, 0, 150) . '...';
      }
      $redirectUrl = AppConfig::$frontendUrl . '/oauth-failure?message=' . urlencode($message);
      header("Location: " . $redirectUrl);
      exit();
   }

   /**
    * GET /api/account/profile
    */
   public function profile() {
      $user = AuthMiddleware::requireAuth();
      $account = $this->accountModel->getAccountByUsername($user['username']);
      if (!$account) {
         http_response_code(404);
         echo json_encode(['success' => false, 'message' => 'Tài khoản không tồn tại.'], JSON_UNESCAPED_UNICODE);
         exit();
      }

      $socialProviders = $this->accountModel->getLinkedSocialProviders($account->id);

      echo json_encode([
         'success' => true,
         'profile' => [
            'id' => $account->id,
            'username' => $account->username,
            'fullname' => $account->fullname,
            'role' => $account->role,
            'has_password' => !empty($account->password),
            'social_accounts' => $socialProviders
         ]
      ], JSON_UNESCAPED_UNICODE);
      exit();
   }

   /**
    * PUT /api/account/password
    */
   public function setPassword() {
      $user = AuthMiddleware::requireAuth();
      $account = $this->accountModel->getAccountByUsername($user['username']);
      if (!$account) {
         http_response_code(404);
         echo json_encode(['success' => false, 'message' => 'Tài khoản không tồn tại.'], JSON_UNESCAPED_UNICODE);
         exit();
      }

      $currentPassword = $_POST['current_password'] ?? '';
      $newPassword = $_POST['new_password'] ?? '';
      $confirmPassword = $_POST['confirm_password'] ?? '';

      // Validate
      if (empty($newPassword)) {
         http_response_code(400);
         echo json_encode(['success' => false, 'message' => 'Vui lòng nhập mật khẩu mới!'], JSON_UNESCAPED_UNICODE);
         exit();
      }
      if (strlen($newPassword) < 6) {
         http_response_code(400);
         echo json_encode(['success' => false, 'message' => 'Mật khẩu mới phải có ít nhất 6 ký tự!'], JSON_UNESCAPED_UNICODE);
         exit();
      }
      if ($newPassword !== $confirmPassword) {
         http_response_code(400);
         echo json_encode(['success' => false, 'message' => 'Xác nhận mật khẩu không khớp!'], JSON_UNESCAPED_UNICODE);
         exit();
      }

      // Nếu đã có mật khẩu, yêu cầu nhập mật khẩu hiện tại
      if (!empty($account->password)) {
         if (empty($currentPassword)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập mật khẩu hiện tại!'], JSON_UNESCAPED_UNICODE);
            exit();
         }
         if (!password_verify($currentPassword, $account->password)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Mật khẩu hiện tại không đúng!'], JSON_UNESCAPED_UNICODE);
            exit();
         }
      }

      $result = $this->accountModel->updatePassword($account->id, $newPassword);
      if ($result) {
         echo json_encode(['success' => true, 'message' => 'Đặt mật khẩu thành công!'], JSON_UNESCAPED_UNICODE);
      } else {
         http_response_code(500);
         echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật mật khẩu.'], JSON_UNESCAPED_UNICODE);
      }
      exit();
   }

   /**
    * PUT /api/account/update
    */
   public function updateProfile() {
      $user = AuthMiddleware::requireAuth();
      $account = $this->accountModel->getAccountByUsername($user['username']);
      if (!$account) {
         http_response_code(404);
         echo json_encode(['success' => false, 'message' => 'Tài khoản không tồn tại.'], JSON_UNESCAPED_UNICODE);
         exit();
      }

      $fullname = $_POST['fullname'] ?? '';
      if (empty($fullname)) {
         http_response_code(400);
         echo json_encode(['success' => false, 'message' => 'Vui lòng nhập họ và tên!'], JSON_UNESCAPED_UNICODE);
         exit();
      }

      $result = $this->accountModel->updateProfile($account->id, $fullname);
      if ($result) {
         // Revoke tokens cũ và tạo cặp mới
         $oldAccessToken  = $_COOKIE['access_token'] ?? '';
         $oldRefreshToken = $_COOKIE['refresh_token'] ?? '';
         if (!empty($oldAccessToken))  TokenHelper::revokeAccessToken($oldAccessToken);
         if (!empty($oldRefreshToken)) TokenHelper::revokeRefreshToken($oldRefreshToken);

         $newAccessToken  = TokenHelper::generateAccessToken($account->username, $account->role);
         $newRefreshToken = TokenHelper::generateRefreshToken($account->username, $account->role);
         TokenHelper::setTokenCookies($newAccessToken, $newRefreshToken);

         echo json_encode([
            'success' => true, 
            'message' => 'Cập nhật thông tin thành công!'
         ], JSON_UNESCAPED_UNICODE);
      } else {
         http_response_code(500);
         echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật thông tin.'], JSON_UNESCAPED_UNICODE);
      }
      exit();
   }
}
?>
