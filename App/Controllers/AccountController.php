<?php 

require_once 'App/Config/Database.php'; 
require_once 'App/Config/Redis.php'; 
require_once 'App/Models/AccountModel.php'; 
require_once 'App/Utils/SessionHelper.php';

class AccountController {

    private PDO $db;
    private RedisClient $redis;
    private AccountModel $accountModel;
 
    public function __construct() { 
        SessionHelper::start();
        $this->db = Database::getConnection(); 
        $this->redis = new RedisClient(); 
        $this->accountModel = new AccountModel($this->db); 
    } 
 
    public function register() { 
        include_once 'App/Views/Account/Register.php'; 
    } 
 
    public function login() { 
        include_once 'App/Views/Account/Login.php'; 
    } 
 
    public function save() { 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
            $username = $_POST['username'] ?? ''; 
            $fullName = $_POST['fullname'] ?? ''; 
            $password = $_POST['password'] ?? ''; 
            $confirmPassword = $_POST['confirmpassword'] ?? ''; 
            $role = $_POST['role'] ?? 'user'; 
 
            $errors = []; 
            if (empty($username)) $errors['username'] = "Vui lòng nhập username!"; 
            if (empty($fullName)) $errors['fullname'] = "Vui lòng nhập fullname!"; 
            if (empty($password)) $errors['password'] = "Vui lòng nhập password!"; 
            if ($password !== $confirmPassword) $errors['confirmPass'] = "Mật khẩu và xác nhận chưa khớp!"; 
            if (!in_array($role, ['admin', 'user'])) $role = 'user'; 
            
            if ($this->accountModel->getAccountByUsername($username)) { 
                $errors['account'] = "Tài khoản này đã được đăng ký!"; 
            } 
 
            if (count($errors) > 0) { 
                include_once 'App/Views/Account/Register.php'; 
            } else { 
                $result = $this->accountModel->save($username, $fullName, $password, $role); 
                if ($result) { 
                    header('Location: /account/login'); 
                    exit; 
                } 
            } 
        } 
    } 
 
    public function logout() { 
        SessionHelper::logout(); 
        header('Location: /'); 
        exit; 
    } 
 
    public function checkLogin() { 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
            $username = $_POST['username'] ?? ''; 
            $password = $_POST['password'] ?? ''; 
 
            $account = $this->accountModel->getAccountByUsername($username); 
            if ($account && password_verify($password, $account->password)) { 
                SessionHelper::login($account->username, $account->role);
                header('Location: /'); 
                exit; 
            } else { 
                $error = $account ? "Mật khẩu không đúng!" : "Không tìm thấy tài khoản!"; 
                include_once 'App/Views/Account/Login.php'; 
                exit; 
            } 
        } 
    } 

    public function role() {
        $accounts = $this->accountModel->getAllAccounts();
        include_once 'App/Views/Account/Role.php';
    }

    public function updateRole() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $role = $_POST['role'] ?? 'user';
            
            if (!empty($username) && in_array($role, ['admin', 'user'])) {
                $result = $this->accountModel->updateRole($username, $role);
                if ($result) {
                    $_SESSION['success'] = "Cập nhật vai trò cho tài khoản " . htmlspecialchars($username) . " thành công!";
                } else {
                    $_SESSION['error'] = "Không thể cập nhật vai trò!";
                }
            } else {
                $_SESSION['error'] = "Dữ liệu không hợp lệ!";
            }
            header('Location: /account/role');
            exit;
        }
    }

    public function googleLogin() {
        require_once 'App/Config/OAuth.php';
        $clientId = OAuthConfig::$google['client_id'];
        $redirectUri = urlencode(OAuthConfig::$google['redirect_uri']);
        $scope = urlencode('email profile');
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state'] = $state;
        
        $url = "https://accounts.google.com/o/oauth2/v2/auth?response_type=code&client_id={$clientId}&redirect_uri={$redirectUri}&scope={$scope}&state={$state}&prompt=select_account";
        header("Location: " . $url);
        exit;
    }

    public function googleCallback() {
        require_once 'App/Config/OAuth.php';
        $state = $_GET['state'] ?? '';
        if (empty($state) || $state !== ($_SESSION['oauth_state'] ?? '')) {
            die('State validation failed. CSRF suspected.');
        }
        unset($_SESSION['oauth_state']);
        
        $code = $_GET['code'] ?? '';
        if (empty($code)) {
            die('Authorization code not found.');
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
        $response = curl_exec($ch);
        curl_close($ch);
        
        $tokenData = json_decode($response, true);
        $accessToken = $tokenData['access_token'] ?? '';
        
        if (empty($accessToken)) {
            die('Failed to obtain access token.');
        }
        
        $userinfoUrl = 'https://www.googleapis.com/oauth2/v3/userinfo?access_token=' . $accessToken;
        $ch = curl_init($userinfoUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $profile = json_decode($response, true);
        $email = $profile['email'] ?? '';
        $name = $profile['name'] ?? '';
        
        if (empty($email)) {
            die('Email not returned by Google.');
        }
    
        $account = $this->accountModel->getAccountByUsername($email);
        if (!$account) {
            $randomPass = bin2hex(random_bytes(16));
            $this->accountModel->save($email, $name, $randomPass, 'user');
            $account = $this->accountModel->getAccountByUsername($email);
        }
        
        if ($account) {
            SessionHelper::login($account->username, $account->role);
            $_SESSION['success'] = "Đăng nhập bằng Google thành công!";
            header('Location: /');
            exit;
        } else {
            die('Đăng nhập thất bại.');
        }
    }

    public function githubLogin() {
        require_once 'App/Config/OAuth.php';
        $clientId = OAuthConfig::$github['client_id'];
        $redirectUri = urlencode(OAuthConfig::$github['redirect_uri']);
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state'] = $state;
        
        $url = "https://github.com/login/oauth/authorize?client_id={$clientId}&redirect_uri={$redirectUri}&state={$state}&scope=user:email&prompt=select_account";
        header("Location: " . $url);
        exit;
    }

    public function githubCallback() {
        require_once 'App/Config/OAuth.php';
        $state = $_GET['state'] ?? '';
        if (empty($state) || $state !== ($_SESSION['oauth_state'] ?? '')) {
            die('State validation failed.');
        }
        unset($_SESSION['oauth_state']);
        
        $code = $_GET['code'] ?? '';
        if (empty($code)) {
            die('Authorization code not found.');
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
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        $response = curl_exec($ch);
        curl_close($ch);
        
        $tokenData = json_decode($response, true);
        $accessToken = $tokenData['access_token'] ?? '';
        
        if (empty($accessToken)) {
            die('Failed to obtain access token.');
        }
        
        $userinfoUrl = 'https://api.github.com/user';
        $ch = curl_init($userinfoUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $checkboxHeaders = [
            'User-Agent: ZeionStore-OAuthApp',
            'Authorization: token ' . $accessToken
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $checkboxHeaders);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $profile = json_decode($response, true);
        $username = $profile['login'] ?? '';
        $name = $profile['name'] ?? $username;
        
        if (empty($username)) {
            die('Failed to retrieve GitHub profile.');
        }
        
        $account = $this->accountModel->getAccountByUsername($username);
        if (!$account) {
            $randomPass = bin2hex(random_bytes(16));
            $this->accountModel->save($username, $name, $randomPass, 'user');
            $account = $this->accountModel->getAccountByUsername($username);
        }
        
        if ($account) {
            SessionHelper::login($account->username, $account->role);
            $_SESSION['success'] = "Đăng nhập bằng GitHub thành công!";
            header('Location: /');
            exit;
        } else {
            die('Đăng nhập thất bại.');
        }
    }
}
?>