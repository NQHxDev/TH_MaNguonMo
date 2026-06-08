<section id="page-manager-role" class="page">
   <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 2rem">
      <i class="fas fa-users-cog"></i> Quản lý vai trò người dùng
   </h2>
   <div class="checkout-box" style="padding: 2rem">
      <div
         style="
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
         "
      >
         <h3>Danh sách tài khoản hệ thống</h3>
         <button class="btn btn-secondary" onclick="loadRoleManagerUsers()">
            <i class="fas fa-sync"></i> Làm mới
         </button>
      </div>
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
            <tbody id="manager-role-tbody">
            </tbody>
         </table>
      </div>
   </div>
</section>
