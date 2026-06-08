async function handleLoginSubmit(e) {
   e.preventDefault();
   const username = document.getElementById('login-username').value;
   const password = document.getElementById('login-password').value;

   const res = await fetchApi('/api/auth/login', 'POST', { username, password });
   if (res && res.success) {
      state.token = res.token;
      state.user = res.user;
      localStorage.setItem('token', res.token);
      showToast('Đăng nhập thành công!', 'success');
      e.target.reset();

      await syncCartAfterLogin();
      window.location.reload();
   } else {
      showToast(res?.message || 'Đăng nhập thất bại!', 'error');
   }
}

async function handleRegisterSubmit(e) {
   e.preventDefault();
   const username = document.getElementById('reg-username').value;
   const fullname = document.getElementById('reg-fullname').value;
   const password = document.getElementById('reg-password').value;
   const confirmpassword = document.getElementById('reg-confirm-password').value;

   const res = await fetchApi('/api/auth/register', 'POST', {
      username,
      fullname,
      password,
      confirmpassword,
   });

   if (res && res.success) {
      showToast('Đăng ký thành công! Hãy đăng nhập...', 'success');
      e.target.reset();
      navigateTo('login');
   } else {
      const msg = res?.errors ? Object.values(res.errors).join('<br>') : 'Đăng ký thất bại!';
      showToast(msg, 'error');
   }
}

async function logout() {
   await fetchApi('/api/auth/logout', 'POST', null, true);
   logoutLocal();
   window.location.reload();
}

function logoutLocal() {
   state.user = null;
   state.token = null;
   localStorage.removeItem('token');
   updateNavbar();
   navigateTo('home');
}

async function syncCartAfterLogin() {
   try {
      const guestCartRes = await fetchApi('/api/cart', 'GET');
      if (guestCartRes && guestCartRes.success && Object.keys(guestCartRes.cart).length > 0) {
         const cartItems = guestCartRes.cart;
         const quantities = {};
         for (const id in cartItems) {
            quantities[id] = cartItems[id].quantity;
         }

         await fetchApi('/api/cart/update', 'PUT', { cart_items: quantities }, true);
         state.guestId = getOrCreateGuestId();
      }
   } catch (e) {
      console.error('Sync cart failed', e);
   }
}

function loginSocial(provider) {
   window.location.href = `/api/auth/${provider}/login`;
}

function togglePassword(btn) {
   const input = btn.parentElement.querySelector('input');
   const icon = btn.querySelector('i');
   if (input.type === 'password') {
      input.type = 'text';
      icon.classList.replace('fa-eye', 'fa-eye-slash');
   } else {
      input.type = 'password';
      icon.classList.replace('fa-eye-slash', 'fa-eye');
   }
}

// ==========================================
// PROFILE PAGE
// ==========================================
async function loadProfilePage() {
   if (!state.token) {
      showToast('Vui lòng đăng nhập để xem thông tin tài khoản!', 'error');
      navigateTo('login');
      return;
   }

   const res = await fetchApi('/api/account/profile', 'GET', null, true);
   if (!res || !res.success) {
      showToast('Không thể tải thông tin tài khoản!', 'error');
      return;
   }

   const p = res.profile;

   // Avatar letter
   const avatarLetter = (p.fullname || p.username || '?').charAt(0).toUpperCase();
   document.getElementById('profile-avatar-letter').textContent = avatarLetter;

   // Display info
   document.getElementById('profile-display-name').textContent = p.fullname;
   document.getElementById('profile-display-username').textContent = p.username;

   // Role badge
   const roleBadge = document.getElementById('profile-role-badge');
   roleBadge.textContent = p.role === 'admin' ? 'Admin' : 'User';
   roleBadge.className = `badge-role ${p.role}`;

   // Fullname input
   document.getElementById('profile-fullname').value = p.fullname;

   // Password section
   const currentPwGroup = document.getElementById('profile-current-pw-group');
   const passwordAlert = document.getElementById('profile-password-alert');
   const pwBtnText = document.getElementById('profile-pw-btn-text');

   if (p.has_password) {
      currentPwGroup.style.display = 'block';
      passwordAlert.style.display = 'none';
      pwBtnText.textContent = 'Đổi mật khẩu';
   } else {
      currentPwGroup.style.display = 'none';
      passwordAlert.style.display = 'flex';
      pwBtnText.textContent = 'Đặt mật khẩu';
   }

   // Social accounts
   const socialList = document.getElementById('profile-social-list');
   if (p.social_accounts && p.social_accounts.length > 0) {
      socialList.innerHTML = '';
      p.social_accounts.forEach((s) => {
         const iconClass = s.provider === 'google' ? 'fab fa-google' : 'fab fa-github';
         socialList.innerHTML += `
            <div class="profile-social-item">
               <div class="social-icon ${s.provider}">
                  <i class="${iconClass}"></i>
               </div>
               <div class="social-info">
                  <div class="provider-name">${s.provider}</div>
                  <div class="provider-email">${s.provider_email || 'Không có email'}</div>
               </div>
               <span class="social-status">Đã liên kết</span>
            </div>
         `;
      });
   } else {
      socialList.innerHTML = `
         <div class="social-empty">
            <i class="fas fa-unlink"></i>
            Chưa liên kết tài khoản mạng xã hội nào.
         </div>
      `;
   }

   // Attach form handlers (remove old listeners by replacing)
   const pwForm = document.getElementById('form-set-password');
   const newPwForm = pwForm.cloneNode(true);
   pwForm.parentNode.replaceChild(newPwForm, pwForm);
   newPwForm.addEventListener('submit', handleSetPassword);

   const profileForm = document.getElementById('form-update-profile');
   const newProfileForm = profileForm.cloneNode(true);
   profileForm.parentNode.replaceChild(newProfileForm, profileForm);
   newProfileForm.addEventListener('submit', handleUpdateProfile);
}

async function handleSetPassword(e) {
   e.preventDefault();
   const currentPassword = document.getElementById('profile-current-password')?.value || '';
   const newPassword = document.getElementById('profile-new-password').value;
   const confirmPassword = document.getElementById('profile-confirm-password').value;

   if (newPassword.length < 6) {
      showToast('Mật khẩu mới phải có ít nhất 6 ký tự!', 'error');
      return;
   }
   if (newPassword !== confirmPassword) {
      showToast('Xác nhận mật khẩu không khớp!', 'error');
      return;
   }

   const body = {
      current_password: currentPassword,
      new_password: newPassword,
      confirm_password: confirmPassword,
   };

   const res = await fetchApi('/api/account/password', 'PUT', body, true);
   if (res && res.success) {
      showToast(res.message, 'success');
      e.target.reset();
      // Reload profile to update UI (has_password)
      loadProfilePage();
   } else {
      showToast(res?.message || 'Đặt mật khẩu thất bại!', 'error');
   }
}

async function handleUpdateProfile(e) {
   e.preventDefault();
   const fullname = document.getElementById('profile-fullname').value;

   if (!fullname.trim()) {
      showToast('Vui lòng nhập họ và tên!', 'error');
      return;
   }

   const res = await fetchApi('/api/account/update', 'PUT', { fullname }, true);
   if (res && res.success) {
      showToast(res.message, 'success');
      // Update token if returned
      if (res.token) {
         state.token = res.token;
         localStorage.setItem('token', res.token);
      }
      // Reload user info
      await checkAuth();
      loadProfilePage();
   } else {
      showToast(res?.message || 'Cập nhật thất bại!', 'error');
   }
}
