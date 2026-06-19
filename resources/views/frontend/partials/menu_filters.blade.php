@php
    $suffix = $suffix ?? '';
    $selectedCategories = $selectedCategories ?? collect(request()->query('categories', []))
        ->filter()
        ->map(fn($slug) => (string) $slug)
        ->all();
@endphp
<div class="menu-filter-bar filter-sidebar shadow-sm" id="filterSidebar{{ $suffix }}">
    <div class="filter-group filter-group-categories">
        <label class="filter-group-label">
            <i class="bi bi-grid-3x3-gap me-1"></i>Categories
        </label>
        <div class="category-checkbox-row">
            <label class="category-checkbox-chip {{ empty($selectedCategories) ? 'is-checked' : '' }}">
                <input type="checkbox" class="menu-category-checkbox" value="" data-all-categories {{ empty($selectedCategories) ? 'checked' : '' }}>
                <span>All Items</span>
            </label>
            @foreach($categories as $category)
                <label class="category-checkbox-chip {{ in_array($category->slug, $selectedCategories) ? 'is-checked' : '' }}">
                    <input type="checkbox" class="menu-category-checkbox" value="{{ $category->slug }}" {{ in_array($category->slug, $selectedCategories) ? 'checked' : '' }}>
                    <span>{{ $category->name }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <div class="filter-group filter-group-price">
        <label class="filter-group-label">
            <i class="bi bi-cash-coin me-1"></i>Price Range
        </label>
        <div class="price-slider-container">
            <div class="dual-range-slider position-relative">
                <div class="slider-track" id="sliderTrack{{ $suffix }}"></div>
                <input type="range" min="{{ $minPriceLimit }}" max="{{ $maxPriceLimit }}" value="{{ $minPrice }}" class="slider-input menu-min-price" id="minPriceInput{{ $suffix }}">
                <input type="range" min="{{ $minPriceLimit }}" max="{{ $maxPriceLimit }}" value="{{ $maxPrice }}" class="slider-input menu-max-price" id="maxPriceInput{{ $suffix }}">
            </div>
            <div class="d-flex justify-content-between align-items-center text-muted mt-3">
                <span class="price-badge px-2 py-1 rounded bg-light border">৳ <span class="menu-min-display" id="minPriceDisplay{{ $suffix }}">{{ number_format($minPrice) }}</span></span>
                <span class="small text-uppercase fw-bold">to</span>
                <span class="price-badge px-2 py-1 rounded bg-light border">৳ <span class="menu-max-display" id="maxPriceDisplay{{ $suffix }}">{{ number_format($maxPrice) }}</span></span>
            </div>
        </div>
    </div>
</div>
