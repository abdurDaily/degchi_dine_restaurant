<x-auth-master>
    @section('title', 'Awaiting Approval')

    @section('content')
        <div class="auth-card-heading text-center">
            <div class="mb-3">
                <span class="auth-pending-icon" aria-hidden="true">
                    <i class="bi bi-hourglass-split"></i>
                </span>
            </div>
            <h5>Awaiting admin approval</h5>
            <p>Your account has been created successfully, but an administrator must approve it before you can access the panel.</p>
        </div>

        <div class="auth-pending-box">
            <ul class="auth-pending-steps">
                <li>
                    <i class="bi bi-check-circle-fill text-success"></i>
                    <span>Registration completed</span>
                </li>
                @if (!Auth::user()->hasVerifiedEmail())
                    <li>
                        <i class="bi bi-envelope-exclamation text-warning"></i>
                        <span>Please verify your email address</span>
                    </li>
                @else
                    <li>
                        <i class="bi bi-check-circle-fill text-success"></i>
                        <span>Email verified</span>
                    </li>
                @endif
                <li>
                    <i class="bi bi-person-check text-primary"></i>
                    <span>Waiting for admin approval</span>
                </li>
            </ul>

            <p class="auth-pending-note">
                You will get full access once a Super Admin activates your account and assigns the required permissions.
                Please check back later or contact your administrator.
            </p>

            @if (!Auth::user()->hasVerifiedEmail())
                <form method="POST" action="{{ route('verification.resend') }}" class="text-center mb-3">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary btn-sm">
                        Resend verification email
                    </button>
                </form>
            @endif

            <div class="d-grid gap-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-auth-primary">Sign out</button>
                </form>
                <a href="{{ url('/') }}" class="btn btn-link text-muted">Back to website</a>
            </div>
        </div>
    @endsection

    @push('auth_css')
        <style>
            .auth-pending-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 72px;
                height: 72px;
                border-radius: 50%;
                background: rgba(17, 107, 131, 0.12);
                color: #116b83;
                font-size: 2rem;
            }

            .auth-pending-box {
                background: #f8fafb;
                border: 1px solid rgba(17, 107, 131, 0.12);
                border-radius: 14px;
                padding: 1.25rem;
            }

            .auth-pending-steps {
                list-style: none;
                padding: 0;
                margin: 0 0 1rem;
            }

            .auth-pending-steps li {
                display: flex;
                align-items: center;
                gap: 0.65rem;
                padding: 0.55rem 0;
                font-size: 0.92rem;
                color: #374151;
            }

            .auth-pending-note {
                font-size: 0.86rem;
                color: #6b7280;
                line-height: 1.55;
                margin-bottom: 1rem;
            }
        </style>
    @endpush
</x-auth-master>
