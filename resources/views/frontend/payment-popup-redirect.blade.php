<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Payment Status</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap">
    <style>
        body {
            font-family: Arial, "Hind Siliguri", sans-serif;
            text-align: center;
            padding: 3rem;
            background: #f8f9fa;
            color: #333;
        }

        .card {
            display: inline-block;
            padding: 2rem;
            border-radius: 0.75rem;
            background: #fff;
            box-shadow: 0 0.75rem 2rem rgba(0, 0, 0, 0.08);
        }

        .status {
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .message {
            margin-bottom: 1.5rem;
        }

        a {
            color: #0d6efd;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="status">Processing payment result...</div>
        <div class="message">Please wait while we finalize your order.</div>
        <div id="fallback">
            If the page does not redirect automatically, <a id="fallbackLink" href="#">click here</a>.
        </div>
    </div>

    <script>
        (function() {
            const status = @json($status);
            const message = @json($message);
            const clearCart = @json($clearCart);
            const checkoutUrl = new URL(@json(route('frontend.checkout')));
            checkoutUrl.searchParams.set('payment_result', status);
            checkoutUrl.searchParams.set('payment_message', message);
            if (clearCart) {
                checkoutUrl.searchParams.set('clear_cart', '1');
            }

            const target = checkoutUrl.toString();
            document.getElementById('fallbackLink').setAttribute('href', target);

            if (window.opener && !window.opener.closed) {
                window.opener.location = target;
                window.close();
                return;
            }

            window.location = target;
        })();
    </script>
</body>

</html>
