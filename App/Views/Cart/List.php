<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng Của Bạn - ZeionStore</title>
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
        .table thead { 
            background-color: #f8fafc; 
            border-bottom: 2px solid #edf2f7;
        }
        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #718096;
            padding: 16px;
        }
        .table td {
            padding: 16px;
            border-bottom: 1px solid #f1f5f9;
        }
        .product-thumb {
            width: 54px;
            height: 54px;
            border-radius: 12px;
            object-fit: cover;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            background: linear-gradient(135deg, #6366f1, #a855f7);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        .qty-input {
            width: 70px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 6px;
            text-align: center;
            font-weight: 600;
            color: #334155;
            transition: all 0.2s ease;
        }
        .qty-input:focus {
            outline: none;
            border-color: #7c3aed;
        }
        .btn-update-cart {
            border: 2px solid #e2e8f0;
            color: #4f46e5;
            background: transparent;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.2s ease;
            padding: 10px 20px;
        }
        .btn-update-cart:hover {
            border-color: #4f46e5;
            background-color: #f5f3ff;
        }
        .btn-checkout {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            transition: all 0.2s ease;
            padding: 12px 24px;
        }
        .btn-checkout:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            box-shadow: 0 6px 15px rgba(16, 185, 129, 0.3);
            transform: translateY(-1px);
        }
        .btn-back-shop {
            color: #64748b;
            font-weight: 600;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .btn-back-shop:hover {
            color: #4f46e5;
        }
        .summary-title {
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #718096;
        }
        .summary-val {
            font-weight: 700;
            font-size: 1.5rem;
            color: #1a202c;
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
        <a href="/" class="btn-back-shop">
            <i class="bi bi-arrow-left me-1"></i> Tiếp tục mua sắm
        </a>
    </div>

    <h2 class="fw-bold text-dark mb-4">Giỏ hàng của bạn</h2>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 p-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-5 me-2 text-success"></i>
                <div>
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($cart)): ?>
        <div class="card p-5 text-center text-muted rounded-4">
            <i class="bi bi-cart-x fs-1 d-block mb-3 text-secondary"></i>
            <h4>Giỏ hàng của bạn đang trống</h4>
            <p class="mb-4">Có vẻ như bạn chưa thêm sản phẩm nào vào giỏ hàng.</p>
            <a href="/" class="btn btn-add px-4 py-2.5 rounded-pill d-inline-block" style="background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; border: none; font-weight: 600; text-decoration: none;">
                <i class="bi bi-shop me-2"></i>Quay lại Cửa hàng
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card p-4">
                    <form method="post" action="/cart/update">
                        <div class="table-responsive">
                            <table class="table align-middle">
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
                                                        <span class="fw-semibold text-dark d-block text-truncate" style="max-width: 200px;"><?= htmlspecialchars($item['name']) ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end fw-semibold text-secondary">$<?= number_format($item['price'], 2) ?></td>
                                            <td class="text-center">
                                                <input type="number" name="quantities[<?= $id ?>]" value="<?= $item['quantity'] ?>" min="1" max="99" class="qty-input">
                                            </td>
                                            <td class="text-end fw-bold text-dark">$<?= number_format($subtotal, 2) ?></td>
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
                    <h5 class="fw-bold mb-4 text-dark">Tóm tắt đơn hàng</h5>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-secondary">Tạm tính</span>
                        <span class="fw-semibold text-dark">$<?= number_format($cartTotal, 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-secondary">Phí vận chuyển</span>
                        <span class="text-success fw-semibold">MIỄN PHÍ</span>
                    </div>
                    <hr class="my-3 border-light">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="summary-title">Tổng tiền</span>
                        <span class="summary-val">$<?= number_format($cartTotal, 2) ?></span>
                    </div>
                    <a href="/cart/checkout" class="btn btn-checkout w-100 py-3 d-flex align-items-center justify-content-center gap-2" style="text-decoration: none;">
                        <i class="bi bi-credit-card"></i> Tiến hành thanh toán
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
