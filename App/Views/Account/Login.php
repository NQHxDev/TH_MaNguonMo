<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - ZeionStore</title>
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
        .login-card {
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 28px;
            background-color: rgba(17, 24, 39, 0.55);
            backdrop-filter: blur(16px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            max-width: 450px;
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
        .form-control {
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
        .form-control:focus {
            background-color: rgba(15, 23, 42, 0.8);
            border-color: #38bdf8;
            box-shadow: 0 0 12px rgba(56, 189, 248, 0.25);
            color: #f8fafc;
        }
        .input-group:focus-within .input-group-text {
            border-color: #38bdf8;
            color: #38bdf8;
        }
        .input-group:focus-within .form-control {
            border-color: #38bdf8;
        }
        .form-label {
            font-weight: 500;
            color: #cbd5e1;
            margin-bottom: 8px;
        }
        .btn-login {
            background: linear-gradient(135deg, #38bdf8 0%, #4f46e5 100%);
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 14px;
            padding: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(56, 189, 248, 0.25);
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #0ea5e9 0%, #4338ca 100%);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(56, 189, 248, 0.45);
        }
        .btn-social-google {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.12);
            transition: all 0.2s ease;
        }
        .btn-social-google:hover {
            background-color: rgba(219, 68, 85, 0.15);
            border-color: rgba(219, 68, 85, 0.4);
            color: #db4437 !important;
            transform: translateY(-1px);
        }
        .btn-social-github {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.12);
            transition: all 0.2s ease;
        }
        .btn-social-github:hover {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            color: #fff !important;
            transform: translateY(-1px);
        }
        .brand-text {
            color: #f8fafc;
            text-decoration: none;
        }
        .brand-text:hover {
            color: #38bdf8;
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
    <div class="login-card">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-white mb-1">Đăng Nhập</h2>
            <p class="text-secondary small">Chào mừng bạn trở lại với ZeionStore</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger border-0 rounded-4 bg-danger bg-opacity-10 text-danger p-3 mb-4 d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <span class="small fw-semibold"><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="/account/checkLogin">
            <div class="mb-3">
                <label for="username" class="form-label">Tên tài khoản</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person-fill"></i>
                    </span>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Nhập username của bạn" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock-fill"></i>
                    </span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required>
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100 mb-3">Đăng Nhập</button>

            <div class="position-relative text-center my-4">
                <hr class="border-secondary border-opacity-30">
                <span class="position-absolute top-50 start-50 translate-middle px-3 text-secondary small bg-dark-card" style="background-color: #111827;">Hoặc đăng nhập bằng</span>
            </div>

            <div class="d-flex gap-3 mb-3">
                <a href="/account/googleLogin" class="btn btn-social-google w-50 py-2.5 rounded-3 fw-semibold text-decoration-none text-white d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-google"></i> Google
                </a>
                <a href="/account/githubLogin" class="btn btn-social-github w-50 py-2.5 rounded-3 fw-semibold text-decoration-none text-white d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-github"></i> GitHub
                </a>
            </div>

            <div class="text-center mt-4">
                <p class="text-secondary small mb-0">Chưa có tài khoản? 
                    <a href="/account/register" class="text-info fw-semibold text-decoration-none">Đăng ký ngay</a>
                </p>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
