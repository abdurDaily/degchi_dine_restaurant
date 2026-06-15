@extends('frontend.layout')

@section('frontend_content')
<style>
    /* Sticky Filter Sidebar */
    .filter-sidebar {
        transition: all 0.3s ease;
    }

    .filter-sidebar.is-sticky {
        position: fixed;
        top: 100px;
        z-index: 50;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    @media (max-width: 991.98px) {
        .filter-sidebar.is-sticky {
            position: static;
            box-shadow: none;
        }
    }
</style>
<!-- COMPLETE MENU HEADER -->
<section class="menu-hero-section py-5 text-center position-relative">
    <div class="container px-4 px-lg-5">
        @if(isset($activeOfferDetails) && $activeOfferDetails)
            <!-- Offer Banner -->
            <div class="alert alert-dismissible fade show" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); border: none; border-radius: 15px; box-shadow: 0 8px 20px rgba(231, 76, 60, 0.3); margin-bottom: 2rem;">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                <div class="d-flex align-items-center justify-content-center flex-wrap gap-3 py-2">
                    <div class="text-white">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-megaphone-fill fs-4"></i>
                            <span class="fs-5 fw-bold">{{ $activeOfferDetails->name }}</span>
                        </div>
                        @if($activeOfferDetails->description)
                            <p class="mb-0 small opacity-90">{{ $activeOfferDetails->description }}</p>
                        @endif
                    </div>
                    <div class="offer-badge-big" style="background: rgba(255,255,255,0.2); padding: 0.75rem 1.5rem; border-radius: 50px; backdrop-filter: blur(10px);">
                        <span class="text-white fs-3 fw-bold">{{ $activeOfferDetails->discount_percent }}% OFF</span>
                    </div>
                </div>
            </div>
        @endif
        
        <span class="luxury-meta-label mb-2">Degchi Dine</span>
        <h1 class="luxury-title text-uppercase mb-2">
            @if(isset($activeOfferDetails) && $activeOfferDetails)
                Special Offer Items
            @else
                Our Complete Menu
            @endif
        </h1>
        <!-- <div class="luxury-accent-line mx-auto mb-3"></div>
        <p class="section-subtitle text-muted max-w-600 mx-auto">
            @if(isset($activeOfferDetails) && $activeOfferDetails)
                All items shown below are eligible for <strong>{{ $activeOfferDetails->discount_percent }}% discount</strong>. Add them to your cart now!
            @else
                Explore our curated culinary creations, prepared with heritage slow-cooking techniques and fresh local spices.
            @endif
        </p>
    </div> -->
</section>

<!-- MAIN FILTER & GRID CONTAINER -->
<section class="menu-grid-section py-5">
    <div class="container px-4 px-lg-5">
        <div class="row g-4">
            
            <!-- Sidebar Filter Panel -->
            <div class="col-12 col-lg-3">
                <div class="filter-sidebar p-4 shadow-sm sticky-filter">
                    <h5 class="filter-title mb-4"><i class="bi bi-sliders2-vertical me-2"></i>Filters</h5>
                    
                    <!-- Category Section -->
                    <div class="filter-group mb-5">
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

                    <!-- Price Section -->
                    <div class="filter-group mb-2">
                        <label class="filter-group-label mb-3">Price Range</label>
                        <div class="price-slider-container px-2">
                            
                            <div class="dual-range-slider position-relative mb-4">
                                <div class="slider-track" id="sliderTrack"></div>
                                <input type="range" min="{{ $minPriceLimit }}" max="{{ $maxPriceLimit }}" value="{{ $minPrice }}" class="slider-input" id="minPriceInput">
                                <input type="range" min="{{ $minPriceLimit }}" max="{{ $maxPriceLimit }}" value="{{ $maxPrice }}" class="slider-input" id="maxPriceInput">
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center text-muted mt-3">
                                <span class="price-badge px-2 py-1 rounded bg-light border">৳ <span id="minPriceDisplay">{{ number_format($minPrice) }}</span></span>
                                <span class="small text-uppercase fw-bold">to</span>
                                <span class="price-badge px-2 py-1 rounded bg-light border">৳ <span id="maxPriceDisplay">{{ number_format($maxPrice) }}</span></span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Menu Grid Column -->
            <div class="col-12 col-lg-9">
                <div class="position-relative min-vh-50">
                    
                    <!-- Loading overlay spinner -->
                    <div id="menuLoader" class="menu-loader-overlay d-none justify-content-center align-items-center position-absolute w-100 h-100 rounded-3">
                        <div class="spinner-border text-brand" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>

                    <!-- Dynamic Menu Grid Container -->
                    <div id="menuGridContainer">
                        @include('frontend.partials.menu_grid')
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>
@endsection

