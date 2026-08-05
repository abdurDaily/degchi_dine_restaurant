<x-admin-master>
    @section('title', 'Dashboard')
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('dashboardFilterForm');
                if (!form) return;

                const fromInput = form.querySelector('input[name="from"]');
                const toInput = form.querySelector('input[name="to"]');

                function fmt(date) {
                    return date.toISOString().split('T')[0];
                }

                document.querySelectorAll('.dd-quick-range').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        const range = btn.dataset.range;
                        const today = new Date();
                        let start = new Date();
                        let end = new Date();

                        if (range === 'today') {
                            start = today;
                            end = today;
                        } else if (range === 'week') {
                            const day = today.getDay() || 7; // Sunday=0 -> 7
                            start = new Date(today);
                            start.setDate(today.getDate() - day + 1); // week start Monday
                            end = today;
                        } else if (range === 'month') {
                            start = new Date(today.getFullYear(), today.getMonth(), 1);
                            end = today;
                        } else if (range === 'year') {
                            start = new Date(today.getFullYear(), 0, 1);
                            end = today;
                        }

                        fromInput.value = fmt(start);
                        toInput.value = fmt(end);
                        form.submit();
                    });
                });
            });
        </script>
    @endpush
    @section('content')
        <div class="dd-dash-page admin-page">
        <div class="dd-dash-hero">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 position-relative"
                style="z-index:1;">
                <div>
                    <h2>Welcome back, {{ auth()->user()->name ?? 'Admin' }}</h2>
                    <p>Degchi Dine control panel · {{ now()->format('l, F j, Y') }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @can('orders-show')
                        <a href="{{ route('orders.index') }}" class="btn btn-light btn-sm"><i
                                class="ri-shopping-cart-2-line me-1"></i> Orders</a>
                    @endcan
                    @can('members-show')
                        <a href="{{ route('members.index') }}" class="btn btn-warning btn-sm text-dark"><i
                                class="ri-user-star-line me-1"></i> Members</a>
                    @endcan
                </div>
            </div>
        </div>

        {{-- ================= DATE FILTER BAR ================= --}}
        <div class="card dd-panel dd-filter-bar mb-4">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('dashboard') }}" class="row g-2 align-items-end"
                    id="dashboardFilterForm">
                    <div class="col-6 col-md-3">
                        <label class="form-label small text-muted mb-1">From Date</label>
                        <input type="date" name="from" value="{{ $from }}"
                            class="form-control form-control-sm" max="{{ now()->toDateString() }}">
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label small text-muted mb-1">To Date</label>
                        <input type="date" name="to" value="{{ $to }}"
                            class="form-control form-control-sm" max="{{ now()->toDateString() }}">
                    </div>
                    <div class="col-12 col-md-auto d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-admin-primary">
                            <i class="ri-filter-3-line me-1"></i> Apply
                        </button>
                        @if ($isFiltered)
                            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                                <i class="ri-refresh-line me-1"></i> Reset
                            </a>
                        @endif
                    </div>

                    <div class="col-12 col-md-auto d-flex flex-wrap gap-2 ms-md-auto">
                        <button type="button" class="btn btn-sm dd-quick-range" data-range="today">Today</button>
                        <button type="button" class="btn btn-sm dd-quick-range" data-range="week">This Week</button>
                        <button type="button" class="btn btn-sm dd-quick-range" data-range="month">This Month</button>
                        <button type="button" class="btn btn-sm dd-quick-range" data-range="year">This Year</button>
                    </div>
                </form>

                <div class="mt-3 d-flex flex-wrap gap-2">
                    @if ($isFiltered)
                        <span class="dd-metric-chip">
                            <i class="ri-calendar-check-line text-primary"></i>
                            <span>{{ \Carbon\Carbon::parse($rangeFrom)->format('M d, Y') }}
                                &mdash; {{ \Carbon\Carbon::parse($rangeTo)->format('M d, Y') }}</span>
                        </span>
                    @else
                        <span class="dd-metric-chip">
                            <i class="ri-database-2-line"></i>
                            <span>Showing all-time data</span>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        @if (!$hasDashboardWidgets)
            <div class="dd-dash-empty mb-4">
                <div class="dd-dash-empty-icon"><i class="ri-dashboard-line"></i></div>
                <h5>Your dashboard is ready</h5>
                <p>No overview widgets are assigned to your account yet. Use the sidebar menu to open the modules you have
                    access to.</p>
            </div>
        @else
            {{-- Primary stats --}}
            <div class="row g-3 mb-4">
                @can('orders-show')
                    <div class="col-sm-6 col-xl-3">
                        <div class="card dd-stat-card">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="dd-stat-icon teal"><i class="ri-shopping-bag-3-line"></i></div>
                                <div>
                                    <div class="dd-stat-label">Total Orders</div>
                                    <div class="dd-stat-value">{{ number_format($stats['orders_total']) }}</div>
                                    <div class="dd-stat-meta">{{ $stats['orders_today'] }} today · {{ $stats['orders_new'] }}
                                        unread</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card dd-stat-card">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="dd-stat-icon gold"><i class="ri-money-dollar-circle-line"></i></div>
                                <div>
                                    <div class="dd-stat-label">Total Revenue</div>
                                    <div class="dd-stat-value">৳ {{ number_format($stats['orders_revenue'], 0) }}</div>
                                    <div class="dd-stat-meta">৳ {{ number_format($stats['revenue_today'], 0) }} today</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card dd-stat-card">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="dd-stat-icon orange"><i class="ri-time-line"></i></div>
                                <div>
                                    <div class="dd-stat-label">Pending Orders</div>
                                    <div class="dd-stat-value">{{ number_format($stats['orders_pending']) }}</div>
                                    <div class="dd-stat-meta">Needs attention</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
                @can('members-show')
                    <div class="col-sm-6 col-xl-3">
                        <div class="card dd-stat-card">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="dd-stat-icon green"><i class="ri-user-heart-line"></i></div>
                                <div>
                                    <div class="dd-stat-label">Members</div>
                                    <div class="dd-stat-value">{{ number_format($stats['members_total']) }}</div>
                                    <div class="dd-stat-meta">{{ $stats['members_golden'] }} golden ·
                                        {{ $stats['members_pending'] }} pending approval</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>

            {{-- Secondary stats --}}
            <div class="row g-3 mb-4">
                @can('menu-list')
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="card dd-stat-card">
                            <div class="card-body text-center py-3">
                                <div class="dd-stat-icon purple mx-auto mb-2"><i class="ri-restaurant-2-line"></i></div>
                                <div class="dd-stat-label">Menu Items</div>
                                <div class="dd-stat-value fs-4">{{ $stats['menu_items'] }}</div>
                            </div>
                        </div>
                    </div>
                @endcan
                @can('category-list')
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="card dd-stat-card">
                            <div class="card-body text-center py-3">
                                <div class="dd-stat-icon teal mx-auto mb-2"><i class="ri-folder-3-line"></i></div>
                                <div class="dd-stat-label">Categories</div>
                                <div class="dd-stat-value fs-4">{{ $stats['categories'] }}</div>
                            </div>
                        </div>
                    </div>
                @endcan
                @can('offers-show')
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="card dd-stat-card">
                            <div class="card-body text-center py-3">
                                <div class="dd-stat-icon gold mx-auto mb-2"><i class="ri-price-tag-3-line"></i></div>
                                <div class="dd-stat-label">Active Offers</div>
                                <div class="dd-stat-value fs-4">{{ $stats['offers_active'] }}</div>
                            </div>
                        </div>
                    </div>
                @endcan
                @can('reviews-show')
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="card dd-stat-card">
                            <div class="card-body text-center py-3">
                                <div class="dd-stat-icon green mx-auto mb-2"><i class="ri-star-line"></i></div>
                                <div class="dd-stat-label">Reviews</div>
                                <div class="dd-stat-value fs-4">{{ $stats['reviews_total'] }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="card dd-stat-card">
                            <div class="card-body text-center py-3">
                                <div class="dd-stat-icon red mx-auto mb-2"><i class="ri-chat-quote-line"></i></div>
                                <div class="dd-stat-label">Pending Reviews</div>
                                <div class="dd-stat-value fs-4">{{ $stats['reviews_pending'] }}</div>
                            </div>
                        </div>
                    </div>
                @endcan
                @can('branch-list')
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="card dd-stat-card">
                            <div class="card-body text-center py-3">
                                <div class="dd-stat-icon purple mx-auto mb-2"><i class="ri-store-2-line"></i></div>
                                <div class="dd-stat-label">Branches</div>
                                <div class="dd-stat-value fs-4">{{ $stats['branches'] }}</div>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>

            <div class="row g-3">
                @can('orders-show')
                    {{-- Recent orders --}}
                    <div class="col-xl-8">
                        <div class="card dd-panel">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title"><i class="ri-list-check-2 me-1"></i> Recent Orders</h5>
                                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover dd-orders-table mb-0">
                                        <thead>
                                            <tr>
                                                <th class="ps-3">Order</th>
                                                <th>Customer</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentOrders as $order)
                                                <tr>
                                                    <td class="ps-3">
                                                        @if (is_null($order->viewed_at))
                                                            <span class="dd-new-dot" title="New"></span>
                                                        @endif
                                                        #{{ $order->id }}
                                                    </td>
                                                    <td>
                                                        <div class="fw-semibold">{{ $order->customer_name ?: 'Guest' }}</div>
                                                        <small class="text-muted">{{ $order->customer_phone }}</small>
                                                    </td>
                                                    <td class="fw-semibold">৳ {{ number_format($order->final_amount, 2) }}
                                                    </td>
                                                    <td><span
                                                            class="dd-status-pill {{ $order->status }}">{{ $order->status }}</span>
                                                    </td>
                                                    <td><small>{{ $order->created_at->format('M d, H:i') }}</small></td>
                                                    <td>
                                                        <a href="{{ route('orders.show', $order) }}"
                                                            class="btn btn-sm btn-soft-info">View</a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4">No orders yet.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan

                {{-- Sidebar panels --}}
                <div class="{{ auth()->user()->can('orders-show') ? 'col-xl-4' : 'col-xl-12' }}">
                    @can('orders-show')
                        <div class="card dd-panel mb-3">
                            <div class="card-header">
                                <h5 class="card-title"><i class="ri-pie-chart-2-line me-1"></i> Orders by Status</h5>
                            </div>
                            <div class="card-body">
                                @php $maxStatus = max(1, $orderStatusCounts->max() ?? 1); @endphp
                                <div class="dd-bar-chart">
                                    @forelse($orderStatusCounts as $status => $count)
                                        <div class="dd-bar-row">
                                            <span class="dd-bar-label">{{ $status }}</span>
                                            <div class="dd-bar-track">
                                                <div class="dd-bar-fill" style="width: {{ ($count / $maxStatus) * 100 }}%;">
                                                </div>
                                            </div>
                                            <span class="dd-bar-count">{{ $count }}</span>
                                        </div>
                                    @empty
                                        <p class="text-muted mb-0 small">No order data.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endcan

                    <div class="card dd-panel mb-3">
                        <div class="card-header">
                            <h5 class="card-title"><i class="ri-links-line me-1"></i> Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            @can('orders-show')
                                <a href="{{ route('orders.index') }}" class="dd-quick-link">
                                    <span class="ql-icon"><i class="ri-shopping-cart-2-line"></i></span>
                                    <span>Manage Orders</span>
                                    @if ($stats['orders_new'] > 0)
                                        <span class="ql-badge">{{ $stats['orders_new'] }} new</span>
                                    @endif
                                </a>
                            @endcan
                            @can('members-edit')
                                <a href="{{ route('members.index', ['approval' => 'pending']) }}" class="dd-quick-link">
                                    <span class="ql-icon"><i class="ri-user-follow-line"></i></span>
                                    <span>Student Approvals</span>
                                    @if ($stats['members_pending'] > 0)
                                        <span class="ql-badge">{{ $stats['members_pending'] }}</span>
                                    @endif
                                </a>
                            @endcan
                            @can('reviews-moderate')
                                <a href="{{ route('admin.reviews.index') }}" class="dd-quick-link">
                                    <span class="ql-icon"><i class="ri-star-line"></i></span>
                                    <span>Review Moderation</span>
                                    @if ($stats['reviews_pending'] > 0)
                                        <span class="ql-badge">{{ $stats['reviews_pending'] }}</span>
                                    @endif
                                </a>
                            @endcan
                            @can('menu-list')
                                <a href="{{ route('admin.menu.index') }}" class="dd-quick-link">
                                    <span class="ql-icon"><i class="ri-restaurant-line"></i></span>
                                    <span>Menu Management</span>
                                </a>
                            @endcan
                            @can('offers-show')
                                <a href="{{ route('offers.index') }}" class="dd-quick-link">
                                    <span class="ql-icon"><i class="ri-price-tag-3-line"></i></span>
                                    <span>Offers &amp; Promotions</span>
                                </a>
                            @endcan
                            @can('branch-list')
                                <a href="{{ route('admin.branch.index') }}" class="dd-quick-link">
                                    <span class="ql-icon"><i class="ri-store-2-line"></i></span>
                                    <span>Branches</span>
                                </a>
                            @endcan
                        </div>
                    </div>

                    @can('members-show')
                        <div class="card dd-panel">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title"><i class="ri-user-add-line me-1"></i> New Members</h5>
                                <a href="{{ route('members.index') }}" class="btn btn-sm btn-link">All</a>
                            </div>
                            <div class="card-body pt-2">
                                @forelse($recentMembers as $member)
                                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                        <div>
                                            <div class="fw-semibold small">{{ $member->name ?: 'Unnamed' }}</div>
                                            <small
                                                class="text-muted">{{ $member->unique_card_number ?? $member->phone }}</small>
                                        </div>
                                        <span class="dd-member-chip {{ $member->type === 'golden' ? 'golden' : 'standard' }}">
                                            {{ ucfirst($member->type ?? 'standard') }}
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-muted small mb-0">No members registered yet.</p>
                                @endforelse
                            </div>
                        </div>
                    @endcan
                </div>
            </div>

            @can('orders-show')
                @if ($monthlyRevenue->isNotEmpty())
                    <div class="row g-3 mt-1">
                        <div class="col-12">
                            <div class="card dd-panel">
                                <div class="card-header">
                                    <h5 class="card-title"><i class="ri-bar-chart-grouped-line me-1"></i> Revenue (Last 6
                                        Months)</h5>
                                </div>
                                <div class="card-body">
                                    @php $maxRev = max(1, (float) $monthlyRevenue->max()); @endphp
                                    <div class="dd-revenue-bars">
                                        @foreach ($monthlyRevenue as $monthKey => $total)
                                            <div class="dd-revenue-col">
                                                <div class="dd-revenue-bar"
                                                    style="height: {{ max(8, ($total / $maxRev) * 100) }}px;"
                                                    title="৳ {{ number_format($total, 0) }}"></div>
                                                <span
                                                    class="dd-revenue-label">{{ \Carbon\Carbon::createFromFormat('Y-m', $monthKey)->format('M') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endcan
        @endif
        </div>{{-- /.dd-dash-page --}}
    @endsection
</x-admin-master>
