const state = {
   user: null,
   token: localStorage.getItem('token') || null,
   guestId: getOrCreateGuestId(),
   products: [],
   categories: [],
   cart: {},
   orders: [],
   activePage: 'home',
   activeAdminTab: 'products',
   editingProductId: null,
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
      showToast('Đăng nhập liên kết thành công!', 'success');
      window.history.replaceState({}, document.title, '/');
   }

   if (params.has('message')) {
      const message = params.get('message');
      showToast('Đăng nhập thất bại: ' + message, 'error');
      window.history.replaceState({}, document.title, '/');
   }

   if (params.has('success') && params.has('txnRef')) {
      const success = params.get('success') === 'true';
      const txnRef = params.get('txnRef');
      if (success) {
         showToast(`Thanh toán đơn hàng #${txnRef} thành công!`, 'success');

         state.cart = {};
         updateCartUI();
         navigateTo('orders');
      } else {
         const code = params.get('responseCode') || 'error';
         showToast(`Thanh toán thất bại! Mã lỗi: ${code}`, 'error');
         navigateTo('cart');
      }
      window.history.replaceState({}, document.title, '/');
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
}

function showToast(message, type = 'success') {
   const container = document.getElementById('toast-container');
   const toast = document.createElement('div');
   toast.className = `toast toast-${type}`;

   const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
   toast.innerHTML = `<i class="fas ${icon}"></i> <span>${message}</span>`;

   container.appendChild(toast);

   setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(100%)';
      setTimeout(() => toast.remove(), 300);
   }, 3000);
}

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

      await checkAuth();
      navigateTo('home');
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
   showToast('Đã đăng xuất tài khoản');
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

async function loadHomePage() {
   const prodRes = await fetchApi('/api/products');
   if (prodRes && prodRes.success) {
      state.products = prodRes.products;
      renderProducts(state.products);
   }
}

