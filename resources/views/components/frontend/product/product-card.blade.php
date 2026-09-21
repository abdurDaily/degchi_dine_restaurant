@props([
    'menu',
    'firstVariation' => null,
    'imageUrl' => null,
    'bestOffer' => null,
    'activeOffers' => null,
    'unitPrice' => 0,
    'offerPrice' => null,
    'offerPercent' => 0,
    'rating' => null,
    'reviewCount' => null,
    'showRating' => false,
    'linked' => true,
    'cartIcon' => 'bi-cart-plus',
    'simpleCart' => false,
    'badgeStyle' => 'standard',
])

@php
    $firstVariation = $firstVariation ?? $menu->variations->sortBy('price')->first();
    $imagePath = $imageUrl ?? ($firstVariation?->image ?? 'assets/frontend/images/signature_menu/2.jpg');
    $imageUrl = \Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://'])
        ? $imagePath
        : asset($imagePath);

    $unitPrice = (float) ($unitPrice ?: ($firstVariation?->price ?? 0));
    $offerPercent = (int) ($bestOffer->discount_percent ?? 0);
    $offerPrice = $offerPrice ?? ($bestOffer ? round($unitPrice * (1 - $offerPercent / 100), 2) : $unitPrice);
    $isFirstOrder = $bestOffer?->is_first_order ?? false;
    $offerApplicableTo = $bestOffer?->applicable_to ?? 'all';
    $offerCount = $activeOffers?->count() ?? 0;
@endphp

@if($linked)
<a href="#" class="pcard">
@else
<div class="pcard">
@endif
    <div class="pcard-img-wrap">
        <img src="{{ $imageUrl }}" alt="{{ $menu->name }}" class="pcard-img"
             width="400" height="300"
             loading="lazy"
             onerror="this.src='{{ asset('assets/placeholder/placeholder.png') }}'" />

        @if($badgeStyle === 'home' && $offerCount > 0)
            <span class="pcard-offer-count">
                {{ $offerCount === 1 ? 'সেরা অফার' : $offerCount . ' টি অফার' }}
            </span>
        @endif

        @if($bestOffer)
            <div class="pcard-flash" title="{{ $bestOffer->name ?? 'Special Offer' }}">
                <i class="bi bi-lightning-charge-fill"></i>
            </div>

            @if($badgeStyle === 'home' && $offerCount > 1)
                <div class="pcard-badge pcard-badge-multi">
                    <i class="bi bi-tags-fill"></i> {{ $offerCount }} অফার
                </div>
                <div class="pcard-badge pcard-badge-stacked">
                    সর্বোচ্চ {{ $offerPercent }}% ছাড়
                </div>
            @else
                <div class="pcard-badge">
                    <i class="bi bi-tag-fill"></i> {{ $offerPercent }}% OFF
                </div>
            @endif

            @if($isFirstOrder)
                <div class="pcard-first-order" title="First order only — members">
                    <i class="bi bi-1-circle-fill" aria-hidden="true"></i>
                    <span>1st Order</span>
                </div>
            @endif
        @endif
    </div>

    <div class="pcard-body">
        <h5 class="pcard-title">{{ $menu->name }}</h5>
        <p class="pcard-desc mb-0">{{ \Illuminate\Support\Str::limit($menu->description ?? 'Signature dish', 33) }}</p>

        <div class="pcard-footer">
            <div class="pcard-price-wrap">
                @if($bestOffer)
                    <span class="pcard-price-old">৳ {{ number_format($unitPrice, 2) }}</span>
                    <span class="pcard-price pcard-price-off">৳ {{ number_format($offerPrice, 2) }}</span>
                @else
                    <span class="pcard-price">৳ {{ number_format($unitPrice, 2) }}</span>
                @endif
            </div>
            <button class="pcard-cart-btn" type="button"
                    data-variation-id="{{ $firstVariation?->id }}"
                    data-original-price="{{ $unitPrice }}"
                    @if(!$simpleCart && $bestOffer)
                        data-offer-price="{{ $offerPrice }}"
                        data-offer-id="{{ $bestOffer->id }}"
                        data-offer-percent="{{ $offerPercent }}"
                        data-is-first-order="{{ $isFirstOrder ? '1' : '0' }}"
                        data-applicable-to="{{ $offerApplicableTo }}"
                    @endif
                    aria-label="Add {{ $menu->name }} to cart">
                <i class="bi {{ $cartIcon }}" aria-hidden="true"></i>
            </button>
        </div>
    </div>
@if($linked)
</a>
@else
</div>
@endif
