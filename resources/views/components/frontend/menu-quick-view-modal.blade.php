{{-- Menu Quick View Modal --}}
<div class="modal fade mc-quick-modal" id="mcQuickViewModal" tabindex="-1" aria-labelledby="mcQuickViewTitle"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content mc-modal-content">
      <button type="button" class="btn-close mc-modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="row g-0">
        <div class="col-md-6">
          <div class="mc-modal-image-wrap">
            <img id="mcQuickViewImage" src="" alt="" class="mc-modal-image" loading="lazy" />
          </div>
        </div>
        <div class="col-md-6">
          <div class="mc-modal-body">
            <p class="mc-modal-kicker mb-2">Menu Preview</p>
            <h4 id="mcQuickViewTitle" class="mc-modal-title mb-2"></h4>
            <p id="mcQuickViewDesc" class="mc-modal-desc"></p>
            <div class="mc-modal-meta">
              <span id="mcQuickViewServe" class="mc-serve-info"></span>
            </div>
            <div class="mc-modal-price-wrap mt-3">
              <span class="mc-price-label">Starts from</span>
              <span id="mcQuickViewPrice" class="mc-price"></span>
            </div>
            <a href="{{ route('frontend.home') }}#menu" class="mc-show-more-btn mt-4" data-bs-dismiss="modal">
              Explore Full Menu
              <i class="bi bi-arrow-right-short" aria-hidden="true"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
