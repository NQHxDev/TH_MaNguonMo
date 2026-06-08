<section id="page-checkout" class="page">
   <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 2rem">
      <i class="fas fa-file-invoice-dollar"></i> Đặt hàng & Thanh toán
   </h2>

   <form id="form-checkout">
      <div class="checkout-layout">
         <div class="checkout-box">
            <h3
               style="
                  margin-bottom: 1.5rem;
                  border-bottom: 1px solid var(--card-border);
                  padding-bottom: 0.75rem;
               "
            >
               Thông tin giao hàng
            </h3>

            <div class="form-group">
               <label class="form-label" for="chk-name">Họ và tên người nhận</label>
               <input
                  type="text"
                  id="chk-name"
                  class="form-input"
                  placeholder="nhập họ và tên..."
                  required
                  autocomplete="name"
               />
            </div>
            <div class="form-group">
               <label class="form-label" for="chk-phone">Số điện thoại</label>
               <input
                  type="tel"
                  id="chk-phone"
                  class="form-input"
                  placeholder="nhập số điện thoại giao hàng..."
                  required
                  autocomplete="tel"
               />
            </div>
            <div class="form-group">
               <label class="form-label" for="chk-address">Địa chỉ chi tiết</label>
               <textarea
                  id="chk-address"
                  class="form-input"
                  rows="3"
                  placeholder="ví dụ: 123 Đường ABC, Phường X, Quận Y, Hà Nội..."
                  required
                  autocomplete="street-address"
               ></textarea>
            </div>

            <h3
               style="
                  margin: 2rem 0 1rem;
                  border-bottom: 1px solid var(--card-border);
                  padding-bottom: 0.75rem;
               "
            >
               Phương thức thanh toán
            </h3>
            <input type="hidden" id="checkout-payment-method" value="vnpay" />

            <div class="payment-method">
               <div
                  class="payment-option disabled"
                  style="opacity: 0.5; cursor: not-allowed; pointer-events: none;"
               >
                  <i
                     class="fas fa-lock"
                     style="font-size: 1.5rem; color: var(--text-muted)"
                  ></i>
                  <div>
                     <div style="font-weight: 600; color: var(--text-muted);">Thanh toán khi nhận hàng (COD) [Đã khóa]</div>
                     <div
                        style="
                           font-size: 0.8rem;
                           color: var(--danger);
                           margin-top: 0.25rem;
                        "
                     >
                        Phương thức COD hiện đang tạm khóa!
                     </div>
                  </div>
               </div>
               <div class="payment-option active" onclick="selectPaymentMethod('vnpay', this)">
                  <i class="fas fa-wallet" style="font-size: 1.5rem; color: #0070ba"></i>
                  <div>
                     <div style="font-weight: 600">Cổng thanh toán VNPay</div>
                     <div
                        style="
                           font-size: 0.8rem;
                           color: var(--text-muted);
                           margin-top: 0.25rem;
                        "
                     >
                        Chuyển hướng qua cổng VNPay để quét mã QR hoặc thẻ ATM nội địa.
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <div class="checkout-box" style="height: fit-content">
            <h3
               style="
                  margin-bottom: 1.5rem;
                  border-bottom: 1px solid var(--card-border);
                  padding-bottom: 0.75rem;
               "
            >
               Tóm tắt đơn hàng
            </h3>

            <div
               id="checkout-items-summary"
               style="
                  margin-bottom: 1.5rem;
                  border-bottom: 1px dashed var(--card-border);
                  padding-bottom: 1rem;
               "
            >
            </div>

            <div
               style="
                  display: flex;
                  justify-content: space-between;
                  font-size: 1.2rem;
                  font-weight: 700;
                  margin-bottom: 2rem;
               "
            >
               <span>Tổng cộng</span>
               <span id="checkout-grand-total" style="color: var(--primary)">$0.00</span>
            </div>

            <button
               type="submit"
               class="btn btn-primary"
               style="width: 100%; justify-content: center; padding: 1rem; font-size: 1rem"
            >
               <i class="fas fa-shopping-basket"></i> Xác nhận đặt hàng
            </button>
         </div>
      </div>
   </form>
</section>
