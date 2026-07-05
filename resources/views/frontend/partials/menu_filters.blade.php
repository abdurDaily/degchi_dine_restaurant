@php
    $suffix = $suffix ?? '';
    $selectedCategories =
        $selectedCategories ??
        collect(request()->query('categories', []))
            ->filter()
            ->map(fn($slug) => (string) $slug)
            ->all();
    $isOfferSelected = request()->has('offerFilter');

@endphp

<div class="menu-filter-bar filter-sidebar shadow-sm" id="filterSidebar{{ $suffix }}">

    {{-- ================= CATEGORY FILTER ================= --}}
    <div class="filter-group filter-group-categories">
        <label class="filter-group-label">
            <i class="bi bi-grid-3x3-gap me-1"></i>Categories
        </label>

        <ul class="category-list-group list-unstyled" id="categoryListGroup{{ $suffix }}">
            <li class="category-list-item {{ empty($selectedCategories) ? 'is-checked' : '' }}">
                <label class="category-list-label">
                    <input type="checkbox" class="menu-category-checkbox" value="" data-all-categories
                        {{ empty($selectedCategories) ? 'checked' : '' }}>
                    <span>Special Items</span>
                </label>
            </li>
           
            <li class="category-list-item  {{ $isOfferSelected ? 'is-checked' : '' }}">
                <a href="#"
                    class="text-decoration-none">
                    <label class="category-list-label">
                        <input type="checkbox" class="menu-category-checkbox" value="offer"
                            {{ $isOfferSelected ? 'checked' : '' }}>
                        <span><i class="bi bi-tag-fill me-1"></i>Offer</span>
                    </label>
                </a>
            </li>

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


{{-- ================= SCRIPT ================= --}}
<script>
    (function() {

        var list = document.getElementById('categoryListGroup{{ $suffix }}');
        if (!list) return;

        var hideTimer;

        // Scroll effect
        list.addEventListener('scroll', function() {
            list.classList.add('is-scrolling');

            clearTimeout(hideTimer);
            hideTimer = setTimeout(function() {
                list.classList.remove('is-scrolling');
            }, 800);
        });


        // ================= CATEGORY CLICK UI FIX =================
        document.querySelectorAll('#categoryListGroup{{ $suffix }} .menu-category-checkbox')
            .forEach(function(checkbox) {

                checkbox.addEventListener('change', function() {

                    let allCheckbox = list.querySelector('[data-all-categories]');

                    if (this.hasAttribute('data-all-categories')) {
                        // If ALL selected → uncheck others
                        list.querySelectorAll('.menu-category-checkbox').forEach(cb => {
                            if (cb !== this) cb.checked = false;
                        });
                    } else {
                        // If any other selected → uncheck ALL
                        allCheckbox.checked = false;
                    }

                    // Toggle active class
                    list.querySelectorAll('.category-list-item').forEach(item => {
                        let input = item.querySelector('input');
                        item.classList.toggle('is-checked', input.checked);
                    });

                });

            });

    })();
</script>
