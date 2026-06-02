<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng Của Bạn - ZeionStore</title>
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
        .table {
            background-color: transparent !important;
            --bs-table-bg: transparent !important;
            color: #f8fafc !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        .table thead { 
            background-color: rgba(255, 255, 255, 0.02) !important; 
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        }
        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #94a3b8;
            padding: 16px;
            background-color: transparent !important;
        }
        .table td {
            padding: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            color: #cbd5e1;
            background-color: transparent !important;
        }
        .product-thumb {
            width: 54px;
            height: 54px;
            border-radius: 12px;
            object-fit: cover;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            background: linear-gradient(135deg, #38bdf8, #818cf8);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        .qty-input {
            width: 70px;
            background-color: rgba(15, 23, 42, 0.6);
            border: 1.5px solid rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            padding: 6px;
            text-align: center;
            font-weight: 600;
            color: #f8fafc;
            transition: all 0.2s ease;
        }
        .qty-input:focus {
            outline: none;
            border-color: #38bdf8;
            box-shadow: 0 0 10px rgba(56, 189, 248, 0.25);
        }
        .btn-update-cart {
            border: 1.5px solid rgba(255, 255, 255, 0.15);
            color: #f8fafc;
            background: transparent;
            font-weight: 600;
            border-radius: 14px;
            transition: all 0.2s ease;
            padding: 10px 20px;
        }
        .btn-update-cart:hover {
            border-color: #38bdf8;
            background-color: rgba(56, 189, 248, 0.08);
            color: #38bdf8;
        }
        .btn-checkout {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 14px;
            transition: all 0.2s ease;
            padding: 12px 24px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }
        .btn-checkout:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.45);
            transform: translateY(-1px);
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
        .summary-title {
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
        }
        .summary-val {
            font-weight: 700;
            font-size: 1.5rem;
            color: #38bdf8;
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
        <a href="/" class="btn-back-shop">
            <i class="bi bi-arrow-left me-1"></i> Tiếp tục mua sắm
        </a>
    </div>

    <h2 class="fw-bold text-white mb-4">Giỏ hàng của bạn</h2>



    <?php if (empty($cart)): ?>
        <div class="card p-5 text-center text-muted rounded-4">
            <i class="bi bi-cart-x fs-1 d-block mb-3 text-secondary"></i>
            <h4 class="text-white">Giỏ hàng của bạn đang trống</h4>
            <p class="text-secondary mb-4">Có vẻ như bạn chưa thêm sản phẩm nào vào giỏ hàng.</p>
            <a href="/" class="btn btn-add px-4 py-2.5 rounded-pill d-inline-block" style="background: linear-gradient(135deg, #38bdf8, #4f46e5); color: white; border: none; font-weight: 600; text-decoration: none;">
                <i class="bi bi-shop me-2"></i>Quay lại Cửa hàng
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card p-4">
                    <form method="post" action="/cart/update">
                        <div class="table-responsive">
                            <table class="table align-middle text-white">
                                <thead>
                                    <tr>
                                        <th class="border-0 px-0">Sản phẩm</th>
                                        <th class="border-0 text-end">Đơn giá</th>
                                        <th class="border-0 text-center" style="width: 120px;">Số lượng</th>
                                        <th class="border-0 text-end">Thành tiền</th>
                                        <th class="border-0 text-center" style="width: 60px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $cartTotal = 0;
                                    foreach ($cart as $id => $item):
                                        $subtotal = $item['price'] * $item['quantity'];
                                        $cartTotal += $subtotal;
                                    ?>
                                        <tr>
                                            <td class="px-0">
                                                <div class="d-flex align-items-center gap-3">
                                                    <?php if ($item['image']): ?>
                                                        <img src="<?= htmlspecialchars($item['image']) ?>" class="product-thumb" alt="<?= htmlspecialchars($item['name']) ?>" onerror="this.onerror=null; this.src=''; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <?php endif; ?>
                                                    <div class="product-thumb" style="display: <?= $item['image'] ? 'none' : 'flex' ?>;">
                                                        <?= strtoupper(substr($item['name'] ?? 'P', 0, 2)) ?>
                                                    </div>
                                                    <div>
                                                        <span class="fw-semibold text-white d-block text-truncate" style="max-width: 200px;"><?= htmlspecialchars($item['name']) ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end fw-semibold text-secondary">$<?= number_format($item['price']) ?></td>
                                            <td class="text-center">
                                                <input type="number" name="quantities[<?= $id ?>]" value="<?= $item['quantity'] ?>" min="1" max="99" class="qty-input">
                                            </td>
                                            <td class="text-end fw-bold text-white">$<?= number_format($subtotal) ?></td>
                                            <td class="text-center">
                                                <a href="/cart/remove/<?= $id ?>" class="text-danger fs-5 hover-scale d-inline-block" title="Xóa sản phẩm">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-start mt-3">
                            <button type="submit" class="btn btn-update-cart">
                                <i class="bi bi-arrow-clockwise me-1"></i> Cập nhật số lượng
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card p-4">
                    <h5 class="fw-bold mb-4 text-white">Tóm tắt đơn hàng</h5>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-secondary">Tạm tính</span>
                        <span class="fw-semibold text-secondary">$<?= number_format($cartTotal) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-secondary">Phí vận chuyển</span>
                        <span class="text-success fw-semibold">MIỄN PHÍ</span>
                    </div>
                    <hr class="my-3 border-secondary border-opacity-35">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="summary-title">Tổng tiền</span>
                        <span class="summary-val">$<?= number_format($cartTotal) ?></span>
                    </div>
                    <a href="/cart/checkout" class="btn btn-checkout w-100 py-3 d-flex align-items-center justify-content-center gap-2" style="text-decoration: none;">
                        <i class="bi bi-credit-card"></i> Tiến hành thanh toán
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
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
