@extends('frontend.layout')

@section('meta_title', 'Complete Menu')
@section('meta_description', 'Browse the full Degchi Dine menu — kacchi, biriyani, platters and signature dishes. Order
    online for delivery or pickup.')

@section('frontend_content')
    <div class="menu-page">

        <x-frontend.page-header
            :title="isset($activeOfferDetails) && $activeOfferDetails ? 'Special Offer Items' : (!empty($offerOnly) ? 'All Offer Items' : (!empty($popularOnly) ? 'Customer Favorites' : 'Our Complete Menu'))"
            eyebrow="Degchi Dine"
        >
            <x-slot:eyebrowIcon>
                <iconify-icon icon="solar:plate-bold"></iconify-icon>
            </x-slot:eyebrowIcon>
        </x-frontend.page-header>

        <section class="menu-grid-section py-4 py-lg-5" style="background: linear-gradient(160deg, #1a3a4a 0%, #0f2a38 50%, #162e3c 100%);">
            <div class="container px-4 px-lg-5">
                <div class="row g-4 menu-layout-row">

                    <div class="col-lg-3">
                        <div class="menu-sticky-filter-wrap" id="menuStickyFilterWrap">
                            @include('frontend.partials.menu_filters', ['suffix' => ''])
                        </div>
                    </div>

                    <div class="position-relative min-vh-50 col-lg-9">
                        <div id="menuLoader"
                            class="menu-loader-overlay d-none justify-content-center align-items-center position-absolute w-100 h-100 rounded-3">
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
document.addEventListener('DOMContentLoaded', function() {
    if (typeof jQuery === 'undefined') {
        console.warn('jQuery not loaded yet');
        return;
    }

    var $ = jQuery;
    var $doc = $(document);

    /* ---------- Sticky filter offset ---------- */
    function setFilterTopVar() {
        var desktopNav = document.getElementById('desktopNavbar');
        var mobileNav = document.querySelector('.mobile-topbar');
        var nav = window.innerWidth >= 992 ? desktopNav : mobileNav;
        var top = nav ? nav.offsetHeight : (window.innerWidth >= 992 ? 86 : 72);
        document.documentElement.style.setProperty('--menu-filter-top', top + 'px');
    }
    setFilterTopVar();
    window.addEventListener('resize', setFilterTopVar, { passive: true });
    window.addEventListener('load', setFilterTopVar);

    function getFilterScope() {
        return document.getElementById('menuStickyFilterWrap');
    }

    /* ---------- Price slider fill ---------- */
    function updateSliderFill() {
        var scope = getFilterScope();
        if (!scope) return;

        var minInput = scope.querySelector('.menu-min-price');
        var maxInput = scope.querySelector('.menu-max-price');
        var track = scope.querySelector('.slider-track');
        if (!minInput || !maxInput || !track) return;

        var minLimit = parseFloat(minInput.min);
        var maxLimit = parseFloat(maxInput.max);
        var val1 = parseFloat(minInput.value);
        var val2 = parseFloat(maxInput.value);

        if (val1 > val2 - 10) {
            minInput.value = val2 - 10;
            val1 = val2 - 10;
        }
        if (val2 < val1 + 10) {
            maxInput.value = val1 + 10;
            val2 = val1 + 10;
        }

        var minDisplayEl = document.getElementById('minPriceDisplay');
        var maxDisplayEl = document.getElementById('maxPriceDisplay');
        if (minDisplayEl) minDisplayEl.textContent = Math.round(val1);
        if (maxDisplayEl) maxDisplayEl.textContent = Math.round(val2);

        var percent1 = ((val1 - minLimit) / (maxLimit - minLimit)) * 100;
        var percent2 = ((val2 - minLimit) / (maxLimit - minLimit)) * 100;
        track.style.background =
            'linear-gradient(to right, #e9ecef ' + percent1 + '%, var(--brand) ' + percent1 + '%, var(--brand) ' + percent2 + '%, #e9ecef ' + percent2 + '%)';
    }

    /* ---------- Helpers ---------- */
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

    /* ---------- Main AJAX fetch ---------- */
    function fetchFilteredMenus(page) {
        page = page || 1;
        var scope = getFilterScope();
        var minInput = scope ? scope.querySelector('.menu-min-price') : null;
        var maxInput = scope ? scope.querySelector('.menu-max-price') : null;

        var categories = getSelectedCategories();
        var minPrice = Math.round(minInput ? minInput.value : 0);
        var maxPrice = Math.round(maxInput ? maxInput.value : 0);
        var offerChecked = isOfferChecked();
        var popularChecked = isPopularChecked();

        var url = new URL(window.location.href);
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
            url.searchParams.delete('offer');
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

    /* ---------- Price slider events ---------- */
    $doc.on('input', '.menu-min-price, .menu-max-price', updateSliderFill);
    $doc.on('change', '.menu-min-price, .menu-max-price', function() {
        fetchFilteredMenus(1);
    });

    /* ---------- Category / Offer / Popular checkbox logic ---------- */
    $doc.on('change', '.menu-category-checkbox', function() {
        var isOffer = $(this).is('[data-offer-filter]');
        var isPopular = $(this).is('[data-popular-filter]');

        if (isOffer) {
            if (this.checked) {
                $('.menu-category-checkbox').not(this).prop('checked', false);
            }
        } else if (isPopular) {
            if (this.checked) {
                $('.menu-category-checkbox').not(this).prop('checked', false);
            }
        } else {
            $('.menu-category-checkbox[data-offer-filter]').prop('checked', false);
            $('.menu-category-checkbox[data-popular-filter]').prop('checked', false);
        }

        $('.menu-category-checkbox').each(function() {
            $(this)
                .closest('.category-list-item, .category-checkbox-chip')
                .toggleClass('is-checked', this.checked);
        });

        fetchFilteredMenus(1);
    });

    /* ---------- AJAX pagination ---------- */
    $doc.on('click', '.pagination-ajax', function(e) {
        e.preventDefault();
        fetchFilteredMenus($(this).data('page'));
    });
});
</script>
@endpush
