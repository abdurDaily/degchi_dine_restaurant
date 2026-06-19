@extends('frontend.layout')

@section('meta_title', $branch->name . ' — Branch Menu')
@section('meta_description', 'Browse the Degchi Dine menu at ' . $branch->name . '. Order for quick delivery via FoodPanda, Pathao, or Foodi.')

@section('frontend_content')
@php
    $phoneDigits = preg_replace('/\D+/', '', $branch->phone ?? '');
@endphp

<section class="dd-apply-wrapper branch-show-page">
    <div class="dd-apply-hero-banner branch-show-hero">
        <div class="container px-4 px-lg-5 text-center position-relative">
            <a href="{{ route('frontend.branches.index') }}" class="dd-apply-back-btn">
                <iconify-icon icon="solar:alt-arrow-left-linear"></iconify-icon>
                <span>All Branches</span>
            </a>

            <span class="branch-show-kicker">Branch Menu</span>
            <h1 class="dd-apply-headline mb-2">{{ $branch->name }}</h1>

            <div class="branch-show-hero-meta">
                @if($branch->location)
                    <span class="branch-show-meta-chip">
                        <i class="bi bi-geo-alt-fill"></i>{{ $branch->location }}
                    </span>
                @endif
                @if($branch->phone)
                    <a href="tel:{{ $phoneDigits }}" class="branch-show-meta-chip branch-show-meta-chip--link">
                        <i class="bi bi-telephone-fill"></i>{{ $branch->phone }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="container px-4 px-lg-5 branch-show-delivery-box">
        <div class="branch-show-delivery-panel">
            <div class="branch-show-delivery-head">
                <h2 class="branch-show-delivery-title">
                    <i class="ri-e-bike-2-fill" aria-hidden="true"></i>
                    For Quick Delivery
                </h2>
                <p class="branch-show-delivery-lead">Order from our delivery partners</p>
            </div>

            @if(!empty($deliveryServices))
                <div class="branch-show-delivery-services">
                    @foreach($deliveryServices as $key => $service)
                        @php
                            $logoUrl = null;
                            if ($key === 'foodpanda' && $branch->foodpanda_logo) {
                                $logoUrl = str_starts_with($branch->foodpanda_logo, 'http')
                                    ? $branch->foodpanda_logo
                                    : asset('uploads/branches/' . $branch->foodpanda_logo);
                            } elseif ($key === 'pathao' && $branch->pathao_logo) {
                                $logoUrl = str_starts_with($branch->pathao_logo, 'http')
                                    ? $branch->pathao_logo
                                    : asset('uploads/branches/' . $branch->pathao_logo);
                            } elseif ($key === 'foodi' && $branch->foodi_logo) {
                                $logoUrl = str_starts_with($branch->foodi_logo, 'http')
                                    ? $branch->foodi_logo
                                    : asset('uploads/branches/' . $branch->foodi_logo);
                            }
                        @endphp

                        <a href="{{ $service['url'] }}" target="_blank" rel="noopener noreferrer"
                            class="branch-show-service-btn" data-brand="{{ $key }}">
                            <span class="branch-show-service-logo">
                                @if($logoUrl)
                                    <img src="{{ $logoUrl }}" alt="{{ $service['name'] }}"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <span class="branch-show-service-fallback" style="display:none;">{{ strtoupper(substr($key, 0, 1)) }}</span>
                                @else
                                    <span class="branch-show-service-fallback branch-show-service-fallback--{{ $key }}">
                                        @if($key === 'foodpanda') F @elseif($key === 'pathao') P @else &amp; @endif
                                    </span>
                                @endif
                            </span>
                            <span class="branch-show-service-name">{{ $service['name'] }}</span>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="branch-show-delivery-empty">
                    <p>Delivery links coming soon.</p>
                    @if($branch->phone)
                        <a href="tel:{{ $phoneDigits }}" class="branch-show-delivery-call">
                            <i class="bi bi-telephone-fill"></i> Call to order
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="container px-4 px-lg-5 branch-show-toolbar-wrap">
        <div class="branch-show-toolbar">
            <div class="branch-show-search-wrap">
                <i class="bi bi-search branch-show-search-icon" aria-hidden="true"></i>
                <input type="text" class="branch-show-search" id="menuSearch" placeholder="Search menu items…" autocomplete="off">
                <div class="branch-show-search-results" id="searchResults"></div>
            </div>

            <div class="branch-show-categories" id="categoryNav">
                <button type="button" class="branch-show-category-btn active" data-category="all">All Items</button>
                @foreach($categories as $category)
                    <button type="button" class="branch-show-category-btn" data-category="{{ $category->id }}">{{ $category->name }}</button>
                @endforeach
            </div>
        </div>
    </div>

    <div class="container px-4 px-lg-5 pb-5 branch-show-menu-wrap">
        @if($categories->isEmpty() || $categories->every(fn($c) => $c->menus->isEmpty()))
            <div class="branch-show-empty">
                <iconify-icon icon="solar:chef-hat-outline" width="56" height="56"></iconify-icon>
                <p>No menu items available for this branch yet.</p>
            </div>
        @else
            <div class="row g-4" id="menuGrid">
                @foreach($categories as $category)
                    @foreach($category->menus as $menu)
                        @php
                            $firstVariation = $menu->variations->sortBy('price')->first();
                            $imagePath = $firstVariation?->image ?? null;
                            $imageUrl = $imagePath
                                ? (str_starts_with($imagePath, 'http') ? $imagePath : asset($imagePath))
                                : null;
                            $minPrice = $menu->variations->min('price') ?? 0;
                        @endphp
                        <div class="col-12 col-sm-6 col-lg-4 d-flex branch-show-menu-item"
                            data-category="{{ $category->id }}"
                            data-menu-id="{{ $menu->id }}"
                            data-menu-name="{{ $menu->name }}"
                            data-menu-price="{{ $minPrice }}">
                            <div class="menu-offer-card branch-show-menu-card">
                                <div class="menu-offer-image-wrap">
                                    @if($imageUrl)
                                        <img src="{{ $imageUrl }}" alt="{{ $menu->name }}" class="menu-offer-image"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <span class="branch-show-menu-placeholder" style="display:none;">
                                            <i class="ri-restaurant-2-line"></i>
                                        </span>
                                    @else
                                        <span class="branch-show-menu-placeholder">
                                            <i class="ri-restaurant-2-line"></i>
                                        </span>
                                    @endif
                                </div>
                                <div class="menu-offer-body">
                                    <h5 class="menu-offer-title">{{ $menu->name }}</h5>
                                    <p class="menu-offer-meta mb-0">{{ Str::limit($menu->description ?? 'Fresh item', 33) }}</p>
                                    <div class="menu-offer-footer">
                                        <div class="menu-offer-price-wrap">
                                            <span class="menu-offer-price">৳ {{ number_format((float) $minPrice, 2) }}</span>
                                        </div>
                                        <button class="menu-offer-cart-btn" type="button"
                                            data-variation-id="{{ $firstVariation?->id }}"
                                            data-original-price="{{ $minPrice }}"
                                            aria-label="Add {{ $menu->name }} to cart">
                                            <i class="bi bi-plus-lg" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        @endif
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryBtns = document.querySelectorAll('.branch-show-category-btn');
    const menuSearch = document.getElementById('menuSearch');
    const searchResults = document.getElementById('searchResults');
    const menuItems = document.querySelectorAll('.branch-show-menu-item');

    categoryBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            categoryBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const categoryId = this.getAttribute('data-category');
            menuItems.forEach(item => {
                if (categoryId === 'all' || item.getAttribute('data-category') === categoryId) {
                    item.classList.remove('branch-show-hidden');
                } else {
                    item.classList.add('branch-show-hidden');
                }
            });

            if (menuSearch) menuSearch.value = '';
            if (searchResults) {
                searchResults.innerHTML = '';
                searchResults.style.display = 'none';
            }
        });
    });

    if (!menuSearch || !searchResults) return;

    let searchTimeout;
    menuSearch.addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim().toLowerCase();

        if (query.length < 1) {
            searchResults.innerHTML = '';
            searchResults.style.display = 'none';
            menuItems.forEach(item => item.classList.remove('branch-show-hidden'));
            return;
        }

        searchTimeout = setTimeout(function() {
            let matchCount = 0;
            let html = '';

            menuItems.forEach(item => {
                const menuName = item.getAttribute('data-menu-name').toLowerCase();

                if (menuName.includes(query)) {
                    item.classList.remove('branch-show-hidden');
                    matchCount++;

                    html += `<button type="button" class="branch-show-search-hit" data-menu-id="${item.getAttribute('data-menu-id')}">
                        <span>${item.getAttribute('data-menu-name')}</span>
                        <strong>৳${parseFloat(item.getAttribute('data-menu-price')).toFixed(2)}</strong>
                    </button>`;
                } else {
                    item.classList.add('branch-show-hidden');
                }
            });

            if (matchCount === 0) {
                html = '<div class="branch-show-search-empty">No items found</div>';
            }

            searchResults.innerHTML = html;
            searchResults.style.display = 'block';
        }, 250);
    });

    searchResults.addEventListener('click', function(e) {
        const hit = e.target.closest('.branch-show-search-hit');
        if (!hit) return;

        const menuId = hit.getAttribute('data-menu-id');
        menuSearch.value = '';
        searchResults.innerHTML = '';
        searchResults.style.display = 'none';

        menuItems.forEach(item => {
            item.classList.toggle('branch-show-hidden', item.getAttribute('data-menu-id') !== menuId);
        });
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.branch-show-search-wrap')) {
            searchResults.style.display = 'none';
        }
    });
});
</script>
@endsection
