<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sản Phẩm - Danh Mục</title>
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
        .btn-add { 
            background: linear-gradient(135deg, #4f46e5, #7c3aed); 
            border: none; 
            color: white; 
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-add:hover { 
            background: linear-gradient(135deg, #3730a3, #6d28d9); 
            color: white; 
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
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
            font-size: 1.35rem;
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

<nav class="navbar navbar-expand-lg navbar-dark mb-5 py-3 shadow-sm">
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
            <h2 class="fw-bold text-dark mb-1">Danh Mục Quản Lý</h2>
            <p class="text-muted small mb-0">Xem và quản lý các sản phẩm trong giao diện danh sách thẻ chuyên nghiệp.</p>
        </div>
        <a href="/product/create" class="btn btn-add px-4 py-2.5 rounded-pill shadow-sm">
            <i class="bi bi-plus-lg me-2"></i>Thêm sản phẩm mới
        </a>
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
        <div class="card p-5 text-center text-muted rounded-4">
            <i class="bi bi-inbox fs-1 d-block mb-3 text-secondary"></i>
            <h4>Không tìm thấy sản phẩm nào</h4>
            <p class="mb-4">Bắt đầu bằng việc thêm sản phẩm đầu tiên của bạn vào danh mục.</p>
            <a href="/product/create" class="btn btn-add px-4 py-2 rounded-pill d-inline-block">
                <i class="bi bi-plus-lg me-2"></i>Thêm sản phẩm mới
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

                            <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top border-light">
                                <span class="price-tag">$<?= number_format($product->getPrice(), 2) ?></span>
                                <div class="d-flex gap-2">
                                    <a href="/product/edit/<?= $product->getID() ?>" class="btn btn-outline-primary btn-action-card" title="Chỉnh sửa">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <a href="/product/delete/<?= $product->getID() ?>" class="btn btn-outline-danger btn-action-card" 
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
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
