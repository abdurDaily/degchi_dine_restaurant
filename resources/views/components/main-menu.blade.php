<div class="m-1 rounded dropdown sidebar-user">
    <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">
        <span class="gap-2 d-flex align-items-center">
            <img class="rounded header-profile-user" src="{{ Auth::user()->profile_image }}"
                alt="Header Avatar">
            <span class="text-start">
                <span class="d-block fw-medium sidebar-user-name-text">{{Session::get('user')['name'] ?? ''}}</span>
                <span class="d-block fs-14 sidebar-user-name-sub-text"><i
                        class="align-baseline ri ri-circle-fill fs-10 text-success"></i> <span
                        class="align-middle">Online</span></span>
            </span>
        </span>
    </button>
    <div class="dropdown-menu dropdown-menu-end">
        <!-- item-->
        <h6 class="dropdown-header">Welcome Anna!</h6>
        <a class="dropdown-item" href="pages-profile.html"><i
                class="align-middle mdi mdi-account-circle text-muted fs-16 me-1"></i> <span
                class="align-middle">Profile</span></a>
        <a class="dropdown-item" href="apps-chat.html"><i
                class="align-middle mdi mdi-message-text-outline text-muted fs-16 me-1"></i> <span
                class="align-middle">Messages</span></a>
        <a class="dropdown-item" href="apps-tasks-kanban.html"><i
                class="align-middle mdi mdi-calendar-check-outline text-muted fs-16 me-1"></i> <span
                class="align-middle">Taskboard</span></a>
        <a class="dropdown-item" href="pages-faqs.html"><i
                class="align-middle mdi mdi-lifebuoy text-muted fs-16 me-1"></i> <span
                class="align-middle">Help</span></a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="pages-profile.html"><i
                class="align-middle mdi mdi-wallet text-muted fs-16 me-1"></i> <span class="align-middle">Balance :
                <b>$5971.67</b></span></a>
        <a class="dropdown-item" href="pages-profile-settings.html"><span
                class="mt-1 badge bg-success-subtle text-success float-end">New</span><i
                class="align-middle mdi mdi-cog-outline text-muted fs-16 me-1"></i> <span
                class="align-middle">Settings</span></a>
        <a class="dropdown-item" href="auth-lockscreen-basic.html"><i
                class="align-middle mdi mdi-lock text-muted fs-16 me-1"></i> <span class="align-middle">Lock
                screen</span></a>
        <a class="dropdown-item" href="javascript:void(0)" class="logout"><i
                class="align-middle mdi mdi-logout text-muted fs-16 me-1"></i> <span class="align-middle"
                data-key="t-logout">Logout</span></a>
    </div>
</div>
<div id="scrollbar">
    <div class="container-fluid">
        <div id="two-column-menu">
        </div>
        <ul class="navbar-nav" id="navbar-nav">
            <x-dashboard-nav></x-dashboard-nav>
            <x-user-nav></x-user-nav>
            <x-setting-nav></x-setting-nav>
            <x-branch></x-branch>
            <x-category></x-category>
            <x-frontend-content-nav></x-frontend-content-nav>
        </ul>
    </div>
    <!-- Sidebar -->
</div>
