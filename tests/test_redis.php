<?php
require_once __DIR__ . '/../App/Config/Redis.php';

try {
    echo "Đang thử kết nối máy chủ Redis (Memurai) cục bộ...\n";
    $redis = new RedisClient();
    
    $key = 'test:key_' . uniqid();
    $val = 'Memurai Redis đang hoạt động hoàn hảo!';
    
    echo "Đang đặt khóa '{$key}'...\n";
    $redis->setex($key, 60, $val);
    echo "THÀNH CÔNG: Khóa được thiết lập với thời gian hết hạn là 60 giây.\n\n";
    
    echo "Đang đọc khóa '{$key}'...\n";
    $res = $redis->get($key);
    echo "Kết quả: '{$res}'\n";
    
    if ($res === $val) {
        echo "THÀNH CÔNG: Giá trị đọc khớp với giá trị ghi!\n\n";
    } else {
        echo "LỖI: Giá trị đọc không khớp!\n\n";
    }
    
    echo "Đang xóa khóa '{$key}'...\n";
    $redis->del($key);
    
    $check = $redis->get($key);
    if ($check === null) {
        echo "THÀNH CÔNG: Đã xóa khóa khỏi Redis thành công.\n\n";
    } else {
        echo "LỖI: Khóa vẫn còn tồn tại!\n\n";
    }
    
} catch (Exception $e) {
    echo "LỖI NGHIÊM TRỌNG: " . $e->getMessage() . "\n";
}
?>
