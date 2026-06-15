@extends('frontend.layout')
@section('frontend_content')
<div class="container px-4 px-lg-5 my-5">
    <h2>Your Orders</h2>
    <div class="list-group mt-4">
        @forelse($orders as $order)
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong>Order #{{ $order->id }}</strong>
                    <div class="text-muted">Placed: {{ $order->created_at->format('d M Y H:i') }}</div>
                    <div class="small">Total: ৳{{ number_format($order->final_amount,2) }} — Status: {{ ucfirst($order->status) }}</div>
                </div>
                <div class="btn-group">
                    <a href="{{ route('account.invoice.show', $order->id) }}" class="btn btn-sm btn-outline-primary">View Invoice</a>
                    <a href="{{ route('account.invoice.download', $order->id) }}" class="btn btn-sm btn-primary">Download PDF</a>
                </div>
            </div>
        @empty
            <div class="list-group-item">You have no orders yet.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
</div>
@endsection
