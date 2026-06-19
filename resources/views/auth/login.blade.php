<x-auth-master>
    @section('title', 'Sign In')

    @section('content')
        <div class="auth-card-heading">
            <h5>Welcome back</h5>
            <p>Sign in with your admin credentials to continue.</p>
        </div>

        <form id="login-form" method="POST" action="{{ route('login') }}">
            @csrf

            <div class="auth-field">
                <label for="email" class="form-label">Email address</label>
                <div class="auth-input-wrap">
                    <i class="bi bi-envelope auth-input-icon" aria-hidden="true"></i>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                        id="email" name="email" value="{{ old('email') }}"
                        placeholder="you@example.com" required autofocus autocomplete="username">
                </div>
                @error('email')
                    <p class="auth-field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="auth-field">
                <div class="auth-field-label-row">
                    <label for="password-input" class="form-label">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="auth-link">Forgot password?</a>
                    @endif
                </div>
                <div class="auth-input-wrap auth-input-wrap--password">
                    <i class="bi bi-lock auth-input-icon" aria-hidden="true"></i>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                        name="password" placeholder="Enter your password" id="password-input"
                        required autocomplete="current-password">
                    <button type="button" class="auth-toggle-password" data-auth-toggle-password
                        data-target="#password-input" aria-label="Show password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                @error('password')
                    <p class="auth-field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="auth-remember">
                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                    {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">Keep me signed in</label>
            </div>

            <button class="btn btn-auth-primary" type="submit">
                <i class="bi bi-box-arrow-in-right"></i>
                Sign In
            </button>

            <p class="auth-secure-note">
                <i class="bi bi-shield-lock-fill"></i>
                Your session is encrypted and secure
            </p>
        </form>

        @if (Route::has('register'))
            <div class="auth-card-footer">
                Need an account?
                <a href="{{ route('register') }}" class="auth-link">Create one</a>
            </div>
        @endif
    @endsection
</x-auth-master>
