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
   if (!state.user || state.user.role !== 'admin') {
      showToast('Bạn không có quyền truy cập trang quản trị!', 'error');
      navigateTo('home');
      return;
   }

   const select = document.getElementById('adm-prod-cat');
   if (!select) return;
   select.innerHTML = '<option value="">Chọn danh mục...</option>';

   if (state.categories && state.categories.length > 0) {
      state.categories.forEach((cat) => {
         select.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
      });
   } else {
      const res = await fetchApi('/api/categories');
      if (res && res.success) {
         state.categories = res.categories;
         res.categories.forEach((cat) => {
            select.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
         });
      }
   }

   const activeBtn = document.querySelector('.admin-menu-btn.active');
   if (activeBtn) {
      switchAdminTab('products', activeBtn);
   }
}

async function loadAdminProducts() {
   const res = await fetchApi('/api/products');
   const tbody = document.getElementById('admin-products-tbody');
   if (!tbody) return;
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
   const res = await fetchApi('/api/admin/accounts', 'GET');
   const tbody = document.getElementById('admin-users-tbody');
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

async function updateUserRole(username, role) {
   const res = await fetchApi('/api/admin/accounts/role', 'PUT', { username, role });
   if (res && res.success) {
      showToast(res.message, 'success');

      // Token mới đã được server set qua cookie tự động (nếu tự đổi role bản thân)
      await checkAuth();

      if (state.activePage === 'admin' && state.activeAdminTab === 'users') {
         loadAdminUsers();
      } else if (state.activePage === 'manager-role') {
         loadRoleManagerUsers();
      }
   } else {
      showToast('Không thể thay đổi quyền tài khoản!', 'error');
   }
}

// ==========================================
// ADMIN IMAGE MANAGEMENT UTILITIES
// ==========================================
function handleImageSelection(files) {
   if (!state.selectedImages) {
      state.selectedImages = [];
   }
   for (let i = 0; i < files.length; i++) {
      const file = files[i];
      if (file.type.startsWith('image/')) {
         const url = URL.createObjectURL(file);
         state.selectedImages.push({
            type: 'new',
            file: file,
            url: url
         });
      }
   }
   renderSelectedImages();
}

function renderSelectedImages() {
   const container = document.getElementById('adm-prod-image-list');
   if (!container) return;
   container.innerHTML = '';
   
   if (!state.selectedImages) {
      state.selectedImages = [];
   }
   
   state.selectedImages.forEach((item, index) => {
      const isMain = index === 0;
      const badgeText = isMain ? 'Ảnh chính' : 'Ảnh phụ';
      const badgeClass = isMain ? 'main' : 'sub';
      
      const itemEl = document.createElement('div');
      itemEl.className = 'prod-image-item';
      itemEl.draggable = true;
      itemEl.dataset.index = index;
      
      itemEl.innerHTML = `
         <span class="prod-image-badge ${badgeClass}">${badgeText}</span>
         <button type="button" class="prod-image-delete" onclick="removeSelectedImage(${index})" title="Xóa ảnh">
            <i class="fas fa-times"></i>
         </button>
         <img src="${item.url}" alt="Product Image Preview">
         <div class="prod-image-controls">
            ${index > 0 ? `<button type="button" onclick="moveImage(${index}, ${index - 1})" title="Di chuyển qua trái"><i class="fas fa-arrow-left"></i></button>` : ''}
            ${index < state.selectedImages.length - 1 ? `<button type="button" onclick="moveImage(${index}, ${index + 1})" title="Di chuyển qua phải"><i class="fas fa-arrow-right"></i></button>` : ''}
         </div>
      `;
      
      // HTML5 Drag and drop implementation
      itemEl.addEventListener('dragstart', (e) => {
         e.dataTransfer.setData('text/plain', index);
         itemEl.classList.add('dragging');
      });
      
      itemEl.addEventListener('dragend', () => {
         itemEl.classList.remove('dragging');
      });
      
      itemEl.addEventListener('dragover', (e) => {
         e.preventDefault();
      });
      
      itemEl.addEventListener('drop', (e) => {
         e.preventDefault();
         const fromIndex = parseInt(e.dataTransfer.getData('text/plain'));
         const toIndex = index;
         if (fromIndex !== toIndex && !isNaN(fromIndex)) {
            reorderImages(fromIndex, toIndex);
         }
      });
      
      container.appendChild(itemEl);
   });
}

function moveImage(fromIndex, toIndex) {
   if (fromIndex < 0 || fromIndex >= state.selectedImages.length || toIndex < 0 || toIndex >= state.selectedImages.length) return;
   const temp = state.selectedImages[fromIndex];
   state.selectedImages.splice(fromIndex, 1);
   state.selectedImages.splice(toIndex, 0, temp);
   renderSelectedImages();
}

function reorderImages(fromIndex, toIndex) {
   if (fromIndex < 0 || fromIndex >= state.selectedImages.length || toIndex < 0 || toIndex >= state.selectedImages.length) return;
   const element = state.selectedImages[fromIndex];
   state.selectedImages.splice(fromIndex, 1);
   state.selectedImages.splice(toIndex, 0, element);
   renderSelectedImages();
}

function removeSelectedImage(index) {
   if (index < 0 || index >= state.selectedImages.length) return;
   if (state.selectedImages[index].type === 'new') {
      URL.revokeObjectURL(state.selectedImages[index].url);
   }
   state.selectedImages.splice(index, 1);
   renderSelectedImages();
}

function openAddProductModal() {
   state.editingProductId = null;
   state.selectedImages = [];
   document.getElementById('adm-prod-title').textContent = 'Thêm sản phẩm mới';
   document.getElementById('form-admin-product').reset();
   renderSelectedImages();
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

      // Tải ảnh cũ của sản phẩm
      state.selectedImages = [];
      if (p.image) {
         state.selectedImages.push({ type: 'existing', url: p.image });
      }
      if (p.sub_images && p.sub_images.length > 0) {
         p.sub_images.forEach(img => {
            state.selectedImages.push({ type: 'existing', url: img });
         });
      }
      renderSelectedImages();

      document.getElementById('modal-admin-product').classList.add('active');
   }
}