function renderProducts(productsList) {
   const grid = document.getElementById('home-products-grid');
   if (productsList.length === 0) {
      grid.innerHTML = `<p style="grid-column: 1/-1; text-align:center; padding: 3rem 0; color: var(--text-muted);">Không có sản phẩm nào trong danh mục này</p>`;
      return;
   }

   grid.innerHTML = '';
   productsList.forEach((p) => {
      const imgPath = p.image
         ? p.image
         : 'https://placehold.co/300x300/151821/ffffff?text=No+Image';
      const priceFormatted = new Intl.NumberFormat('en-US', {
         style: 'currency',
         currency: 'USD',
      }).format(p.price);

      grid.innerHTML += `
            <div class="product-card" onclick="openProductDetails(${p.id})">
                <div class="product-img-wrapper">
                    <img src="${imgPath}" alt="${p.name}" class="product-img">
                </div>
                <div class="product-info">
                    <div class="product-cat">${p.category_name || 'Khác'}</div>
                    <div class="product-name">${p.name}</div>
                    <div class="product-footer">
                        <div class="product-price">${priceFormatted}</div>
                        <button class="btn btn-primary" onclick="handleAddToCartClick(event, ${p.id})">
                            <i class="fas fa-shopping-cart"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
   });
}

function handleAddToCartClick(event, id) {
   event.stopPropagation();
   addToCart(id);
}

async function openProductDetails(id) {
   const res = await fetchApi(`/api/products/${id}`);
   if (res && res.success) {
      const p = res.product;
      const mainImg = p.image
         ? p.image
         : 'https://placehold.co/500x500/151821/ffffff?text=No+Image';
      const priceFormatted = new Intl.NumberFormat('en-US', {
         style: 'currency',
         currency: 'USD',
      }).format(p.price);

      const content = document.getElementById('product-detail-modal-body');
      content.innerHTML = `
            <div class="product-detail-layout">
                <div class="detail-img-box">
                    <img src="${mainImg}" id="detail-main-view" class="detail-main-img">
                    <div class="detail-thumbs" id="detail-thumbnails-box">
                        <img src="${mainImg}" class="detail-thumb active" onclick="changeDetailImage('${mainImg}', this)">
                    </div>
                </div>
                <div class="detail-info">
                    <span class="product-cat" style="font-size:0.85rem;">${p.category_name || 'Khác'}</span>
                    <h2 style="font-size:1.8rem; margin:0.5rem 0 1rem;">${p.name}</h2>
                    <div class="product-price" style="font-size:1.6rem; margin-bottom: 1.5rem; color: var(--primary);">${priceFormatted}</div>
                    <p style="color: var(--text-muted); line-height: 1.6; margin-bottom: 2rem;">
                        ${p.description ? p.description.replace(/\n/g, '<br>') : 'Không có mô tả cho sản phẩm này.'}
                    </p>
                    <button class="btn btn-primary" style="width:100%; justify-content:center; padding: 0.9rem;" onclick="addToCart(${p.id})">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                    </button>
                </div>
            </div>
        `;

      if (p.sub_images && p.sub_images.length > 0) {
         const thumbs = document.getElementById('detail-thumbnails-box');
         p.sub_images.forEach((subImg) => {
            thumbs.innerHTML += `<img src="${subImg}" class="detail-thumb" onclick="changeDetailImage('${subImg}', this)">`;
         });
      }

      document.getElementById('modal-product-detail').classList.add('active');
   }
}

function changeDetailImage(src, thumbEl) {
   document.getElementById('detail-main-view').src = src;
   document.querySelectorAll('.detail-thumb').forEach((t) => t.classList.remove('active'));
   thumbEl.classList.add('active');
}

function toggleCartDrawer() {
   if (!state.token) {
      showToast('Vui lòng đăng nhập để xem giỏ hàng!', 'error');
      return;
   }
   document.getElementById('cart-drawer').classList.add('active');
   loadCartItems();
}

function closeCartDrawer() {
   document.getElementById('cart-drawer').classList.remove('active');
}

async function loadCartCount() {
   const res = await fetchApi('/api/cart', 'GET', null, !!state.token);
   let count = 0;
   if (res && res.success) {
      state.cart = res.cart;
      count = Object.keys(res.cart).length;
   }
   document.getElementById('cart-count-badge').textContent = count;
}

async function loadCartItems() {
   const res = await fetchApi('/api/cart', 'GET', null, !!state.token);
   if (res && res.success) {
      state.cart = res.cart;
      updateCartUI();
   }
}

function updateCartUI() {
   const container = document.getElementById('cart-drawer-items');
   const totalEl = document.getElementById('cart-drawer-total');

   if (Object.keys(state.cart).length === 0) {
      container.innerHTML = `<p style="text-align:center; padding:3rem 0; color: var(--text-muted);">Giỏ hàng đang trống.</p>`;
      totalEl.textContent = '$0.00';
      return;
   }

   container.innerHTML = '';
   let grandTotal = 0;

   for (const id in state.cart) {
      const item = state.cart[id];
      const price = parseFloat(item.price);
      const qty = parseInt(item.quantity);
      grandTotal += price * qty;

      const priceFormatted = new Intl.NumberFormat('en-US', {
         style: 'currency',
         currency: 'USD',
      }).format(price);
      const imgPath = item.image
         ? item.image
         : 'https://placehold.co/100x100/151821/ffffff?text=No+Img';

      container.innerHTML += `
            <div class="cart-item">
                <img src="${imgPath}" alt="${item.name}" class="cart-item-img">
                <div class="cart-item-info">
                    <div class="cart-item-title">${item.name}</div>
                    <div class="cart-item-price">${priceFormatted}</div>
                    <div class="cart-item-qty">
                        <button class="qty-btn" onclick="updateQuantity(${id}, ${qty - 1})">-</button>
                        <span class="qty-input">${qty}</span>
                        <button class="qty-btn" onclick="updateQuantity(${id}, ${qty + 1})">+</button>
                        <button class="btn btn-secondary" style="padding: 0.2rem 0.5rem; margin-left:auto; border-radius: 4px; background: transparent; border:none;" onclick="removeFromCart(${id})">
                            <i class="fas fa-trash" style="color:var(--danger)"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
   }

   totalEl.textContent = new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD',
   }).format(grandTotal);

   document.getElementById('cart-count-badge').textContent = Object.keys(state.cart).length;
}

async function addToCart(productId) {
   const res = await fetchApi(`/api/cart/add`, 'POST', { product_id: productId }, !!state.token);
   if (res && res.success) {
      showToast(res.message, 'success');
      loadCartCount();

      if (document.getElementById('cart-drawer').classList.contains('active')) {
         loadCartItems();
      }
   } else {
      showToast('Không thể thêm vào giỏ hàng!', 'error');
   }
}

async function updateQuantity(productId, qty) {
   const quantities = {};
   quantities[productId] = qty;

   const res = await fetchApi(`/api/cart/update`, 'PUT', { cart_items: quantities }, !!state.token);
   if (res && res.success) {
      state.cart = res.cart;
      updateCartUI();
   }
}

