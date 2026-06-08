<div class="modal-overlay" id="modal-product-detail">
   <div class="modal-content" style="max-width: 800px">
      <button class="close-modal"><i class="fas fa-times"></i></button>
      <div id="product-detail-modal-body">
      </div>
   </div>
</div>

<div class="modal-overlay" id="modal-admin-product">
   <div class="modal-content" style="max-width: 600px">
      <button class="close-modal"><i class="fas fa-times"></i></button>
      <h3
         id="adm-prod-title"
         style="
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--card-border);
            padding-bottom: 0.75rem;
         "
      >
         Thêm sản phẩm
      </h3>

      <form id="form-admin-product">
         <div class="form-group">
            <label class="form-label" for="adm-prod-name"
               >Tên sản phẩm (từ 10 đến 100 ký tự)</label
            >
            <input
               type="text"
               id="adm-prod-name"
               class="form-input"
               placeholder="ví dụ: Laptop ASUS ZenBook Duo..."
               required
               minlength="10"
               maxlength="100"
            />
         </div>
         <div class="form-group">
            <label class="form-label" for="adm-prod-price">Giá bán (USD)</label>
            <input
               type="number"
               id="adm-prod-price"
               class="form-input"
               placeholder="ví dụ: 1299"
               required
               min="1"
               step="0.01"
            />
         </div>
         <div class="form-group">
            <label class="form-label" for="adm-prod-cat">Danh mục</label>
            <select id="adm-prod-cat" class="form-input" required>
            </select>
         </div>
         <div class="form-group">
            <label class="form-label" for="adm-prod-desc">Mô tả sản phẩm</label>
            <textarea
               id="adm-prod-desc"
               class="form-input"
               rows="4"
               placeholder="nhập chi tiết cấu hình, tính năng nổi bật..."
            ></textarea>
         </div>
         <div class="form-group">
             <label class="form-label">Hình ảnh sản phẩm (Kéo thả sắp xếp thứ tự, ảnh đầu tiên là ảnh chính)</label>
             <div class="image-upload-wrapper" id="adm-prod-upload-wrapper">
                <input
                   type="file"
                   id="adm-prod-images-input"
                   accept="image/*"
                   multiple
                   style="display: none;"
                />
                <button type="button" class="btn btn-secondary" id="btn-select-images" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 1.25rem; border: 2px dashed var(--card-border); background: transparent; border-radius: 8px; cursor: pointer;">
                   <i class="fas fa-cloud-upload-alt" style="font-size: 1.5rem; color: var(--primary);"></i>
                   <span>Chọn ảnh hoặc Kéo thả ảnh vào đây</span>
                </button>
             </div>
             <div id="adm-prod-image-list" class="prod-image-list">
                <!-- Sắp xếp ảnh bằng kéo thả hoặc nút bấm -->
             </div>
          </div>
         <div
            style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 2rem"
         >
            <button type="button" class="btn btn-secondary close-modal">Hủy</button>
            <button type="submit" class="btn btn-primary">Xác nhận</button>
         </div>
      </form>
   </div>
</div>
