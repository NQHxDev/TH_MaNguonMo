async function loadHomePage() {
   if (state.products && state.products.length > 0) {
      renderProducts(state.products);
   } else {
      const prodRes = await fetchApi('/api/products');
      if (prodRes && prodRes.success) {
         state.products = prodRes.products;
         renderProducts(state.products);
      }
   }
}

function renderProducts(productsList) {
   const grid = document.getElementById('home-products-grid');
   if (!grid) return;
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
