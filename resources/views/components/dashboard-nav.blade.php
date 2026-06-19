<li class="nav-item">
    <a class="nav-link menu-link" href="{{ route('dashboard') }}">
        <i class="ri-dashboard-line"></i><span data-key="t-dashboard">{{ __('Dashboard') }}</span>
    </a>
</li>
@can('orders-show')
<li class="nav-item">
    <a class="nav-link menu-link" href="{{ route('orders.index') }}">
        <i class="ri-shopping-cart-2-line"></i><span data-key="t-orders">{{ __('Orders') }}</span>
    </a>
</li>
@endcan
@can('members-show')
<li class="nav-item">
    <a class="nav-link menu-link" href="{{ route('members.index') }}">
        <i class="ri-user-line"></i><span data-key="t-members">{{ __('Members') }}</span>
    </a>
</li>
@endcan
@can('offers-show')
<li class="nav-item">
    <a class="nav-link menu-link" href="{{ route('offers.index') }}">
        <i class="ri-price-tag-3-line"></i><span data-key="t-offers">{{ __('Offers') }}</span>
    </a>
</li>
@endcan
@can('reviews-show')
<li class="nav-item">
    <a class="nav-link menu-link" href="{{ route('admin.reviews.index') }}">
        <i class="ri-star-line"></i><span data-key="t-reviews">{{ __('Reviews') }}</span>
    </a>
</li>
@endcan
