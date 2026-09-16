{{-- Membership Prompt Modal --}}
<div class="modal fade" id="memberPromptModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Become a Member</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Members get exclusive discounts, priority service and rewards. Register now to apply your membership benefits.</p>
      </div>
      <div class="modal-footer">
        <a href="{{ route('frontend.card.apply') }}" class="btn btn-primary">Register Now</a>
        <button id="continueAsGuestBtn" type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Continue as Guest</button>
      </div>
    </div>
  </div>
</div>
