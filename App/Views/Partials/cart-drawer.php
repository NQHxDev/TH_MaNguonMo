<div class="cart-drawer" id="cart-drawer">
   <div class="cart-header">
      <h3><i class="fas fa-shopping-bag"></i> Giỏ hàng tạm tính</h3>
      <button class="close-modal" id="close-cart" style="position: static">
         <i class="fas fa-times"></i>
      </button>
   </div>
   <div class="cart-items" id="cart-drawer-items">
   </div>
   <div class="cart-footer">
      <div class="cart-total">
         <span>Tổng tiền:</span>
         <span id="cart-drawer-total" style="color: var(--primary)">$0.00</span>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem">
         <button
            class="btn btn-secondary"
            onclick="navigateTo('cart')"
            style="justify-content: center"
         >
            Xem chi tiết
         </button>
         <button
            class="btn btn-primary"
            onclick="navigateTo('checkout')"
            style="justify-content: center"
         >
            Thanh toán
         </button>
      </div>
   </div>
</div>
