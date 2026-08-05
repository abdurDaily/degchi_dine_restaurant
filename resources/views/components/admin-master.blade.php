<!doctype html>
@php
    $adminThemeDefaults = [
        'data-layout' => 'vertical',
        'data-topbar' => 'light',
        'data-sidebar' => 'dark',
        'data-sidebar-size' => 'lg',
        'data-sidebar-image' => 'none',
        'data-preloader' => 'disable',
        'data-theme' => 'default',
        'data-theme-colors' => 'default',
        'data-bs-theme' => 'light',
        'data-layout-width' => 'fluid',
        'data-layout-position' => 'fixed',
        'data-layout-style' => 'default',
        'data-sidebar-visibility' => 'show',
        'data-body-image' => 'none',
    ];
    $adminTheme = $adminThemeDefaults;
    if (auth()->check()) {
        try {
            $savedTheme = \App\Models\Setting::query()
                ->where('setting_group', 'theme_customization')
                ->where('user_id', auth()->id())
                ->pluck('value', 'key');
            foreach ($adminThemeDefaults as $key => $default) {
                if (! empty($savedTheme[$key])) {
                    $adminTheme[$key] = $savedTheme[$key];
                }
            }
        } catch (\Throwable $e) {
            // keep defaults if settings table unavailable
        }
    }
@endphp
<html lang="en"
    data-layout="{{ $adminTheme['data-layout'] }}"
    data-topbar="{{ $adminTheme['data-topbar'] }}"
    data-sidebar="{{ $adminTheme['data-sidebar'] }}"
    data-sidebar-size="{{ $adminTheme['data-sidebar-size'] }}"
    data-sidebar-image="{{ $adminTheme['data-sidebar-image'] }}"
    data-preloader="{{ $adminTheme['data-preloader'] }}"
    data-theme="{{ $adminTheme['data-theme'] }}"
    data-theme-colors="{{ $adminTheme['data-theme-colors'] }}"
    data-bs-theme="{{ $adminTheme['data-bs-theme'] }}"
    data-layout-width="{{ $adminTheme['data-layout-width'] }}"
    data-layout-position="{{ $adminTheme['data-layout-position'] }}"
    data-layout-style="{{ $adminTheme['data-layout-style'] }}"
    data-sidebar-visibility="{{ $adminTheme['data-sidebar-visibility'] }}"
    data-body-image="{{ $adminTheme['data-body-image'] }}">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', Session::get('company'))</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <meta name="admin-placeholder-img" content="{{ asset('assets/placeholder/placeholder.png') }}">
    <meta name="admin-avatar-placeholder" content="{{ asset('assets/images/user-dummy-img.jpg') }}">
    @auth
        @can('orders-show')
        <meta name="orders-latest-url" content="{{ route('orders.latestId') }}">
        @endcan
    @endauth
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ Session::get('favicon') }}">
    <script>
        /* Seed Velzon localStorage from saved theme so customizer + layout.js stay in sync */
        (function () {
            try {
                var theme = @json($adminTheme);
                Object.keys(theme).forEach(function (key) {
                    if (theme[key] != null && theme[key] !== '') {
                        localStorage.setItem(key, theme[key]);
                    }
                });
            } catch (e) {}
        })();
    </script>
    <!-- Layout config Js -->
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/frontend/images/logo.webp') }}">
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css" rel="stylesheet">
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Remixicon Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.min.css" rel="stylesheet" type="text/css" />
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet" type="text/css" />

    <!-- select2 -->
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Datatable -->
    <link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/buttons.dataTables.min.css') }}">

    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- custom Css-->
    <link rel="stylesheet" href="{{ asset('/assets/css/custom-style.css') }}">

    <!-- Degchi premium admin theme (sidebar + content shell; after app/custom) -->
    <link rel="stylesheet" href="{{ asset('assets/css/admin-theme.css') }}">
    <!-- Shared CRUD primitives (forms, tables, uploads) — loaded once for all backend pages -->
    <link rel="stylesheet" href="{{ asset('assets/css/admin-crud.css') }}">

    {{-- tagify --}}
    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />

    <!-- toastr css -->
    <link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">

    @auth
        @can('orders-show')
        <link rel="stylesheet" href="{{ asset('assets/css/admin-order-alert.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/admin-notifications.css') }}">
        <meta name="admin-user-id" content="{{ auth()->id() }}">
        <meta name="pusher-key" content="{{ config('broadcasting.connections.pusher.key') }}">
        <meta name="pusher-cluster" content="{{ config('broadcasting.connections.pusher.options.cluster') }}">
        <meta name="notify-read-all-url" content="{{ route('notify.readAll') }}">
        <meta name="notify-read-url-template" content="{{ url('/notifications/__ID__/read') }}">
        @endcan
    @endauth

    <!-- DateRange Picker CSS-->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/libs/date-range-picker/css/daterangepicker.css') }}" />

    {{-- jquery ui --}}
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">

    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    @stack('styles')

