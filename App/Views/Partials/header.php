<header>
   <div class="navbar">
      <a href="#" class="brand" onclick="navigateTo('home')">
         <i class="fas fa-bolt"></i> <span>ZeionStore</span>
      </a>

      <ul class="nav-links">
         <li>
            <a href="#" onclick="navigateTo('home')" class="active"
               ><i class="fas fa-home"></i> Cửa hàng</a
            >
         </li>
         <li>
            <a href="#" id="nav-orders-link" onclick="navigateTo('orders')"
               ><i class="fas fa-history"></i> Đơn hàng</a
            >
         </li>
         <li>
            <a
               href="#"
               id="nav-admin-link"
               onclick="navigateTo('admin')"
               style="display: none"
               ><i class="fas fa-user-shield"></i> Admin Panel</a
            >
         </li>
      </ul>

      <div class="nav-actions">
         <button class="icon-btn" id="cart-btn">
            <i class="fas fa-shopping-bag"></i>
            <span class="badge" id="cart-count-badge">0</span>
         </button>

         <div id="auth-links" class="nav-links">
         </div>
      </div>
   </div>
</header>
