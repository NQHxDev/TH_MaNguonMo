<?php
require_once __DIR__ . '/../App/Config/Database.php';

try {
    echo "Đang thử kết nối cơ sở dữ liệu...\n";
    $db = Database::getConnection();
    echo "THÀNH CÔNG: Đã kết nối cơ sở dữ liệu thành công!\n\n";
    
    $tables = ['category', 'product', 'product_image'];
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "THÀNH CÔNG: Bảng '{$table}' tồn tại.\n";
            if ($table === 'category') {
                $count = $db->query("SELECT COUNT(*) FROM category")->fetchColumn();
                echo "THÔNG TIN: Số lượng danh mục: {$count}\n";
            }
            if ($table === 'product') {
                $count = $db->query("SELECT COUNT(*) FROM product")->fetchColumn();
                echo "THÔNG TIN: Số lượng sản phẩm: {$count}\n";
            }
            if ($table === 'product_image') {
                $count = $db->query("SELECT COUNT(*) FROM product_image")->fetchColumn();
                echo "THÔNG TIN: Số lượng ảnh phụ: {$count}\n";
            }
            echo "\n";
        } else {
            echo "CẢNH BÁO: Bảng '{$table}' KHÔNG tồn tại. Vui lòng kiểm tra lại quá trình nhập database.\n\n";
        }
    }

    echo "Đang kiểm tra thư mục 'uploads/'...\n";
    $uploadsDir = __DIR__ . '/../uploads';
    if (is_dir($uploadsDir)) {
        echo "THÀNH CÔNG: Thư mục 'uploads/' tồn tại.\n";
        $files = array_diff(scandir($uploadsDir), array('.', '..'));
        echo "THÔNG TIN: Số lượng tệp đã tải lên: " . count($files) . "\n";
    } else {
        echo "THÔNG TIN: Thư mục 'uploads/' chưa tồn tại (sẽ tự động được tạo khi tải lên ảnh đầu tiên).\n";
    }
} catch (Exception $e) {
    echo "LỖI NGHIÊM TRỌNG: " . $e->getMessage() . "\n";
}
?>
