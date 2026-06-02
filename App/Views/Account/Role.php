<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cấu Hình Vai Trò - ZeionStore</title>
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
            min-height: 100vh;
        }
        .navbar { 
            background: rgba(8, 12, 20, 0.85); 
            backdrop-filter: blur(16px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }
        .role-card {
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            background-color: rgba(17, 24, 39, 0.55);
            backdrop-filter: blur(16px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            padding: 35px;
            margin-bottom: 30px;
        }
        .form-select {
            background-color: rgba(15, 23, 42, 0.6);
            border: 1.5px solid rgba(255, 255, 255, 0.12);
            border-radius: 14px;
            color: #f8fafc;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }
        .form-select:focus {
            background-color: rgba(15, 23, 42, 0.8);
            border-color: #38bdf8;
            box-shadow: 0 0 12px rgba(56, 189, 248, 0.25);
            color: #f8fafc;
            outline: none;
        }
        .form-select option {
            background-color: #0f172a;
            color: #f8fafc;
        }
        .form-label {
            font-weight: 500;
            color: #cbd5e1;
            margin-bottom: 8px;
        }
        .btn-update {
            background: linear-gradient(135deg, #38bdf8 0%, #4f46e5 100%);
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 14px;
            padding: 12px 24px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(56, 189, 248, 0.25);
        }
        .btn-update:hover {
            background: linear-gradient(135deg, #0ea5e9 0%, #4338ca 100%);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(56, 189, 248, 0.45);
        }
        .table-card {
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            background-color: rgba(17, 24, 39, 0.4);
            backdrop-filter: blur(16px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            padding: 30px;
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
            padding: 14px;
            background-color: rgba(255, 255, 255, 0.02) !important;
            border-bottom: 1.5px solid rgba(255, 255, 255, 0.1) !important;
        }
        .table td {
            padding: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            color: #cbd5e1;
            background-color: transparent !important;
        }
        .badge-role-admin {
            background-color: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
            font-size: 0.7rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 30px;
            text-transform: uppercase;
        }
        .badge-role-user {
            background-color: rgba(56, 189, 248, 0.1);
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.2);
            font-size: 0.7rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 30px;
            text-transform: uppercase;
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
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container mb-5">
    <div class="mb-4">
        <h2 class="fw-bold text-white mb-1"><i class="bi bi-shield-lock text-warning me-2"></i>Cấu Hình Vai Trò Tài Khoản</h2>
        <p class="text-secondary small mb-0">Trang quản trị bí mật để nâng/hạ cấp vai trò thành viên hệ thống.</p>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="role-card">
                <h5 class="fw-bold text-white mb-4"><i class="bi bi-pencil-square text-primary me-2"></i>Thay đổi vai trò</h5>
                <form method="POST" action="/account/updateRole">
                    <div class="mb-3">
                        <label for="username" class="form-label">Chọn tài khoản</label>
                        <select class="form-select" id="username" name="username" required onchange="updateRolePlaceholder(this)">
                            <option value="">-- Chọn tài khoản cần sửa --</option>
                            <?php foreach ($accounts as $acc): ?>
                                <option value="<?= htmlspecialchars($acc->username) ?>" data-role="<?= htmlspecialchars($acc->role) ?>">
                                    <?= htmlspecialchars($acc->username) ?> (<?= htmlspecialchars($acc->fullname) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="role" class="form-label">Vai trò mong muốn</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="user">User (Khách hàng)</option>
                            <option value="admin">Admin (Quản trị viên)</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-update w-100"><i class="bi bi-save me-1"></i> Cập Nhật Vai Trò</button>
                </form>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="table-card">
                <h5 class="fw-bold text-white mb-4"><i class="bi bi-people text-info me-2"></i>Danh sách thành viên</h5>
                <div class="table-responsive">
                    <table class="table align-middle text-white mb-0">
                        <thead>
                            <tr>
                                <th>Tên tài khoản</th>
                                <th>Họ và tên</th>
                                <th class="text-center">Vai trò hiện tại</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($accounts as $acc): ?>
                                <tr>
                                    <td class="fw-semibold text-white"><?= htmlspecialchars($acc->username) ?></td>
                                    <td><?= htmlspecialchars($acc->fullname) ?></td>
                                    <td class="text-center">
                                        <?php if ($acc->role === 'admin'): ?>
                                            <span class="badge-role-admin">Admin</span>
                                        <?php else: ?>
                                            <span class="badge-role-user">User</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateRolePlaceholder(selectEl) {
    const selectedOption = selectEl.options[selectEl.selectedIndex];
    const currentRole = selectedOption.getAttribute('data-role');
    const roleSelect = document.getElementById('role');
    
    if (currentRole) {
        roleSelect.value = currentRole;
    }
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
