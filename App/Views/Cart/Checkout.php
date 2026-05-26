<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán - ZeionStore</title>
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
            border-radius: 20px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.03); 
            background-color: #ffffff;
        }
        .form-label { 
            font-weight: 600; 
            color: #4a5568; 
            font-size: 0.9rem;
            margin-bottom: 8px;
        }
        .input-group-text { 
            background-color: #f8fafc; 
            border-right: none; 
            color: #94a3b8; 
            border-color: #e2e8f0;
        }
        .form-control { 
            border-left: none; 
            border-color: #e2e8f0;
            padding: 11px 14px;
            color: #334155;
        }
        .form-control:focus { 
            box-shadow: none; 
            border-color: #7c3aed; 
        }
        .input-group:focus-within .input-group-text { 
            border-color: #7c3aed; 
            color: #7c3aed; 
        }
        .input-group:focus-within .form-control { 
            border-color: #7c3aed; 
        }
        .btn-order {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            transition: all 0.2s ease;
            padding: 14px 24px;
        }
        .btn-order:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            box-shadow: 0 6px 15px rgba(16, 185, 129, 0.3);
            transform: translateY(-1px);
        }
        .btn-back-cart {
            color: #64748b;
            font-weight: 600;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .btn-back-cart:hover {
            color: #4f46e5;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .item-row:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark mb-5 py-3 shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3 d-flex align-items-center" href="/">
            <i class="bi bi-box-seam-fill me-2 text-warning"></i>ZeionStore
        </a>
        <a href="/product/list" class="nav-link text-white fw-semibold hover-opacity">
            <i class="bi bi-gear-fill me-1"></i>Trang quản trị
        </a>
    </div>
</nav>

<div class="container mb-5">
    <div class="mb-4">
        <a href="/cart" class="btn-back-cart">
            <i class="bi bi-arrow-left me-1"></i> Quay lại giỏ hàng
        </a>
    </div>

    <h2 class="fw-bold text-dark mb-4">Thông Tin Giao Hàng & Thanh Toán</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 p-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2 text-danger"></i>
                <div>
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card p-4 p-md-5">
                <h4 class="fw-bold text-dark mb-4"><i class="bi bi-truck me-2 text-primary"></i>Địa chỉ nhận hàng</h4>
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
                        <label class="form-label d-block text-dark fw-bold">Phương thức thanh toán</label>
                        <div class="form-check p-3 border rounded-3 mb-2 d-flex align-items-center justify-content-between">
                            <div>
                                <input class="form-check-input ms-0 me-2" type="radio" name="payment_method" id="payment_cod" value="cod" checked>
                                <label class="form-check-label fw-semibold text-dark" for="payment_cod">
                                    Thanh toán khi nhận hàng (COD)
                                </label>
                            </div>
                            <span class="text-secondary"><i class="bi bi-cash-coin fs-5"></i></span>
                        </div>
                        <div class="form-check p-3 border rounded-3 d-flex align-items-center justify-content-between">
                            <div>
                                <input class="form-check-input ms-0 me-2" type="radio" name="payment_method" id="payment_vnpay" value="vnpay">
                                <label class="form-check-label fw-semibold text-dark" for="payment_vnpay">
                                    Cổng thanh toán điện tử VNPAY (Sandbox)
                                </label>
                            </div>
                            <span class="text-primary"><i class="bi bi-credit-card-2-front fs-5 text-indigo" style="color: #4f46e5;"></i></span>
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
                <h5 class="fw-bold mb-4 text-dark">Sản phẩm đặt mua</h5>
                <div class="mb-4">
                    <?php 
                    $orderTotal = 0;
                    foreach ($cart as $id => $item): 
                        $subtotal = $item['price'] * $item['quantity'];
                        $orderTotal += $subtotal;
                    ?>
                        <div class="item-row">
                            <div style="max-width: 250px;">
                                <span class="fw-semibold text-dark text-truncate d-block small"><?= htmlspecialchars($item['name']) ?></span>
                                <span class="text-secondary small">SL: <?= $item['quantity'] ?> x $<?= number_format($item['price'], 2) ?></span>
                            </div>
                            <span class="fw-bold text-dark text-end small">$<?= number_format($subtotal, 2) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <hr class="my-3 border-light">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-secondary">Tạm tính (USD)</span>
                    <span class="fw-semibold text-dark">$<?= number_format($orderTotal, 2) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-secondary">Quy đổi sang VND</span>
                    <span class="fw-bold text-dark"><?= number_format($orderTotal * VNPayConfig::$config['exchange_rate']) ?> VND</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-secondary">Phí giao hàng</span>
                    <span class="text-success fw-semibold">MIỄN PHÍ</span>
                </div>
                <hr class="my-3 border-light">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-uppercase fw-bold text-secondary small">Tổng thanh toán</span>
                    <span class="fw-bold text-primary fs-3">$<?= number_format($orderTotal, 2) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
