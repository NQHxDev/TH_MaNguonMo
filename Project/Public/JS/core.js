const state = {
   user: null,
   token: localStorage.getItem('token') || null,
   guestId: getOrCreateGuestId(),
   products: window.initialProducts || [],
   categories: window.initialCategories || [],
   cart: {},
   orders: [],
   activePage: 'home',
   activeAdminTab: 'products',
   editingProductId: null,
   paymentResult: null,
};

document.addEventListener('DOMContentLoaded', () => {
   initApp();
});

function getOrCreateGuestId() {
   let id = localStorage.getItem('guest_id');
   if (!id) {
      id =
         'guest_' +
         Math.random().toString(36).substring(2, 15) +
         Math.random().toString(36).substring(2, 15);
      localStorage.setItem('guest_id', id);
   }
   return id;
}

async function initApp() {
   setupEventListeners();
   handleUrlParams();

   if (state.token) {
      await checkAuth();
   } else {
      updateNavbar();
   }

   window.addEventListener('popstate', () => {
      handleInitialRoute();
   });

   handleInitialRoute();
}

function handleUrlParams() {
   const params = new URLSearchParams(window.location.search);

   if (params.has('token')) {
      const token = params.get('token');
      localStorage.setItem('token', token);
      state.token = token;
      window.location.href = '/';
      return;
   }

   if (params.has('message')) {
      const message = params.get('message');
      showToast('Đăng nhập thất bại: ' + message, 'error');
      window.history.replaceState({}, document.title, '/');
   }

   if (params.has('success') && params.has('txnRef')) {
      const success = params.get('success') === 'true';
      const txnRef = params.get('txnRef');
      const amount = params.get('amount') || '0';
      const bankCode = params.get('bankCode') || '';
      const responseCode = params.get('responseCode') || '';

      state.paymentResult = { success, txnRef, amount, bankCode, responseCode };

      if (success) {
         state.cart = {};
         updateCartUI();
      }
      navigateTo('payment-result');
      window.history.replaceState({}, document.title, '/payment-result');
   }
}

async function checkAuth() {
   try {
      const res = await fetchApi('/api/auth/me', 'GET', null, true);
      if (res && res.success) {
         state.user = res.user;
      } else {
         logoutLocal();
      }
   } catch (e) {
      logoutLocal();
   }
   updateNavbar();
}

async function fetchApi(endpoint, method = 'GET', body = null, authenticated = false) {
   const headers = {
      Accept: 'application/json',
      'X-Guest-Id': state.guestId,
   };

   if (authenticated && state.token) {
      headers['Authorization'] = `Bearer ${state.token}`;
   }

   let options = { method, headers };

   if (body) {
      if (body instanceof FormData) {
         options.body = body;
      } else {
         headers['Content-Type'] = 'application/json';
         options.body = JSON.stringify(body);
      }
   }

   try {
      const response = await fetch(endpoint, options);
      if (response.status === 401) {
         showToast('Phiên đăng nhập hết hạn. Vui lòng đăng nhập lại!', 'error');
         logoutLocal();
         navigateTo('login');
         return null;
      }
      return await response.json();
   } catch (error) {
      console.error('API Error: ', error);
      showToast('Có lỗi xảy ra khi kết nối máy chủ!', 'error');
      return null;
   }
}

function updateNavbar() {
   const authLinks = document.getElementById('auth-links');
   const adminLink = document.getElementById('nav-admin-link');

   if (state.user) {
      authLinks.innerHTML = `
            <span class="user-greeting" style="font-size: 0.9rem; color: var(--text-muted); display: inline-flex; align-items: center; gap: 0.5rem;">
                Xin chào, <strong style="color: var(--text-main);">${state.user.fullname}</strong>
                <span class="badge-role ${state.user.role === 'admin' ? 'admin' : 'user'}">
                    ${state.user.role === 'admin' ? 'Admin' : 'User'}
                </span>
            </span>
            <button class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;" onclick="navigateTo('profile')" title="Thông tin tài khoản">
               <i class="fas fa-user-cog"></i>
            </button>
            <button class="btn btn-secondary" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Đăng xuất</button>
        `;

      if (state.user.role === 'admin') {
         adminLink.style.display = 'block';
      } else {
         adminLink.style.display = 'none';
      }
   } else {
      authLinks.innerHTML = `
            <button class="btn btn-secondary" onclick="navigateTo('login')">Đăng nhập</button>
            <button class="btn btn-primary" onclick="navigateTo('register')">Đăng ký</button>
        `;
      adminLink.style.display = 'none';
   }

   loadCartCount();
}

