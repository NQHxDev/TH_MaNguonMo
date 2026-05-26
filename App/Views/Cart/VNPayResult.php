<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết Quả Thanh Toán - ZeionStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            background-color: #f6f8fb; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: #2D3748;
        }
        .navbar { 
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); 
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        .card { 
            border: none; 
            border-radius: 24px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.05); 
            background-color: #ffffff;
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
            background-color: #d1fae5;
            color: #10b981;
        }
        .status-danger {
            background-color: #fee2e2;
            color: #ef4444;
        }
        .btn-action {
            font-weight: 600;
            border-radius: 12px;
            padding: 12px 28px;
            transition: all 0.2s ease;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            color: #718096;
            font-size: 0.9rem;
        }
        .detail-val {
            font-weight: 600;
            color: #1a202c;
            font-size: 0.9rem;
            text-align: right;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark mb-5 py-3 shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3 d-flex align-items-center" href="/">
            <i class="bi bi-box-seam-fill me-2 text-warning"></i>ZeionStore
        </a>
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
                    <h3 class="fw-bold text-dark mb-2">Thanh Toán Thành Công</h3>
                    <p class="text-secondary small mb-4">Giao dịch qua VNPAY của bạn đã được xác thực và xử lý thành công.</p>
                    
                    <div class="text-start mb-4 bg-light p-4 rounded-4">
                        <h5 class="fw-bold text-dark mb-3">Chi tiết giao dịch</h5>
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
                            <span class="detail-val text-dark fw-bold">$<?= number_format($orderData['recipient']['amount_usd'] ?? 0, 2) ?></span>
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
                        <div class="text-start mb-4 bg-light p-4 rounded-4">
                            <h5 class="fw-bold text-dark mb-3">Thông tin nhận hàng</h5>
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

                    <a href="/" class="btn btn-success btn-action w-100 py-3 rounded-pill shadow-sm">
                        <i class="bi bi-house-door me-2"></i>Quay lại Trang chủ
                    </a>
                <?php else: ?>
                    <div class="status-icon status-danger">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-2">Thanh Toán Thất Bại</h3>
                    <p class="text-secondary small mb-4">Giao dịch đã bị hủy bỏ hoặc xảy ra lỗi trong quá trình xử lý.</p>

                    <div class="text-start mb-4 bg-light p-4 rounded-4">
                        <h5 class="fw-bold text-dark mb-3">Chi tiết lỗi</h5>
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
                        <a href="/cart" class="btn btn-outline-secondary btn-action w-50 py-3 rounded-pill">
                            Về giỏ hàng
                        </a>
                        <a href="/cart/checkout" class="btn btn-danger btn-action w-50 py-3 rounded-pill shadow-sm">
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
