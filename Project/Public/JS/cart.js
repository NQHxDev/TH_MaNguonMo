function toggleCartDrawer() {
   if (!state.token) {
      showToast('Vui lòng đăng nhập để xem giỏ hàng!', 'error');
      return;
   }
   document.getElementById('cart-drawer').classList.add('active');
   loadCartItems();
}

function closeCartDrawer() {
   const drawer = document.getElementById('cart-drawer');
   if (drawer) drawer.classList.remove('active');
}

async function loadCartCount() {
   if (!state.token) {
      document.getElementById('cart-count-badge').textContent = '0';
      return;
   }

   const res = await fetchApi('/api/cart', 'GET', null, true);
   if (res && res.success) {
      state.cart = res.cart;
      document.getElementById('cart-count-badge').textContent = Object.keys(state.cart).length;
   }
}

async function loadCartItems() {
   const res = await fetchApi('/api/cart', 'GET', null, true);
   if (res && res.success) {
      state.cart = res.cart;
      updateCartUI();
   }
}

function updateCartUI() {
   const container = document.getElementById('cart-drawer-items');
   const totalEl = document.getElementById('cart-drawer-total');
   if (!container || !totalEl) return;

   if (Object.keys(state.cart).length === 0) {
      container.innerHTML = `<p style="text-align:center; padding: 2rem 0; color: var(--text-muted);">Giỏ hàng trống!</p>`;
      totalEl.textContent = '$0.00';
      document.getElementById('cart-count-badge').textContent = '0';
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
   if (!grid) return;
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
   if (!itemsContainer) return;
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
   if (!container) return;

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

function loadPaymentResultPage() {
   if (!state.paymentResult) {
      navigateTo('home');
      return;
   }

   const { success, txnRef, amount, bankCode, responseCode } = state.paymentResult;

   const iconEl = document.getElementById('payment-result-icon');
   const titleEl = document.getElementById('payment-result-title');
   const msgEl = document.getElementById('payment-result-message');
   const refEl = document.getElementById('payment-result-ref');
   const amountEl = document.getElementById('payment-result-amount');
   const bankEl = document.getElementById('payment-result-bank');
   const bankRow = document.getElementById('payment-result-bank-row');
   const actionBtn = document.getElementById('payment-result-action-btn');

   if (!iconEl || !titleEl) return;

   refEl.textContent = txnRef;

   if (success) {
      iconEl.innerHTML = '<i class="fas fa-check-circle" style="color: var(--accent);"></i>';
      titleEl.textContent = 'Thanh toán thành công!';
      titleEl.style.color = 'var(--accent)';
      msgEl.textContent = 'Cảm ơn bạn đã mua sắm tại ZeionStore. Đơn hàng của bạn đã được thanh toán hoàn tất và đang được chuẩn bị để giao hàng.';

      const realAmountVnd = parseFloat(amount) / 100;
      amountEl.textContent = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(realAmountVnd);

      if (bankCode && bankCode !== '') {
         bankRow.style.display = 'flex';
         bankEl.textContent = bankCode;
      } else {
         bankRow.style.display = 'none';
      }

      actionBtn.textContent = 'Xem đơn hàng của bạn';
      actionBtn.onclick = () => navigateTo('orders');
   } else {
      iconEl.innerHTML = '<i class="fas fa-times-circle" style="color: var(--danger);"></i>';
      titleEl.textContent = 'Thanh toán thất bại!';
      titleEl.style.color = 'var(--danger)';

      let failMessage = 'Có lỗi xảy ra trong quá trình thanh toán qua cổng VNPay.';
      if (responseCode) {
         failMessage += ` (Mã phản hồi từ ngân hàng: ${responseCode})`;
      }
      msgEl.textContent = failMessage + ' Vui lòng thử thanh toán lại hoặc liên hệ với bộ phận chăm sóc khách hàng.';

      amountEl.textContent = 'Chưa thanh toán';
      bankRow.style.display = 'none';

      actionBtn.textContent = 'Quay lại giỏ hàng';
      actionBtn.onclick = () => navigateTo('cart');
   }
}
