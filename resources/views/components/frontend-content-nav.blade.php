@canAny(['signature-platters-list', 'facebook-reels-list', 'about-show', 'contact-show', 'general-setting'])
<li class="nav-item">
    <a class="nav-link menu-link" href="#frontendContentNav" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="frontendContentNav">
        <i class="ri-layout-masonry-line"></i>
        <span data-key="t-frontend-content">Frontend Content</span>
    </a>
    <div class="collapse menu-dropdown" id="frontendContentNav">
        <ul class="nav nav-sm flex-column">

            @can('signature-platters-list')
            <li class="nav-item">
                <a href="{{ route('admin.signature-platters.index') }}" class="nav-link" data-key="t-signature-platters">
                    <i class="ri-restaurant-2-line me-1"></i> Signature Platters
                </a>
            </li>
            @endcan

            @can('facebook-reels-list')
            <li class="nav-item">
                <a href="{{ route('admin.facebook-reels.index') }}" class="nav-link" data-key="t-facebook-reels">
                    <i class="bi bi-facebook me-1"></i> Facebook Reels
                </a>
            </li>
            @endcan

            @can('about-show')
            <li class="nav-item">
                <a href="{{ route('admin.about.index') }}" class="nav-link" data-key="t-about">
                    <i class="ri-information-line me-1"></i> About Section
                </a>
            </li>
            @endcan

            @can('contact-show')
            <li class="nav-item">
                <a href="{{ route('admin.contact.index') }}" class="nav-link" data-key="t-contact">
                    <i class="ri-map-pin-line me-1"></i> Contact / Location
                </a>
            </li>
            @endcan

            @can('general-setting')
            <li class="nav-item">
                <a href="{{ route('seo-setting') }}" class="nav-link" data-key="t-seo-setting">
                    <i class="ri-search-eye-line me-1"></i> SEO & Tracking
                </a>
            </li>
            @endcan

        </ul>
    </div>
</li>
@endcanAny