</head>

<body class="admin-ui">


    <!-- Begin page -->
    <div id="layout-wrapper">

        <x-header></x-header>


        <!-- removeNotificationModal -->
        <div id="removeNotificationModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            id="NotificationModalbtn-close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mt-2 text-center">
                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                            <div class="pt-2 mx-4 mt-4 fs-15 mx-sm-5">
                                <h4>Are you sure ?</h4>
                                <p class="mx-4 mb-0 text-muted">Are you sure you want to remove this Notification ?</p>
                            </div>
                        </div>
                        <div class="gap-2 mt-4 mb-2 d-flex justify-content-center">
                            <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn w-sm btn-danger" id="delete-notification">Yes, Delete
                                It!</button>
                        </div>
                    </div>

                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
        <!-- ========== App Menu ========== -->
        <div class="app-menu navbar-menu">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ Session::get('logo') ?? 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==' }}" alt="Degchi">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ Session::get('logo') ?? 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==' }}" alt="Degchi">
                    </span>
                </a>
                <a href="{{ route('dashboard') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ Session::get('logo') ?? 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==' }}" alt="Degchi">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ Session::get('logo') ?? 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==' }}" alt="Degchi">
                    </span>
                </a>
                <button type="button" class="p-0 btn btn-sm fs-20 header-item float-end btn-vertical-sm-hover"
                    id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>
            <x-main-menu></x-main-menu>

            <div class="sidebar-background"></div>
        </div>
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">
                    @yield('breadcrumb')
                </div>
                @yield('content')
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>
                                document.write(new Date().getFullYear())
                            </script> &copy; HAMKO GROUP
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Design & Develop by <a href="http://hamkoict.com.bd" target="_blank"
                                    rel="noopener noreferrer">HAMKO ICT LTD.</a>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->



    <!--start back-to-top-->
    <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>
    <!--end back-to-top-->

    @auth
        @can('orders-show')
        <div id="ddOrderAlertRoot">
            <div id="ddOrderAlertToast" class="toast align-items-center border-0" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <span style="font-size:1.3rem;">🛎️</span>
                        <strong class="ms-1">New Order!</strong>
                        <div class="mt-1 small" id="ddOrderAlertCount"></div>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>

            <button type="button" id="ddOrderAlertGearBtn" title="Order alert sound settings">
                <i class="ri-settings-3-line" id="ddOrderAlertGearIcon"></i>
            </button>

            <div id="ddOrderAlertPanel">
                <h6><i class="ri-notification-3-line me-1"></i> Order alert sound</h6>
                <label for="ddOrderAlertVolume">Volume</label>
                <input type="range" id="ddOrderAlertVolume" min="20" max="100" step="5" value="100">
                <div class="text-end"><span id="ddOrderAlertVolumeVal">100%</span></div>
                <div class="dd-order-alert-actions">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="ddOrderAlertTestBtn">Test sound</button>
                    <button type="button" class="btn btn-success btn-sm" id="ddOrderAlertToggleBtn">Mute alerts</button>
                </div>
            </div>
        </div>
        <audio id="adminNotifSound" preload="auto" src="{{ asset('sounds/notification.wav') }}"></audio>
        <div class="admin-notif-toast-wrap" id="adminNotifToastWrap" aria-live="polite"></div>
        @endcan
    @endauth
    {{-- pri loader --}}
    <x-preloader></x-preloader>

    @can('theme-customization')
        <div class="customizer-setting d-none d-md-block">
            <div class="p-2 shadow-lg btn-info rounded-pill btn btn-icon btn-lg" data-bs-toggle="offcanvas"
                data-bs-target="#theme-settings-offcanvas" aria-controls="theme-settings-offcanvas">
                <i class='mdi mdi-spin mdi-cog-outline fs-22'></i>
            </div>
        </div>
        <x-customizer></x-customizer>
    @endcan

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>

    <!-- Iconify Icons -->
    <script src="{{ asset('assets/libs/iconify-icon/iconify.min.js') }}"></script>

    <script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="{{ asset('assets/js/plugins.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
    {{-- jquery --}}

    <!-- Moment JS -->
    <script src="{{ asset('assets/libs/date-range-picker/js/moment.min.js') }}"></script>

    <!-- DateRange Picker JS Start -->
    <script type="text/javascript" src="{{ asset('assets/libs/date-range-picker/js/daterangepicker.min.js') }}"></script>

    {{-- api data handler --}}
    <script src="{{ asset('assets/js/apiDataHandler.js') }}"></script>

    {{-- datatable --}}
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/js/buttons.print.min.js') }}"></script>

    <!-- apexcharts -->
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- Dashboard init -->
    <script src="{{ asset('assets/js/pages/dashboard-crm.init.js') }}"></script>

    <!-- toastify js -->
    <script src="{{ asset('assets/libs/toastify-js.js') }}"></script>
    <!-- choices js -->
    <script src="{{ asset('assets/libs/choices.min.js') }}"></script>
    <!-- flatpicker js -->
    <script src="{{ asset('assets/libs/flatpickr.min.js') }}"></script>
    <!-- toastr js -->
    <script src="{{ asset('assets/js/toastr.min.js') }}"></script>

    <!-- Select2 js -->
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>

    {{-- Taggify --}}
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <script>
        document.querySelectorAll('.taggable').forEach(function(input) {
            new Tagify(input, {
                delimiters: ","
            });
        });
    </script>

    <script src="{{ asset('assets/js/sweetalert2@11.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
    <!-- Bootstrap Select JS -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
    <script src="{{ asset('assets/js/jquery.keyboard.extension-autocomplete.js') }}"></script>
    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
            $('#preloader').hide();

            // Only clear form validation errors inside the closed modal —
            // never wipe every span.text-danger on the page (that also
            // destroyed order-status badges in the Orders DataTable).
            $(document).on('hidden.bs.modal', function (e) {
                $(e.target).find('span.error-text, .error-text, span.invalid-feedback').html('');
            })

            $('#cacheClear').on('click', function() {
                $('#preloader').show();
                $.ajax({
                    type: "GET",
                    url: "{{ route('cache.clear') }}",
                    success: res => {
                        $('#preloader').hide();
                        Command: toastr[res.status](res.msg);
                    },
                    error: err => {
                        $('#preloader').hide();
                        Command: toastr["error"]('Something went wrong..');
                        console.log(err);
                    }
                })
            });

            document.getElementById('appLocatization').addEventListener('change', function(e) {
                e.preventDefault();
                document.getElementById('preloader').style.display = 'block';
                let locale = this.value;
                window.location.href = "/locale?lang=" + locale
            });
        });
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });



        //* SET ACTIVE  LINKS ON PAGE LOAD + smooth UX helpers
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('admin-ready');

            // Get the current URL
            const currentUrl = window.location.href;

            // Get all the nav links
            const navLinks = document.querySelectorAll('.nav-link');
            let activeLink = null;

            navLinks.forEach(link => {
                // Check if the current link href matches the current URL
                // link.href === currentUrl
                //currentUrl.includes(link.href)
                if (link.href === currentUrl) {
                    // Add active class to the current link
                    link.classList.add('active');
                    activeLink = link;

                    // Expand parents by adding 'show' class
                    let parent = link.closest('.collapse');

                    while (parent) {
                        parent.classList.add('show');
                        let parentLink = parent.previousElementSibling
                        parentLink.classList.add('active');
                        parentLink.setAttribute('aria-expanded', 'true');
                        parent = parent.parentNode.closest('.collapse');
                    }


                }

            });

            // Keep active menu item in view (smooth sidebar scroll)
            if (activeLink) {
                requestAnimationFrame(function() {
                    try {
                        activeLink.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                    } catch (e) {
                        activeLink.scrollIntoView(false);
                    }
                });
            }

            // Broken / missing images → local placeholders (no external CDN)
            const contentPlaceholder = document.querySelector('meta[name="admin-placeholder-img"]')?.content;
            const avatarPlaceholder = document.querySelector('meta[name="admin-avatar-placeholder"]')?.content;
            if (contentPlaceholder || avatarPlaceholder) {
                document.body.addEventListener('error', function(e) {
                    const img = e.target;
                    if (!(img instanceof HTMLImageElement) || img.dataset.placeholderApplied) return;
                    img.dataset.placeholderApplied = '1';
                    const isAvatar = img.classList.contains('header-profile-user')
                        || img.classList.contains('rounded-circle')
                        || img.classList.contains('name-avatar')
                        || img.classList.contains('profile-wid-img');
                    const fallback = isAvatar ? (avatarPlaceholder || contentPlaceholder) : contentPlaceholder;
                    if (fallback && img.src !== fallback) {
                        img.src = fallback;
                    }
                }, true);
            }

            // Select2: polish open state only (do not re-init — pages own their select2 setup)
            if (window.jQuery && jQuery.fn.select2) {
                jQuery(document).on('select2:open', function() {
                    const dropdown = document.querySelector('.select2-container--open .select2-dropdown');
                    if (dropdown) dropdown.classList.add('admin-select2-open');
                });
            }

            // Move modals to <body> so backdrop never blocks inputs (nested layout stacking)
            document.querySelectorAll('.modal').forEach(function(modal) {
                if (modal.parentElement !== document.body) {
                    document.body.appendChild(modal);
                }
            });

            // If a leftover backdrop/preloader is stuck, clear it when any modal hides
            if (window.jQuery) {
                jQuery(document).on('hidden.bs.modal', function() {
                    if (!document.querySelector('.modal.show')) {
                        document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
                        document.body.classList.remove('modal-open');
                        document.body.style.removeProperty('overflow');
                        document.body.style.removeProperty('padding-right');
                    }
                    var pre = document.getElementById('preloader');
                    if (pre) {
                        pre.style.display = 'none';
                    }
                });
            }

        });

        // initilize jquery ui datepicker
        $(function() {
            $(".datepicker").datepicker({
                dateFormat: "yy-mm-dd"
            });
        });
    </script>

    <!-- API Response Success/Error Message Script Start -->
    @if (Session::has('success'))
        <script>
            Toast.fire({
                icon: 'success',
                title: "{{ addslashes(Session::get('success')) }}"
            });
        </script>
    @elseif (Session::has('error'))
        <script>
            Toast.fire({
                icon: 'error',
                title: "{{ addslashes(Session::get('error')) }}"
            });
        </script>
    @elseif (Session::has('info'))
        <script>
            Toast.fire({
                icon: 'info',
                title: "{{ addslashes(Session::get('info')) }}"
            });
        </script>
    @endif
    <!-- API Response Success/Error Message Script End -->


    {{--
    - To ensure valid numeric data on input field
    - You can just use *input-number* class
    - on any input field to use this script
    --}}
    <script>
        $(document).on('keypress', 'input.input-number', function(e) {
            if (e.which !== 8 && e.which !== 0 && e.which !== 46 && (e.which < 48 || e.which > 57)) {
                return false; // prevent the keypress if it's not a number or decimal
            }

            // Ensure only one decimal point is allowed
            if (e.which === 46 && $(this).val().indexOf('.') !== -1) {
                return false; // if there's already a decimal, prevent another
            }
        })

        $(document).on('input', 'input.input-number', function(e) {
            var value = $(this).val();

            // If value contains a decimal, ensure there are only 3 digits after it
            if (value.indexOf('.') !== -1) {
                var parts = value.split('.');
                if (parts[1].length > 3) {
                    $(this).val(parts[0] + '.' + parts[1].substring(0, 3)); // truncate to 3 decimal places
                }
            }
        })

        // Enable bootstrap tooltip
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // initilize select2
        $('.select2').select2();
    </script>
    <script src="{{ asset('assets/js/auto-required.js') }}"></script>
    <script src="{{ asset('assets/frontend/dd-credit.js') }}" defer></script>
    @auth
        @can('orders-show')
        <script src="{{ asset('assets/js/admin-order-alert.js') }}" defer></script>
        <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
        <script src="{{ asset('js/admin-notifications.js') }}" defer></script>
        @endcan
    @endauth
    @yield('scripts')
    @stack('scripts')
</body>

</html>
