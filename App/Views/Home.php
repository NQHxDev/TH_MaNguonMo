<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZeionStore - Cửa Hàng Công Nghệ Cao Cấp</title>
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
        .cart-link {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            transition: all 0.2s ease;
        }
        .cart-link:hover {
            background-color: rgba(255, 255, 255, 0.25);
            color: #fcd34d;
            transform: scale(1.05);
        }
        .cart-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background-color: #ef4444;
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 50px;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
            border: 2px solid #7c3aed;
        }
        .product-card {
            border: none;
            border-radius: 24px;
            overflow: hidden;
            background-color: #ffffff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        }
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(79, 70, 229, 0.08) !important;
        }
        .card-img-container {
            position: relative;
            height: 220px;
            background-color: #f8fafc;
            overflow: hidden;
        }
        .product-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.5s ease;
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
            background: linear-gradient(135deg, #6366f1, #a855f7);
        }
        .category-badge {
            position: absolute;
            top: 16px;
            left: 16px;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            z-index: 3;
            display: inline-block;
        }
        .badge-cat-1 { background-color: #e0f2fe; color: #0369a1; }
        .badge-cat-2 { background-color: #f3e8ff; color: #6b21a8; }
        .badge-cat-3 { background-color: #dcfce7; color: #15803d; }
        .badge-cat-4 { background-color: #fef3c7; color: #b45309; }
        .badge-cat-5 { background-color: #ffe4e6; color: #be123c; }
        .badge-cat-default { background-color: #f1f5f9; color: #475569; }

        .price-tag {
            color: #4f46e5;
            font-weight: 700;
            font-size: 1.4rem;
        }
        .btn-cart {
            border: 2px solid #e2e8f0;
            color: #4f46e5;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.2s ease;
            background-color: transparent;
            padding: 10px 16px;
        }
        .btn-cart:hover {
            border-color: #4f46e5;
            background-color: #f5f3ff;
            color: #4f46e5;
        }
        .btn-buy {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            transition: all 0.2s ease;
            padding: 10px 20px;
        }
        .btn-buy:hover {
            background: linear-gradient(135deg, #3730a3, #6d28d9);
            color: white;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
            transform: translateY(-1px);
        }
        .thumb-bubble {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 2px solid #e2e8f0;
            background-size: cover;
            background-position: center;
            cursor: pointer;
            transition: all 0.2s ease;
            opacity: 0.6;
        }
        .thumb-bubble:hover, .thumb-bubble.active {
            opacity: 1;
            border-color: #4f46e5;
            transform: scale(1.1);
        }
    </style>
</head>
<body>

<?php
$cartCount = $cartCount ?? 0;
?>

<nav class="navbar navbar-expand-lg navbar-dark mb-5 py-3 shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3 d-flex align-items-center" href="/">
            <i class="bi bi-box-seam-fill me-2 text-warning"></i>ZeionStore
        </a>
        <div class="d-flex align-items-center gap-3">
            <a href="/product/list" class="nav-link text-white fw-semibold me-2 d-none d-sm-inline-block hover-opacity">
                <i class="bi bi-gear-fill me-1"></i>Trang quản trị
            </a>
            <a href="/cart" class="cart-link" title="Xem giỏ hàng">
                <i class="bi bi-cart3 fs-5"></i>
                <?php if ($cartCount > 0): ?>
                    <span class="cart-badge"><?= $cartCount ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>
</nav>

<div class="container mb-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold text-dark mb-2">Chào mừng đến với ZeionStore</h1>
        <p class="text-secondary col-md-7 mx-auto">Khám phá các sản phẩm điện tử cao cấp chất lượng, thêm chúng vào giỏ hàng và trải nghiệm dịch vụ giao hàng siêu tốc của chúng tôi.</p>
    </div>

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

    <?php if (empty($products)): ?>
        <div class="card p-5 text-center text-muted rounded-4 border-0 shadow-sm">
            <i class="bi bi-basket3 fs-1 d-block mb-3 text-secondary"></i>
            <h4>Danh mục sản phẩm hiện đang trống</h4>
            <p class="mb-4">Vui lòng quay lại sau hoặc truy cập Trang quản trị để đăng sản phẩm mới.</p>
            <a href="/product/list" class="btn btn-primary px-4 py-2.5 rounded-pill d-inline-block" style="background: linear-gradient(135deg, #4f46e5, #7c3aed); border: none; font-weight: 600;">
                <i class="bi bi-gear-fill me-2"></i>Đi đến Trang quản trị
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($products as $product): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="product-card card h-100" id="prod-card-<?= $product->getID() ?>">
                        <div class="card-img-container">
                            <?php 
                            $catId = $product->getCategoryID();
                            $badgeClass = ($catId >= 1 && $catId <= 5) ? 'badge-cat-' . $catId : 'badge-cat-default';
                            ?>
                            <span class="category-badge <?= $badgeClass ?>">
                                <i class="bi bi-tag-fill me-1"></i>
                                <?= htmlspecialchars($product->getCategoryName() ?? 'Chưa phân loại') ?>
                            </span>

                            <?php if ($product->getImage()): ?>
                                <img src="<?= htmlspecialchars($product->getImage()) ?>" class="product-img" id="main-img-<?= $product->getID() ?>" alt="<?= htmlspecialchars($product->getName()) ?>" onerror="this.onerror=null; this.src=''; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <?php endif; ?>
                            <div class="placeholder-img" id="placeholder-<?= $product->getID() ?>" style="display: <?= $product->getImage() ? 'none' : 'flex' ?>;">
                                <?= strtoupper(substr($product->getName() ?? 'P', 0, 2)) ?>
                            </div>
                        </div>

                        <div class="card-body p-4 d-flex flex-column">
                            <h5 class="fw-bold text-dark mb-1 text-truncate" title="<?= htmlspecialchars($product->getName()) ?>">
                                <?= htmlspecialchars($product->getName()) ?>
                            </h5>
                            
                            <?php 
                            $allImages = [];
                            if ($product->getImage()) {
                                $allImages[] = $product->getImage();
                            }
                            if (!empty($product->getSubImages())) {
                                $allImages = array_merge($allImages, $product->getSubImages());
                            }
                            ?>
                            <?php if (count($allImages) > 1): ?>
                                <div class="d-flex gap-1.5 justify-content-start mb-3 mt-1 px-1">
                                    <?php foreach ($allImages as $index => $img): ?>
                                        <button class="thumb-bubble btn p-0 me-1 <?= $index === 0 ? 'active' : '' ?>" 
                                                style="background-image: url('<?= htmlspecialchars($img) ?>');" 
                                                onclick="swapImage(<?= $product->getID() ?>, '<?= htmlspecialchars($img) ?>', this)"
                                                onmouseover="swapImage(<?= $product->getID() ?>, '<?= htmlspecialchars($img) ?>', this)"
                                                title="Xem ảnh <?= $index + 1 ?>">
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div style="height: 12px;"></div>
                            <?php endif; ?>

                            <p class="text-secondary small flex-grow-1 mb-4" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; height: 4.5em; line-height: 1.5em;">
                                <?= htmlspecialchars($product->getDescription() ?? 'Không có mô tả sản phẩm.') ?>
                            </p>

                            <div class="d-flex align-items-center justify-content-between mt-auto pt-3 border-top border-light">
                                <span class="price-tag">$<?= number_format($product->getPrice(), 2) ?></span>
                                <div class="d-flex gap-2">
                                    <a href="/cart/add/<?= $product->getID() ?>" class="btn btn-cart d-flex align-items-center gap-1" title="Thêm vào giỏ hàng">
                                        <i class="bi bi-cart-plus"></i><span class="d-none d-sm-inline">Thêm</span>
                                    </a>
                                    <a href="/cart/buyNow/<?= $product->getID() ?>" class="btn btn-buy d-flex align-items-center gap-1">
                                        Mua ngay
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function swapImage(prodId, imgSrc, btn) {
    const mainImg = document.getElementById('main-img-' + prodId);
    const placeholder = document.getElementById('placeholder-' + prodId);
    
    if (mainImg) {
        mainImg.src = imgSrc;
        mainImg.style.display = 'block';
        if (placeholder) {
            placeholder.style.display = 'none';
        }
    }
    
    const card = document.getElementById('prod-card-' + prodId);
    if (card) {
        const buttons = card.querySelectorAll('.thumb-bubble');
        buttons.forEach(b => b.classList.remove('active'));
    }
    btn.classList.add('active');
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
