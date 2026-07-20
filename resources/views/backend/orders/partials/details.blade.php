<div class="order-details-panel row">
    <div class="col-md-8">
        <div class="card order-details-card mb-3">
            <div class="card-header order-details-card-header">
                <h5 class="card-title mb-0"><i class="ri-shopping-bag-3-line me-2"></i>Order Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="order-meta-box">
                            <span class="order-meta-label">Order ID</span>
                            <span class="order-meta-value">#{{ $order->id }}</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="order-meta-box">
                            <span class="order-meta-label">Date</span>
                            <span class="order-meta-value">{{ $order->created_at->format('M d, Y · H:i') }}</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="order-meta-box">
                            <span class="order-meta-label">Payment Method</span>
                            <span class="order-meta-value text-uppercase">{{ $order->payment_method }}</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="order-meta-box">
                            <span class="order-meta-label">Payment Status</span>
                            <span class="order-meta-value text-capitalize">{{ $order->payment_status ?? 'unpaid' }}</span>
                        </div>
                    </div>
                    @if($order->transaction_id)
                        <div class="col-12">
                            <div class="order-meta-box">
                                <span class="order-meta-label">Transaction ID</span>
                                <span class="order-meta-value">{{ $order->transaction_id }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card order-details-card">
            <div class="card-header order-details-card-header">
                <h5 class="card-title mb-0"><i class="ri-list-check-2 me-2"></i>Ordered Items</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover order-items-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="text-center">Price</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end pe-4">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $orderItems = $order->normalizedItems();
                            @endphp
                            @forelse($orderItems as $item)
                                @php
                                    $title = $item['title'] ?? $item['name'] ?? 'Item';
                                    $price = (float) ($item['price'] ?? 0);
                                    $qty = max(1, (int) ($item['quantity'] ?? $item['qty'] ?? 1));
                                    $image = $item['image'] ?? null;
                                    $note = $item['note'] ?? null;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if(!empty($image))
                                                <img src="{{ str_starts_with($image, 'http') ? $image : asset($image) }}" alt="{{ $title }}" class="order-item-thumb me-2">
                                            @else
                                                <div class="order-item-thumb order-item-thumb-placeholder me-2">
                                                    <i class="ri-restaurant-line"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-semibold text-dark">{{ $title }}</div>
                                                @if(!empty($note))
                                                    <small class="text-muted">{{ $note }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">৳{{ number_format($price, 2) }}</td>
                                    <td class="text-center fw-bold">{{ $qty }}</td>
                                    <td class="text-end fw-bold pe-4">৳{{ number_format($price * $qty, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No items stored for this order.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card order-details-card order-actions-card mb-3">
            <div class="card-header order-actions-header">
                <h5 class="card-title mb-0"><i class="ri-settings-3-line me-2"></i>Actions &amp; Status</h5>
            </div>
            <div class="card-body">
                <form id="updateOrderStatusForm" data-action-url="{{ route('orders.updateStatus', $order->id) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="order_status" class="form-label fw-semibold">Order Status</label>
                        <select name="status" id="order_status" class="form-select">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="canceled" {{ $order->status === 'canceled' ? 'selected' : '' }}>Canceled</option>
                        </select>
                    </div>
                    <div class="mb-3" id="orderStatusRemarksWrap" style="{{ $order->status === 'canceled' ? '' : 'display:none;' }}">
                        <label for="order_status_remarks" class="form-label fw-semibold">Cancellation Remarks</label>
                        <textarea name="status_remarks" id="order_status_remarks" class="form-control" rows="3" placeholder="Reason shown to the customer (e.g. item unavailable, wrong address…)">{{ old('status_remarks', $order->status_remarks) }}</textarea>
                        <small class="text-muted">Visible on the customer order tracking page when the order is canceled.</small>
                    </div>
                    <div class="mb-3">
                        <label for="order_payment_status" class="form-label fw-semibold">Payment Status</label>
                        <select name="payment_status" id="order_payment_status" class="form-select">
                            <option value="unpaid" {{ $order->payment_status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="cancelled" {{ $order->payment_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" id="saveOrderStatusBtn" class="btn order-save-btn w-100 fw-bold">
                        <i class="ri-save-3-line me-1"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>

        <div class="card order-details-card mb-3">
            <div class="card-header order-details-card-header">
                <h5 class="card-title mb-0"><i class="ri-user-3-line me-2"></i>Customer Info</h5>
            </div>
            <div class="card-body order-info-list">
                <div><strong>Name</strong><span>{{ $order->customer_name }}</span></div>
                <div><strong>Phone</strong><span>{{ $order->customer_phone }}</span></div>
                <div><strong>Address</strong><span>{{ $order->customer_address }}</span></div>
            </div>
        </div>

        @if($order->member)
            <div class="card order-details-card order-member-card mb-3">
                <div class="card-header order-member-header">
                    <h5 class="card-title mb-0"><i class="ri-vip-card-line me-2"></i>Card Details</h5>
                </div>
                <div class="card-body order-info-list">
                    <div><strong>Member</strong><span>{{ $order->member->name }}</span></div>
                    <div><strong>Card No.</strong><span class="badge bg-light text-dark border">{{ $order->unique_card_number }}</span></div>
                    <div>
                        <strong>Type</strong>
                        <span class="badge {{ $order->member->type === 'golden' ? 'bg-warning text-dark' : 'bg-primary' }} text-capitalize">{{ $order->member->type }}</span>
                    </div>
                    <div><strong>Total Purchase</strong><span>৳{{ number_format($order->member->total_purchase, 2) }}</span></div>
                    @if($order->member->expires_at)
                        <div>
                            <strong>Expires</strong>
                            <span class="{{ $order->member->expires_at < now() ? 'text-danger fw-bold' : '' }}">{{ $order->member->expires_at->format('Y-m-d') }}</span>
                        </div>
                    @endif
                    @if($order->member->is_student)
                        <div class="mt-2 p-2 rounded border">
                            <span class="badge bg-success mb-2">Student Member</span>
                            @if($order->member->student_card_path)
                                <a href="{{ asset('storage/' . $order->member->student_card_path) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                                    <i class="ri-image-line me-1"></i> View Student Card
                                </a>
                            @else
                                <small class="text-danger">Student card file missing.</small>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <div class="card order-details-card order-summary-card">
            <div class="card-header order-details-card-header">
                <h5 class="card-title mb-0"><i class="ri-bill-line me-2"></i>Order Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal</span>
                    <span>৳{{ number_format($order->total_amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 text-success">
                    <span>Discount</span>
                    <span>- ৳{{ number_format($order->discount_amount, 2) }}</span>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between fw-bold fs-5 order-total-row">
                    <span>Total</span>
                    <span>৳{{ number_format($order->final_amount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