async function removeFromCart(productId) {
   const res = await fetchApi(`/api/cart/remove`, 'POST', { product_id: productId }, !!state.token);
   if (res && res.success) {
      showToast(res.message, 'success');
      loadCartItems();
   }
}

function loadCartPage() {
   const grid = document.getElementById('cart-page-layout');
   let grandTotal = 0;

   if (Object.keys(state.cart).length === 0) {
      grid.innerHTML = `
            <div style="grid-column: 1/-1; text-align:center; padding: 4rem 1rem;">
                <i class="fas fa-shopping-basket" style="font-size: 4rem; color: var(--text-muted); margin-bottom: 1.5rem;"></i>
                <h3 style="margin-bottom: 1rem;">Giỏ hàng của bạn đang trống!</h3>
                <button class="btn btn-primary" onclick="navigateTo('home')">Quay lại trang chủ</button>
            </div>
        `;
      return;
   }

   let itemsHtml = '';
   for (const id in state.cart) {
      const item = state.cart[id];
      const price = parseFloat(item.price);
      const qty = parseInt(item.quantity);
      const itemTotal = price * qty;
      grandTotal += itemTotal;

      const priceFormatted = new Intl.NumberFormat('en-US', {
         style: 'currency',
         currency: 'USD',
      }).format(price);
      const totalFormatted = new Intl.NumberFormat('en-US', {
         style: 'currency',
         currency: 'USD',
      }).format(itemTotal);
      const imgPath = item.image
         ? item.image
         : 'https://placehold.co/100x100/151821/ffffff?text=No+Img';

      itemsHtml += `
            <tr class="cart-row-item">
                <td style="display:flex; align-items:center; gap: 1rem;">
                    <img src="${imgPath}" style="width:60px; height:60px; object-fit:cover; border-radius:8px;">
                    <div>
                        <div style="font-weight:600;">${item.name}</div>
                    </div>
                </td>
                <td style="font-weight:600;">${priceFormatted}</td>
                <td>
                    <div style="display:flex; align-items:center; gap:0.5rem;">
                        <button class="qty-btn" onclick="updateCartPageQuantity(${id}, ${qty - 1})">-</button>
                        <span>${qty}</span>
                        <button class="qty-btn" onclick="updateCartPageQuantity(${id}, ${qty + 1})">+</button>
                    </div>
                </td>
                <td style="font-weight:700; color:var(--primary);">${totalFormatted}</td>
                <td>
                    <button class="btn btn-secondary" style="border:none; padding: 0.5rem;" onclick="removeCartPageItem(${id})">
                        <i class="fas fa-trash" style="color:var(--danger);"></i>
                    </button>
                </td>
            </tr>
        `;
   }

   const grandTotalFormatted = new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD',
   }).format(grandTotal);

   grid.innerHTML = `
        <div class="checkout-layout">
            <div class="checkout-box" style="padding: 1.5rem; overflow-x:auto;">
                <table style="width:100%;">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Tổng</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itemsHtml}
                    </tbody>
                </table>
            </div>
            <div class="checkout-box" style="height:fit-content;">
                <h3 style="margin-bottom:1.5rem; border-bottom: 1px solid var(--card-border); padding-bottom: 0.75rem;">Tổng tiền</h3>
                <div style="display:flex; justify-content:space-between; margin-bottom:1rem; font-size:1.1rem;">
                    <span>Tạm tính</span>
                    <span style="font-weight:600;">${grandTotalFormatted}</span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:2rem; font-size:1.2rem; font-weight:700; border-top: 1px solid var(--card-border); padding-top: 1rem;">
                    <span>Tổng tiền</span>
                    <span style="color:var(--primary);">${grandTotalFormatted}</span>
                </div>
                <button class="btn btn-primary" style="width:100%; justify-content:center; padding: 0.9rem;" onclick="navigateTo('checkout')">
                    Tiến hành đặt hàng
                </button>
            </div>
        </div>
    `;
}

async function updateCartPageQuantity(id, qty) {
   await updateQuantity(id, qty);
   loadCartPage();
}

async function removeCartPageItem(id) {
   await removeFromCart(id);
   loadCartPage();
}

