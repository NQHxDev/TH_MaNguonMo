<?php 

class SessionHelper {

    public static function start() { 
        if (session_status() === PHP_SESSION_NONE) { 
            session_start(); 
        } 
    } 
 
    public static function isLoggedIn() { 
        self::start(); 
        return isset($_SESSION['username']); 
    } 
 
    public static function isAdmin() { 
        return self::getRole() === 'admin'; 
    } 
 
    public static function getRole() { 
        self::start(); 
        if (!isset($_SESSION['username'])) {
            return 'guest';
        }

        if (class_exists('Database')) {
            try {
                $db = Database::getConnection();
                $stmt = $db->prepare("SELECT role FROM account WHERE username = :username LIMIT 1");
                $stmt->execute(['username' => $_SESSION['username']]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row) {
                    $_SESSION['role'] = $row['role'];
                    return $row['role'];
                }
            } catch (Exception $e) {
                // Fallback về session nếu lỗi kết nối DB
            }
        }
        
        return $_SESSION['role'] ?? 'guest'; 
    } 

    // Đăng nhập người dùng (lưu thông tin vào session)
    public static function login(string $username, string $role) {
        self::start();
        session_regenerate_id(true);
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
    }

    // Đăng xuất người dùng (xóa thông tin đăng nhập khỏi session)
    public static function logout() {
        self::start();
        if (isset($_SESSION['username'])) {
            unset($_SESSION['username']);
        }
        if (isset($_SESSION['role'])) {
            unset($_SESSION['role']);
        }
        session_regenerate_id(true);
    }

    // Lấy tên đăng nhập của tài khoản hiện tại
    public static function getUsername() {
        self::start();
        return $_SESSION['username'] ?? null;
    }
} 
?>