document.getElementById('form-admin-product').addEventListener('submit', async (e) => {
   e.preventDefault();
   const name = document.getElementById('adm-prod-name').value;
   const price = document.getElementById('adm-prod-price').value;
   const description = document.getElementById('adm-prod-desc').value;
   const category_id = document.getElementById('adm-prod-cat').value;

   const formData = new FormData();
   formData.append('name', name);
   formData.append('price', price);
   formData.append('description', description);
   if (category_id) formData.append('category_id', category_id);

   if (!state.selectedImages || state.selectedImages.length === 0) {
      showToast('Vui lòng chọn ít nhất một hình ảnh cho sản phẩm!', 'error');
      return;
   }

   state.selectedImages.forEach((img, index) => {
      if (img.type === 'existing') {
         formData.append(`images[${index}]`, img.url);
      } else if (img.type === 'new') {
         formData.append(`images[${index}]`, img.file);
      }
   });

   let res;
   if (state.editingProductId) {
      res = await fetchApi(`/api/products/${state.editingProductId}`, 'POST', formData);
   } else {
      res = await fetchApi('/api/products', 'POST', formData);
   }

   if (res && res.success) {
      showToast(res.message, 'success');
      document.getElementById('modal-admin-product').classList.remove('active');
      loadAdminProducts();
   } else {
      const msg = res?.errors ? res.errors.join('<br>') : (res?.message || 'Thao tác thất bại!');
      showToast(msg, 'error');
   }
});

async function deleteProduct(id) {
   if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')) {
      const res = await fetchApi(`/api/products/${id}`, 'DELETE');
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
   const res = await fetchApi('/api/admin/accounts', 'GET');
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
   } else if (path === 'payment-result') {
      navigateTo('payment-result');
   } else {
      navigateTo('home');
   }
}
