<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sản Phẩm - Cập Nhật</title>
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
        .card { 
            border: 1px solid rgba(255, 255, 255, 0.06); 
            border-radius: 24px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.15); 
            background-color: rgba(17, 24, 39, 0.55);
            backdrop-filter: blur(10px);
            color: #f8fafc;
        }
        .btn-update { 
            background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
            border: none; 
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }
        .btn-update:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.45);
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
        .form-control, .form-select { 
            background-color: rgba(15, 23, 42, 0.6);
            border: 1.5px solid rgba(255, 255, 255, 0.12);
            border-left: none; 
            padding: 11px 14px;
            color: #f8fafc;
        }
        .form-select option {
            background-color: #0f172a;
            color: #f8fafc;
        }
        .form-control::placeholder {
            color: #64748b;
        }
        .form-control:focus, .form-select:focus { 
            box-shadow: none; 
            background-color: rgba(15, 23, 42, 0.7);
            border-color: #10b981; 
            color: #f8fafc;
        }
        .input-group:focus-within .input-group-text { 
            border-color: #10b981; 
            color: #10b981; 
        }
        .input-group:focus-within .form-control, 
        .input-group:focus-within .form-select { 
            border-color: #10b981; 
        }
        .back-link {
            color: #94a3b8;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .back-link:hover {
            color: #10b981;
        }
        .preview-img {
            max-height: 110px;
            object-fit: cover;
            border-radius: 12px;
            border: 2px solid rgba(255,255,255,0.1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .gallery-preview-img {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid rgba(255,255,255,0.1);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
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
    <?php if (!empty($errors)): ?>
        <div class="toast show custom-toast toast-error" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex align-items-start justify-content-between">
                <div class="d-flex flex-column gap-1">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-octagon-fill toast-icon-error fs-5"></i>
                        <span class="toast-text fw-bold">Lỗi nhập liệu:</span>
                    </div>
                    <ul class="mb-0 ps-3 small text-white-50" style="font-size: 0.85rem; line-height: 1.4;">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <a href="/product/list" class="text-decoration-none back-link">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại danh mục
                </a>
                <span class="text-secondary small">Mã sản phẩm: #<?= $product->getID() ?></span>
            </div>

            <div class="card p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-dark bg-opacity-35 rounded-circle mb-3" style="width: 60px; height: 60px; border: 1px solid rgba(255,255,255,0.08);">
                        <i class="bi bi-pencil-square fs-3 text-success"></i>
                    </div>
                    <h3 class="fw-bold text-white mb-1">Cập Nhật Sản Phẩm</h3>
                    <p class="text-secondary small">Chỉnh sửa chi tiết sản phẩm, tải lên ảnh thay thế hoặc sửa thư viện ảnh phụ.</p>
                </div>

                <form method="post" action="/product/edit/<?= $product->getID() ?>" enctype="multipart/form-data" onsubmit="return validateForm()">
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên sản phẩm</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-tag"></i></span>
                            <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($product->getName()) ?>" placeholder="Nhập tên sản phẩm" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Danh mục</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-grid-3x3-gap"></i></span>
                            <select name="category_id" id="category_id" class="form-select" required>
                                <option value="" disabled>Chọn danh mục</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category->getId() ?>" <?= ($product->getCategoryID() === $category->getId()) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category->getName()) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả sản phẩm</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                            <textarea name="description" id="description" class="form-control" rows="3" placeholder="Thông tin chi tiết ngắn gọn về sản phẩm"><?= htmlspecialchars($product->getDescription() ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label">Giá bán ($)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                            <input type="text" name="price" id="price" class="form-control" value="<?= htmlspecialchars($product->getPrice()) ?>" placeholder="0.00" required>
                        </div>
                    </div>

                    <?php if ($product->getImage()): ?>
                        <div class="mb-3">
                            <label class="form-label d-block text-secondary small">Ảnh chính hiện tại</label>
                            <img src="<?= htmlspecialchars($product->getImage()) ?>" class="preview-img" alt="Main image">
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="image" class="form-label">Tải lên ảnh chính mới</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-image"></i></span>
                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        </div>
                        <div class="form-text text-secondary small mt-1">Để trống nếu muốn giữ nguyên ảnh chính hiện tại.</div>
                    </div>

                    <?php if (!empty($product->getSubImages())): ?>
                        <div class="mb-3">
                            <label class="form-label d-block text-secondary small">Thư viện ảnh phụ hiện tại</label>
                            <div class="d-flex gap-2 flex-wrap">
                                <?php foreach ($product->getSubImages() as $subImg): ?>
                                    <img src="<?= htmlspecialchars($subImg) ?>" class="gallery-preview-img" alt="Sub image">
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <label for="sub_images" class="form-label">Thay thế ảnh phụ (Thư viện ảnh)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-images"></i></span>
                            <input type="file" name="sub_images[]" id="sub_images" class="form-control" accept="image/*" multiple>
                        </div>
                        <div class="form-text text-secondary small mt-1">Tải lên ảnh phụ mới sẽ thay thế toàn bộ ảnh phụ hiện tại.</div>
                    </div>

                    <button type="submit" class="btn btn-update w-100 py-3 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-save"></i> Lưu thay đổi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function validateForm(){
    let name = document.getElementById('name').value;
    let price = document.getElementById('price').value;
    let category = document.getElementById('category_id').value;
    let errors = [];

    if(name.length < 10 || name.length > 100){
        errors.push('Tên sản phẩm phải từ 10 đến 100 ký tự');
    }
    if(isNaN(price) || price <= 0){
        errors.push('Giá bán phải là số và lớn hơn 0');
    }
    if(!category) {
        errors.push('Vui lòng chọn danh mục');
    }

    if(errors.length > 0){
        alert(errors.join('\n'));
        return false;
    }
    return true;
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
