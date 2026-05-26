<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sản Phẩm - Danh Mục</title>
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
        .btn-add { 
            background: linear-gradient(135deg, #38bdf8 0%, #4f46e5 100%); 
            border: none; 
            color: white; 
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(56, 189, 248, 0.25);
        }
        .btn-add:hover { 
            background: linear-gradient(135deg, #0ea5e9 0%, #4338ca 100%); 
            color: white; 
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(56, 189, 248, 0.45);
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
        .btn-action-card {
            width: 38px;
            height: 38px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        .btn-action-card:hover {
            transform: scale(1.05);
        }
        .thumb-bubble {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.1);
            background-size: cover;
            background-position: center;
            cursor: pointer;
            transition: all 0.2s ease;
            opacity: 0.5;
        }
        .thumb-bubble:hover, .thumb-bubble.active {
            opacity: 1;
            border-color: #38bdf8;
            transform: scale(1.1);
            box-shadow: 0 0 8px rgba(56, 189, 248, 0.4);
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
        <a class="navbar-brand fw-bold fs-4 d-flex align-items-center" href="/">
            <i class="bi bi-box-seam-fill me-2 text-warning"></i>ZeionStore
        </a>
        <a href="/" class="nav-link text-white fw-semibold hover-opacity">
            <i class="bi bi-shop me-1"></i>Quay lại cửa hàng
        </a>
    </div>
</nav>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold text-white mb-1">Danh Mục Quản Lý</h2>
            <p class="text-secondary small mb-0">Xem và quản lý các sản phẩm trong giao diện danh sách thẻ chuyên nghiệp.</p>
        </div>
        <a href="/product/create" class="btn btn-add px-4 py-2.5 rounded-pill shadow-sm">
            <i class="bi bi-plus-lg me-2"></i>Thêm sản phẩm mới
        </a>
    </div>



    <?php if (empty($products)): ?>
        <div class="card p-5 text-center text-muted rounded-4 bg-dark bg-opacity-25 border border-secondary border-opacity-10">
            <i class="bi bi-inbox fs-1 d-block mb-3 text-secondary"></i>
            <h4 class="text-white">Không tìm thấy sản phẩm nào</h4>
            <p class="text-secondary mb-4">Bắt đầu bằng việc thêm sản phẩm đầu tiên của bạn vào danh mục.</p>
            <a href="/product/create" class="btn btn-add px-4 py-2 rounded-pill d-inline-block">
                <i class="bi bi-plus-lg me-2"></i>Thêm sản phẩm mới
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($products as $product): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="product-card card h-100" id="prod-card-<?= $product->getID() ?>">
                        <div class="card-img-wrapper">
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
                        </div>

                        <div class="card-body p-4 d-flex flex-column">
                            <h5 class="fw-bold text-white mb-2 text-truncate" title="<?= htmlspecialchars($product->getName()) ?>">
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

                            <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top border-secondary border-opacity-20">
                                <div>
                                    <span class="price-tag">$<?= number_format($product->getPrice(), 2) ?></span>
                                    <span class="price-vnd-sub"><?= number_format($product->getPrice() * 25400) ?> đ</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="/product/edit/<?= $product->getID() ?>" class="btn btn-outline-primary btn-action-card border-secondary text-white" title="Chỉnh sửa">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <a href="/product/delete/<?= $product->getID() ?>" class="btn btn-outline-danger btn-action-card border-danger text-danger" 
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')" title="Xóa">
                                        <i class="bi bi-trash-fill"></i>
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
