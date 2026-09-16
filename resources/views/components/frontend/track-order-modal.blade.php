{{-- Quick Track Order Modal --}}
@guest('member')
<div class="modal fade track-order-modal" id="trackOrderModal" tabindex="-1" aria-labelledby="trackOrderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="trackOrderModalLabel">
          <iconify-icon icon="solar:delivery-linear" style="color: var(--dd-gold); font-size: 1.4rem;"></iconify-icon>
          Track Your Order
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted mb-4" style="font-size: 0.88rem;">Enter your order number and the phone number you used at checkout.</p>

        <form method="POST" action="{{ route('frontend.order.track.submit') }}" class="dd-apply-form-element" style="margin-bottom: 0;">
          @csrf
          <div class="dd-input-group">
            <input type="number" name="order_id" id="modal_track_order_id" class="dd-input-field" placeholder=" " value="{{ old('order_id', request('order')) }}" required min="1">
            <label for="modal_track_order_id" class="dd-floating-label">Order Number</label>
          </div>
          <div class="dd-input-group">
            <input type="tel" name="phone" id="modal_track_phone" class="dd-input-field" placeholder=" " value="{{ old('phone') }}" required>
            <label for="modal_track_phone" class="dd-floating-label">Phone Number</label>
          </div>
          <button type="submit" class="dd-submit-btn" style="margin-top: 8px;">
            <span>View Order</span>
            <iconify-icon icon="solar:magnifer-linear" class="dd-btn-icon"></iconify-icon>
          </button>
        </form>
        <div class="modal-footer-tip">
          Or go to the full <a href="{{ route('frontend.order.track') }}">Track Order page</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endguest
