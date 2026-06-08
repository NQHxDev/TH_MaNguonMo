<section id="page-profile" class="page">
   <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 2rem">
      <i class="fas fa-user-circle"></i> Thông tin tài khoản
   </h2>
   <div class="profile-layout">
      <div class="profile-card">
         <div class="profile-avatar-section">
            <div class="profile-avatar" id="profile-avatar-letter">?</div>
            <div>
               <h3 id="profile-display-name" style="font-size: 1.3rem">--</h3>
               <p
                  id="profile-display-username"
                  style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem"
               >
                  --
               </p>
            </div>
            <span class="badge-role" id="profile-role-badge" style="margin-left: auto"
               >--</span
            >
         </div>

         <div class="profile-section">
            <h4><i class="fas fa-edit"></i> Cập nhật thông tin</h4>
            <form id="form-update-profile">
               <div class="form-group">
                  <label class="form-label" for="profile-fullname">Họ và tên</label>
                  <input
                     type="text"
                     id="profile-fullname"
                     class="form-input"
                     placeholder="họ và tên..."
                     required
                  />
               </div>
               <button
                  type="submit"
                  class="btn btn-primary"
                  style="
                     width: 100%;
                     justify-content: center;
                     padding: 0.75rem;
                     margin-top: 0.5rem;
                  "
               >
                  <i class="fas fa-save"></i> Lưu thay đổi
               </button>
            </form>
         </div>

         <div class="profile-section">
            <h4><i class="fas fa-link"></i> Tài khoản liên kết</h4>
            <div id="profile-social-list" class="profile-social-list">
            </div>
         </div>
      </div>

      <div class="profile-card">
         <div class="profile-section" id="profile-password-section">
            <h4 id="profile-password-title"><i class="fas fa-lock"></i> Mật khẩu</h4>
            <div id="profile-password-alert" style="display: none" class="profile-alert">
               <i class="fas fa-exclamation-triangle"></i>
               <span>Tài khoản của bạn chưa đặt mật khẩu!</span>
            </div>
            <form id="form-set-password">
               <div class="form-group" id="profile-current-pw-group">
                  <label class="form-label" for="profile-current-password"
                     >Mật khẩu hiện tại</label
                  >
                  <div class="password-wrapper">
                     <input
                        type="password"
                        id="profile-current-password"
                        class="form-input"
                        placeholder="nhập mật khẩu hiện tại..."
                        autocomplete="current-password"
                     />
                     <button type="button" class="pw-toggle" onclick="togglePassword(this)">
                        <i class="fas fa-eye"></i>
                     </button>
                  </div>
               </div>
               <div class="form-group">
                  <label class="form-label" for="profile-new-password">Mật khẩu mới</label>
                  <div class="password-wrapper">
                     <input
                        type="password"
                        id="profile-new-password"
                        class="form-input"
                        placeholder="mật khẩu tối thiểu 6 ký tự..."
                        required
                        minlength="6"
                        autocomplete="new-password"
                     />
                     <button type="button" class="pw-toggle" onclick="togglePassword(this)">
                        <i class="fas fa-eye"></i>
                     </button>
                  </div>
               </div>
               <div class="form-group">
                  <label class="form-label" for="profile-confirm-password">Xác nhận mật khẩu mới</label>
                  <div class="password-wrapper">
                     <input
                        type="password"
                        id="profile-confirm-password"
                        class="form-input"
                        placeholder="nhập lại mật khẩu mới..."
                        required
                        minlength="6"
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
                  style="
                     width: 100%;
                     justify-content: center;
                     padding: 0.75rem;
                     margin-top: 0.5rem;
                  "
               >
                  <i class="fas fa-key"></i>
                  <span id="profile-pw-btn-text">Đặt mật khẩu</span>
               </button>
            </form>
         </div>
      </div>
   </div>
</section>
