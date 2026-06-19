@extends('layouts.dashboard')
@section('title', 'Order #' . $order->id)

@section('content')
    <x-breadcrumb></x-breadcrumb>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="mb-1 fw-bold" style="color:#0d5566;">Order #{{ $order->id }}</h4>
            <p class="text-muted mb-0 small">Placed {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
        </div>
        <a href="{{ route('orders.index') }}" class="btn btn-soft-secondary">
            <i class="ri-arrow-left-line me-1"></i> Back to Orders
        </a>
    </div>

    @include('backend.orders.partials.details')
@endsection

@push('scripts')
<script>
    function toggleOrderStatusRemarks() {
        const isCanceled = $('#order_status').val() === 'canceled';
        $('#orderStatusRemarksWrap').toggle(isCanceled);
        if (!isCanceled) {
            $('#order_status_remarks').val('');
        }
    }

    $(document).on('change', '#order_status', toggleOrderStatusRemarks);

    $(document).on('submit', '#updateOrderStatusForm', function (e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#saveOrderStatusBtn');
        const url = form.data('action-url');

        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving…');

        $.ajax({
            url,
            type: 'POST',
            data: form.serialize(),
            success: function (res) {
                submitBtn.prop('disabled', false).html('<i class="ri-save-3-line me-1"></i> Save Changes');
                if (res.success) {
                    if (typeof toastr !== 'undefined') toastr.success(res.message);
                }
            },
            error: function (xhr) {
                submitBtn.prop('disabled', false).html('<i class="ri-save-3-line me-1"></i> Save Changes');
                alert(xhr.responseJSON?.message || 'Failed to update status.');
            }
        });
    });
</script>
@endpush