function navigateTo(pageId) {
   document.querySelectorAll('.page').forEach((page) => page.classList.remove('active'));
   document.querySelectorAll('.nav-links a').forEach((link) => link.classList.remove('active'));

   const targetPage = document.getElementById(`page-${pageId}`);
   if (targetPage) {
      targetPage.classList.add('active');
      state.activePage = pageId;

      const activeLink = document.querySelector(`.nav-links a[onclick="navigateTo('${pageId}')"]`);
      if (activeLink) activeLink.classList.add('active');

      // Cập nhật đường dẫn trên thanh địa chỉ tương ứng với trang (SPA Routing)
      let path = '/';
      if (pageId === 'manager-role') {
         path = '/manager/role';
      } else if (pageId === 'profile') {
         path = '/profile';
      } else if (pageId !== 'home') {
         path = '/' + pageId;
      }

      if (window.location.pathname !== path) {
         window.history.pushState({}, document.title, path);
      }

      if (pageId === 'home') {
         loadHomePage();
      } else if (pageId === 'cart') {
         loadCartPage();
      } else if (pageId === 'checkout') {
         loadCheckoutPage();
      } else if (pageId === 'orders') {
         loadOrdersPage();
      } else if (pageId === 'admin') {
         loadAdminPage();
      } else if (pageId === 'manager-role') {
         loadRoleManagerPage();
      } else if (pageId === 'profile') {
         loadProfilePage();
      } else if (pageId === 'payment-result') {
         loadPaymentResultPage();
      }
   }

   closeCartDrawer();
}

function setupEventListeners() {
   document.getElementById('cart-btn').addEventListener('click', toggleCartDrawer);
   document.getElementById('close-cart').addEventListener('click', closeCartDrawer);

   document.getElementById('form-login').addEventListener('submit', handleLoginSubmit);
   document.getElementById('form-register').addEventListener('submit', handleRegisterSubmit);
   document.getElementById('form-checkout').addEventListener('submit', handleCheckoutSubmit);

   document.querySelectorAll('.close-modal').forEach((btn) => {
      btn.addEventListener('click', () => {
         document.querySelectorAll('.modal-overlay').forEach((m) => m.classList.remove('active'));
      });
   });

   document.querySelectorAll('.modal-content').forEach((content) => {
      content.addEventListener('click', (e) => e.stopPropagation());
   });

   // Tự động đóng giỏ hàng khi click ra ngoài
   document.addEventListener('click', (event) => {
      const drawer = document.getElementById('cart-drawer');
      const cartBtn = document.getElementById('cart-btn');

      if (drawer.classList.contains('active')) {
         if (!drawer.contains(event.target) && !cartBtn.contains(event.target)) {
            closeCartDrawer();
         }
      }
   });

   // Chặn hành vi mặc định của thẻ a có href="#" để tránh thêm '#' vào URL
   document.addEventListener('click', (e) => {
      const target = e.target.closest('a');
      if (target && target.getAttribute('href') === '#') {
         e.preventDefault();
      }
   });

   // Unified Image Upload Listeners
   const selectImgBtn = document.getElementById('btn-select-images');
   const imagesInput = document.getElementById('adm-prod-images-input');
   const uploadWrapper = document.getElementById('adm-prod-upload-wrapper');

   if (selectImgBtn && imagesInput) {
      selectImgBtn.addEventListener('click', () => {
         imagesInput.click();
      });

      imagesInput.addEventListener('change', (e) => {
         handleImageSelection(e.target.files);
         e.target.value = '';
      });
   }

   if (uploadWrapper) {
      uploadWrapper.addEventListener('dragover', (e) => {
         e.preventDefault();
         uploadWrapper.classList.add('dragover');
      });

      uploadWrapper.addEventListener('dragleave', () => {
         uploadWrapper.classList.remove('dragover');
      });

      uploadWrapper.addEventListener('drop', (e) => {
         e.preventDefault();
         uploadWrapper.classList.remove('dragover');
         if (e.dataTransfer.files.length > 0) {
            handleImageSelection(e.dataTransfer.files);
         }
      });
   }
}

let toastTimeout = null;

function showToast(message, type = 'success') {
   const container = document.getElementById('toast-container');
   if (!container) return;

   let toast = container.querySelector('.toast');

   if (toast) {
      if (toastTimeout) {
         clearTimeout(toastTimeout);
      }
      toast.className = `toast toast-${type} show`;
      const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
      toast.innerHTML = `<i class="fas ${icon}"></i> <span>${message}</span><div class="toast-progress"></div>`;
   } else {
      toast = document.createElement('div');
      toast.className = `toast toast-${type}`;
      const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
      toast.innerHTML = `<i class="fas ${icon}"></i> <span>${message}</span><div class="toast-progress"></div>`;
      container.appendChild(toast);

      // Force reflow for transitions to start
      toast.offsetHeight;

      toast.classList.add('show');
   }

   toastTimeout = setTimeout(() => {
      toast.classList.remove('show');
      toastTimeout = setTimeout(() => {
         toast.remove();
         toastTimeout = null;
      }, 500);
   }, 2000);
}
