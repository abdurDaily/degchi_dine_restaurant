<div class="row g-4 justify-content-center">
    @if($menus->total() > 0)
    <div class="col-12">
        <div class="menu-results-bar">
            <span class="menu-results-count">
                <i class="bi bi-grid-3x3-gap-fill"></i>
                {{ $menus->total() }} item{{ $menus->total() !== 1 ? 's' : '' }}
            </span>
            @if($menus->hasPages())
            <span class="menu-results-page text-muted">Page {{ $menus->currentPage() }} of {{ $menus->lastPage() }}</span>
            @endif
        </div>
    </div>
    @endif
    @php
        $viewerMember = Auth::guard('member')->user();
    @endphp
    @forelse($menus as $menu)
        @php
            $firstVariation = $menu->variations->sortBy('price')->first();
            $imagePath = $firstVariation?->image ?? 'assets/frontend/images/signature_menu/2.jpg';
            $imageUrl = \Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://'])
                ? $imagePath
                : asset($imagePath);

            // Includes specific_items pivot + all_items global offers; highest % first
            $activeOffers = $firstVariation
                ? $firstVariation->resolveApplicableOffers($viewerMember, true)
                : collect();
            $bestOffer = $activeOffers->first();
            $unitPrice = (float) ($firstVariation?->price ?? 0);
            $offerPercent = (int) ($bestOffer->discount_percent ?? 0);
            $offerPrice = $bestOffer
                ? round($unitPrice * (1 - $offerPercent / 100), 2)
                : $unitPrice;
        @endphp
        <div class="col-12 col-sm-6 col-lg-4 d-flex reveal-scale visible">
            <a href="#" class="menu-offer-card">
                <div class="menu-offer-image-wrap" style="position: relative;">
                    <img src="{{ $imageUrl }}" alt="{{ $menu->name }}" class="menu-offer-image" />

                    @if($bestOffer)
                        <div class="offer-icon-badge" title="{{ $bestOffer->name }}">
                          <i class="bi bi-lightning-charge-fill"></i>
                        </div>

                        <div class="offer-badge-card">
                            <i class="bi bi-tag-fill"></i> {{ $bestOffer->discount_percent }}% OFF
                        </div>

                        @if($bestOffer->is_first_order)
                          <div class="offer-first-order-badge" title="First order only — members">
                            <i class="bi bi-1-circle-fill" aria-hidden="true"></i>
                            <span>1st Order</span>
                          </div>
                        @endif
                    @endif
                </div>
                <div class="menu-offer-body">
                    <h5 class="menu-offer-title">{{ $menu->name }}</h5>
                    <p class="menu-offer-meta mb-0">
                        {{ \Illuminate\Support\Str::limit($menu->description ?? 'Signature dish', 33) }}
                    </p>
                    <div class="menu-offer-footer">
                        <div class="menu-offer-price-wrap">
                            @if($bestOffer)
                                <span class="menu-offer-price menu-offer-price-old">
                                  ৳ {{ number_format($unitPrice, 2) }}
                                </span>
                                <span class="menu-offer-price text-danger fw-bold">
                                  ৳ {{ number_format($offerPrice, 2) }}
                                </span>
                            @else
                                <span class="menu-offer-price">৳ {{ number_format($unitPrice, 2) }}</span>
                            @endif
                        </div>
                        <button class="menu-offer-cart-btn" type="button"
                                data-variation-id="{{ $firstVariation?->id }}"
                                data-original-price="{{ $unitPrice }}"
                                data-offer-price="{{ $offerPrice }}"
                                data-offer-id="{{ $bestOffer?->id }}"
                                data-offer-percent="{{ $offerPercent }}"
                                data-is-first-order="{{ $bestOffer?->is_first_order ? '1' : '0' }}"
                                data-applicable-to="{{ $bestOffer?->applicable_to ?? 'all' }}"
                                aria-label="Add {{ $menu->name }} to cart">
                            <i class="bi bi-plus-lg" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </a>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="empty-state-wrap p-5">
                <iconify-icon icon="solar:sad-smiley-outline" width="64" height="64" class="text-muted mb-3"></iconify-icon>
                <h4 class="text-muted">No items found matching your criteria.</h4>
                <p class="text-muted">Try choosing a different category or adjusting the price range.</p>
            </div>
        </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($menus->hasPages())
    <div class="d-flex justify-content-center mt-5">
        <nav class="modern-pagination" aria-label="Menu pagination">
            <ul class="pagination mb-0">
                {{-- Previous Page Link --}}
                @if ($menus->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link" aria-hidden="true"><i class="bi bi-chevron-left"></i></span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link pagination-ajax" href="{{ $menus->previousPageUrl() }}" data-page="{{ $menus->currentPage() - 1 }}" rel="prev" aria-label="Previous">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Pagination Links --}}
                @foreach ($menus->getUrlRange(max(1, $menus->currentPage() - 2), min($menus->lastPage(), $menus->currentPage() + 2)) as $page => $url)
                    @if ($page == $menus->currentPage())
                        <li class="page-item active" aria-current="page">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link pagination-ajax" href="{{ $url }}" data-page="{{ $page }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($menus->hasMorePages())
                    <li class="page-item">
                        <a class="page-link pagination-ajax" href="{{ $menus->nextPageUrl() }}" data-page="{{ $menus->currentPage() + 1 }}" rel="next" aria-label="Next">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link" aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endif
