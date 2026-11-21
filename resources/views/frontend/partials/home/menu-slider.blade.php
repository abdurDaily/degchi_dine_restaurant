<!-- MENU -->
<section class="menu-section section-block py-5"  id="menu">
    <div class="container px-4 px-lg-5">
        <x-frontend.section-heading title="Our Popular Dishes" subtitle="Our best dishes that will bring you back again and again"
            class="menu-section-header" />

        <div id="menuSlider" class="menu-slider reveal">
            <p class="menu-slider-mobile-hint d-md-none" aria-hidden="true">
                <i class="bi bi-arrow-left-right me-1"></i> Swipe or use the arrows
            </p>
            <div class="menu-slider-viewport">
                <div class="menu-slider-track">
                    @php
                        $sliderMenus = $categories
                            ->pluck('menus')
                            ->flatten()
                            ->filter(fn($menu) => $menu->variations->isNotEmpty())
                            ->take(10);
                    @endphp

                    @php
                        $viewerMember = Auth::guard('member')->user();
                        $memberCardOffers = \App\Models\Offer::activeMemberCardOffers();
                    @endphp

                    @forelse($sliderMenus as $menu)
                        @php
                            $firstVariation = $menu->variations->sortBy('price')->first();
                            // Same resolver the menu grid uses: only active, in-date food
                            // promos (specific + all_items), biggest % first. Reading the
                            // raw `offers` relation here would surface inactive/expired and
                            // membership-tier rows and miss all_items promos entirely.
                            $activeOffers = $firstVariation
                                ? $firstVariation->resolveApplicableOffers($viewerMember, true)
                                : collect();
                            $bestOffer = $activeOffers->first();
                            $rating = round(4.5 + rand(0, 5) / 10, 1);
                            $reviewCount = rand(40, 150);
                        @endphp
                        <div class="menu-slide-item">
                            <x-frontend.product.product-card :menu="$menu" :firstVariation="$firstVariation" :bestOffer="$bestOffer" :activeOffers="$activeOffers" :memberCardOffers="$memberCardOffers"
                                :rating="$rating" :reviewCount="$reviewCount" showRating badgeStyle="home"
                                cartIcon="bi-cart-plus" />
                            
                        </div>
                    @empty
                        <div class="menu-slide-item">
                            <div class="menu-offer-card">
                                <div class="menu-offer-body">
                                    <h5 class="menu-offer-title">Coming Soon</h5>
                                    <p class="menu-offer-meta mb-0">New dishes will be added soon.</p>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <button class="menu-slider-btn menu-slider-prev" aria-label="Previous">
                <span class="menu-control-icon" aria-hidden="true"><i class="bi bi-chevron-left"></i></span>
            </button>
            <button class="menu-slider-btn menu-slider-next" aria-label="Next">
                <span class="menu-control-icon" aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
            </button>
        </div>

        <div class="menu-slider-footer reveal">
            <a href="{{ route('frontend.completeMenu') }}" class="btn-dine btn-dine-primary">
                <span>View Full Menu <i class="bi bi-arrow-right-short ms-1"></i></span>
            </a>
        </div>
    </div>
</section>
