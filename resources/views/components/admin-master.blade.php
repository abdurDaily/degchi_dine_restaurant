<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', Session::get('company'))</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ Session::get('favicon') }}">

    <!-- Layout config Js -->
    <script src="{{ asset('assets/js/layout.js') }}"></script>

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

    {{-- tagify --}}
    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />

    <!-- toastr css -->
    <link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">

    <!-- DateRange Picker CSS-->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/libs/date-range-picker/css/daterangepicker.css') }}" />

    {{-- jquery ui --}}
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">

    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    @stack('styles')

</head>

<body>


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
                <!-- Dark Logo-->
                <a href="{{ route('dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ Session::get('logo') ?? 'assets/null.jpg' }}" alt=""
                            style="max-width: 130px">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ Session::get('logo') ?? 'assets/null.jpg' }}" alt=""
                            style="max-width: 130px">
                    </span>
                </a>
                <!-- Light Logo-->
                <a href="{{ route('dashboard') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ Session::get('logo') ?? 'assets/null.jpg' }}" alt=""
                            style="max-width: 130px">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ Session::get('logo') ?? 'assets/null.jpg' }}" alt=""
                            style="max-width: 130px">
                    </span>
                </a>
                <button type="button" class="p-0 btn btn-sm fs-20 header-item float-end btn-vertical-sm-hover"
                    id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <!-- Dark Logo-->
                <a href="{{ route('dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ Session::get('logo') ?? 'assets/null.jpg' }}" alt=""
                            style="max-width: 130px">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ Session::get('logo') ?? 'assets/null.jpg' }}" alt=""
                            style="max-width: 130px">
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

            $(document).on('hidden.bs.modal', function() {
                $('span.text-danger').html('')
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



        //* SET ACTIVE  LINKS ON PAGE LOAD
        document.addEventListener('DOMContentLoaded', function() {
            // Get the current URL
            const currentUrl = window.location.href;

            // Get all the nav links
            const navLinks = document.querySelectorAll('.nav-link');

            navLinks.forEach(link => {
                // Check if the current link href matches the current URL
                // link.href === currentUrl
                //currentUrl.includes(link.href)
                if (link.href === currentUrl) {
                    // Add active class to the current link
                    link.classList.add('active');

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

        });

        // initilize jquery ui datepicker
        $(function() {
            $(".datepicker").datepicker({
                dateFormat: "yy-mm-dd"
            });
        });
    </script>

    <!-- API Response Success/Error Message Script Start -->
    <script>
        @if (Session::has('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ Session::get('success') }}"
            });
        @elseif (Session::has('error'))
            Toast.fire({
                icon: 'error',
                title: "{{ Session::get('error') }}"
            });
        @elseif (Session::has('info'))
            Toast.fire({
                icon: 'info',
                title: "{{ Session::get('info') }}"
            });
        @endif
    </script>
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
    @stack('scripts')
</body>

</html>
