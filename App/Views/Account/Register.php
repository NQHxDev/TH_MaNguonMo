<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - ZeionStore</title>
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
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .navbar { 
            background: rgba(8, 12, 20, 0.85); 
            backdrop-filter: blur(16px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
        }
        .register-card {
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 28px;
            background-color: rgba(17, 24, 39, 0.55);
            backdrop-filter: blur(16px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            margin: 80px auto 40px auto;
            padding: 40px;
        }
        .input-group-text {
            background-color: rgba(15, 23, 42, 0.4);
            border: 1.5px solid rgba(255, 255, 255, 0.12);
            border-right: none;
            color: #94a3b8;
            border-radius: 14px 0 0 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 46px;
        }
        .form-control, .form-select {
            background-color: rgba(15, 23, 42, 0.6);
            border: 1.5px solid rgba(255, 255, 255, 0.12);
            border-left: none;
            border-radius: 0 14px 14px 0;
            color: #f8fafc;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }
        .form-control::placeholder {
            color: #94a3b8;
            opacity: 0.6;
        }
        .form-control:focus, .form-select:focus {
            background-color: rgba(15, 23, 42, 0.8);
            border-color: #38bdf8;
            box-shadow: 0 0 12px rgba(56, 189, 248, 0.25);
            color: #f8fafc;
            outline: none;
        }
        .input-group:focus-within .input-group-text {
            border-color: #38bdf8;
            color: #38bdf8;
        }
        .input-group:focus-within .form-control, .input-group:focus-within .form-select {
            border-color: #38bdf8;
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
        .btn-register {
            background: linear-gradient(135deg, #38bdf8 0%, #4f46e5 100%);
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 14px;
            padding: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(56, 189, 248, 0.25);
        }
        .btn-register:hover {
            background: linear-gradient(135deg, #0ea5e9 0%, #4338ca 100%);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(56, 189, 248, 0.45);
        }
        .brand-text {
            color: #f8fafc;
            text-decoration: none;
        }
        .brand-text:hover {
            color: #38bdf8;
        }
        .error-feedback {
            color: #f87171;
            font-size: 0.8rem;
            margin-top: 4px;
            font-weight: 500;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark py-3 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4 d-flex align-items-center" href="/">
            <i class="bi bi-box-seam-fill me-2 text-warning"></i>ZeionStore
        </a>
        <a href="/" class="brand-text fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Quay lại cửa hàng
        </a>
    </div>
</nav>

<div class="container d-flex align-items-center justify-content-center">
    <div class="register-card">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-white mb-1">Đăng Ký</h2>
            <p class="text-secondary small">Tạo tài khoản mới để trải nghiệm mua sắm tuyệt vời</p>
        </div>

        <?php if (isset($errors['account'])): ?>
            <div class="alert alert-danger border-0 rounded-4 bg-danger bg-opacity-10 text-danger p-3 mb-4 d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <span class="small fw-semibold"><?= htmlspecialchars($errors['account']) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="/account/save">
            <div class="mb-3">
                <label for="username" class="form-label">Tên tài khoản</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person-fill"></i>
                    </span>
                    <input type="text" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" id="username" name="username" placeholder="Ví dụ: username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                </div>
                <?php if (isset($errors['username'])): ?>
                    <div class="error-feedback"><i class="bi bi-exclamation-circle me-1"></i><?= $errors['username'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="fullname" class="form-label">Họ và tên</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-card-text"></i>
                    </span>
                    <input type="text" class="form-control <?= isset($errors['fullname']) ? 'is-invalid' : '' ?>" id="fullname" name="fullname" placeholder="Nhập họ và tên đầy đủ" value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>" required>
                </div>
                <?php if (isset($errors['fullname'])): ?>
                    <div class="error-feedback"><i class="bi bi-exclamation-circle me-1"></i><?= $errors['fullname'] ?></div>
                <?php endif; ?>
            </div>

            <div class="row mb-3">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock-fill"></i>
                        </span>
                        <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" id="password" name="password" placeholder="Mật khẩu" required>
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <div class="error-feedback"><i class="bi bi-exclamation-circle me-1"></i><?= $errors['password'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label for="confirmpassword" class="form-label">Xác nhận mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock-fill"></i>
                        </span>
                        <input type="password" class="form-control <?= isset($errors['confirmPass']) ? 'is-invalid' : '' ?>" id="confirmpassword" name="confirmpassword" placeholder="Nhập lại mật khẩu" required>
                    </div>
                    <?php if (isset($errors['confirmPass'])): ?>
                        <div class="error-feedback"><i class="bi bi-exclamation-circle me-1"></i><?= $errors['confirmPass'] ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-register w-100 mb-3">Đăng Ký Tài Khoản</button>

            <div class="text-center mt-4">
                <p class="text-secondary small mb-0">Đã có tài khoản? 
                    <a href="/account/login" class="text-info fw-semibold text-decoration-none">Đăng nhập tại đây</a>
                </p>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
