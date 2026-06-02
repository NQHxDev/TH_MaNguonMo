<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZeionStore - Cửa Hàng Công Nghệ Cao Cấp</title>
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
        .cart-link {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background-color: rgba(255, 255, 255, 0.05);
            color: #f8fafc;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .cart-link:hover {
            background-color: rgba(56, 189, 248, 0.15);
            color: #38bdf8;
            border-color: rgba(56, 189, 248, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(56, 189, 248, 0.2);
        }
        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: linear-gradient(135deg, #f43f5e, #e11d48);
            color: white;
            font-size: 0.65rem;
            font-weight: 800;
            padding: 3px 7px;
            border-radius: 50px;
            box-shadow: 0 4px 10px rgba(225, 29, 72, 0.4);
            border: 2px solid #080c14;
        }
        .hero-section {
            background: linear-gradient(135deg, rgba(22, 28, 45, 0.6) 0%, rgba(30, 41, 59, 0.6) 100%);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 28px;
            padding: 56px 32px;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
        }
        .product-card {
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 28px;
            overflow: hidden;
            background-color: rgba(17, 24, 39, 0.55);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(56, 189, 248, 0.15) !important;
            border-color: rgba(56, 189, 248, 0.4);
        }
        .card-img-wrapper {
            padding: 16px 16px 0 16px;
        }
        .card-img-container {
            position: relative;
            height: 210px;
            background-color: rgba(15, 23, 42, 0.3);
            overflow: hidden;
            cursor: pointer;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .product-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .product-card:hover .product-img {
            transform: scale(1.06);
        }
        .img-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(8, 12, 20, 0.55);
            backdrop-filter: blur(6px);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 2;
        }
        .card-img-container:hover .img-overlay {
            opacity: 1;
        }
        .btn-quickview {
            background: linear-gradient(135deg, #38bdf8, #4f46e5);
            color: white;
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 50px;
            border: none;
            box-shadow: 0 5px 15px rgba(56, 189, 248, 0.3);
            transition: all 0.3s ease;
            font-size: 0.85rem;
        }
        .btn-quickview:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(56, 189, 248, 0.5);
        }
        .placeholder-img {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            font-size: 2.2rem;
            color: #ffffff;
            font-weight: 700;
            background: linear-gradient(135deg, #38bdf8, #818cf8);
        }
        .category-badge {
            position: absolute;
            top: 14px;
            left: 14px;
            font-size: 0.6rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 30px;
            z-index: 3;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .badge-cat-1 { background-color: rgba(56, 189, 248, 0.06); color: #7dd3fc; border: 1px solid rgba(56, 189, 248, 0.15); }
        .badge-cat-2 { background-color: rgba(167, 139, 250, 0.06); color: #c4b5fd; border: 1px solid rgba(167, 139, 250, 0.15); }
        .badge-cat-3 { background-color: rgba(52, 211, 153, 0.06); color: #6ee7b7; border: 1px solid rgba(52, 211, 153, 0.15); }
        .badge-cat-4 { background-color: rgba(251, 191, 36, 0.06); color: #fde047; border: 1px solid rgba(251, 191, 36, 0.15); }
        .badge-cat-5 { background-color: rgba(244, 63, 94, 0.06); color: #fda4af; border: 1px solid rgba(244, 63, 94, 0.15); }
        .badge-cat-default { background-color: rgba(148, 163, 184, 0.06); color: #cbd5e1; border: 1px solid rgba(148, 163, 184, 0.15); }

        .btn-floating-cart {
            position: absolute;
            top: 14px;
            right: 14px;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            z-index: 4;
            text-decoration: none;
        }
        .btn-floating-cart:hover {
            background: #38bdf8;
            color: #080c14;
            transform: scale(1.1);
            box-shadow: 0 0 12px rgba(56, 189, 248, 0.5);
            border-color: #38bdf8;
        }

        .price-tag {
            color: #38bdf8;
            font-weight: 700;
            font-size: 1.45rem;
            text-shadow: 0 0 10px rgba(56, 189, 248, 0.2);
        }
        .price-vnd-sub {
            color: #94a3b8;
            font-size: 0.8rem;
            font-weight: 500;
            display: block;
            margin-top: 2px;
        }
        .btn-detail {
            border: 1.5px solid rgba(255, 255, 255, 0.12);
            color: #f8fafc;
            font-weight: 600;
            border-radius: 20px;
            transition: all 0.2s ease;
            background-color: transparent;
            font-size: 0.85rem;
        }
        .btn-detail:hover {
            border-color: #38bdf8;
            background-color: rgba(56, 189, 248, 0.08);
            color: #38bdf8;
        }
        .btn-buy {
            background: linear-gradient(135deg, #38bdf8 0%, #4f46e5 100%);
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 20px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(56, 189, 248, 0.25);
            font-size: 0.85rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-buy:hover {
            background: linear-gradient(135deg, #0ea5e9 0%, #4338ca 100%);
            color: white;
            box-shadow: 0 6px 18px rgba(56, 189, 248, 0.45);
            transform: translateY(-1px);
        }
        .modal-content {
            background-color: #0f172a;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            overflow: hidden;
        }
        .modal-thumb-bubble {
            width: 52px;
            height: 52px;
            border-radius: 10px;
            border: 2px solid rgba(255, 255, 255, 0.1);
            background-size: cover;
            background-position: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .modal-thumb-bubble:hover, .modal-thumb-bubble.active {
            border-color: #38bdf8;
            transform: scale(1.05);
            box-shadow: 0 0 10px rgba(56, 189, 248, 0.3);
        }
        .text-dark-theme-title {
            color: #f8fafc !important;
        }
        .text-dark-theme-desc {
            color: #94a3b8 !important;
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

<?php
$cartCount = $cartCount ?? 0;
?>

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
                <a href="/cart/orders" class="nav-link text-white fw-semibold me-2 d-none d-sm-inline-block hover-opacity">
                    <i class="bi bi-receipt me-1"></i>Đơn hàng
                </a>
                <?php if (SessionHelper::isAdmin()): ?>
                    <a href="/product/list" class="nav-link text-white fw-semibold me-2 d-none d-sm-inline-block hover-opacity">
                        <i class="bi bi-gear-fill me-1"></i>Trang quản trị
                    </a>
                <?php endif; ?>
                <a href="/account/logout" class="btn btn-outline-danger btn-sm px-3 rounded-pill fw-semibold text-decoration-none">
                    Đăng xuất
                </a>
            <?php else: ?>
                <a href="/account/login" class="nav-link text-white fw-semibold hover-opacity">
                    Đăng nhập
                </a>
                <a href="/account/register" class="btn btn-outline-info btn-sm px-3 rounded-pill fw-semibold text-decoration-none">
                    <i class="bi bi-person-plus me-1"></i>Đăng ký
                </a>
            <?php endif; ?>
            
            <a href="/cart" class="cart-link" title="Xem giỏ hàng">
                <i class="bi bi-cart3 fs-5"></i>
                <?php if ($cartCount > 0): ?>
                    <span class="cart-badge"><?= $cartCount ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>
</nav><div class="container mb-5">
    <?php if (empty($products)): ?>
        <div class="card p-5 text-center text-muted rounded-4 border-0 shadow-sm bg-dark bg-opacity-25 border border-secondary border-opacity-10">
            <i class="bi bi-basket3 fs-1 d-block mb-3 text-secondary"></i>
            <h4 class="text-white">Danh mục sản phẩm hiện đang trống</h4>
            <p class="text-secondary mb-4">Vui lòng quay lại sau hoặc truy cập Trang quản trị để đăng sản phẩm mới.</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($products as $product): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="product-card card h-100" id="prod-card-<?= $product->getID() ?>">
                        <div class="card-img-wrapper">
                            <div class="card-img-container" onclick="openDetail(<?= $product->getID() ?>)">

                                <a href="/cart/add/<?= $product->getID() ?>" class="btn-floating-cart" onclick="event.stopPropagation();" title="Thêm vào giỏ hàng">
                                    <i class="bi bi-cart-plus"></i>
                                </a>

                                <div class="img-overlay">
                                    <button type="button" class="btn-quickview">
                                        <i class="bi bi-eye-fill me-1"></i> Xem chi tiết
                                    </button>
                                </div>

                                <?php if ($product->getImage()): ?>
                                    <img src="<?= htmlspecialchars($product->getImage()) ?>" class="product-img" id="main-img-<?= $product->getID() ?>" alt="<?= htmlspecialchars($product->getName()) ?>" onerror="this.onerror=null; this.src=''; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <?php endif; ?>
                                <div class="placeholder-img" id="placeholder-<?= $product->getID() ?>" style="display: <?= $product->getImage() ? 'none' : 'flex' ?>;">
                                    <?= strtoupper(substr($product->getName() ?? 'P', 0, 2)) ?>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4 d-flex flex-column">
                            <h5 class="fw-bold text-white mb-3 text-truncate" style="cursor: pointer;" onclick="openDetail(<?= $product->getID() ?>)" title="<?= htmlspecialchars($product->getName()) ?>">
                                <?= htmlspecialchars($product->getName()) ?>
                            </h5>

                            <div class="d-flex align-items-center justify-content-between mb-4 mt-auto">
                                <div>
                                    <span class="price-tag">$<?= number_format($product->getPrice()) ?></span>
                                    <span class="price-vnd-sub"><?= number_format($product->getPrice() * 25400) ?> đ</span>
                                </div>
                            </div>

                            <div class="d-flex gap-2 w-100">
                                <button onclick="openDetail(<?= $product->getID() ?>)" class="btn btn-detail w-50 py-2.5 rounded-pill fw-bold">
                                    Chi tiết
                                </button>
                                <a href="/cart/buyNow/<?= $product->getID() ?>" class="btn btn-buy w-50 py-2.5 rounded-pill fw-bold text-center">
                                    Mua ngay
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="productDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-md-6 bg-dark bg-opacity-20 d-flex flex-column align-items-center justify-content-center p-4 position-relative" style="min-height: 380px; border-right: 1px solid rgba(255, 255, 255, 0.05);">
                        <button type="button" class="btn-close btn-close-white position-absolute" data-bs-dismiss="modal" aria-label="Close" style="top: 15px; left: 15px; z-index: 10;"></button>
                        <div class="w-100 text-center mb-3">
                            <img id="modal-main-img" src="" class="img-fluid rounded-4 shadow-sm" style="max-height: 280px; object-fit: cover;">
                        </div>
                        <div id="modal-thumbnails" class="d-flex gap-2 justify-content-center w-100 flex-wrap"></div>
                    </div>
                    <div class="col-md-6 p-4 p-md-5 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span id="modal-category" class="badge rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 0.75rem;"></span>
                                <button type="button" class="btn-close btn-close-white d-none d-md-block" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.5;"></button>
                            </div>
                            <h3 id="modal-title" class="fw-bold text-white mb-3"></h3>
                            <div class="mb-3">
                                <span id="modal-price" class="text-primary fw-bold fs-3 me-2"></span>
                                <span id="modal-price-vnd" class="text-secondary small fw-semibold"></span>
                            </div>
                            <div class="mb-3">
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-2 py-1"><i class="bi bi-shield-check me-1"></i>Còn hàng</span>
                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-20 px-2 py-1 ms-1"><i class="bi bi-truck me-1"></i>Giao nhanh 2h</span>
                            </div>
                            <h6 class="fw-bold text-white mb-2">Mô tả sản phẩm:</h6>
                            <p id="modal-description" class="text-secondary small mb-4" style="line-height: 1.6;"></p>
                        </div>
                        <div class="d-flex gap-3">
                            <a id="modal-btn-cart" href="" class="btn btn-detail w-50 py-2.5 rounded-pill fw-bold d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-cart-plus"></i> Thêm
                            </a>
                            <a id="modal-btn-buy" href="" class="btn btn-buy w-50 py-2.5 rounded-pill fw-bold d-flex align-items-center justify-content-center gap-2">
                                Mua ngay
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const productsData = {};
<?php foreach ($products as $product): ?>
productsData[<?= $product->getID() ?>] = {
    id: <?= $product->getID() ?>,
    name: <?= json_encode($product->getName()) ?>,
    description: <?= json_encode($product->getDescription() ?? 'Không có mô tả sản phẩm.') ?>,
    price: <?= $product->getPrice() ?>,
    priceVnd: <?= $product->getPrice() * 25400 ?>,
    image: <?= json_encode($product->getImage()) ?>,
    categoryName: <?= json_encode($product->getCategoryName() ?? 'Chưa phân loại') ?>,
    categoryId: <?= (int)$product->getCategoryID() ?>,
    subImages: <?= json_encode($product->getSubImages()) ?>
};
<?php endforeach; ?>

function openDetail(productId) {
    const p = productsData[productId];
    if (!p) return;
    
    document.getElementById('modal-title').textContent = p.name;
    document.getElementById('modal-description').textContent = p.description;
    
    const catBadge = document.getElementById('modal-category');
    catBadge.textContent = p.categoryName;
    catBadge.className = 'badge rounded-pill px-3 py-1.5 fw-semibold';
    const catId = p.categoryId;
    if (catId === 1) catBadge.className += ' bg-info text-info bg-opacity-10 border border-info border-opacity-30';
    else if (catId === 2) catBadge.className += ' bg-primary text-primary bg-opacity-10 border border-primary border-opacity-30';
    else if (catId === 3) catBadge.className += ' bg-success text-success bg-opacity-10 border border-success border-opacity-30';
    else if (catId === 4) catBadge.className += ' bg-warning text-warning bg-opacity-10 border border-warning border-opacity-30';
    else if (catId === 5) catBadge.className += ' bg-danger text-danger bg-opacity-10 border border-danger border-opacity-30';
    else catBadge.className += ' bg-secondary text-secondary bg-opacity-10 border border-secondary border-opacity-30';
    
    document.getElementById('modal-price').textContent = '$' + parseFloat(p.price).toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    document.getElementById('modal-price-vnd').textContent = '(' + Math.round(p.priceVnd).toLocaleString('vi-VN') + ' VND)';
    
    const mainImg = document.getElementById('modal-main-img');
    mainImg.src = p.image || '';
    mainImg.style.display = p.image ? 'block' : 'none';
    
    document.getElementById('modal-btn-cart').href = '/cart/add/' + p.id;
    document.getElementById('modal-btn-buy').href = '/cart/buyNow/' + p.id;
    
    const thumbsDiv = document.getElementById('modal-thumbnails');
    thumbsDiv.innerHTML = '';
    
    const allImgs = [];
    if (p.image) allImgs.push(p.image);
    if (p.subImages && p.subImages.length > 0) {
        p.subImages.forEach(img => allImgs.push(img));
    }
    
    if (allImgs.length > 1) {
        allImgs.forEach((img, idx) => {
            const btn = document.createElement('button');
            btn.className = 'modal-thumb-bubble btn p-0 me-1' + (idx === 0 ? ' active' : '');
            btn.style.backgroundImage = "url('" + img + "')";
            btn.onclick = function() {
                mainImg.src = img;
                thumbsDiv.querySelectorAll('.modal-thumb-bubble').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            };
            btn.onmouseover = function() {
                mainImg.src = img;
                thumbsDiv.querySelectorAll('.modal-thumb-bubble').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            };
            thumbsDiv.appendChild(btn);
        });
    }
    
    const modal = new bootstrap.Modal(document.getElementById('productDetailModal'));
    modal.show();
}

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
