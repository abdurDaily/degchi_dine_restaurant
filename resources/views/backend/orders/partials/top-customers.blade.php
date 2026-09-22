<div class="card top-customers-card shadow-sm border-0">
    <div class="card-body p-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <h6 class="fw-bold mb-0">
                <i class="fas fa-crown text-warning me-1"></i>
                Top Customers &mdash; {{ $monthLabel }}
            </h6>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="topCustomersClose">
                <i class="fas fa-times me-1"></i>Close
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width:70px;">#</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th class="text-center" style="width:110px;">Orders</th>
                        <th class="text-end" style="width:170px;">Total Spent</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topCustomers as $i => $c)
                        <tr>
                            <td class="text-center fw-bold">
                                @if($i === 0)
                                    <span class="badge top-rank rank-1"><i class="fas fa-medal me-1"></i>1</span>
                                @elseif($i === 1)
                                    <span class="badge top-rank rank-2"><i class="fas fa-medal me-1"></i>2</span>
                                @elseif($i === 2)
                                    <span class="badge top-rank rank-3"><i class="fas fa-medal me-1"></i>3</span>
                                @else
                                    {{ $i + 1 }}
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $c->customer_name ?: 'Walk-in Customer' }}</td>
                            <td>{{ $c->customer_phone ?: '-' }}</td>
                            <td class="text-center">{{ $c->orders_count }}</td>
                            <td class="text-end fw-bold">৳ {{ number_format((float) $c->total_spent, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No customer orders found for {{ $monthLabel }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>