<div class="m-1 rounded dropdown sidebar-user">
    <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown-sidebar" data-bs-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">
        <span class="gap-2 d-flex align-items-center">
            <img class="rounded header-profile-user"
                src="{{ Auth::user()->profile_image }}"
                alt="{{ Auth::user()->name }}"
                onerror="this.onerror=null;this.src='{{ asset('assets/images/user-dummy-img.jpg') }}';">
            <span class="text-start">
                <span class="d-block fw-medium sidebar-user-name-text">{{ Session::get('user')['name'] ?? Auth::user()->name }}</span>
                <span class="d-block fs-14 sidebar-user-name-sub-text"><i
                        class="align-baseline ri ri-circle-fill fs-10 text-success"></i> <span
                        class="align-middle">Online</span></span>
            </span>
        </span>
    </button>
    <div class="dropdown-menu dropdown-menu-end">
        <h6 class="dropdown-header">Welcome {{ Auth::user()->name }}</h6>
        <a class="dropdown-item" href="{{ route('profile') }}"><i
                class="align-middle mdi mdi-account-circle text-muted fs-16 me-1"></i> <span
                class="align-middle">Profile</span></a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dropdown-item"><i
                    class="align-middle mdi mdi-logout text-muted fs-16 me-1"></i> <span
                    class="align-middle" data-key="t-logout">Logout</span></button>
        </form>
    </div>
</div>
<div id="scrollbar">
    <div class="container-fluid">
        <div id="two-column-menu">
        </div>
        <ul class="navbar-nav" id="navbar-nav">
            <li class="menu-title"><span data-key="t-menu-main">Main</span></li>
            <x-dashboard-nav></x-dashboard-nav>

            @canAny(['branch-list', 'category-list', 'menu-list', 'coupon-list'])
                <li class="menu-title"><span data-key="t-menu-catalog">Catalog</span></li>
            @endcanAny
            <x-branch></x-branch>
            <x-category></x-category>
            <x-coupon></x-coupon>

            <li class="menu-title"><span data-key="t-menu-content">Content</span></li>
            <x-frontend-content-nav></x-frontend-content-nav>
            <x-party-booking-nav></x-party-booking-nav>
            <x-blog-nav></x-blog-nav>

            @if (auth()->user()->hasRole('Super Admin') || auth()->user()->canAny(['users-show', 'theme-customization', 'general-setting', 'email-setting', 'pusher-setting']))
                <li class="menu-title"><span data-key="t-menu-system">System</span></li>
            @endif
            <x-user-nav></x-user-nav>
            <x-setting-nav></x-setting-nav>
        </ul>
    </div>
    <!-- Sidebar -->
</div>
