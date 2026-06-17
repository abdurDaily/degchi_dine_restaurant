@php
    $suffix = $suffix ?? '';
@endphp
<div class="filter-sidebar shadow-sm" id="filterSidebar{{ $suffix }}">
    <div class="filter-sidebar-head">
        <h5 class="filter-title mb-0"><i class="bi bi-sliders2-vertical me-2"></i>Filters</h5>
    </div>
    <div class="filter-sidebar-body">
        <div class="filter-group mb-4 mb-lg-4">
            <label class="filter-group-label mb-3">Categories</label>
            <div class="category-pills-column d-flex flex-column gap-2">
                <button type="button" class="btn category-pill-btn text-start {{ !$selectedCategorySlug ? 'active' : '' }}" data-category="">
                    <span class="d-flex align-items-center justify-content-between">
                        <span>All Items</span>
                    </span>
                </button>
                @foreach($categories as $category)
                    <button type="button" class="btn category-pill-btn text-start {{ $selectedCategorySlug == $category->slug ? 'active' : '' }}" data-category="{{ $category->slug }}">
                        <span class="d-flex align-items-center justify-content-between">
                            <span>{{ $category->name }}</span>
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="filter-group mb-0">
            <label class="filter-group-label mb-3">Price Range</label>
            <div class="price-slider-container px-2">
                <div class="dual-range-slider position-relative mb-4">
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
</div>
