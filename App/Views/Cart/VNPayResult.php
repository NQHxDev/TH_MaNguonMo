<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết Quả Thanh Toán - ZeionStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            background-color: #080c14; 
            font-family: 'Outfit', sans-serif; 
            color: #f8fafc;
            background-image: radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.05) 0%, transparent 40%), radial-gradient(circle at 90% 80%, rgba(168, 85, 247, 0.05) 0%, transparent 40%);
            background-attachment: fixed;
        }
        .navbar { 
            background: rgba(8, 12, 20, 0.85); 
            backdrop-filter: blur(16px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }
        .card { 
            border: 1px solid rgba(255, 255, 255, 0.06); 
            border-radius: 24px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.15); 
            background-color: rgba(17, 24, 39, 0.55);
            backdrop-filter: blur(10px);
            color: #f8fafc;
        }
        .status-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            margin-bottom: 20px;
        }
        .status-success {
            background-color: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .status-danger {
            background-color: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .btn-action {
            font-weight: 600;
            border-radius: 14px;
            padding: 12px 28px;
            transition: all 0.2s ease;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            color: #94a3b8;
            font-size: 0.9rem;
        }
        .detail-val {
            font-weight: 600;
            color: #f8fafc;
            font-size: 0.9rem;
            text-align: right;
        }
        .bg-light-dark {
            background-color: rgba(15, 23, 42, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark mb-5 py-3 shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3 d-flex align-items-center" href="/">
            <i class="bi bi-box-seam-fill me-2 text-warning"></i>ZeionStore
        </a>
        <div class="d-flex align-items-center gap-3">
            <?php if (SessionHelper::isLoggedIn()): ?>
                <span class="text-secondary small d-none d-md-inline-block">
                    Xin chào, <strong class="text-white"><?= htmlspecialchars(SessionHelper::getUsername()) ?></strong> 
                    <span class="badge bg-secondary ms-1 text-uppercase" style="font-size: 0.65rem;"><?= htmlspecialchars(SessionHelper::getRole()) ?></span>
                </span>
                <a href="/cart/orders" class="nav-link text-white fw-semibold me-2 d-inline-block hover-opacity">
                    <i class="bi bi-receipt me-1"></i>Đơn hàng
                </a>
                <?php if (SessionHelper::isAdmin()): ?>
                    <a href="/product/list" class="nav-link text-white fw-semibold hover-opacity">
                        <i class="bi bi-gear-fill me-1"></i>Trang quản trị
                    </a>
                <?php endif; ?>
                <a href="/account/logout" class="btn btn-outline-danger btn-sm px-3 rounded-pill fw-semibold text-decoration-none">
                    <i class="bi bi-box-arrow-right me-1"></i>Đăng xuất
                </a>
            <?php else: ?>
                <a href="/account/login" class="nav-link text-white fw-semibold hover-opacity">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập
                </a>
                <a href="/account/register" class="btn btn-outline-info btn-sm px-3 rounded-pill fw-semibold text-decoration-none">
                    <i class="bi bi-person-plus me-1"></i>Đăng ký
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container py-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card p-4 p-md-5 text-center">
                <?php if ($orderData['success']): ?>
                    <div class="status-icon status-success">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <h3 class="fw-bold text-white mb-2">Thanh Toán Thành Công</h3>
                    <p class="text-secondary small mb-4">Giao dịch qua VNPAY của bạn đã được xác thực và xử lý thành công.</p>
                    
                    <div class="text-start mb-4 bg-light-dark p-4 rounded-4">
                        <h5 class="fw-bold text-white mb-3">Chi tiết giao dịch</h5>
                        <div class="detail-row">
                            <span class="detail-label">Mã đơn hàng VNPAY</span>
                            <span class="detail-val"><?= htmlspecialchars($orderData['txnRef']) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Số tiền thanh toán</span>
                            <span class="detail-val text-success fw-bold"><?= number_format($orderData['amount'] / 100) ?> VND</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Giá trị USD tương đương</span>
                            <span class="detail-val text-primary fw-bold">$<?= number_format($orderData['recipient']['amount_usd'] ?? 0) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Ngân hàng giao dịch</span>
                            <span class="detail-val"><?= htmlspecialchars($orderData['bankCode']) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Thời gian thanh toán</span>
                            <span class="detail-val"><?= htmlspecialchars(date("Y-m-d H:i:s", strtotime($orderData['payDate']))) ?></span>
                        </div>
                    </div>

                    <?php if (!empty($orderData['recipient'])): ?>
                        <div class="text-start mb-4 bg-light-dark p-4 rounded-4">
                            <h5 class="fw-bold text-white mb-3">Thông tin nhận hàng</h5>
                            <div class="detail-row">
                                <span class="detail-label">Tên người nhận</span>
                                <span class="detail-val"><?= htmlspecialchars($orderData['recipient']['name']) ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Số điện thoại</span>
                                <span class="detail-val"><?= htmlspecialchars($orderData['recipient']['phone']) ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Địa chỉ chi tiết</span>
                                <span class="detail-val text-wrap" style="max-width: 250px;"><?= htmlspecialchars($orderData['recipient']['address']) ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <a href="/" class="btn btn-success btn-action w-100 py-3 rounded-pill shadow-sm" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">
                        <i class="bi bi-house-door me-2"></i>Quay lại Trang chủ
                    </a>
                <?php else: ?>
                    <div class="status-icon status-danger">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <h3 class="fw-bold text-white mb-2">Thanh Toán Thất Bại</h3>
                    <p class="text-secondary small mb-4">Giao dịch đã bị hủy bỏ hoặc xảy ra lỗi trong quá trình xử lý.</p>

                    <div class="text-start mb-4 bg-light-dark p-4 rounded-4">
                        <h5 class="fw-bold text-white mb-3">Chi tiết lỗi</h5>
                        <div class="detail-row">
                            <span class="detail-label">Mã giao dịch VNPAY</span>
                            <span class="detail-val"><?= htmlspecialchars($orderData['txnRef']) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Mã phản hồi</span>
                            <span class="detail-val text-danger fw-bold"><?= htmlspecialchars($orderData['responseCode']) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Thông báo từ VNPAY</span>
                            <span class="detail-val text-secondary">Giao dịch không thành công hoặc đã bị hủy.</span>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <a href="/cart" class="btn btn-outline-secondary btn-action w-50 py-3 rounded-pill text-white border-secondary">
                            Về giỏ hàng
                        </a>
                        <a href="/cart/checkout" class="btn btn-danger btn-action w-50 py-3 rounded-pill shadow-sm border-0" style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);">
                            Thực hiện lại
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
