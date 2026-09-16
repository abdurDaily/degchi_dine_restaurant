{{-- Cart Drawer --}}
<div class="offcanvas offcanvas-end cart-drawer" tabindex="-1" id="cartDrawer" aria-labelledby="cartDrawerLabel">
  <div class="offcanvas-header cart-drawer-header">
    <div class="cart-drawer-heading">
      <h5 class="offcanvas-title" id="cartDrawerLabel">Your Cart</h5>
      <p class="cart-drawer-subtitle mb-0" id="cartDrawerCount">No items yet</p>
    </div>
    <button type="button" class="btn-close-custom" data-bs-dismiss="offcanvas" aria-label="Close">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>

  <div class="offcanvas-body cart-drawer-body d-flex flex-column">
    <div id="cartDrawerItems" class="cart-drawer-items flex-grow-1">
      <div class="cart-drawer-empty">
        <div class="cart-drawer-empty-icon" aria-hidden="true">
          <i class="bi bi-bag"></i>
        </div>
        <p class="cart-drawer-empty-title">Your cart is empty</p>
        <p class="cart-drawer-empty-text">Add dishes from the menu to get started.</p>
      </div>
    </div>

    <div class="cart-drawer-footer">
      <div class="cart-drawer-total-row">
        <span class="cart-total-label">Subtotal</span>
        <strong id="cartDrawerSubtotal" class="cart-total-value">৳ 0.00</strong>
      </div>
      <div class="cart-drawer-actions d-grid gap-2">
        <a href="{{ route('frontend.addtocart') }}" class="btn cart-view-btn">View Full Cart</a>
        <a href="{{ route('frontend.checkout') }}" class="btn cart-checkout-btn">
          <span>Proceed to Checkout <i class="bi bi-arrow-right ms-1"></i></span>
        </a>
      </div>
    </div>
  </div>
</div>
