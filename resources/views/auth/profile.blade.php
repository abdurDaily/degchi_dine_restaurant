<x-admin-master>
    @section('title', 'Profile')
    @section('content')
        <div class="container-fluid">
            <div class="profile-foreground position-relative mx-n4 mt-n4">
                <div class="profile-wid-bg">
                    <img src="{{ Auth::user()->profile_image }}" alt="" class="profile-wid-img" />
                </div>
            </div>
            <div class="pt-4 mb-4 mb-lg-3 pb-lg-4 profile-wrapper">
                <div class="row g-4">
                    <div class="col-auto">
                        <div class="avatar-lg">
                            <img src="{{ Auth::user()->profile_image }}" alt="user-img"
                                class="img-thumbnail rounded-circle">
                        </div>
                    </div>
                    <!--end col-->
                    {{-- @dd(Session::get('user')) --}}
                    <div class="col">
                        <div class="p-2">
                            <h3 class="mb-1 text-white">{{ Auth::user()->name }}</h3>
                            {{-- <p class="text-white text-opacity-75">{{Session::get('user')['designation'] ?? ''}}</p> --}}
                            <div class="gap-1 hstack text-white-50">
                                {{-- <div class="me-2"><i class="text-white text-opacity-75 align-middle ri-map-pin-user-line me-1 fs-16"></i>California, United States</div>
                            <div>
                                <i class="text-white text-opacity-75 align-middle ri-building-line me-1 fs-16"></i>Themesbrand
                            </div> --}}
                            </div>
                        </div>
                    </div>
                    <!--end col-->
                    <div class="order-last col-12 col-lg-auto order-lg-0">
                        <div class="text-center row text text-white-50">
                            <div class="col-12">
                                <div class="p-2">
                                    <h4 class="mb-1 text-white"><span id="clockDisplay"></span></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end col-->

                </div>
                <!--end row-->
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div>
                        <div class="d-flex profile-wrapper">
                            <!-- Nav tabs -->
                            <ul class="gap-2 nav nav-pills animation-nav profile-nav gap-lg-3 flex-grow-1" id="profileTab"
                                role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link fs-14 active" data-bs-toggle="tab" href="#personalInfo"
                                        data-info="personalInfo" role="tab">
                                        <i class="ri-airplay-fill d-inline-block d-md-none"></i> <span
                                            class="d-none d-md-inline-block">Personal Information</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fs-14" data-bs-toggle="tab" href="#passwordChange"
                                        data-info="passwordChange" role="tab">
                                        <i class="ri-list-unordered d-inline-block d-md-none"></i> <span
                                            class="d-none d-md-inline-block">Change Password</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- Tab panes -->
                        <div class="pt-4 tab-content text-muted">
                            @include('auth.includes.personal_info')
                            @include('auth.includes.password_change')
                        </div>
                        <!--end tab-content-->
                    </div>
                </div>
                <!--end col-->
            </div>
            <!--end row-->

        </div><!-- container-fluid -->
    @endsection
    @push('scripts')
        <script>
            $(document).ready(function() {
                function showTime() {
                    let date = new Date();
                    let hour = date.getHours();
                    let minute = date.getMinutes();
                    let second = date.getSeconds();
                    let session = "AM";

                    if (hour == 0) {
                        hour = 12;
                    }

                    if (hour > 12) {
                        hour = hour - 12;
                        session = "PM";
                    }

                    hour = (hour < 10) ? "0" + hour : hour;
                    minute = (minute < 10) ? "0" + minute : minute;
                    second = (second < 10) ? "0" + second : second;

                    let time = hour + ":" + minute + ":" + second + " " + session;
                    $("#clockDisplay").text(time);

                    setTimeout(showTime, 1000);
                }
                showTime();

                // update profile
                $('#profile-update-form').submit(function(e) {
                    e.preventDefault(); // Prevent the default form submission


                    const url = '{{ route('profile.update') }}';

                    const formElement = document.querySelector('#profile-update-form');
                    const formData = new FormData(formElement);
                    formData.append('_method', 'PUT');

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        processData: false, // Prevent jQuery from processing the data
                        contentType: false, // Prevent jQuery from setting content-type header
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: (data) => {
                            if (data.success) {
                                Toast.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: data.message,
                                });

                                setTimeout(() => {
                                    location.reload();
                                }, 1000);
                            } else {
                                // enable submit button
                                $('.form-submit-btn').prop('disabled', false);

                                Command: toastr["error"](
                                    'An unexpected issue occurred. Please try again.');
                            }
                        },
                        error: (xhr) => {
                            // enable submit button
                            $('.form-submit-btn').prop('disabled', false);

                            if (xhr.status === 422) {
                                const errData = xhr.responseJSON;
                                Object.values(errData.errors).forEach(errorArray => {
                                    errorArray.forEach(message => {
                                        Command: toastr["error"](message);
                                    });
                                });
                            } else {
                                console.error('Error:', xhr.responseJSON?.message || xhr
                                    .statusText);
                                Command: toastr["error"](xhr.responseJSON?.message ||
                                    'An unexpected error occurred. Please try again.');
                            }
                        }
                    });


                });

                // change password
                $('#password-update-form').submit(function(e) {
                    e.preventDefault(); // Prevent the default form submission


                    const url = '{{ route('password.update') }}';

                    const formElement = document.querySelector('#password-update-form');
                    const formData = new FormData(formElement);
                    formData.append('_method', 'PUT');

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        processData: false, // Prevent jQuery from processing the data
                        contentType: false, // Prevent jQuery from setting content-type header
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: (data) => {
                            if (data.success) {
                                Toast.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: data.message,
                                });

                                setTimeout(() => {
                                    location.reload();
                                }, 1000);
                            } else {
                                // enable submit button
                                $('.form-submit-btn').prop('disabled', false);

                                Command: toastr["error"](
                                    'An unexpected issue occurred. Please try again.');
                            }
                        },
                        error: (xhr) => {
                            // enable submit button
                            $('.form-submit-btn').prop('disabled', false);

                            if (xhr.status === 422) {
                                const errData = xhr.responseJSON;
                                Object.values(errData.errors).forEach(errorArray => {
                                    errorArray.forEach(message => {
                                        Command: toastr["error"](message);
                                    });
                                });
                            } else {
                                console.error('Error:', xhr.responseJSON?.message || xhr
                                    .statusText);
                                Command: toastr["error"](xhr.responseJSON?.message ||
                                    'An unexpected error occurred. Please try again.');
                            }
                        }
                    });


                });
            })
        </script>
    @endpush
</x-admin-master>
