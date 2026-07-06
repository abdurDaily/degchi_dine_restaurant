@php
    $suffix = $suffix ?? '';

    // Fresh visit কিনা চেক করা হচ্ছে — মানে ইউজার এখনো কোনো filter apply করেনি
    $hasAnyFilterParam =
        request()->has('categories') ||
        request()->has('category') ||
        request()->has('offerFilter') ||
        request()->has('popularFilter') ||
        request()->has('min_price') ||
        request()->has('max_price');

    $isOfferSelected = filter_var(request()->query('offerFilter', false), FILTER_VALIDATE_BOOLEAN);

    // Fresh visit হলে Popular ডিফল্ট checked, নাহলে normal query value নেওয়া হবে
    $isPopularSelected = $hasAnyFilterParam
        ? filter_var(request()->query('popularFilter', false), FILTER_VALIDATE_BOOLEAN)
        : true;
@endphp

<div class="menu-filter-bar filter-sidebar shadow-sm" id="filterSidebar{{ $suffix }}">

    {{-- ================= CATEGORY + OFFER + POPULAR FILTER ================= --}}
    <div class="filter-group filter-group-categories">
        <label class="filter-group-label">
            <i class="bi bi-grid-3x3-gap me-1"></i>Categories
        </label>

        <ul class="category-list-group list-unstyled" id="categoryListGroup{{ $suffix }}">

            {{-- Popular --}}
            <li class="category-list-item {{ $isPopularSelected ? 'is-checked' : '' }}">
                <label class="category-list-label">
                    <input type="checkbox" class="menu-category-checkbox" value="popular" data-popular-filter
                        {{ $isPopularSelected ? 'checked' : '' }}>
                    <span>Customer Favorites</span>
                </label>
            </li>

            {{-- Offer --}}
            <li class="category-list-item {{ $isOfferSelected ? 'is-checked' : '' }}">
                <label class="category-list-label">
                    <input type="checkbox" class="menu-category-checkbox" value="offer" data-offer-filter
                        {{ $isOfferSelected ? 'checked' : '' }}>
                    <span>Offer</span>
                </label>
            </li>

            {{-- Dynamic categories --}}
            @foreach ($categories as $category)
                <li class="category-list-item {{ in_array($category->slug, $selectedCategories) ? 'is-checked' : '' }}">
                    <label class="category-list-label">
                        <input type="checkbox" class="menu-category-checkbox" value="{{ $category->slug }}"
                            {{ in_array($category->slug, $selectedCategories) ? 'checked' : '' }}>
                        <span>{{ $category->name }}</span>
                    </label>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- ================= PRICE FILTER ================= --}}
    <div class="filter-group filter-group-price">
        <label class="filter-group-label">
            <i class="bi bi-cash-coin me-1"></i> Price Range
        </label>

        <div class="price-slider-container">
            <div class="dual-range-slider position-relative">
                <div class="slider-track" id="sliderTrack{{ $suffix }}"></div>

                <input type="range" min="{{ $minPriceLimit }}" max="{{ $maxPriceLimit }}"
                    value="{{ $minPrice }}" class="slider-input menu-min-price"
                    id="minPriceInput{{ $suffix }}">

                <input type="range" min="{{ $minPriceLimit }}" max="{{ $maxPriceLimit }}"
                    value="{{ $maxPrice }}" class="slider-input menu-max-price"
                    id="maxPriceInput{{ $suffix }}">
            </div>

            <div class="d-flex justify-content-between align-items-center text-muted mt-3">
                <span class="price-badge px-2 py-1 rounded bg-light border">
                    ৳ <span id="minPriceDisplay{{ $suffix }}">{{ number_format($minPrice) }}</span>
                </span>
                <span class="small text-uppercase fw-bold">to</span>
                <span class="price-badge px-2 py-1 rounded bg-light border">
                    ৳ <span id="maxPriceDisplay{{ $suffix }}">{{ number_format($maxPrice) }}</span>
                </span>
            </div>
        </div>
    </div>

</div>

{{-- ================= SCRIPT (শুধু UI scroll visual effect) ================= --}}
<script>
    (function() {
        var list = document.getElementById('categoryListGroup{{ $suffix }}');
        if (!list) return;

        var hideTimer;
        list.addEventListener('scroll', function() {
            list.classList.add('is-scrolling');
            clearTimeout(hideTimer);
            hideTimer = setTimeout(function() {
                list.classList.remove('is-scrolling');
            }, 800);
        });
    })();
</script>
