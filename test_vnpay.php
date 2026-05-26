<?php
require_once 'App/Config/VNPay.php';

try {
    echo "Đang kiểm tra cấu hình VNPAY...\n";
    $config = VNPayConfig::$config;
    
    if (empty($config['vnp_TmnCode']) || empty($config['vnp_HashSecret']) || empty($config['vnp_Url'])) {
        throw new Exception("Các tham số cấu hình đang trống!");
    }
    echo "THÀNH CÔNG: Đã tải xong các tham số cấu hình.\n\n";

    echo "Đang kiểm tra băm chữ ký điện tử HMAC-SHA512...\n";
    $params = [
        "vnp_Version" => "2.1.0",
        "vnp_Command" => "pay",
        "vnp_TmnCode" => $config['vnp_TmnCode'],
        "vnp_Amount" => "1000000",
        "vnp_CreateDate" => "20260526080000",
        "vnp_CurrCode" => "VND",
        "vnp_IpAddr" => "127.0.0.1",
        "vnp_Locale" => "vn",
        "vnp_OrderInfo" => "Test Order Signature",
        "vnp_ReturnUrl" => $config['vnp_ReturnUrl'],
        "vnp_TxnRef" => "TEST_123456"
    ];

    ksort($params);
    $i = 0;
    $hashdata = "";
    foreach ($params as $key => $value) {
        if ($i == 1) {
            $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
        } else {
            $hashdata .= urlencode($key) . "=" . urlencode($value);
            $i = 1;
        }
    }

    $secret = $config['vnp_HashSecret'];
    $secureHash = hash_hmac('sha512', $hashdata, $secret);
    
    echo "Đã tạo mã băm an toàn thành công: \n" . $secureHash . "\n\n";
    
    if (strlen($secureHash) === 128) {
        echo "THÀNH CÔNG: Độ dài chuỗi băm chữ ký chính xác là 128 ký tự (SHA512 hợp lệ).\n";
    } else {
        throw new Exception("Độ dài chữ ký bảo mật không hợp lệ: " . strlen($secureHash));
    }
    
} catch (Exception $e) {
    echo "LỖI NGHIÊM TRỌNG: " . $e->getMessage() . "\n";
}
?>
