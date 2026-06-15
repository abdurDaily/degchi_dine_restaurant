<x-admin-master>
    @section('title', __('Add user'))

    @section('content')
        <x-breadcrumb />
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h4>{{ __('Add user') }}</h4>
                        </div>
                        <div class="card-body">
                            <form id="usersForm" action="javascript:void(0)" method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="mt-4 col-md-4">
                                        <div class="form-group">
                                            <label for="name">{{ __('Name') }}</label>
                                            <input id="name" type="text" name="name" required
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="mt-4 col-md-4">
                                        <div class="form-group">
                                            <label for="email">{{ __('Email') }}</label>
                                            <input id="email" type="text" name="email" required
                                                class="form-control">
                                        </div>
                                    </div>

                                    <div class="mt-4 col-md-4">
                                        <div class="form-group">
                                            <label>{{ trans('Image') }}</label>
                                            <input type="file" name="image" class="form-control"
                                                accept=".jpg, .png, image/jpeg, image/png">
                                        </div>
                                    </div>

                                    <div class="mt-4 col-md-4">
                                        <div class="form-group">
                                            <label for="password">{{ __('Password') }}</label>
                                            <input id="password" type="password" name="password" required
                                                class="form-control ">
                                        </div>
                                    </div>

                                    <div class="mt-4 col-md-4">
                                        <div class="form-group">
                                            <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                                            <input id="password_confirmation" type="password" name="password_confirmation"
                                                required class="form-control">
                                        </div>
                                    </div>

                                    @if (auth()->user()->hasRole('Super Admin'))
                                        <div class="mt-4 col-md-4">
                                            <div class="form-group">
                                                <label for="select-roles">{{ __('Roles') }}</label>
                                                <select name="roles[]" class="form-control select2" id="select-roles"
                                                    multiple>
                                                    @foreach ($roles as $role)
                                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    {{-- checkbox --}}
                                    <div class="mt-4 col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value=""
                                                id="showPassword">
                                            <label class="form-check-label" for="showPassword">
                                                Show Password
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mt-4 form-group">
                                            <input type="submit" value="{{ __('Submit') }}"
                                                class="btn btn-primary form-submit-btn">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            $(document).ready(function() {

                $('#usersForm').submit(function(e) {
                    e.preventDefault(); // Prevent the default form submission


                    const url = '{{ route('users.store') }}';

                    const formElement = document.querySelector('#usersForm');
                    const formData = new FormData(formElement);
                    formData.append('_method', 'POST');

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
                                    // redirect to users index page
                                    window.location.href = '{{ route('users.index') }}';
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

            });

            // on showPassword change, show/hide password
            $('#showPassword').on('change', function() {
                var x = document.getElementById("password");
                var y = document.getElementById("password_confirmation");
                var check = document.getElementById("showPassword");
                if (check.checked == true) {
                    x.type = "text";
                    y.type = "text";
                } else {
                    x.type = "password";
                    y.type = "password";
                }
            });
        </script>
    @endpush
</x-admin-master>
