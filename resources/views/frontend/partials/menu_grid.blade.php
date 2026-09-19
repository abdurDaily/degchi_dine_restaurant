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
            $unitPrice = (float) ($firstVariation?->price ?? 0);
            $activeOffers = $firstVariation
                ? $firstVariation->resolveApplicableOffers($viewerMember, true)
                : collect();
            $bestOffer = $activeOffers->first();
            $offerPercent = (int) ($bestOffer->discount_percent ?? 0);
            $offerPrice = $bestOffer
                ? round($unitPrice * (1 - $offerPercent / 100), 2)
                : $unitPrice;
        @endphp
        <div class="col-12 col-sm-6 col-lg-4 d-flex reveal-scale visible">
            <x-frontend.product.product-card
                :menu="$menu"
                :firstVariation="$firstVariation"
                :activeOffers="$activeOffers"
                :bestOffer="$bestOffer"
                :unitPrice="$unitPrice"
                :offerPrice="$offerPrice"
                cartIcon="bi-cart-plus"
            />
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
