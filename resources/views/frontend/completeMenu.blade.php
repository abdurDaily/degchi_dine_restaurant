@extends('frontend.layout')

@section('meta_title', 'Complete Menu')
@section('meta_description', 'Browse the full Degchi Dine menu — kacchi, biriyani, platters and signature dishes. Order online for delivery or pickup.')

@section('frontend_content')
<div class="menu-page">

<section class="menu-hero-section py-5 text-center position-relative">
    <div class="container px-4 px-lg-5">
        @if(isset($activeOfferDetails) && $activeOfferDetails)
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
            @elseif(!empty($offerOnly))
                All Offer Items
            @else
                Our Complete Menu
            @endif
        </h1>
    </div>
</section>

<section class="menu-grid-section py-4 py-lg-5">
    <div class="container px-4 px-lg-5">
        <div class="row g-4 menu-layout-row">

            <div class="col-lg-3">
                <div class="menu-sticky-filter-wrap" id="menuStickyFilterWrap">
                    @include('frontend.partials.menu_filters', ['suffix' => ''])
                </div>
            </div>

            <div class="position-relative min-vh-50 col-lg-9">
                <div id="menuLoader" class="menu-loader-overlay d-none justify-content-center align-items-center position-absolute w-100 h-100 rounded-3">
                    <div class="spinner-border text-brand" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div id="menuGridContainer">
                    @include('frontend.partials.menu_grid')
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

        // ---------- Sticky filter offset ----------
        function setFilterTopVar() {
            const desktopNav = document.getElementById('desktopNavbar');
            const mobileNav = document.querySelector('.mobile-topbar');
            const nav = window.innerWidth >= 992 ? desktopNav : mobileNav;
            const top = (nav ? nav.offsetHeight : (window.innerWidth >= 992 ? 86 : 72));
            document.documentElement.style.setProperty('--menu-filter-top', top + 'px');
        }
        setFilterTopVar();
        window.addEventListener('resize', setFilterTopVar, { passive: true });
        window.addEventListener('load', setFilterTopVar);

        function getFilterScope() {
            return document.getElementById('menuStickyFilterWrap');
        }

        // ---------- Price slider fill ----------
        function updateSliderFill() {
            const scope = getFilterScope();
            if (!scope) return;

            const minInput = scope.querySelector('.menu-min-price');
            const maxInput = scope.querySelector('.menu-max-price');
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

            const minDisplayEl = document.getElementById('minPriceDisplay');
            const maxDisplayEl = document.getElementById('maxPriceDisplay');
            if (minDisplayEl) minDisplayEl.textContent = Math.round(val1);
            if (maxDisplayEl) maxDisplayEl.textContent = Math.round(val2);

            const percent1 = ((val1 - minLimit) / (maxLimit - minLimit)) * 100;
            const percent2 = ((val2 - minLimit) / (maxLimit - minLimit)) * 100;
            track.style.background = `linear-gradient(to right, #e9ecef ${percent1}%, var(--brand) ${percent1}%, var(--brand) ${percent2}%, #e9ecef ${percent2}%)`;
        }

        // ---------- Helpers ----------
        function getSelectedCategories() {
            return $('.menu-category-checkbox:checked')
                .not('[data-all-categories]')
                .not('[data-offer-filter]')
                .not('[data-popular-filter]') 
                .map(function() { return this.value; })
                .get()
                .filter(Boolean);
        }

        function isOfferChecked() {
            return $('.menu-category-checkbox[data-offer-filter]').is(':checked');
        }

        function isPopularChecked() {
            return $('.menu-category-checkbox[data-popular-filter]').is(':checked');
        }

        // ---------- Main AJAX fetch ----------
        function fetchFilteredMenus(page = 1) {
            const scope = getFilterScope();
            const minInput = scope?.querySelector('.menu-min-price');
            const maxInput = scope?.querySelector('.menu-max-price');
            const categories = getSelectedCategories();
            const minPrice = Math.round(minInput?.value || 0);
            const maxPrice = Math.round(maxInput?.value || 0);
            const offerChecked = isOfferChecked();
            const popularChecked = isPopularChecked(); 

            const url = new URL(window.location.href);
            url.searchParams.delete('categories[]');
            url.searchParams.delete('category');

            categories.forEach(function(slug) {
                url.searchParams.append('categories[]', slug);
            });

            url.searchParams.set('min_price', minPrice);
            url.searchParams.set('max_price', maxPrice);
            url.searchParams.set('page', page);

            if (offerChecked) {
                url.searchParams.set('offerFilter', 1);
            } else {
                url.searchParams.delete('offerFilter');
            }

            
            if (popularChecked) {
                url.searchParams.set('popularFilter', 1);
            } else {
                url.searchParams.delete('popularFilter');
            }

            history.pushState(null, '', url.toString());

            $('#menuLoader').removeClass('d-none').addClass('d-flex');

            $.ajax({
                url: url.toString(),
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    $('#menuGridContainer').html(response);
                    $('#menuLoader').removeClass('d-flex').addClass('d-none');

                    $('html, body').animate({
                        scrollTop: $('#menuGridContainer').offset().top - 160
                    }, 300);
                },
                error: function() {
                    $('#menuLoader').removeClass('d-flex').addClass('d-none');
                }
            });
        }

        updateSliderFill();

        // ---------- Price slider events ----------
        $(document).on('input', '.menu-min-price, .menu-max-price', function() {
            updateSliderFill();
        });
        $(document).on('change', '.menu-min-price, .menu-max-price', function() {
            fetchFilteredMenus(1);
        });

        // ---------- Category / Offer / Popular / All checkbox logic ----------
        $(document).on('change', '.menu-category-checkbox', function() {
            const isAll     = $(this).is('[data-all-categories]');
            const isOffer   = $(this).is('[data-offer-filter]');
            const isPopular = $(this).is('[data-popular-filter]'); 

            if (isAll) {
                if (this.checked) {
                    $('.menu-category-checkbox').not(this).prop('checked', false);
                } else if (getSelectedCategories().length === 0 && !isOfferChecked() && !isPopularChecked()) {
                    $(this).prop('checked', true);
                    return;
                }
            } else if (isOffer) {
                if (this.checked) {
                    $('.menu-category-checkbox').not(this).prop('checked', false);
                } else if (getSelectedCategories().length === 0 && !isPopularChecked()) {
                    $('.menu-category-checkbox[data-all-categories]').prop('checked', true);
                }
            } else if (isPopular) {
                // ✅ Popular সিলেক্ট করলে বাকি সব বাদ যাবে (mutually exclusive)
                if (this.checked) {
                    $('.menu-category-checkbox').not(this).prop('checked', false);
                } else if (getSelectedCategories().length === 0 && !isOfferChecked()) {
                    $('.menu-category-checkbox[data-all-categories]').prop('checked', true);
                }
            } else {
                $('.menu-category-checkbox[data-all-categories]').prop('checked', false);
                $('.menu-category-checkbox[data-offer-filter]').prop('checked', false);
                $('.menu-category-checkbox[data-popular-filter]').prop('checked', false); 

                if (getSelectedCategories().length === 0) {
                    $('.menu-category-checkbox[data-all-categories]').prop('checked', true);
                }
            }

            // Sync visual state
            $('.menu-category-checkbox').each(function() {
                $(this)
                    .closest('.category-list-item, .category-checkbox-chip')
                    .toggleClass('is-checked', this.checked);
            });

            fetchFilteredMenus(1);
        });

        // ---------- AJAX pagination ----------
        $(document).on('click', '.pagination-ajax', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            fetchFilteredMenus(page);
        });
    });
</script>
@endpush