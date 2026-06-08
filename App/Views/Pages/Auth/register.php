<section id="page-register" class="page">
   <div class="auth-container">
      <div class="auth-header">
         <h2>Đăng ký tài khoản</h2>
         <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.5rem">
            Tạo tài khoản mới để trải nghiệm mua sắm không giới hạn
         </p>
      </div>

      <form id="form-register">
         <div class="form-group">
            <label class="form-label" for="reg-username">Tên đăng nhập / Email</label>
            <input
               type="text"
               id="reg-username"
               class="form-input"
               placeholder="tên đăng nhập..."
               required
               autocomplete="username"
            />
         </div>
         <div class="form-group">
            <label class="form-label" for="reg-fullname">Họ và tên</label>
            <input
               type="text"
               id="reg-fullname"
               class="form-input"
               placeholder="họ và tên hiển thị..."
               required
               autocomplete="name"
            />
         </div>
         <div class="form-group">
            <label class="form-label" for="reg-password">Mật khẩu</label>
            <div class="password-wrapper">
               <input
                  type="password"
                  id="reg-password"
                  class="form-input"
                  placeholder="mật khẩu từ 6 ký tự..."
                  required
                  autocomplete="new-password"
               />
               <button type="button" class="pw-toggle" onclick="togglePassword(this)">
                  <i class="fas fa-eye"></i>
               </button>
            </div>
         </div>
         <div class="form-group">
            <label class="form-label" for="reg-confirm-password">Xác nhận mật khẩu</label>
            <div class="password-wrapper">
               <input
                  type="password"
                  id="reg-confirm-password"
                  class="form-input"
                  placeholder="nhập lại mật khẩu..."
                  required
                  autocomplete="new-password"
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
            Tạo tài khoản
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
         Đã có tài khoản?
         <a
            href="#"
            onclick="navigateTo('login')"
            style="color: var(--primary); font-weight: 600; text-decoration: none"
            >Đăng nhập</a
         >
      </p>
   </div>
</section>
