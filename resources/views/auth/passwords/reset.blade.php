<x-auth-master>
    @section('title', 'Set New Password')

    @section('content')
        <div class="auth-card-heading">
            <h5>Set new password</h5>
            <p>Choose a strong password for your admin account.</p>
        </div>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="auth-field">
                <label for="email" class="form-label">Email address</label>
                <div class="auth-input-wrap">
                    <i class="bi bi-envelope auth-input-icon" aria-hidden="true"></i>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus
                        placeholder="you@example.com">
                </div>
                @error('email')
                    <p class="auth-field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="auth-field">
                <label for="password" class="form-label">New password</label>
                <div class="auth-input-wrap auth-input-wrap--password">
                    <i class="bi bi-lock auth-input-icon" aria-hidden="true"></i>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                        name="password" required autocomplete="new-password" placeholder="Create a new password">
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
                        name="password_confirmation" required autocomplete="new-password"
                        placeholder="Repeat your new password">
                    <button type="button" class="auth-toggle-password" data-auth-toggle-password
                        data-target="#password-confirm" aria-label="Show password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-auth-primary">
                <i class="bi bi-shield-check"></i>
                Reset Password
            </button>
        </form>

        <div class="auth-card-footer">
            <a href="{{ route('login') }}" class="auth-link">Back to sign in</a>
        </div>
    @endsection
</x-auth-master>
