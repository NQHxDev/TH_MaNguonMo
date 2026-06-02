<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch Sử Đơn Hàng - ZeionStore</title>
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
        .order-card { 
            border: 1px solid rgba(255, 255, 255, 0.06); 
            border-radius: 24px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.15); 
            background-color: rgba(17, 24, 39, 0.55);
            backdrop-filter: blur(10px);
            color: #f8fafc;
            margin-bottom: 24px;
            transition: all 0.3s ease;
        }
        .order-card:hover {
            border-color: rgba(56, 189, 248, 0.3);
            box-shadow: 0 12px 35px rgba(56, 189, 248, 0.08);
        }
        .order-header {
            background-color: rgba(255, 255, 255, 0.02);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            padding: 20px 24px;
            border-radius: 24px 24px 0 0;
        }
        .order-body {
            padding: 24px;
        }
        .table {
            background-color: transparent !important;
            --bs-table-bg: transparent !important;
            color: #f8fafc !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #94a3b8;
            padding: 12px 16px;
            background-color: transparent !important;
        }
        .table td {
            padding: 12px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            color: #cbd5e1;
            background-color: transparent !important;
        }
        .product-thumb {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            object-fit: cover;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            background: linear-gradient(135deg, #38bdf8, #818cf8);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 0.85rem;
        }
        .btn-back-shop {
            color: #94a3b8;
            font-weight: 600;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .btn-back-shop:hover {
            color: #38bdf8;
        }
        .btn[aria-expanded="true"] .bi-chevron-down {
            transform: rotate(180deg);
        }
        .btn .bi-chevron-down {
            transition: transform 0.2s ease;
            display: inline-block;
        }
        .badge-status {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 50px;
        }
        .collapse-custom {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s ease-out;
        }
        .collapse-custom.show {
            max-height: 2000px;
        }
        .badge-paid {
            background-color: rgba(16, 185, 129, 0.1);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .badge-pending {
            background-color: rgba(245, 158, 11, 0.1);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }
        .info-label {
            font-size: 0.8rem;
            color: #94a3b8;
            margin-bottom: 2px;
        }
        .info-value {
            font-size: 0.9rem;
            font-weight: 500;
            color: #f8fafc;
        }
        .total-badge-usd {
            color: #38bdf8;
            font-weight: 700;
            font-size: 1.25rem;
            text-shadow: 0 0 10px rgba(56, 189, 248, 0.15);
        }
        .total-badge-vnd {
            font-size: 0.8rem;
            color: #94a3b8;
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
                <a href="/cart/orders" class="nav-link text-info fw-semibold me-2 d-inline-block hover-opacity">
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
        <a href="/" class="btn-back-shop">
            <i class="bi bi-arrow-left me-1"></i> Quay lại cửa hàng
        </a>
    </div>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-white mb-1">
                <?= $isAdmin ? 'Quản lý đơn hàng (Admin)' : 'Đơn hàng của bạn' ?>
            </h2>
            <p class="text-secondary mb-0">
                <?= $isAdmin ? 'Xem toàn bộ danh sách đơn hàng đã mua trên hệ thống' : 'Theo dõi và xem lại lịch sử các đơn hàng đã thanh toán' ?>
            </p>
        </div>
        <div class="small text-secondary">
            Tổng số đơn hàng: <strong class="text-white"><?= count($orders) ?></strong>
        </div>
    </div>

    <?php if (empty($orders)): ?>
        <div class="card p-5 text-center text-muted rounded-4 border-0 shadow-sm bg-dark bg-opacity-25 border border-secondary border-opacity-10" style="border: 1px solid rgba(255, 255, 255, 0.06) !important;">
            <i class="bi bi-receipt-cutoff fs-1 d-block mb-3 text-secondary"></i>
            <h4 class="text-white">Không tìm thấy đơn hàng nào</h4>
            <p class="text-secondary mb-4">Bạn chưa thực hiện bất kỳ giao dịch mua sắm nào hoặc không có đơn hàng nào tồn tại.</p>
            <a href="/" class="btn px-4 py-2.5 rounded-pill d-inline-block" style="background: linear-gradient(135deg, #38bdf8, #4f46e5); color: white; border: none; font-weight: 600; text-decoration: none;">
                <i class="bi bi-shop me-2"></i>Mua sắm ngay
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-card card">
                <div class="order-header">
                    <div class="row align-items-center g-3">
                        <div class="col-md-3">
                            <span class="info-label d-block">MÃ GIAO DỊCH VNPay</span>
                            <span class="fw-bold text-warning small text-truncate d-block" title="<?= htmlspecialchars($order['vnpay_txn_ref']) ?>">
                                #<?= htmlspecialchars(explode('_', $order['vnpay_txn_ref'])[0] ?? $order['vnpay_txn_ref']) ?>
                            </span>
                        </div>
                        <div class="col-md-3 col-6">
                            <span class="info-label d-block">NGÀY ĐẶT</span>
                            <span class="info-value d-block"><?= date('H:i - d/m/Y', strtotime($order['created_at'])) ?></span>
                        </div>
                        <div class="col-md-2 col-6">
                            <span class="info-label d-block">TRẠNG THÁI</span>
                            <div>
                                <span class="badge-status badge-paid">
                                    <i class="bi bi-check-circle-fill me-1"></i>Đã thanh toán
                                </span>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 text-md-end">
                            <span class="info-label d-block">TỔNG TIỀN</span>
                            <span class="total-badge-usd d-block">$<?= number_format($order['total_usd']) ?></span>
                            <span class="total-badge-vnd d-block"><?= number_format($order['total_vnd']) ?> đ</span>
                        </div>
                        <div class="col-md-2 col-6 text-end">
                            <button class="btn btn-outline-light btn-sm px-3 rounded-pill fw-semibold" type="button" onclick="toggleOrder(<?= $order['id'] ?>, this)" aria-expanded="false">
                                Chi tiết <i class="bi bi-chevron-down ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="collapse-custom" id="order-detail-<?= $order['id'] ?>">
                    <div class="order-body">
                        <!-- Thông tin người nhận -->
                        <div class="row g-4 mb-4 pb-4 border-bottom border-secondary border-opacity-10">
                            <div class="col-md-4">
                                <span class="info-label d-block"><i class="bi bi-person me-1"></i> Người nhận</span>
                                <span class="info-value d-block text-white"><?= htmlspecialchars($order['customer_name']) ?></span>
                                <?php if ($isAdmin && !empty($order['username'])): ?>
                                    <span class="text-secondary small">Tài khoản đặt: <strong class="text-info"><?= htmlspecialchars($order['username']) ?></strong></span>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-3">
                                <span class="info-label d-block"><i class="bi bi-telephone me-1"></i> Số điện thoại</span>
                                <span class="info-value d-block text-white"><?= htmlspecialchars($order['customer_phone']) ?></span>
                            </div>
                            <div class="col-md-5">
                                <span class="info-label d-block"><i class="bi bi-geo-alt me-1"></i> Địa chỉ giao hàng</span>
                                <span class="info-value d-block text-white text-wrap"><?= htmlspecialchars($order['customer_address']) ?></span>
                            </div>
                        </div>

                        <!-- Danh sách sản phẩm -->
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 text-white">
                                <thead>
                                    <tr>
                                        <th class="border-0 px-0" style="min-width: 250px;">Sản phẩm</th>
                                        <th class="border-0 text-end" style="width: 150px;">Đơn giá</th>
                                        <th class="border-0 text-center" style="width: 100px;">SL</th>
                                        <th class="border-0 text-end" style="width: 150px;">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order['details'] as $detail): ?>
                                        <tr>
                                            <td class="px-0">
                                                <div class="d-flex align-items-center gap-3">
                                                    <?php if (!empty($detail['image'])): ?>
                                                        <img src="<?= htmlspecialchars($detail['image']) ?>" class="product-thumb" alt="<?= htmlspecialchars($detail['product_name']) ?>" onerror="this.onerror=null; this.src=''; this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                                                    <?php endif; ?>
                                                    <div class="product-thumb" style="display: <?= !empty($detail['image']) ? 'none' : 'inline-flex' ?>;">
                                                        <?= strtoupper(substr($detail['product_name'] ?? 'P', 0, 2)) ?>
                                                    </div>
                                                    <div>
                                                        <span class="fw-semibold text-white d-block text-truncate" style="max-width: 350px;">
                                                            <?= htmlspecialchars($detail['product_name']) ?>
                                                        </span>
                                                        <span class="text-secondary small">ID sản phẩm: <?= $detail['product_id'] ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end fw-semibold text-secondary">$<?= number_format($detail['price']) ?></td>
                                            <td class="text-center fw-bold text-white"><?= $detail['quantity'] ?></td>
                                            <td class="text-end fw-bold text-info">$<?= number_format($detail['price'] * $detail['quantity']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 pt-3 d-flex justify-content-between align-items-center bg-dark bg-opacity-20 p-3 rounded-3 border border-secondary border-opacity-10">
                            <div class="small text-secondary">
                                Ngân hàng thanh toán: <strong class="text-white"><?= htmlspecialchars($order['bank_code'] ?? 'N/A') ?></strong>
                            </div>
                            <div class="small text-secondary">
                                Tổng cộng VNPay: <strong class="text-warning fs-6"><?= number_format($order['total_vnd']) ?> đ</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
function toggleOrder(orderId, btn) {
    const el = document.getElementById('order-detail-' + orderId);
    if (el) {
        const isShow = el.classList.contains('show');
        if (isShow) {
            el.classList.remove('show');
            btn.setAttribute('aria-expanded', 'false');
        } else {
            el.classList.add('show');
            btn.setAttribute('aria-expanded', 'true');
        }
    }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
