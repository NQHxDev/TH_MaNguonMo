<section id="page-admin" class="page">
   <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 2rem">
      <i class="fas fa-user-shield"></i> Trang Quản trị Hệ thống
   </h2>

   <div class="admin-layout">
      <aside class="admin-sidebar">
         <button class="admin-menu-btn active" onclick="switchAdminTab('products', this)">
            <i class="fas fa-boxes"></i> Quản lý sản phẩm
         </button>
         <button class="admin-menu-btn" onclick="switchAdminTab('users', this)">
            <i class="fas fa-users"></i> Quản lý tài khoản
         </button>
      </aside>

      <div class="admin-content">
         <div id="admin-tab-products" class="admin-tab-pane">
            <div
               style="display: flex; justify-content: space-between; align-items: center"
            >
               <h3>Danh sách sản phẩm</h3>
               <button class="btn btn-primary" onclick="openAddProductModal()">
                  <i class="fas fa-plus"></i> Thêm sản phẩm
               </button>
            </div>

            <div class="table-wrapper">
               <table>
                  <thead>
                     <tr>
                        <th>ID</th>
                        <th>Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Giá</th>
                        <th>Thao tác</th>
                     </tr>
                  </thead>
                  <tbody id="admin-products-tbody">
                  </tbody>
               </table>
            </div>
         </div>

         <div id="admin-tab-users" class="admin-tab-pane" style="display: none">
            <h3>Phân quyền người dùng</h3>
            <div class="table-wrapper">
               <table>
                  <thead>
                     <tr>
                        <th>ID</th>
                        <th>Họ và tên</th>
                        <th>Tên đăng nhập (Email)</th>
                        <th>Vai trò (Role)</th>
                     </tr>
                  </thead>
                  <tbody id="admin-users-tbody">
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</section>