function loadCheckoutPage() {
   if (!state.token) {
      showToast('Vui lòng đăng nhập để đặt hàng!', 'error');
      navigateTo('login');
      return;
   }

   let grandTotal = 0;
   const itemsContainer = document.getElementById('checkout-items-summary');
   itemsContainer.innerHTML = '';

   for (const id in state.cart) {
      const item = state.cart[id];
      const price = parseFloat(item.price);
      const qty = parseInt(item.quantity);
      grandTotal += price * qty;

      const itemTotalFormatted = new Intl.NumberFormat('en-US', {
         style: 'currency',
         currency: 'USD',
      }).format(price * qty);

      itemsContainer.innerHTML += `
            <div style="display:flex; justify-content:space-between; font-size:0.9rem; margin-bottom:0.75rem;">
                <span style="color:var(--text-muted);">${item.name} <strong style="color:var(--text-main);">x${qty}</strong></span>
                <span style="font-weight:600;">${itemTotalFormatted}</span>
            </div>
        `;
   }

   const grandTotalFormatted = new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD',
   }).format(grandTotal);
   document.getElementById('checkout-grand-total').textContent = grandTotalFormatted;
}

function selectPaymentMethod(method, optionEl) {
   document.querySelectorAll('.payment-option').forEach((opt) => opt.classList.remove('active'));
   optionEl.classList.add('active');
   document.getElementById('checkout-payment-method').value = method;
}

async function handleCheckoutSubmit(e) {
   e.preventDefault();
   const name = document.getElementById('chk-name').value;
   const phone = document.getElementById('chk-phone').value;
   const address = document.getElementById('chk-address').value;
   const payment_method = document.getElementById('checkout-payment-method').value;

   const body = { name, phone, address, payment_method };

   const res = await fetchApi('/api/orders', 'POST', body, true);
   if (res && res.success) {
      if (res.paymentUrl) {
         showToast('Đang kết nối cổng VNPay...');
         window.location.href = res.paymentUrl;
      } else {
         showToast('Đặt hàng thành công! Đơn hàng đang được xử lý.', 'success');
         state.cart = {};
         updateNavbar();
         e.target.reset();
         navigateTo('orders');
      }
   } else {
      showToast(res?.message || 'Đặt hàng thất bại!', 'error');
   }
}

async function loadOrdersPage() {
   if (!state.token) {
      showToast('Vui lòng đăng nhập để xem đơn hàng!', 'error');
      navigateTo('login');
      return;
   }

   const res = await fetchApi('/api/orders', 'GET', null, true);
   const container = document.getElementById('orders-list');

   if (res && res.success) {
      state.orders = res.orders;

      if (state.orders.length === 0) {
         container.innerHTML = `<p style="text-align:center; padding:4rem 0; color:var(--text-muted);">Bạn chưa đặt đơn hàng nào!</p>`;
         return;
      }

      container.innerHTML = '';
      state.orders.forEach((o) => {
         const dateStr = new Date(o.created_at).toLocaleString('vi-VN');
         const totalFormatted = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
         }).format(o.total_usd);
         const statusClass = o.payment_status === 'paid' ? 'status-paid' : 'status-unpaid';
         const statusText = o.payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán (COD)';

         let detailsHtml = '';
         if (o.details) {
            o.details.forEach((d) => {
               const priceFormatted = new Intl.NumberFormat('en-US', {
                  style: 'currency',
                  currency: 'USD',
               }).format(d.price);
               const img = d.image
                  ? d.image
                  : 'https://placehold.co/60x60/151821/ffffff?text=No+Img';
               detailsHtml += `
                        <div style="display:flex; gap:1rem; align-items:center;">
                            <img src="${img}" style="width:40px; height:40px; object-fit:cover; border-radius:4px;">
                            <div style="flex-grow:1;">
                                <div style="font-size:0.9rem; font-weight:600;">${d.product_name}</div>
                                <div style="font-size:0.8rem; color:var(--text-muted);">${priceFormatted} x ${d.quantity}</div>
                            </div>
                        </div>
                    `;
            });
         }

         container.innerHTML += `
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <div style="font-weight:700;">Đơn hàng #${o.vnpay_txn_ref}</div>
                            <div style="font-size:0.8rem; color:var(--text-muted); margin-top:0.25rem;">${dateStr}</div>
                        </div>
                        <div>
                            <span class="status-badge ${statusClass}">${statusText}</span>
                        </div>
                    </div>
                    <div class="order-items" style="margin-bottom:1rem; padding-bottom:1rem; border-bottom:1px dashed var(--card-border);">
                        ${detailsHtml}
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:0.85rem; color:var(--text-muted);">Người nhận: <strong>${o.customer_name}</strong> - ${o.customer_phone} (${o.customer_address})</span>
                        <span style="font-size:1.1rem; font-weight:700; color:var(--primary);">Tổng: ${totalFormatted}</span>
                    </div>
                </div>
            `;
      });
   }
}

