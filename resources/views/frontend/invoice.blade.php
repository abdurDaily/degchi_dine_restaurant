@extends('frontend.layout')
@section('frontend_content')
<div class="container px-4 px-lg-5 my-5">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Invoice</h3>
                <div>
                    <a href="{{ route('account.invoice.download', $order->id) }}" class="btn btn-sm btn-primary">Download PDF</a>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h6>Billing To</h6>
                    <p>{{ $order->customer_name }}<br>{{ $order->customer_phone }}<br>{{ $order->customer_address }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h6>Order</h6>
                    <p>Order #: {{ $order->id }}<br>Date: {{ $order->created_at->format('d M Y H:i') }}<br>Status: {{ ucfirst($order->status) }}</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Unit</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item['title'] ?? 'Item' }}</td>
                            <td class="text-end">{{ $item['quantity'] ?? 1 }}</td>
                            <td class="text-end">৳{{ number_format($item['price'] ?? 0,2) }}</td>
                            <td class="text-end">৳{{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1),2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end">Subtotal</td>
                            <td class="text-end">৳{{ number_format($order->total_amount,2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end">Discount</td>
                            <td class="text-end">৳{{ number_format($order->discount_amount,2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total</strong></td>
                            <td class="text-end"><strong>৳{{ number_format($order->final_amount,2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
