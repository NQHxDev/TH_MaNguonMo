<section id="page-payment-result" class="page">
   <div class="auth-container" style="max-width: 550px; margin: 4rem auto; text-align: center;">
      <div id="payment-result-icon" style="font-size: 4.5rem; margin-bottom: 1.5rem;"></div>
      <h2 id="payment-result-title" style="font-size: 2rem; font-weight: 700; margin-bottom: 1rem;">--</h2>
      <p id="payment-result-message" style="color: var(--text-muted); margin-bottom: 2rem; line-height: 1.6;"></p>
      
      <div class="checkout-box" style="text-align: left; padding: 1.5rem; margin-bottom: 2rem; background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 12px;">
         <h3 style="border-bottom: 1px solid var(--card-border); padding-bottom: 0.75rem; margin-bottom: 1rem; font-size: 1.1rem; font-weight: 600;">Thông tin giao dịch</h3>
         <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
            <span style="color: var(--text-muted); font-size: 0.95rem;">Mã giao dịch (TxnRef):</span>
            <strong id="payment-result-ref" style="color: var(--text-main); font-size: 0.95rem;">--</strong>
         </div>
         <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
            <span style="color: var(--text-muted); font-size: 0.95rem;">Số tiền thanh toán:</span>
            <strong id="payment-result-amount" style="color: var(--primary); font-size: 0.95rem;">--</strong>
         </div>
         <div id="payment-result-bank-row" style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
            <span style="color: var(--text-muted); font-size: 0.95rem;">Ngân hàng:</span>
            <strong id="payment-result-bank" style="color: var(--text-main); font-size: 0.95rem;">--</strong>
         </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
         <button class="btn btn-secondary" onclick="navigateTo('home')" style="justify-content: center; padding: 0.8rem;">Tiếp tục mua sắm</button>
         <button class="btn btn-primary" id="payment-result-action-btn" onclick="navigateTo('orders')" style="justify-content: center; padding: 0.8rem;">Xem đơn hàng</button>
      </div>
   </div>
</section>
