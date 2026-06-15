<div class="row">
    <!-- Left Column: Order details & items -->
    <div class="col-md-8">
        <!-- Order Meta Info -->
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0 fw-bold"><i class="fas fa-shopping-bag me-2"></i>Order Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6 mb-2">
                        <strong class="text-muted">Order ID:</strong>
                        <span class="ms-1 fw-semibold">#{{ $order->id }}</span>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <strong class="text-muted">Date:</strong>
                        <span class="ms-1">{{ $order->created_at->format('Y-m-d H:i:s') }}</span>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <strong class="text-muted">Payment Method:</strong>
                        <span class="ms-1 text-uppercase">{{ $order->payment_method }}</span>
                    </div>
                    @if($order->transaction_id)
                        <div class="col-sm-6 mb-2">
                            <strong class="text-muted">Transaction ID:</strong>
                            <span class="ms-1">{{ $order->transaction_id }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0 fw-bold"><i class="fas fa-list me-2"></i>Ordered Items</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th class="text-center">Price</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end" style="padding-right: 1.5rem;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(is_array($order->items) || is_object($order->items))
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if(!empty($item['image']))
                                                    <img src="{{ asset($item['image']) }}" alt="{{ $item['title'] }}" class="rounded me-2" style="width: 45px; height: 45px; object-fit: cover;">
                                                @else
                                                    <div class="rounded bg-secondary me-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; color: #fff;">
                                                        <i class="fas fa-utensils"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $item['title'] }}</div>
                                                    @if(!empty($item['note']))
                                                        <small class="text-muted">{{ $item['note'] }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">৳{{ number_format($item['price'], 2) }}</td>
                                        <td class="text-center fw-bold">{{ $item['quantity'] }}</td>
                                        <td class="text-end fw-bold text-dark" style="padding-right: 1.5rem;">৳{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No items stored for this order.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Status controllers, Customer details, Member details -->
    <div class="col-md-4">
        <!-- Status & Payment Status Controls -->
        <div class="card mb-3 shadow-sm border-warning">
            <div class="card-header bg-warning text-dark fw-bold">
                <i class="fas fa-cog me-2"></i>Actions & Statuses
            </div>
            <div class="card-body">
                <form id="updateOrderStatusForm" data-action-url="{{ route('orders.updateStatus', $order->id) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="order_status" class="form-label fw-bold">Order Status</label>
                        <select name="status" id="order_status" class="form-select">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="canceled" {{ $order->status === 'canceled' ? 'selected' : '' }}>Canceled</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="order_payment_status" class="form-label fw-bold">Payment Status</label>
                        <select name="payment_status" id="order_payment_status" class="form-select">
                            <option value="unpaid" {{ $order->payment_status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="cancelled" {{ $order->payment_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <button type="submit" id="saveOrderStatusBtn" class="btn btn-warning w-100 fw-bold">
                        <i class="fas fa-save me-1"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>

        <!-- Customer Card -->
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0 fw-bold"><i class="fas fa-user me-2"></i>Customer Info</h5>
            </div>
            <div class="card-body" style="font-size: 0.9rem;">
                <div class="mb-2"><strong>Name:</strong> {{ $order->customer_name }}</div>
                <div class="mb-2"><strong>Phone:</strong> {{ $order->customer_phone }}</div>
                <div class="mb-0"><strong>Address:</strong> {{ $order->customer_address }}</div>
            </div>
        </div>

        <!-- Member Discount Info Card -->
        @if($order->member)
            <div class="card mb-3 shadow-sm border-info">
                <div class="card-header bg-info text-white fw-bold">
                    <i class="fas fa-id-card me-2"></i>Card Details
                </div>
                <div class="card-body" style="font-size: 0.9rem;">
                    <div class="mb-2"><strong>Member Name:</strong> {{ $order->member->name }}</div>
                    <div class="mb-2"><strong>Card Number:</strong> <span class="badge bg-light text-dark border fw-bold">{{ $order->unique_card_number }}</span></div>
                    <div class="mb-2">
                        <strong>Card Type:</strong> 
                        <span class="badge {{ $order->member->type === 'golden' ? 'bg-warning text-dark' : 'bg-primary' }} text-capitalize fw-bold">
                            {{ $order->member->type }}
                        </span>
                    </div>
                    <div class="mb-2"><strong>Accumulated Purchase:</strong> ৳{{ number_format($order->member->total_purchase, 2) }}</div>
                    
                    @if($order->member->expires_at)
                        <div class="mb-2">
                            <strong>Expires At:</strong> 
                            <span class="{{ $order->member->expires_at < now() ? 'text-danger fw-bold' : '' }}">
                                {{ $order->member->expires_at->format('Y-m-d') }}
                            </span>
                        </div>
                    @endif

                    @if($order->member->is_student)
                        <div class="mt-3 p-2 rounded bg-light border border-info">
                            <span class="badge bg-success mb-2">Student Member</span>
                            @if($order->member->student_card_path)
                                <div class="d-grid mt-1">
                                    <a href="{{ asset('storage/' . $order->member->student_card_path) }}" target="_blank" class="btn btn-sm btn-outline-info fw-bold">
                                        <i class="fas fa-image me-1"></i> View Student Card
                                    </a>
                                </div>
                            @else
                                <div class="text-danger small mt-1"><i class="fas fa-exclamation-triangle"></i> Student card file missing.</div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Order Financial Summary -->
        <div class="card shadow-sm">
            <div class="card-body" style="font-size: 0.95rem;">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span>৳{{ number_format($order->total_amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 text-success">
                    <span>Discount:</span>
                    <span>- ৳{{ number_format($order->discount_amount, 2) }}</span>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between fw-bold text-dark fs-5">
                    <span>Total:</span>
                    <span>৳{{ number_format($order->final_amount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
