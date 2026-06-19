<x-auth-master>
    @section('title', 'Reset Password')

    @section('content')
        <div class="auth-card-heading">
            <h5>Reset password</h5>
            <p>Enter your email and we'll send you a reset link.</p>
        </div>

        @if (session('status'))
            <div class="auth-alert auth-alert--success" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="auth-field">
                <label for="email" class="form-label">Email address</label>
                <div class="auth-input-wrap">
                    <i class="bi bi-envelope auth-input-icon" aria-hidden="true"></i>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                        id="email" name="email" value="{{ old('email') }}" required
                        autocomplete="email" autofocus placeholder="you@example.com">
                </div>
                @error('email')
                    <p class="auth-field-error">{{ $message }}</p>
                @enderror
            </div>

            <button class="btn btn-auth-primary" type="submit">
                <i class="bi bi-send"></i>
                Send Reset Link
            </button>
        </form>

        <div class="auth-card-footer">
            Remember your password?
            <a href="{{ route('login') }}" class="auth-link">Back to sign in</a>
        </div>
    @endsection
</x-auth-master>
