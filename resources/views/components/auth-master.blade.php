<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Sign In') | Degchi Dine</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <link rel="shortcut icon" href="{{ Session::get('favicon') ?? asset('assets/frontend/images/logo.webp') }}">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link href="{{ asset('assets/css/auth-degchi.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">
    @stack('auth_css')
</head>

<body class="auth-degchi-page">
    <div class="auth-page-wrapper">
        <div class="auth-page-content">
            <div class="auth-split">
                <aside class="auth-split-brand">
                    <div class="auth-split-brand-inner">
                        <a href="{{ url('/') }}" class="auth-brand-logo" aria-label="Degchi Dine home">
                            <img src="{{ asset('assets/frontend/images/logo.webp') }}" alt="Degchi Dine">
                        </a>
                        <h1 class="auth-brand-title">Degchi Dine</h1>
                        <p class="auth-brand-tagline">Secure admin access to manage your menu, orders, members, and restaurant settings.</p>
                        <ul class="auth-feature-list">
                            <li><i class="bi bi-check-circle-fill"></i> Real-time order management</li>
                            <li><i class="bi bi-check-circle-fill"></i> Menu & offer updates</li>
                            <li><i class="bi bi-check-circle-fill"></i> Member & review control</li>
                        </ul>
                        <a href="{{ url('/') }}" class="auth-back-site">
                            <i class="bi bi-arrow-left"></i> Back to website
                        </a>
                    </div>
                </aside>

                <main class="auth-split-main">
                    <div class="auth-mobile-logo">
                        <a href="{{ url('/') }}" class="auth-brand-logo" aria-label="Degchi Dine home">
                            <img src="{{ asset('assets/frontend/images/logo.webp') }}" alt="Degchi Dine">
                        </a>
                    </div>

                    @yield('content')
                </main>
            </div>
        </div>

        <footer class="footer">
            <div class="container-fluid">
                <div class="text-center">
                    <p class="mb-0 text-muted">&copy; {{ date('Y') }} Degchi Dine. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/toastr.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
        });

        document.querySelectorAll('[data-auth-toggle-password]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const input = document.querySelector(this.getAttribute('data-target'));
                const icon = this.querySelector('i');
                if (!input) return;
                const show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                icon.classList.toggle('bi-eye', !show);
                icon.classList.toggle('bi-eye-slash', show);
            });
        });
    </script>
    @stack('auth_js')
</body>

</html>
