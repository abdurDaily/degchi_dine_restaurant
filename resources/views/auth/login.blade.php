<x-auth-master>
    @section('content')
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="mt-4 card card-bg-fill">
                    <div class="p-4 card-body">
                        <div class="mt-2 text-center">
                            <h5 class="text-primary">{{ app_name() }}</h5>
                            <p class="text-muted">Sign in to continue {{ isset($company) ? 'to ' . $company : '' }}.</p>
                        </div>
                        <div class="p-2 mt-4">
                            <form id="login-form" method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" class="form-control" id="email" name="email"
                                        placeholder="Enter email">
                                    @error('email')
                                        <p class="mt-1 text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <div class="float-end">
                                        <a href="{{ route('password.request') }}" class="text-muted">Forgot
                                            password?</a>
                                    </div>
                                    <label class="form-label" for="password-input">Password</label>
                                    <div class="mb-3 position-relative auth-pass-inputgroup">
                                        <input type="password" class="form-control pe-5 password-input" name="password"
                                            placeholder="Enter password" id="password-input">
                                        <span class="text-danger"></span>
                                        <button
                                            class="top-0 btn btn-link position-absolute end-0 text-decoration-none text-muted password-addon material-shadow-none"
                                            type="button" id="password-addon"><i
                                                class="align-middle ri-eye-fill"></i></button>
                                    </div>
                                    @error('password')
                                        <p class="mt-1 text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                        {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auth-remember-check">Remember
                                        me</label>
                                </div>
                                <div class="mt-4">
                                    <button class="btn btn-success w-100" type="submit">Sign In</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- end card body -->
                </div>
                <!-- end card -->
            </div>
        </div>
    @endsection
</x-auth-master>
