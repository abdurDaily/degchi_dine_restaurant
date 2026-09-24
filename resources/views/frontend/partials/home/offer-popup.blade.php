@if (isset($popupOffer) && $popupOffer)
    <div id="offerPopupOverlay"
        style="display:none; position:fixed; inset:0; background:rgba(6,18,25,.68); -webkit-backdrop-filter:blur(6px); backdrop-filter:blur(6px); z-index:9990; align-items:center; justify-content:center; padding:16px;">

        <div class="op-card" role="dialog" aria-modal="true" aria-label="{{ $popupOffer->name }}">
            {{-- Close button --}}
            <button type="button" onclick="closeOfferPopup()" class="op-close" aria-label="Close popup">&times;</button>

            {{-- Badge --}}
            @if ($popupOffer->popup_badge)
                <span class="op-badge">{{ $popupOffer->popup_badge }}</span>
            @endif

            {{-- Media --}}
            <div class="op-media">
                @if ($popupOffer->popup_image)
                    <img src="{{ asset('storage/' . $popupOffer->popup_image) }}" class="op-media-img"
                        alt="{{ $popupOffer->name }} - Special Offer at Degchi Dine"
                        onerror="this.classList.add('op-img-error');">
                @endif
                <div class="op-media-fallback">🎉</div>
                <span class="op-special-chip"><i class="bi bi-fire"></i> Special Offer</span>
            </div>

            {{-- Body --}}
            <div class="op-body">
                @if ($popupOffer->discount_percent > 0)
                    <div class="op-discount">
                        {{ $popupOffer->discount_percent }}% <span>OFF</span>
                    </div>
                @endif

                <h3 class="op-title">{{ $popupOffer->name }}</h3>

                @if ($popupOffer->description)
                    <p class="op-desc">{{ $popupOffer->description }}</p>
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

                <div class="op-actions">
                    <a href="{{ $redirectUrl }}" class="op-btn op-btn-primary">
                        Order Now <i class="bi bi-arrow-right"></i>
                    </a>
                    <button type="button" onclick="closeOfferPopup()" class="op-btn op-btn-ghost">
                        Maybe Later
                    </button>
                </div>

                <label class="op-dontshow">
                    <input type="checkbox" id="offerDontShow">
                    Don't show again today
                </label>
            </div>
        </div>
    </div>

    <style>
        /* ── Offer popup — styled with the site design tokens (base.css) ── */
        #offerPopupOverlay .op-card {
            position: relative;
            width: min(100%, 440px);
            max-height: 92vh;
            overflow-y: auto;
            border-radius: 22px;
            background: var(--brand-gradient, linear-gradient(160deg, #0f6a8b, #1a6674 50%, #0d3d4a));
            border: 1px solid rgba(250, 245, 235, .16);
            box-shadow: 0 30px 70px rgba(0, 0, 0, .5), 0 6px 20px rgba(0, 0, 0, .3);
            scrollbar-width: none;
            animation: offerPopIn .45s cubic-bezier(.34, 1.56, .64, 1);
        }

        #offerPopupOverlay .op-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 12%;
            right: 12%;
            height: 4px;
            z-index: 3;
            border-radius: 0 0 10px 10px;
            background: var(--brand-gold-gradient, linear-gradient(135deg, #e7ae07, #c99606));
            box-shadow: 0 4px 18px rgba(231, 174, 7, .45);
        }

        #offerPopupOverlay .op-card::-webkit-scrollbar { display: none; }

        #offerPopupOverlay .op-close {
            position: absolute;
            top: 14px;
            right: 14px;
            z-index: 4;
            width: 36px;
            height: 36px;
            border: none;
            border-radius: 50%;
            background: rgba(250, 245, 235, .14);
            color: #faf5eb;
            font-size: 1.15rem;
            line-height: 1;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            -webkit-backdrop-filter: blur(4px);
            backdrop-filter: blur(4px);
            transition: background .2s, transform .2s;
        }

        #offerPopupOverlay .op-close:hover {
            background: var(--brand-secondary-dark, #e63946);
            transform: rotate(90deg);
        }

        #offerPopupOverlay .op-badge {
            position: absolute;
            top: 18px;
            left: 18px;
            z-index: 4;
            background: var(--brand-gold-gradient, linear-gradient(135deg, #e7ae07, #c99606));
            color: #0d3d4a;
            font-size: .68rem;
            font-weight: 800;
            letter-spacing: .1em;
            text-transform: uppercase;
            padding: 5px 14px;
            border-radius: 999px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .25);
        }

        #offerPopupOverlay .op-media {
            position: relative;
            height: 210px;
            overflow: hidden;
        }

        #offerPopupOverlay .op-media img {
            width: 100%;
            height: 210px;
            object-fit: cover;
            display: block;
        }

        #offerPopupOverlay .op-media-fallback {
            display: none;
            width: 100%;
            height: 210px;
            background: var(--brand-gradient-vertical, linear-gradient(135deg, #1a6674, #0f6a8b));
            align-items: center;
            justify-content: center;
            font-size: 3.5rem;
        }

        #offerPopupOverlay .op-media img.op-img-error { display: none; }
        #offerPopupOverlay .op-media img.op-img-error ~ .op-media-fallback { display: flex; }

        #offerPopupOverlay .op-media::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(13, 61, 74, 0) 40%, #1a6674 100%);
            pointer-events: none;
        }

        #offerPopupOverlay .op-special-chip {
            position: absolute;
            bottom: 14px;
            left: 18px;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #0d3d4a;
            background: var(--cream, #faf5eb);
            padding: 5px 12px;
            border-radius: 999px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .28);
        }

        #offerPopupOverlay .op-body {
            padding: 22px 26px 28px;
            color: var(--text-on-dark, #fff);
        }

        #offerPopupOverlay .op-discount {
            display: flex;
            align-items: baseline;
            gap: 8px;
            font-family: "Poppins", sans-serif;
            font-size: 2.8rem;
            font-weight: 800;
            line-height: 1;
            color: var(--brand-gold, #fcc00a);
            text-shadow: 0 2px 14px rgba(252, 192, 10, .35);
        }

        #offerPopupOverlay .op-discount span {
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: .16em;
            color: rgba(250, 245, 235, .92);
            text-shadow: none;
        }

        #offerPopupOverlay .op-title {
            margin: .6rem 0 .5rem;
            font-family: "Poppins", sans-serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.3;
        }

        #offerPopupOverlay .op-desc {
            margin: 0 0 1.4rem;
            font-size: .88rem;
            line-height: 1.6;
            color: rgba(250, 245, 235, .78);
        }

        #offerPopupOverlay .op-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        #offerPopupOverlay .op-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            border: none;
            border-radius: 999px;
            font-family: "Poppins", sans-serif;
            font-weight: 700;
            font-size: .92rem;
            letter-spacing: .06em;
            text-transform: uppercase;
            padding: .9rem 1.5rem;
            text-decoration: none !important;
            cursor: pointer;
            transition: transform .22s, box-shadow .22s, background .22s;
        }

        #offerPopupOverlay .op-btn:active { transform: scale(.97); }

        #offerPopupOverlay .op-btn-primary {
            background: var(--brand-secondary-gradient, linear-gradient(135deg, #ff6b35, #e63946));
            color: #fff !important;
            box-shadow: 0 10px 26px rgba(230, 57, 70, .35);
        }

        #offerPopupOverlay .op-btn-primary:hover {
            transform: translateY(-2px);
            filter: brightness(1.05);
            box-shadow: 0 14px 32px rgba(230, 57, 70, .45);
        }

        #offerPopupOverlay .op-btn-ghost {
            background: rgba(250, 245, 235, .08);
            border: 1.5px solid rgba(250, 245, 235, .28);
            color: #faf5eb !important;
        }

        #offerPopupOverlay .op-btn-ghost:hover {
            background: rgba(250, 245, 235, .16);
            border-color: var(--brand-gold, #e7ae07);
            transform: translateY(-2px);
        }

        #offerPopupOverlay .op-dontshow {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 1.15rem;
            font-size: .78rem;
            color: rgba(250, 245, 235, .55);
            cursor: pointer;
        }

        #offerPopupOverlay .op-dontshow input {
            cursor: pointer;
            accent-color: var(--brand-gold, #e7ae07);
        }

        @keyframes offerPopIn {
            from {
                opacity: 0;
                transform: scale(.78) translateY(18px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        @media (max-width: 420px) {
            #offerPopupOverlay .op-media,
            #offerPopupOverlay .op-media img,
            #offerPopupOverlay .op-media-fallback { height: 170px; }
            #offerPopupOverlay .op-body { padding: 18px 20px 24px; }
            #offerPopupOverlay .op-discount { font-size: 2.3rem; }
        }
    </style>

    <script>
        (function() {
            var key = 'offer_hidden_{{ $popupOffer->id }}';
            var hidden = sessionStorage.getItem(key);
            if (!hidden) {
                setTimeout(function() {
                    var el = document.getElementById('offerPopupOverlay');
                    if (el) {
                        el.style.display = 'flex';
                    }
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