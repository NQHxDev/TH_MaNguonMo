<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sản Phẩm - Thêm Mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            background-color: #f6f8fb; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: #2D3748;
        }
        .card { 
            border: none; 
            border-radius: 24px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.05); 
            background-color: #ffffff;
        }
        .btn-primary { 
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); 
            border: none; 
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #3730a3 0%, #6d28d9 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
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
        .form-control, .form-select { 
            border-left: none; 
            border-color: #e2e8f0;
            padding: 11px 14px;
            color: #334155;
        }
        .form-control:focus, .form-select:focus { 
            box-shadow: none; 
            border-color: #7c3aed; 
        }
        .input-group:focus-within .input-group-text { 
            border-color: #7c3aed; 
            color: #7c3aed; 
        }
        .input-group:focus-within .form-control, 
        .input-group:focus-within .form-select { 
            border-color: #7c3aed; 
        }
        .back-link {
            color: #64748b;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .back-link:hover {
            color: #4f46e5;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <a href="/product/list" class="text-decoration-none back-link">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại danh mục
                </a>
                <span class="text-muted small">Bước 1 / 1</span>
            </div>

            <div class="card p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-light text-primary rounded-circle mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-box-seam fs-3 text-indigo" style="color: #4f46e5;"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">Thêm Sản Phẩm Mới</h3>
                    <p class="text-muted small">Đăng sản phẩm với hình ảnh thực tế và bộ sưu tập ảnh phụ.</p>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger border-0 rounded-4 shadow-sm small mb-4 p-3">
                        <div class="d-flex">
                            <i class="bi bi-exclamation-octagon-fill fs-5 me-2 text-danger"></i>
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="post" action="/product/create" enctype="multipart/form-data" onsubmit="return validateForm()">
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên sản phẩm</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-tag"></i></span>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Nhập tên sản phẩm (từ 10 đến 100 ký tự)" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Danh mục</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-grid-3x3-gap"></i></span>
                            <select name="category_id" id="category_id" class="form-select" required>
                                <option value="" disabled selected>Chọn danh mục</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category->getId() ?>"><?= htmlspecialchars($category->getName()) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả sản phẩm</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                            <textarea name="description" id="description" class="form-control" rows="3" placeholder="Nhập thông tin mô tả chi tiết sản phẩm"></textarea>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label">Giá bán ($)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                            <input type="text" name="price" id="price" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Tải lên ảnh chính</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-image"></i></span>
                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        </div>
                        <div class="form-text text-muted small mt-1">Định dạng hỗ trợ: JPG, PNG, WEBP, GIF. Dung lượng tối đa 5MB.</div>
                    </div>

                    <div class="mb-4">
                        <label for="sub_images" class="form-label">Ảnh phụ (Thư viện ảnh)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-images"></i></span>
                            <input type="file" name="sub_images[]" id="sub_images" class="form-control" accept="image/*" multiple>
                        </div>
                        <div class="form-text text-muted small mt-1">Chọn tải lên nhiều ảnh phụ để hiển thị trong bộ sưu tập sản phẩm.</div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-check-lg"></i> Xác nhận & Thêm sản phẩm
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
</script>
</body>
</html>
