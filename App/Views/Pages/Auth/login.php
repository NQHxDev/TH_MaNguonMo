<section id="page-login" class="page">
   <div class="auth-container">
      <div class="auth-header">
         <h2>Đăng nhập</h2>
         <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.5rem">
            Chào mừng bạn trở lại với ZeionStore
         </p>
      </div>

      <div class="auth-social">
         <button class="btn btn-secondary" onclick="loginSocial('google')">
            <i class="fab fa-google" style="color: #ea4335"></i> Google
         </button>
         <button class="btn btn-secondary" onclick="loginSocial('github')">
            <i class="fab fa-github"></i> GitHub
         </button>
      </div>

      <div class="divider">Hoặc bằng tài khoản</div>

      <form id="form-login">
         <div class="form-group">
            <label class="form-label" for="login-username">Tên đăng nhập / Email</label>
            <input
               type="text"
               id="login-username"
               class="form-input"
               placeholder="nhập tên đăng nhập..."
               required
               autocomplete="username"
            />
         </div>
         <div class="form-group">
            <label class="form-label" for="login-password">Mật khẩu</label>
            <div class="password-wrapper">
               <input
                  type="password"
                  id="login-password"
                  class="form-input"
                  placeholder="nhập mật khẩu..."
                  required
                  autocomplete="current-password"
               />
               <button type="button" class="pw-toggle" onclick="togglePassword(this)">
                  <i class="fas fa-eye"></i>
               </button>
            </div>
         </div>
         <button
            type="submit"
            class="btn btn-primary"
            style="width: 100%; justify-content: center; padding: 0.8rem; margin-top: 1rem"
         >
            Đăng nhập
         </button>
      </form>

      <p
         style="
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: var(--text-muted);
         "
      >
         Chưa có tài khoản?
         <a
            href="#"
            onclick="navigateTo('register')"
            style="color: var(--primary); font-weight: 600; text-decoration: none"
            >Đăng ký ngay</a
         >
      </p>
   </div>
</section>
