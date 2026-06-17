@extends('frontend.layout')

@section('frontend_content')
<div class="menu-page">
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
<section class="menu-grid-section py-4 py-lg-5">
    <div class="container px-4 px-lg-5">
        <div class="row g-4 menu-layout-row">
            
            <!-- Desktop sidebar filters -->
            <div class="col-lg-3 filter-col filter-col-desktop d-none d-lg-block">
                <div class="filter-sticky-boundary" id="filterStickyBoundary">
                    @include('frontend.partials.menu_filters', ['suffix' => ''])
                </div>
            </div>

            <!-- Menu Grid Column -->
            <div class="col-12 col-lg-9 menu-grid-col">
                <!-- Mobile: sticky filter bar at top, filter data below header -->
                <div class="menu-mobile-sticky-filter d-lg-none" id="menuMobileStickyFilter">
                    @include('frontend.partials.menu_filters', ['suffix' => 'Mobile'])
                </div>

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
</div>
@endsection

@push('front_js')
<script>
    $(document).ready(function() {
        const filterSidebar = document.getElementById('filterSidebar');
        const filterBoundary = document.getElementById('filterStickyBoundary');
        const filterCol = document.querySelector('.filter-col-desktop');
        const mobileFilterWrap = document.getElementById('menuMobileStickyFilter');
        const mobileFilterSidebar = document.getElementById('filterSidebarMobile');
        const menuGridContainer = document.getElementById('menuGridContainer');
        const DESKTOP_MIN = 992;

        let stickStart = 0;
        let stickEnd = 0;
        let sidebarWidth = 0;
        let sidebarLeft = 0;
        let mobileStickStart = 0;
        let mobileStickEnd = 0;
        let mobileFilterHeight = 0;
        let mobileFilterLeft = 0;
        let mobileFilterWidth = 0;
        let stickyTimer = null;

        function setMobileFilterTopVar() {
            const mobileNav = document.querySelector('.mobile-topbar');
            const top = mobileNav ? mobileNav.offsetHeight : 72;
            document.documentElement.style.setProperty('--menu-filter-top', top + 'px');
        }

        function getDesktopStickyTop() {
            const desktopNav = document.getElementById('desktopNavbar');
            return (desktopNav ? desktopNav.offsetHeight : 86) + 20;
        }

        function getMobileStickyTop() {
            const mobileNav = document.querySelector('.mobile-topbar');
            return mobileNav ? mobileNav.offsetHeight : 72;
        }

        function getMobileFilterBox() {
            const container = mobileFilterWrap?.closest('.container');
            if (container) {
                const rect = container.getBoundingClientRect();
                return { left: rect.left, width: rect.width };
            }
            if (mobileFilterWrap) {
                const rect = mobileFilterWrap.getBoundingClientRect();
                return { left: rect.left, width: rect.width };
            }
            return { left: 0, width: window.innerWidth };
        }

        function getVisibleFilterScope() {
            return window.innerWidth < DESKTOP_MIN
                ? document.getElementById('menuMobileStickyFilter')
                : document.querySelector('.filter-col-desktop');
        }

        function getCardsBottom() {
            if (!menuGridContainer) return 0;

            const pagination = menuGridContainer.querySelector('.modern-pagination')?.closest('.d-flex');
            if (pagination) {
                return pagination.getBoundingClientRect().top + window.scrollY;
            }

            const cardsRow = menuGridContainer.querySelector('.row');
            if (cardsRow) {
                const rect = cardsRow.getBoundingClientRect();
                return rect.bottom + window.scrollY;
            }

            const rect = menuGridContainer.getBoundingClientRect();
            return rect.bottom + window.scrollY;
        }

        function resetFilterSticky() {
            if (filterSidebar) {
                filterSidebar.classList.remove('is-sticky', 'is-sticky-bottom');
                filterSidebar.style.position = '';
                filterSidebar.style.top = '';
                filterSidebar.style.left = '';
                filterSidebar.style.width = '';
                filterSidebar.style.bottom = '';
                filterSidebar.style.zIndex = '';
                filterSidebar.style.maxHeight = '';
            }
            if (filterBoundary) {
                filterBoundary.style.minHeight = '';
            }
        }

        function resetMobileFilterSticky() {
            if (!mobileFilterWrap || !mobileFilterSidebar) return;
            mobileFilterWrap.style.height = '';
            mobileFilterWrap.classList.remove('is-js-sticky-active');
            mobileFilterSidebar.classList.remove('is-sticky', 'is-sticky-bottom', 'is-mobile-fixed');
            mobileFilterSidebar.style.position = '';
            mobileFilterSidebar.style.top = '';
            mobileFilterSidebar.style.left = '';
            mobileFilterSidebar.style.width = '';
            mobileFilterSidebar.style.right = '';
            mobileFilterSidebar.style.bottom = '';
            mobileFilterSidebar.style.zIndex = '';
        }

        function measureDesktopFilterSticky() {
            resetFilterSticky();

            if (window.innerWidth < DESKTOP_MIN || !filterSidebar || !filterBoundary || !filterCol) {
                return;
            }

            const stickyTop = getDesktopStickyTop();
            const boundaryTop = filterBoundary.getBoundingClientRect().top + window.scrollY;
            const cardsBottom = getCardsBottom();
            const cardsHeight = Math.max(cardsBottom - boundaryTop, filterSidebar.offsetHeight);

            filterBoundary.style.minHeight = cardsHeight + 'px';

            const colRect = filterCol.getBoundingClientRect();
            sidebarWidth = colRect.width;
            sidebarLeft = colRect.left;
            stickStart = boundaryTop - stickyTop;
            stickEnd = cardsBottom - stickyTop - filterSidebar.offsetHeight;

            if (stickEnd < stickStart) {
                stickEnd = stickStart;
            }
        }

        function measureMobileFilterSticky() {
            resetMobileFilterSticky();

            if (window.innerWidth >= DESKTOP_MIN || !mobileFilterWrap || !mobileFilterSidebar) {
                return;
            }

            const stickyTop = getMobileStickyTop();
            const wrapTop = mobileFilterWrap.getBoundingClientRect().top + window.scrollY;
            const cardsBottom = getCardsBottom();
            const box = getMobileFilterBox();

            mobileFilterHeight = mobileFilterSidebar.offsetHeight;
            mobileFilterLeft = box.left;
            mobileFilterWidth = box.width;
            mobileStickStart = wrapTop - stickyTop;
            mobileStickEnd = cardsBottom - stickyTop - mobileFilterHeight;

            if (mobileStickEnd < mobileStickStart) {
                mobileStickEnd = mobileStickStart;
            }
        }

        function measureFilterSticky() {
            setMobileFilterTopVar();
            measureDesktopFilterSticky();
            measureMobileFilterSticky();
            updateFilterSticky();
            updateMobileFilterSticky();
        }

        function updateDesktopFilterSticky() {
            if (window.innerWidth < DESKTOP_MIN || !filterSidebar || !filterBoundary) {
                resetFilterSticky();
                return;
            }

            const stickyTop = getDesktopStickyTop();
            const scrollY = window.scrollY;
            const colRect = filterCol.getBoundingClientRect();
            sidebarLeft = colRect.left;
            sidebarWidth = colRect.width;

            if (scrollY <= stickStart) {
                resetFilterSticky();
                return;
            }

            if (scrollY >= stickEnd) {
                filterSidebar.classList.remove('is-sticky');
                filterSidebar.classList.add('is-sticky-bottom');
                filterSidebar.style.position = 'absolute';
                filterSidebar.style.top = 'auto';
                filterSidebar.style.bottom = '0';
                filterSidebar.style.left = '0';
                filterSidebar.style.width = '100%';
                filterSidebar.style.zIndex = '30';
                return;
            }

            filterSidebar.classList.add('is-sticky');
            filterSidebar.classList.remove('is-sticky-bottom');
            filterSidebar.style.position = 'fixed';
            filterSidebar.style.top = stickyTop + 'px';
            filterSidebar.style.left = sidebarLeft + 'px';
            filterSidebar.style.width = sidebarWidth + 'px';
            filterSidebar.style.bottom = 'auto';
            filterSidebar.style.zIndex = '30';
        }

        function updateMobileFilterSticky() {
            if (window.innerWidth >= DESKTOP_MIN || !mobileFilterWrap || !mobileFilterSidebar) {
                resetMobileFilterSticky();
                return;
            }

            const stickyTop = getMobileStickyTop();
            const scrollY = window.scrollY;
            const box = getMobileFilterBox();
            mobileFilterLeft = box.left;
            mobileFilterWidth = box.width;
            mobileFilterHeight = mobileFilterSidebar.offsetHeight;

            if (scrollY <= mobileStickStart) {
                resetMobileFilterSticky();
                return;
            }

            const stickTravel = mobileStickEnd - mobileStickStart;

            if (scrollY >= mobileStickEnd) {
                mobileFilterWrap.style.height = (stickTravel + mobileFilterHeight) + 'px';
                mobileFilterWrap.classList.add('is-js-sticky-active');
                mobileFilterSidebar.classList.remove('is-sticky', 'is-mobile-fixed');
                mobileFilterSidebar.classList.add('is-sticky-bottom');
                mobileFilterSidebar.style.position = 'absolute';
                mobileFilterSidebar.style.top = stickTravel + 'px';
                mobileFilterSidebar.style.left = '0';
                mobileFilterSidebar.style.width = '100%';
                mobileFilterSidebar.style.bottom = 'auto';
                mobileFilterSidebar.style.zIndex = '1025';
                return;
            }

            mobileFilterWrap.style.height = mobileFilterHeight + 'px';
            mobileFilterWrap.classList.add('is-js-sticky-active');
            mobileFilterSidebar.classList.add('is-sticky', 'is-mobile-fixed');
            mobileFilterSidebar.classList.remove('is-sticky-bottom');
            mobileFilterSidebar.style.position = 'fixed';
            mobileFilterSidebar.style.top = stickyTop + 'px';
            mobileFilterSidebar.style.left = mobileFilterLeft + 'px';
            mobileFilterSidebar.style.width = mobileFilterWidth + 'px';
            mobileFilterSidebar.style.bottom = 'auto';
            mobileFilterSidebar.style.zIndex = '1025';
        }

        function updateFilterSticky() {
            updateDesktopFilterSticky();
            updateMobileFilterSticky();
        }

        function scheduleFilterSticky() {
            clearTimeout(stickyTimer);
            stickyTimer = setTimeout(measureFilterSticky, 50);
        }

        measureFilterSticky();
        window.addEventListener('scroll', updateFilterSticky, { passive: true });
        window.addEventListener('resize', scheduleFilterSticky, { passive: true });
        window.addEventListener('load', scheduleFilterSticky);

        function updateSliderFillForScope(scope) {
            if (!scope) return;

            const minInput = scope.querySelector('.menu-min-price');
            const maxInput = scope.querySelector('.menu-max-price');
            const minDisplay = scope.querySelector('.menu-min-display');
            const maxDisplay = scope.querySelector('.menu-max-display');
            const track = scope.querySelector('.slider-track');
            if (!minInput || !maxInput || !track) return;

            const minLimit = parseFloat(minInput.min);
            const maxLimit = parseFloat(maxInput.max);
            let val1 = parseFloat(minInput.value);
            let val2 = parseFloat(maxInput.value);

            if (val1 > val2 - 10) {
                minInput.value = val2 - 10;
                val1 = val2 - 10;
            }
            if (val2 < val1 + 10) {
                maxInput.value = val1 + 10;
                val2 = val1 + 10;
            }

            if (minDisplay) minDisplay.textContent = Math.round(val1);
            if (maxDisplay) maxDisplay.textContent = Math.round(val2);

            const percent1 = ((val1 - minLimit) / (maxLimit - minLimit)) * 100;
            const percent2 = ((val2 - minLimit) / (maxLimit - minLimit)) * 100;
            track.style.background = `linear-gradient(to right, #e9ecef ${percent1}%, var(--brand) ${percent1}%, var(--brand) ${percent2}%, #e9ecef ${percent2}%)`;
        }

        function updateSliderFill() {
            updateSliderFillForScope(document.querySelector('.filter-col-desktop'));
            updateSliderFillForScope(document.getElementById('menuMobileStickyFilter'));
        }

        function syncSlidersFromSource(sourceScope) {
            if (!sourceScope) return;
            const minVal = sourceScope.querySelector('.menu-min-price')?.value;
            const maxVal = sourceScope.querySelector('.menu-max-price')?.value;
            [document.querySelector('.filter-col-desktop'), document.getElementById('menuMobileStickyFilter')].forEach(function(scope) {
                if (!scope || scope === sourceScope) return;
                const minInput = scope.querySelector('.menu-min-price');
                const maxInput = scope.querySelector('.menu-max-price');
                if (minInput && minVal !== undefined) minInput.value = minVal;
                if (maxInput && maxVal !== undefined) maxInput.value = maxVal;
                updateSliderFillForScope(scope);
            });
        }

        function fetchFilteredMenus(page = 1) {
            const scope = getVisibleFilterScope();
            const minInput = scope?.querySelector('.menu-min-price');
            const maxInput = scope?.querySelector('.menu-max-price');
            const category = $('.category-pill-btn.active').data('category') || '';
            const minPrice = Math.round(minInput?.value || 0);
            const maxPrice = Math.round(maxInput?.value || 0);

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
                    scheduleFilterSticky();
                    
                    // Smoothly scroll back to the top of the grid list
                    $('html, body').animate({
                        scrollTop: $('#menuGridContainer').offset().top - 120
                    }, 300, function() {
                        measureFilterSticky();
                    });
                },
                error: function() {
                    $('#menuLoader').removeClass('d-flex').addClass('d-none');
                }
            });
        }

        // Initialize slider track styling
        updateSliderFill();

        $(document).on('input', '.menu-min-price, .menu-max-price', function() {
            const scope = this.closest('.filter-col-desktop, .menu-mobile-sticky-filter');
            updateSliderFillForScope(scope);
            syncSlidersFromSource(scope);
        });

        $(document).on('change', '.menu-min-price, .menu-max-price', function() {
            fetchFilteredMenus(1);
        });

        // Category button filter trigger
        $(document).on('click', '.category-pill-btn', function(e) {
            e.preventDefault();
            const category = $(this).data('category') || '';
            $('.category-pill-btn').removeClass('active');
            $('.category-pill-btn[data-category="' + category + '"]').addClass('active');
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
