<x-auth-master>
    @section('title', 'Register')

    @section('content')
        <div class="auth-card-heading">
            <h5>Create account</h5>
            <p>Set up a new admin user for Degchi Dine.</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="auth-field">
                <label for="name" class="form-label">Full name</label>
                <div class="auth-input-wrap">
                    <i class="bi bi-person auth-input-icon" aria-hidden="true"></i>
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                        name="name" value="{{ old('name') }}" placeholder="Your full name" required
                        autocomplete="name" autofocus>
                </div>
                @error('name')
                    <p class="auth-field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="auth-field">
                <label for="email" class="form-label">Email address</label>
                <div class="auth-input-wrap">
                    <i class="bi bi-envelope auth-input-icon" aria-hidden="true"></i>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" placeholder="you@example.com" required
                        autocomplete="email">
                </div>
                @error('email')
                    <p class="auth-field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="auth-field">
                <label for="password" class="form-label">Password</label>
                <div class="auth-input-wrap auth-input-wrap--password">
                    <i class="bi bi-lock auth-input-icon" aria-hidden="true"></i>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                        name="password" placeholder="Create a strong password" required autocomplete="new-password">
                    <button type="button" class="auth-toggle-password" data-auth-toggle-password
                        data-target="#password" aria-label="Show password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                @error('password')
                    <p class="auth-field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="auth-field">
                <label for="password-confirm" class="form-label">Confirm password</label>
                <div class="auth-input-wrap auth-input-wrap--password">
                    <i class="bi bi-shield-lock auth-input-icon" aria-hidden="true"></i>
                    <input id="password-confirm" type="password" class="form-control"
                        name="password_confirmation" placeholder="Repeat your password" required
                        autocomplete="new-password">
                    <button type="button" class="auth-toggle-password" data-auth-toggle-password
                        data-target="#password-confirm" aria-label="Show password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-auth-primary">
                <i class="bi bi-person-plus"></i>
                Create Account
            </button>

            <p class="auth-secure-note">
                <i class="bi bi-shield-lock-fill"></i>
                Only authorized staff should register
            </p>
        </form>

        <div class="auth-card-footer">
            Already have an account?
            <a href="{{ route('login') }}" class="auth-link">Sign in</a>
        </div>
    @endsection
</x-auth-master>