@push('front_js')
<script>
    $(document).ready(function() {
        const filterSidebar = document.querySelector('.filter-sidebar');
        const menuGridSection = document.querySelector('.menu-grid-section');
        const filterColumn = filterSidebar.closest('.col-12');
        const container = menuGridSection.querySelector('.container');
        
        const sectionTop = menuGridSection.offsetTop;
        const sectionHeight = menuGridSection.offsetHeight;
        let filterWidth = filterSidebar.offsetWidth;
        let filterLeftPos = 0;
        let containerLeftPos = 0;
        let scrollTimeout;

        // Calculate filter position relative to viewport
        function calculatePositions() {
            const filterRect = filterSidebar.getBoundingClientRect();
            const containerRect = container.getBoundingClientRect();
            
            filterWidth = filterSidebar.offsetWidth;
            filterLeftPos = filterRect.left + window.scrollX; // Absolute position from left
            containerLeftPos = containerRect.left + window.scrollX; // Container absolute position
        }

        // Handle window resize to update positions
        window.addEventListener('resize', calculatePositions);
        calculatePositions(); // Initial calculation

        // Throttle scroll event for smooth performance
        window.addEventListener('scroll', function() {
            clearTimeout(scrollTimeout);
            
            scrollTimeout = setTimeout(function() {
                const scrollTop = window.scrollY;
                const sectionBottom = sectionTop + sectionHeight;

                // Check if we're within the menu grid section
                if (scrollTop >= sectionTop && scrollTop < sectionBottom - 300) {
                    // Add sticky class
                    if (!filterSidebar.classList.contains('is-sticky')) {
                        calculatePositions();
                        filterSidebar.classList.add('is-sticky');
                        // Set the left position to keep it in place
                        filterSidebar.style.left = filterLeftPos + 'px';
                        filterSidebar.style.width = filterWidth + 'px';
                    }
                } else {
                    // Remove sticky class
                    if (filterSidebar.classList.contains('is-sticky')) {
                        filterSidebar.classList.remove('is-sticky');
                        filterSidebar.style.left = 'auto';
                        filterSidebar.style.width = 'auto';
                    }
                }
            }, 10);
        }, { passive: true });

        const minInput = document.getElementById('minPriceInput');
        const maxInput = document.getElementById('maxPriceInput');
        const minDisplay = document.getElementById('minPriceDisplay');
        const maxDisplay = document.getElementById('maxPriceDisplay');
        const track = document.getElementById('sliderTrack');
        const minLimit = parseFloat(minInput.min);
        const maxLimit = parseFloat(maxInput.max);

        function updateSliderFill() {
            let val1 = parseFloat(minInput.value);
            let val2 = parseFloat(maxInput.value);
            
            // Constrain minimum slider from passing maximum slider
            if (val1 > val2 - 10) {
                minInput.value = val2 - 10;
                val1 = val2 - 10;
            }

            // Constrain maximum slider from passing minimum slider
            if (val2 < val1 + 10) {
                maxInput.value = val1 + 10;
                val2 = val1 + 10;
            }

            minDisplay.textContent = Math.round(val1);
            maxDisplay.textContent = Math.round(val2);

            // Calculate fill percentages
            const percent1 = ((val1 - minLimit) / (maxLimit - minLimit)) * 100;
            const percent2 = ((val2 - minLimit) / (maxLimit - minLimit)) * 100;

            track.style.background = `linear-gradient(to right, #e9ecef ${percent1}%, var(--brand) ${percent1}%, var(--brand) ${percent2}%, #e9ecef ${percent2}%)`;
        }

        function fetchFilteredMenus(page = 1) {
            const category = $('.category-pill-btn.active').data('category') || '';
            const minPrice = Math.round(minInput.value);
            const maxPrice = Math.round(maxInput.value);

            // Build page URL with parameters
            const url = new URL(window.location.href);
            url.searchParams.set('category', category);
            url.searchParams.set('min_price', minPrice);
            url.searchParams.set('max_price', maxPrice);
            url.searchParams.set('page', page);

            // Update browser location URL without reload
            history.pushState(null, '', url.toString());

            // Show AJAX spinner
            $('#menuLoader').removeClass('d-none').addClass('d-flex');

            // AJAX request to fetch items
            $.ajax({
                url: url.toString(),
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    $('#menuGridContainer').html(response);
                    $('#menuLoader').removeClass('d-flex').addClass('d-none');
                    
                    // Smoothly scroll back to the top of the grid list
                    $('html, body').animate({
                        scrollTop: $('#menuGridContainer').offset().top - 120
                    }, 300);
                },
                error: function() {
                    $('#menuLoader').removeClass('d-flex').addClass('d-none');
                }
            });
        }

        // Initialize slider track styling
        updateSliderFill();

        // Listen for input slider drags
        $(minInput).on('input', updateSliderFill);
        $(maxInput).on('input', updateSliderFill);

        // Listen for user finishing slider adjustments to fire filters
        $(minInput).on('change', function() { fetchFilteredMenus(1); });
        $(maxInput).on('change', function() { fetchFilteredMenus(1); });

        // Category button filter trigger
        $(document).on('click', '.category-pill-btn', function(e) {
            e.preventDefault();
            $('.category-pill-btn').removeClass('active');
            $(this).addClass('active');
            fetchFilteredMenus(1);
        });

        // AJAX pagination clicks
        $(document).on('click', '.pagination-ajax', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            fetchFilteredMenus(page);
        });
    });
</script>
@endpush