function switchAdminTab(tab, btnEl) {
   document.querySelectorAll('.admin-menu-btn').forEach((btn) => btn.classList.remove('active'));
   btnEl.classList.add('active');

   document.querySelectorAll('.admin-tab-pane').forEach((pane) => (pane.style.display = 'none'));
   document.getElementById(`admin-tab-${tab}`).style.display = 'block';
   state.activeAdminTab = tab;

   if (tab === 'products') {
      loadAdminProducts();
   } else if (tab === 'users') {
      loadAdminUsers();
   }
}

async function loadAdminPage() {
   if (!state.token || !state.user || state.user.role !== 'admin') {
      showToast('Bạn không có quyền truy cập trang quản trị!', 'error');
      navigateTo('home');
      return;
   }

   const res = await fetchApi('/api/categories');
   if (res && res.success) {
      const select = document.getElementById('adm-prod-cat');
      select.innerHTML = '<option value="">Chọn danh mục...</option>';
      res.categories.forEach((cat) => {
         select.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
      });
   }

   switchAdminTab('products', document.querySelector('.admin-menu-btn.active'));
}

async function loadAdminProducts() {
   const res = await fetchApi('/api/products');
   const tbody = document.getElementById('admin-products-tbody');
   tbody.innerHTML = '';

   if (res && res.success) {
      res.products.forEach((p) => {
         const priceFormatted = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
         }).format(p.price);
         const img = p.image ? p.image : 'https://placehold.co/50x50/151821/ffffff?text=No+Img';
         tbody.innerHTML += `
                <tr class="admin-product-row">
                    <td>${p.id}</td>
                    <td><img src="${img}" alt="${p.name}"></td>
                    <td style="font-weight:600; width: 30%;">${p.name}</td>
                    <td>${p.category_name || 'Khác'}</td>
                    <td style="font-weight:700;">${priceFormatted}</td>
                    <td>
                        <button class="btn btn-secondary" style="padding:0.4rem 0.8rem; font-size:0.8rem;" onclick="openEditProduct(${p.id})">Sửa</button>
                        <button class="btn btn-danger" style="padding:0.4rem 0.8rem; font-size:0.8rem;" onclick="deleteProduct(${p.id})">Xóa</button>
                    </td>
                </tr>
            `;
      });
   }
}

async function loadAdminUsers() {
   const res = await fetchApi('/api/admin/accounts', 'GET', null, true);
   const tbody = document.getElementById('admin-users-tbody');
   tbody.innerHTML = '';

   if (res && res.success) {
      res.accounts.forEach((acc) => {
         const isSelf = false; // Bỏ chặn tự thay đổi quyền của bản thân
         tbody.innerHTML += `
                 <tr>
                     <td>${acc.id}</td>
                     <td style="font-weight:600;">${acc.fullname}</td>
                     <td>${acc.username}</td>
                     <td>
                         <div class="role-switch-container ${isSelf ? 'disabled' : ''}">
                             <button class="role-switch-btn ${acc.role === 'user' ? 'active' : ''}"
                                 onclick="${isSelf ? '' : `updateUserRole('${acc.username}', 'user')`}">
                                 User
                             </button>
                             <button class="role-switch-btn ${acc.role === 'admin' ? 'active' : ''}"
                                 onclick="${isSelf ? '' : `updateUserRole('${acc.username}', 'admin')`}">
                                 Admin
                             </button>
                         </div>
                     </td>
                 </tr>
             `;
      });
   }
}

async function updateUserRole(username, role) {
   const res = await fetchApi('/api/admin/accounts/role', 'PUT', { username, role }, true);
   if (res && res.success) {
      showToast(res.message, 'success');

      // Nếu có token mới (do tự thay đổi vai trò của bản thân)
      if (res.token) {
         state.token = res.token;
         localStorage.setItem('token', res.token);
         await checkAuth(); // Tải lại thông tin user mới và cập nhật navbar
      }

      if (state.activePage === 'admin' && state.activeAdminTab === 'users') {
         loadAdminUsers();
      } else if (state.activePage === 'manager-role') {
         loadRoleManagerUsers();
      }
   } else {
      showToast('Không thể thay đổi quyền tài khoản!', 'error');
   }
}

