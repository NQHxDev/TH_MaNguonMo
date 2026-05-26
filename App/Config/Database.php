<?php

class Database {
    private static ?PDO $connection = null;

    public static function getConnection(): PDO {
        if (self::$connection === null) {
            $host = 'localhost';
            $dbname = 'production';
            $username = 'root';
            $password = '';
            
            $ports = ['3306', '3636'];
            $connected = false;
            $lastException = null;

            foreach ($ports as $port) {
                try {
                    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
                    $options = [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ];
                    self::$connection = new PDO($dsn, $username, $password, $options);
                    $connected = true;
                    break;
                } catch (PDOException $e) {
                    $lastException = $e;
                }
            }

            if (!$connected && $lastException) {
                die("<div style='font-family: sans-serif; padding: 20px; border: 1px solid #ffccd5; background-color: #fff0f2; border-radius: 8px; max-width: 600px; margin: 50px auto;'>" .
                    "<h3 style='color: #d9534f; margin-top: 0;'>Kết nối Cơ sở dữ liệu thất bại</h3>" .
                    "<p>Không thể kết nối đến máy chủ MySQL. Lỗi: <code>" . htmlspecialchars($lastException->getMessage()) . "</code></p>" .
                    "<h4>Hướng dẫn khắc phục:</h4>" .
                    "<ol>" .
                    "<li>Mở <strong>phpMyAdmin</strong> (thường tại <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a> hoặc qua bảng điều khiển Laragon).</li>" .
                    "<li>Tạo một cơ sở dữ liệu mới với tên là <code>" . htmlspecialchars($dbname) . "</code>.</li>" .
                    "<li>Nhập (Import) tệp tin <code>zeion_store.sql</code> nằm tại thư mục gốc của dự án.</li>" .
                    "<li>Đảm bảo rằng máy chủ MySQL của bạn đang chạy trong Laragon.</li>" .
                    "</ol>" .
                    "</div>");
            }
        }

        return self::$connection;
    }
}
?>
