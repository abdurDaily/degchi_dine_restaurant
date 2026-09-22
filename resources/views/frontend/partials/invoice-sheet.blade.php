{{--
    Reusable standalone invoice sheet.
    Renders the full invoice document (HTML tables + scoped CSS).
    Used by:
      - PDF generation  -> PDF::loadView('frontend.partials.invoice-sheet', ...)
      - On-screen page  -> @include('frontend.partials.invoice-sheet')

    Expects: $order, optional $contact (key/value map from contact_section settings).
--}}
<style>
    .invoice-sheet { max-width: 760px; margin: 0 auto; font-family: "DejaVu Sans", "Segoe UI", Arial, sans-serif; color: #1c2b33; font-size: 12px; line-height: 1.5; }
    .invoice-sheet * { box-sizing: border-box; }
    .invoice-sheet .inv-top { width: 100%; border-bottom: 3px solid #0d5a6e; padding-bottom: 16px; margin-bottom: 18px; }
    .invoice-sheet .inv-brand { font-size: 24px; font-weight: 800; color: #0d5a6e; letter-spacing: 1px; }
    .invoice-sheet .inv-brand small { display: block; font-size: 10px; font-weight: 600; color: #5b7a86; letter-spacing: 3px; margin-top: 3px; text-transform: uppercase; }
    .invoice-sheet .inv-invoice-label { font-size: 30px; font-weight: 800; color: #062a33; text-align: right; letter-spacing: 6px; }
    .invoice-sheet .inv-meta { width: 100%; margin-bottom: 18px; }
    .invoice-sheet .inv-meta td { vertical-align: top; }
    .invoice-sheet .inv-card { border: 1px solid #dfe6e9; border-radius: 6px; padding: 12px 14px; background: #fdfefe; }
    .invoice-sheet .inv-card h4 { margin: 0 0 6px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: #0d5a6e; }
    .invoice-sheet .inv-card p { margin: 0; color: #2c4050; }
    .invoice-sheet .inv-kv { width: 100%; }
    .invoice-sheet .inv-kv td { padding: 2px 0; }
    .invoice-sheet .inv-kv .k { color: #5b7a86; font-size: 11px; white-space: nowrap; padding-right: 10px; vertical-align: top; }
    .invoice-sheet .inv-kv .v { font-weight: 600; color: #1c2b33; }
    .invoice-sheet .inv-status { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; }
    .invoice-sheet .inv-status.ok { background: #e5f6ec; color: #188a4b; }
    .invoice-sheet .inv-status.warn { background: #fff4dd; color: #a06a10; }
    .invoice-sheet .inv-status.bad { background: #fde8e8; color: #c0392b; }
    .invoice-sheet .inv-status.info { background: #e4f2f6; color: #0d5a6e; }
    .invoice-sheet table.inv-items { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    .invoice-sheet table.inv-items th { background: #0d5a6e; color: #fff; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; padding: 9px 12px; text-align: left; }
    .invoice-sheet table.inv-items th.num, .invoice-sheet table.inv-items td.num { text-align: right; }
    .invoice-sheet table.inv-items td { padding: 9px 12px; border-bottom: 1px solid #e8edef; font-size: 12px; vertical-align: top; }
    .invoice-sheet table.inv-items tr:nth-child(even) td { background: #f8fafb; }
    .invoice-sheet table.inv-items .item-name { font-weight: 600; color: #1c2b33; }
    .invoice-sheet table.inv-items .item-note { display: block; font-size: 10px; color: #7a8f9a; margin-top: 2px; }
    .invoice-sheet .inv-summary { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    .invoice-sheet .inv-summary td { padding: 5px 12px; border: 1px solid #e8edef; font-size: 12px; }
    .invoice-sheet .inv-summary .lbl { color: #5b7a86; background: #f8fafb; }
    .invoice-sheet .inv-summary .amt { text-align: right; font-weight: 600; color: #1c2b33; }
    .invoice-sheet .inv-summary tr.inv-total td { background: #0d5a6e; color: #fff; font-size: 14px; font-weight: 800; }
    .invoice-sheet .inv-summary tr.inv-total .lbl { color: #cfe3e8; background: #0d5a6e; }
    .invoice-sheet .inv-summary tr.inv-total .amt { color: #fff; }
    .invoice-sheet .inv-notes { border: 1px solid #f2d9b3; background: #fffaf1; border-radius: 6px; padding: 10px 14px; margin-bottom: 16px; }
    .invoice-sheet .inv-notes h4 { margin: 0 0 4px; font-size: 10px; text-transform: uppercase; letter-spacing: 1.5px; color: #a06a10; }
    .invoice-sheet .inv-notes p { margin: 0; color: #6b5d41; font-size: 11px; }
    .invoice-sheet .inv-footer { border-top: 1px solid #dfe6e9; padding-top: 12px; text-align: center; color: #7a8f9a; font-size: 10px; }
</style>

<div class="invoice-sheet">

    <table class="inv-top" cellpadding="0" cellspacing="0">
        <tr>
            <td width="55%">
                <div class="inv-brand">
                    {{ $contact['contact_restaurant_name'] ?? 'Degchi Dine' }}
                    @if (!empty($contact['contact_address']) || !empty($contact['contact_phone']))
                        <small>
                            {{ trim(($contact['contact_address'] ?? '') . '  ' . ($contact['contact_phone'] ?? '')) }}
                        </small>
                    @endif
                </div>
            </td>
            <td width="45%">
                <div class="inv-invoice-label">INVOICE</div>
            </td>
        </tr>
    </table>

    <table class="inv-meta" cellpadding="0" cellspacing="0">
        <tr>
            <td width="50%">
                <div class="inv-card">
                    <h4>Billed To</h4>
                    <p>
                        <strong>{{ $order->customer_name }}</strong>
                        @if (!empty($order->customer_phone))
                            <br>Tel: {{ $order->customer_phone }}
                        @endif
                        @if (!empty($order->customer_address))
                            <br>{{ $order->customer_address }}
                        @endif
                    </p>
                </div>
            </td>
            <td width="4%"></td>
            <td width="46%">
                <div class="inv-card">
                    <h4>Order Details</h4>
                    <table class="inv-kv" cellpadding="0" cellspacing="0">
                        <tr><td class="k">Order No</td><td class="v">#{{ $order->id }}</td></tr>
                        <tr><td class="k">Date</td><td class="v">{{ $order->created_at->format('d M Y, h:i A') }}</td></tr>
                        <tr>
                            <td class="k">Status</td>
                            <td class="v">
                                @php
                                    $statusLabel = ucfirst($order->status);
                                    $statusClass = $order->status === 'canceled' ? 'bad'
                                        : ($order->status === 'confirmed' ? 'info'
                                        : ($order->status === 'completed' ? 'ok' : 'warn'));
                                @endphp
                                <span class="inv-status {{ $statusClass }}">{{ $statusLabel }}</span>
                            </td>
                        </tr>
                        <tr><td class="k">Payment</td><td class="v">{{ strtoupper($order->payment_method ?? 'N/A') }}</td></tr>
                        @if ($order->transaction_id)
                            <tr><td class="k">Trx ID</td><td class="v">{{ $order->transaction_id }}</td></tr>
                        @endif
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <table class="inv-items" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width:28px;">#</th>
                <th>Item</th>
                <th class="num" style="width:50px;">Qty</th>
                <th class="num" style="width:110px;">Unit Price</th>
                <th class="num" style="width:110px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($order->normalizedItems() as $index => $item)
                @php
                    $qty    = $item['quantity'] ?? 1;
                    $price  = (float) ($item['price'] ?? 0);
                    $title  = $item['title'] ?? $item['name'] ?? 'Item';
                    $note   = $item['note'] ?? '';
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <span class="item-name">{{ $title }}</span>
                        @if ($note)
                            <span class="item-note">{{ $note }}</span>
                        @endif
                    </td>
                    <td class="num">{{ $qty }}</td>
                    <td class="num">TK {{ number_format($price, 2) }}</td>
                    <td class="num">TK {{ number_format($price * $qty, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No items recorded for this order.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="inv-summary" cellpadding="0" cellspacing="0">
        <tr>
            <td class="lbl">Subtotal</td>
            <td class="amt" width="160" style="width:160px;">TK {{ number_format($order->total_amount, 2) }}</td>
        </tr>
        @if ((float) $order->delivery_charge > 0)
            <tr>
                <td class="lbl">Delivery Charge</td>
                <td class="amt" width="160" style="width:160px;">TK {{ number_format($order->delivery_charge, 2) }}</td>
            </tr>
        @endif
        @if ((float) $order->coupon_discount > 0)
            <tr>
                <td class="lbl">Coupon Discount ({{ $order->coupon_code ?: 'Coupon' }})</td>
                <td class="amt" width="160" style="width:160px;">- TK {{ number_format($order->coupon_discount, 2) }}</td>
            </tr>
        @endif
        @if ((float) $order->discount_amount > 0)
            <tr>
                <td class="lbl">Discount</td>
                <td class="amt" width="160" style="width:160px;">- TK {{ number_format($order->discount_amount, 2) }}</td>
            </tr>
        @endif
        <tr class="inv-total">
            <td class="lbl">Total</td>
            <td class="amt" width="160" style="width:160px;">TK {{ number_format($order->final_amount, 2) }}</td>
        </tr>
    </table>

    @if (!empty($order->status_remarks))
        <div class="inv-notes">
            <h4>Remarks</h4>
            <p>{{ $order->status_remarks }}</p>
        </div>
    @endif

    <div class="inv-footer">
        @php
            $brandName = $contact['contact_restaurant_name'] ?? '';
            $footerBits = collect([
                $brandName,
                !empty($contact['contact_address']) ? $contact['contact_address'] : null,
                !empty($contact['contact_phone']) ? 'Tel: ' . $contact['contact_phone'] : null,
            ])->filter()->implode(' &middot; ');
            $thanks = 'Thank you for dining with us.';
        @endphp
        {{ $thanks }} @if ($footerBits) &copy; {{ date('Y') }} {{ $footerBits }} @endif
    </div>

</div>