function openAddProductModal() {
   state.editingProductId = null;
   document.getElementById('adm-prod-title').textContent = 'Thêm sản phẩm mới';
   document.getElementById('form-admin-product').reset();
   document.getElementById('modal-admin-product').classList.add('active');
}

async function openEditProduct(id) {
   const res = await fetchApi(`/api/products/${id}`);
   if (res && res.success) {
      const p = res.product;
      state.editingProductId = p.id;
      document.getElementById('adm-prod-title').textContent = 'Chỉnh sửa sản phẩm';
      document.getElementById('adm-prod-name').value = p.name;
      document.getElementById('adm-prod-price').value = p.price;
      document.getElementById('adm-prod-desc').value = p.description || '';
      document.getElementById('adm-prod-cat').value = p.category_id || '';

      document.getElementById('modal-admin-product').classList.add('active');
   }
}

document.getElementById('form-admin-product').addEventListener('submit', async (e) => {
   e.preventDefault();
   const name = document.getElementById('adm-prod-name').value;
   const price = document.getElementById('adm-prod-price').value;
   const description = document.getElementById('adm-prod-desc').value;
   const category_id = document.getElementById('adm-prod-cat').value;

   const imageInput = document.getElementById('adm-prod-img');
   const subImagesInput = document.getElementById('adm-prod-sub-imgs');

   const formData = new FormData();
   formData.append('name', name);
   formData.append('price', price);
   formData.append('description', description);
   if (category_id) formData.append('category_id', category_id);

   if (imageInput.files[0]) {
      formData.append('image', imageInput.files[0]);
   }

   if (subImagesInput.files.length > 0) {
      for (let i = 0; i < subImagesInput.files.length; i++) {
         formData.append('sub_images[]', subImagesInput.files[i]);
      }
   }

   let res;
   if (state.editingProductId) {
      res = await fetchApi(`/api/products/${state.editingProductId}`, 'POST', formData, true);
   } else {
      res = await fetchApi('/api/products', 'POST', formData, true);
   }

   if (res && res.success) {
      showToast(res.message, 'success');
      document.getElementById('modal-admin-product').classList.remove('active');
      loadAdminProducts();
   } else {
      const msg = res?.errors ? res.errors.join('<br>') : 'Thao tác thất bại!';
      showToast(msg, 'error');
   }
});

async function deleteProduct(id) {
   if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')) {
      const res = await fetchApi(`/api/products/${id}`, 'DELETE', null, true);
      if (res && res.success) {
         showToast(res.message, 'success');
         loadAdminProducts();
      } else {
         showToast(res?.message || 'Lỗi khi xóa sản phẩm!', 'error');
      }
   }
}

// ==========================================
// DEDICATED MANAGER ROLE PAGE
// ==========================================
async function loadRoleManagerPage() {
   await loadRoleManagerUsers();
}

async function loadRoleManagerUsers() {
   const res = await fetchApi('/api/admin/accounts', 'GET', null, true);
   const tbody = document.getElementById('manager-role-tbody');
   if (!tbody) return;
   tbody.innerHTML = '';

   if (res && res.success) {
      res.accounts.forEach((acc) => {
         const isSelf = false; // Bỏ chặn tự thay đổi quyền của bản thân
         tbody.innerHTML += `
            <tr>
               <td>${acc.id}</td>
               <td style="font-weight:600;">${acc.fullname}</td>
               <td>${acc.username}</td>
               <td>
                  <div class="role-switch-container ${isSelf ? 'disabled' : ''}">
                     <button class="role-switch-btn ${acc.role === 'user' ? 'active' : ''}"
                        onclick="${isSelf ? '' : `updateUserRole('${acc.username}', 'user')`}">
                        User
                     </button>
                     <button class="role-switch-btn ${acc.role === 'admin' ? 'active' : ''}"
                        onclick="${isSelf ? '' : `updateUserRole('${acc.username}', 'admin')`}">
                        Admin
                     </button>
                  </div>
               </td>
            </tr>
         `;
      });
   }
}

function handleInitialRoute() {
   const path = window.location.pathname.replace(/^\/|\/$/g, '');
   if (path === 'manager/role') {
      navigateTo('manager-role');
   } else if (path === 'profile') {
      navigateTo('profile');
   } else if (path === 'orders') {
      navigateTo('orders');
   } else if (path === 'cart') {
      navigateTo('cart');
   } else if (path === 'checkout') {
      navigateTo('checkout');
   } else {
      navigateTo('home');
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
