<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="{{ route('frontend.home') }}" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{asset(Session::get('logo'))}}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{asset(Session::get('logo'))}}" alt="" height="17">
                        </span>
                    </a>

                    <a href="{{ route('frontend.home') }}" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{asset(Session::get('logo'))}}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{asset(Session::get('logo'))}}" alt="" height="17">
                        </span>
                    </a>
                </div>

                <button type="button"
                    class="px-3 btn btn-sm fs-16 header-item vertical-menu-btn topnav-hamburger material-shadow-none"
                    id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>

                <!-- App Search-->
                <form class="app-search d-none d-md-block">
                    <div class="position-relative">
                        <input type="text" class="form-control" placeholder="Search..." autocomplete="off"
                            id="search-options" value="">
                        <span class="mdi mdi-magnify search-widget-icon"></span>
                        <span class="mdi mdi-close-circle search-widget-icon search-widget-icon-close d-none"
                            id="search-close-options"></span>
                    </div>
                    <div class="dropdown-menu dropdown-menu-lg" id="search-dropdown">
                        <div data-simplebar style="max-height: 320px;">
                            <!-- item-->
                            <div class="dropdown-header">
                                <h6 class="mb-0 text-overflow text-muted text-uppercase">Recent Searches</h6>
                            </div>

                            <div class="bg-transparent dropdown-item text-wrap">
                                <a href="{{route('dashboard')}}" class="btn btn-soft-secondary btn-sm rounded-pill">how
                                    to setup <i class="mdi mdi-magnify ms-1"></i></a>
                                <a href="{{route('dashboard')}}"
                                    class="btn btn-soft-secondary btn-sm rounded-pill">buttons <i
                                        class="mdi mdi-magnify ms-1"></i></a>
                            </div>
                            <!-- item-->
                            <div class="mt-2 dropdown-header">
                                <h6 class="mb-1 text-overflow text-muted text-uppercase">Pages</h6>
                            </div>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="align-middle ri-bubble-chart-line fs-18 text-muted me-2"></i>
                                <span>Analytics Dashboard</span>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="align-middle ri-lifebuoy-line fs-18 text-muted me-2"></i>
                                <span>Help Center</span>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="align-middle ri-user-settings-line fs-18 text-muted me-2"></i>
                                <span>My account settings</span>
                            </a>

                            <!-- item-->
                            <div class="mt-2 dropdown-header">
                                <h6 class="mb-2 text-overflow text-muted text-uppercase">Members</h6>
                            </div>

                            <div class="notification-list">
                                <!-- item -->
                                <a href="javascript:void(0);" class="py-2 dropdown-item notify-item">
                                    <div class="d-flex">
                                        <img src="{{asset('assets/images/users/avatar-2.jpg')}}"
                                            class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                        <div class="flex-grow-1">
                                            <h6 class="m-0">Angela Bernier</h6>
                                            <span class="mb-0 fs-11 text-muted">Manager</span>
                                        </div>
                                    </div>
                                </a>
                                <!-- item -->
                                <a href="javascript:void(0);" class="py-2 dropdown-item notify-item">
                                    <div class="d-flex">
                                        <img src="{{asset('assets/images/users/avatar-3.jpg')}}"
                                            class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                        <div class="flex-grow-1">
                                            <h6 class="m-0">David Grasso</h6>
                                            <span class="mb-0 fs-11 text-muted">Web Designer</span>
                                        </div>
                                    </div>
                                </a>
                                <!-- item -->
                                <a href="javascript:void(0);" class="py-2 dropdown-item notify-item">
                                    <div class="d-flex">
                                        <img src="{{asset('assets/images/users/avatar-5.jpg')}}"
                                            class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                        <div class="flex-grow-1">
                                            <h6 class="m-0">Mike Bunch</h6>
                                            <span class="mb-0 fs-11 text-muted">React Developer</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="pt-3 pb-1 text-center">
                            <a href="pages-search-results.html" class="btn btn-primary btn-sm">View All Results <i
                                    class="ri-arrow-right-line ms-1"></i></a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="d-flex align-items-center">

                <div class="dropdown d-md-none topbar-head-dropdown header-item">
                    <button type="button"
                        class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle"
                        id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <i class="bx bx-search fs-22"></i>
                    </button>
                    <div class="p-0 dropdown-menu dropdown-menu-lg dropdown-menu-end"
                        aria-labelledby="page-header-search-dropdown">
                        <form class="p-3">
                            <div class="m-0 form-group">

                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search ..."
                                        aria-label="Recipient's username">
                                    <button class="btn btn-primary" type="submit"><i
                                            class="mdi mdi-magnify"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div>
                    <button class="btn btn-primary" id="cacheClear" type="button"><i class="ri-brush-line"></i> Clear
                        Cache</button>
                </div>

                @can('orders-show')
                    @php
                        $adminNotifications = auth()->user()->notifications()->latest()->take(10)->get();
                        $adminUnreadCount = auth()->user()->unreadNotifications()->count();
                    @endphp
                    <div class="dropdown topbar-head-dropdown ms-1 header-item" id="adminNotificationDropdown">
                        <button type="button"
                            class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle"
                            id="page-header-notifications-dropdown"
                            data-bs-toggle="dropdown"
                            data-bs-auto-close="outside"
                            aria-haspopup="true"
                            aria-expanded="false">
                            <i class="bx bx-bell fs-22"></i>
                            <span class="admin-notif-badge {{ $adminUnreadCount > 0 ? '' : 'is-hidden' }}"
                                id="adminNotifBadge"
                                data-count="{{ $adminUnreadCount }}">{{ $adminUnreadCount > 99 ? '99+' : $adminUnreadCount }}</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0 admin-notif-dropdown"
                            aria-labelledby="page-header-notifications-dropdown">
                            <div class="admin-notif-header">
                                <span class="fw-semibold">Notifications</span>
                                <button type="button" class="btn btn-sm btn-link p-0" id="adminNotifMarkAll">Mark all as read</button>
                            </div>
                            <div class="admin-notif-list" id="adminNotifList">
                                @forelse ($adminNotifications as $notification)
                                    @php $data = $notification->data; @endphp
                                    <div class="admin-notif-item {{ $notification->read_at ? '' : 'unread' }}"
                                        data-id="{{ $notification->id }}"
                                        role="button"
                                        tabindex="0">
                                        <div class="fw-medium">Order #{{ $data['order_id'] ?? '—' }}</div>
                                        <div class="text-muted fs-12">{{ $data['message'] ?? '' }}</div>
                                        <div class="text-muted fs-11">{{ $notification->created_at->diffForHumans() }}</div>
                                    </div>
                                @empty
                                    <div class="admin-notif-empty" id="adminNotifEmpty">No notifications yet</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endcan

                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button"
                        class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle"
                        data-toggle="fullscreen">
                        <i class='bx bx-fullscreen fs-22'></i>
                    </button>
                </div>

                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button"
                        class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle light-dark-mode">
                        <i class='bx bx-moon fs-22'></i>
                    </button>
                </div>

               

                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user"
                                src="{{ Auth::user()->profile_image }}" alt="Header Avatar">
                            <span class="text-start ms-xl-2">
                                <span
                                    class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{Auth::user()->name}}</span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <h6 class="dropdown-header">Welcome {{Auth::user()->name}}</h6>
                        <a class="dropdown-item" href="{{route('profile')}}"><i
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
            </div>
        </div>
    </div>
</header>
