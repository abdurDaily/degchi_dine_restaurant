@if (isset($popupOffer) && $popupOffer)
    <div id="offerPopupOverlay"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.65); z-index:9990; align-items:center; justify-content:center;">
        <div
            style="position:relative; background:#1a6674; border-radius:18px; max-width:480px; width:90%; overflow:hidden; box-shadow:0 24px 60px rgba(0,0,0,.4); animation:offerPopIn .4s cubic-bezier(.34,1.56,.64,1);">
            {{-- Close button --}}
            <button onclick="closeOfferPopup()"
                style="position:absolute; top:12px; right:12px; z-index:2; background:rgba(250,245,235,.2); border:none; color:#faf5eb; border-radius:50%; width:32px; height:32px; font-size:1.1rem; cursor:pointer; display:flex; align-items:center; justify-content:center;">&times;</button>

            {{-- Badge --}}
            @if ($popupOffer->popup_badge)
                <div
                    style="position:absolute; top:16px; left:16px; z-index:2; background:#e7ae07; color:#0d3d4a; font-size:.72rem; font-weight:700; letter-spacing:.08em; padding:4px 12px; border-radius:20px; text-transform:uppercase;">
                    {{ $popupOffer->popup_badge }}
                </div>
            @endif

            {{-- Image --}}
            @if ($popupOffer->popup_image)
                <img src="{{ asset('storage/' . $popupOffer->popup_image) }}"
                    alt="{{ $popupOffer->name }} - Special Offer at Degchi Dine"
                    onerror="this.src='{{ asset('assets/placeholder/placeholder.png') }}'"
                    style="width:100%; max-height:240px; object-fit:cover; display:block;">
            @else
                <div
                    style="background:linear-gradient(135deg,#0f4a55,#1a6674); height:140px; display:flex; align-items:center; justify-content:center;">
                    <span style="font-size:3.5rem;">🎉</span>
                </div>
            @endif

            {{-- Body --}}
            <div style="padding:24px 26px 28px;">
                @if ($popupOffer->discount_percent > 0)
                    <div style="font-size:2.8rem; font-weight:800; color:#e7ae07; line-height:1; margin-bottom:4px;">
                        {{ $popupOffer->discount_percent }}% <span
                            style="font-size:1.1rem; font-weight:600; color:#faf5eb;">OFF</span>
                    </div>
                @endif
                <h3
                    style="font-size:1.2rem; font-weight:700; color:#faf5eb; margin:6px 0 8px; font-family:'Poppins',sans-serif;">
                    {{ $popupOffer->name }}</h3>
                @if ($popupOffer->description)
                    <p style="font-size:.88rem; color:rgba(250,245,235,.75); margin:0 0 20px; line-height:1.55;">
                        {{ $popupOffer->description }}</p>
                @endif

                @php
                    $redirectUrl = route('frontend.completeMenu');
                    if ($popupOffer->offer_type === 'specific_items' && $popupOffer->menuVariations->isNotEmpty()) {
                        $popupCategories = $popupOffer->menuVariations->pluck('menu.category')->unique()->filter();
                        if ($popupCategories->count() === 1) {
                            $redirectUrl = route('frontend.completeMenu', [
                                'category' => $popupCategories->first()->slug,
                                'offer' => $popupOffer->id,
                            ]);
                        } else {
                            $redirectUrl = route('frontend.completeMenu', ['offer' => $popupOffer->id]);
                        }
                    }
                @endphp

                <div style="display:flex; gap:10px;">
                    <a href="{{ $redirectUrl }}"
                        style="flex:1; background:#e7ae07; color:#0d3d4a; text-align:center; padding:11px; border-radius:10px; text-decoration:none; font-weight:700; font-size:.88rem;">
                        Order Now &rarr;
                    </a>
                    <button onclick="closeOfferPopup()"
                        style="flex:1; background:rgba(250,245,235,.1); color:#faf5eb; border:1px solid rgba(250,245,235,.2); padding:11px; border-radius:10px; font-weight:600; font-size:.88rem; cursor:pointer;">
                        Maybe Later
                    </button>
                </div>
                <label
                    style="display:flex; align-items:center; gap:6px; margin-top:14px; font-size:.78rem; color:rgba(250,245,235,.5); cursor:pointer;">
                    <input type="checkbox" id="offerDontShow" style="cursor:pointer;">
                    Don't show again today
                </label>
            </div>
        </div>
    </div>
    <style>
        @keyframes offerPopIn {
            from { transform: scale(.7); opacity: 0; }
            to   { transform: scale(1); opacity: 1; }
        }
    </style>
    <script>
        (function() {
            var key = 'offer_hidden_{{ $popupOffer->id }}';
            var hidden = sessionStorage.getItem(key);
            if (!hidden) {
                setTimeout(function() {
                    var el = document.getElementById('offerPopupOverlay');
                    if (el) { el.style.display = 'flex'; }
                }, 1200);
            }
        })();
        function closeOfferPopup() {
            var el = document.getElementById('offerPopupOverlay');
            if (el) el.style.display = 'none';
            if (document.getElementById('offerDontShow')?.checked) {
                sessionStorage.setItem('offer_hidden_{{ $popupOffer->id }}', '1');
            }
        }
        document.getElementById('offerPopupOverlay')?.addEventListener('click', function(e) {
            if (e.target === this) closeOfferPopup();
        });
    </script>
@endif
