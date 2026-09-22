<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Invoice #{{ $order->id }} &mdash; {{ $contact['contact_restaurant_name'] ?? 'Degchi Dine' }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; background: #eef2f4; font-family: "Segoe UI", Arial, sans-serif; }
        .invoice-toolbar { position: sticky; top: 0; z-index: 20; background: #062a33; padding: 12px 20px; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .invoice-toolbar .tb-title { color: #cfe3e8; font-size: 13px; font-weight: 600; letter-spacing: 0.5px; display: flex; align-items: center; gap: 10px; }
        .invoice-toolbar .tb-title strong { color: #fff; }
        .invoice-toolbar .tb-actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .invoice-toolbar .tb-btn { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; border: 1px solid transparent; cursor: pointer; transition: background .15s ease, transform .15s ease; }
        .invoice-toolbar .tb-btn:hover { transform: translateY(-1px); }
        .invoice-toolbar .tb-btn-primary { background: #0d5a6e; color: #fff; }
        .invoice-toolbar .tb-btn-primary:hover { background: #0f6c83; }
        .invoice-toolbar .tb-btn-ghost { background: rgba(255,255,255,.08); color: #e8f2f5; border-color: rgba(255,255,255,.15); }
        .invoice-toolbar .tb-btn-ghost:hover { background: rgba(255,255,255,.16); }
        .invoice-doc { padding: 32px 16px 48px; }
        .invoice-doc .inv-shell { background: #fff; border-radius: 12px; box-shadow: 0 6px 24px rgba(6,42,51,.10); padding: 34px 40px; }
        @media (max-width: 640px) {
            .invoice-doc .inv-shell { padding: 20px 14px; }
            .invoice-toolbar { flex-direction: column; align-items: flex-start; }
        }
        @media print {
            .invoice-toolbar { display: none !important; }
            html, body { background: #fff; }
            .invoice-doc { padding: 0; }
            .invoice-doc .inv-shell { border-radius: 0; box-shadow: none; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="invoice-toolbar">
        <div class="tb-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#37d28a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12v3a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V5h18v3"/><path d="M3 5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2"/><polyline points="6 12 8 14 11 11"/></svg>
            Invoice <strong>#{{ $order->id }}</strong>
        </div>
        <div class="tb-actions">
            <button type="button" class="tb-btn tb-btn-ghost" onclick="window.print()">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Print
            </button>
            <a class="tb-btn tb-btn-primary" href="{{ route('account.invoice.download', $order->id) }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Download PDF
            </a>
            <a class="tb-btn tb-btn-ghost" href="{{ route('account.orders') }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Back to Orders
            </a>
        </div>
    </div>

    <div class="invoice-doc">
        <div class="inv-shell">
            @include('frontend.partials.invoice-sheet', ['order' => $order, 'contact' => $contact])
        </div>
    </div>
</body>
</html>