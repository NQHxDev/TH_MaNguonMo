<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán - ZeionStore</title>
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
        .form-label { 
            font-weight: 600; 
            color: #cbd5e1; 
            font-size: 0.9rem;
            margin-bottom: 8px;
        }
        .input-group-text { 
            background-color: rgba(15, 23, 42, 0.4); 
            border: 1.5px solid rgba(255, 255, 255, 0.12);
            border-right: none; 
            color: #94a3b8; 
        }
        .form-control { 
            background-color: rgba(15, 23, 42, 0.6);
            border: 1.5px solid rgba(255, 255, 255, 0.12);
            border-left: none; 
            padding: 11px 14px;
            color: #f8fafc;
        }
        .form-control::placeholder {
            color: #64748b;
        }
        .form-control:focus { 
            box-shadow: none; 
            background-color: rgba(15, 23, 42, 0.7);
            border-color: #38bdf8; 
            color: #f8fafc;
        }
        .input-group:focus-within .input-group-text { 
            border-color: #38bdf8; 
            color: #38bdf8; 
        }
        .input-group:focus-within .form-control { 
            border-color: #38bdf8; 
        }
        .form-check {
            background-color: rgba(15, 23, 42, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            transition: all 0.2s ease;
        }
        .form-check:focus-within {
            border-color: #38bdf8 !important;
        }
        .btn-order {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 14px;
            transition: all 0.2s ease;
            padding: 14px 24px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }
        .btn-order:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.45);
            transform: translateY(-1px);
        }
        .btn-back-cart {
            color: #94a3b8;
            font-weight: 600;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .btn-back-cart:hover {
            color: #38bdf8;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .item-row:last-child {
            border-bottom: none;
        }
        .text-vnd-highlight {
            color: #38bdf8;
            font-weight: 700;
            text-shadow: 0 0 10px rgba(56, 189, 248, 0.2);
        }
        .toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 380px !important;
            max-width: calc(100vw - 48px) !important;
            z-index: 9999;
        }
        .custom-toast {
            width: 100% !important;
            background: rgba(15, 23, 42, 0.95) !important;
            backdrop-filter: blur(12px) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: 16px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5) !important;
            padding: 16px !important;
            margin-bottom: 12px !important;
            transition: all 0.3s ease !important;
        }
        .custom-toast.toast-success {
            border-left: 4px solid #10b981 !important;
        }
        .custom-toast.toast-error {
            border-left: 4px solid #ef4444 !important;
        }
        .toast-icon-success {
            color: #10b981 !important;
        }
        .toast-icon-error {
            color: #ef4444 !important;
        }
        .toast-text {
            color: #f8fafc !important;
            font-weight: 500 !important;
            font-size: 0.9rem !important;
            line-height: 1.5;
        }
    </style>
</head>
<body>

<div class="toast-container">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="toast show custom-toast toast-success" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill toast-icon-success fs-5"></i>
                    <span class="toast-text"><?= $_SESSION['success']; unset($_SESSION['success']); ?></span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="toast show custom-toast toast-error" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill toast-icon-error fs-5"></i>
                    <span class="toast-text"><?= $_SESSION['error']; unset($_SESSION['error']); ?></span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>
</div>

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

<div class="container mb-5">
    <div class="mb-4">
        <a href="/cart" class="btn-back-cart">
            <i class="bi bi-arrow-left me-1"></i> Quay lại giỏ hàng
        </a>
    </div>

    <h2 class="fw-bold text-white mb-4">Thông Tin Giao Hàng & Thanh Toán</h2>



    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card p-4 p-md-5">
                <h4 class="fw-bold text-white mb-4"><i class="bi bi-truck me-2 text-primary"></i>Địa chỉ nhận hàng</h4>
                <form method="post" action="/cart/placeOrder">
                    <div class="mb-3">
                        <label for="name" class="form-label">Họ và tên</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Nhập họ và tên người nhận" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input type="tel" name="phone" id="phone" class="form-control" placeholder="Nhập số điện thoại người nhận" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Địa chỉ chi tiết</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                            <input type="text" name="address" id="address" class="form-control" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label d-block text-white fw-bold">Phương thức thanh toán</label>
                        <div class="form-check p-3 border rounded-3 mb-2 d-flex align-items-center justify-content-between" style="opacity: 0.5;">
                            <div>
                                <input class="form-check-input ms-0 me-2" type="radio" name="payment_method" id="payment_cod" value="cod" disabled>
                                <label class="form-check-label fw-semibold text-secondary" for="payment_cod">
                                    Thanh toán khi nhận hàng<span class="small text-danger ms-1"></span>
                                </label>
                            </div>
                            <span class="text-secondary"><i class="bi bi-cash-coin fs-5"></i></span>
                        </div>
                        <div class="form-check p-3 border rounded-3 d-flex align-items-center justify-content-between">
                            <div>
                                <input class="form-check-input ms-0 me-2" type="radio" name="payment_method" id="payment_vnpay" value="vnpay" checked>
                                <label class="form-check-label fw-semibold text-white" for="payment_vnpay">
                                    Cổng thanh toán điện tử VNPAY
                                </label>
                            </div>
                            <span class="text-primary"><i class="bi bi-credit-card-2-front fs-5 text-indigo" style="color: #38bdf8;"></i></span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-order w-100 py-3 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-shield-check"></i> Xác nhận đặt hàng
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card p-4">
                <h5 class="fw-bold mb-4 text-white">Sản phẩm đặt mua</h5>
                <div class="mb-4">
                    <?php 
                    $orderTotal = 0;
                    foreach ($cart as $id => $item): 
                        $subtotal = $item['price'] * $item['quantity'];
                        $orderTotal += $subtotal;
                    ?>
                        <div class="item-row">
                            <div style="max-width: 250px;">
                                <span class="fw-semibold text-white text-truncate d-block small"><?= htmlspecialchars($item['name']) ?></span>
                                <span class="text-secondary small">SL: <?= $item['quantity'] ?> x $<?= number_format($item['price']) ?></span>
                            </div>
                            <span class="fw-bold text-white text-end small">$<?= number_format($subtotal) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <hr class="my-3 border-secondary border-opacity-35">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-secondary">Tạm tính (USD)</span>
                    <span class="fw-semibold text-white">$<?= number_format($orderTotal) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-secondary">Quy đổi sang VND</span>
                    <span class="fw-bold text-vnd-highlight"><?= number_format($orderTotal * VNPayConfig::$config['exchange_rate']) ?> VND</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-secondary">Phí giao hàng</span>
                    <span class="text-success fw-semibold">MIỄN PHÍ</span>
                </div>
                <hr class="my-3 border-secondary border-opacity-35">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-uppercase fw-bold text-secondary small">Tổng thanh toán</span>
                    <span class="fw-bold text-primary fs-3">$<?= number_format($orderTotal) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toasts = document.querySelectorAll('.toast');
    toasts.forEach(toastEl => {
        setTimeout(() => {
            const bsToast = bootstrap.Toast.getOrCreateInstance(toastEl);
            if (bsToast) bsToast.hide();
        }, 4000);
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
