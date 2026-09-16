{{-- Offer Member Login Modal --}}
<div class="modal fade" id="offerMemberLoginModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered custom-modal">
      <div class="modal-content custom-modal-content">
          <div class="custom-modal-header">
              <div class="header-left">
                  <i class="fas fa-user-circle"></i>
                  <span>সদস্য লগইন প্রয়োজন</span>
              </div>
              <button type="button" class="close-btn" data-bs-dismiss="modal">&times;</button>
          </div>

          <div class="custom-modal-body">
              <div class="message-box">
                  <i class="fas fa-gem"></i>
                  <p>
                      এই অফারটি শুধুমাত্র সদস্যদের জন্য (ফার্স্ট অর্ডার অনলি ডিল সহ)। 
                      দয়া করে সদস্য হিসেবে লগইন করুন এই আইটেমটি অফার মূল্যে যুক্ত করতে।
                  </p>
              </div>
          </div>

          <div class="custom-modal-footer">
              <a href="{{ route('frontend.member.login') }}" class="btn-login">
                  <i class="fas fa-sign-in-alt"></i>
                  সদস্য লগইন
              </a>
              <a href="{{ route('frontend.card.apply') }}" class="btn-register">
                  <i class="fas fa-user-plus"></i>
                  নিবন্ধন
              </a>
              <button class="btn-close-outline" data-bs-dismiss="modal">
                  ✕ বন্ধ করুন
              </button>
          </div>
      </div>
  </div>
</div>
