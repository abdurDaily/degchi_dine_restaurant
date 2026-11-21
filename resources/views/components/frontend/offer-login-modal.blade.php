{{-- Offer Member Login Modal --}}
<div class="modal fade" id="offerMemberLoginModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered custom-modal">
      <div class="modal-content custom-modal-content">
          <div class="custom-modal-header">
              <div class="header-left">
                  <i class="fas fa-user-circle"></i>
                  <span>Member Login Required</span>
              </div>
              <button type="button" class="close-btn" data-bs-dismiss="modal">&times;</button>
          </div>

          <div class="custom-modal-body">
              <div class="message-box">
                  <i class="fas fa-gem"></i>
                  <p>
                      This offer is for members only (including first-order-only deals). 
                      Please log in as a member to add this item at the offer price.
                  </p>
              </div>
          </div>

          <div class="custom-modal-footer">
              <a href="{{ route('frontend.member.login') }}" class="btn-login">
                  <i class="fas fa-sign-in-alt"></i>
                  Member Login
              </a>
              <a href="{{ route('frontend.card.apply') }}" class="btn-register">
                  <i class="fas fa-user-plus"></i>
                  Register
              </a>
              <button class="btn-close-outline" data-bs-dismiss="modal">
                  ✕ Close
              </button>
          </div>
      </div>
  </div>
</div>
