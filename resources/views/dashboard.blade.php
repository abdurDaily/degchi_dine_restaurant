<x-admin-master>
    @section('title', 'Dashboard')
    @push('styles')
    <style>
        :root {
            --dd-teal: #116b83;
            --dd-teal-dark: #0d5566;
            --dd-teal-deep: #083844;
            --dd-gold: #e7ae07;
            --dd-gold-soft: rgba(231, 174, 7, 0.12);
        }

        .dd-dash-hero {
            background: linear-gradient(135deg, var(--dd-teal-deep) 0%, var(--dd-teal) 55%, var(--dd-teal-dark) 100%);
            border-radius: 16px;
            padding: 1.75rem 1.85rem;
            color: #fff;
            position: relative;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .dd-dash-hero::after {
            content: "";
            position: absolute;
            top: -40%;
            right: -8%;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(231, 174, 7, 0.18) 0%, transparent 70%);
            pointer-events: none;
        }

        .dd-dash-hero h2 {
            font-size: 1.45rem;
            font-weight: 700;
            margin-bottom: 0.35rem;
        }

        .dd-dash-hero p {
            margin: 0;
            opacity: 0.85;
            font-size: 0.92rem;
        }

        .dd-stat-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(17, 107, 131, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            height: 100%;
            overflow: hidden;
        }

        .dd-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 32px rgba(17, 107, 131, 0.14);
        }

        .dd-stat-card .card-body {
            padding: 1.15rem 1.2rem;
        }

        .dd-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }

        .dd-stat-icon.teal { background: rgba(17, 107, 131, 0.12); color: var(--dd-teal); }
        .dd-stat-icon.gold { background: var(--dd-gold-soft); color: #b8860b; }
        .dd-stat-icon.green { background: rgba(16, 185, 129, 0.12); color: #10b981; }
        .dd-stat-icon.orange { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
        .dd-stat-icon.purple { background: rgba(139, 92, 246, 0.12); color: #8b5cf6; }
        .dd-stat-icon.red { background: rgba(239, 68, 68, 0.12); color: #ef4444; }

        .dd-stat-label {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #6b7280;
            margin-bottom: 0.15rem;
        }

        .dd-stat-value {
            font-size: 1.55rem;
            font-weight: 700;
            color: #111827;
            line-height: 1.2;
        }

        .dd-stat-meta {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 0.25rem;
        }

        .dd-panel {
            border: none;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(17, 107, 131, 0.07);
        }

        .dd-panel .card-header {
            background: #fff;
            border-bottom: 1px solid rgba(17, 107, 131, 0.08);
            padding: 1rem 1.25rem;
            border-radius: 14px 14px 0 0;
        }

        .dd-panel .card-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--dd-teal-dark);
            margin: 0;
        }

        .dd-quick-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.85rem 1rem;
            border-radius: 12px;
            border: 1px solid rgba(17, 107, 131, 0.1);
            text-decoration: none;
            color: #374151;
            transition: all 0.2s ease;
            margin-bottom: 0.65rem;
        }

        .dd-quick-link:last-child { margin-bottom: 0; }

        .dd-quick-link:hover {
            background: rgba(17, 107, 131, 0.06);
            border-color: rgba(17, 107, 131, 0.22);
            color: var(--dd-teal-dark);
            transform: translateX(4px);
        }

        .dd-quick-link .ql-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(17, 107, 131, 0.1);
            color: var(--dd-teal);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .dd-quick-link .ql-badge {
            margin-left: auto;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            background: var(--dd-gold-soft);
            color: #92680a;
        }

        .dd-orders-table th {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
            font-weight: 600;
            border-bottom-width: 1px;
        }

        .dd-orders-table td {
            vertical-align: middle;
            font-size: 0.86rem;
        }

        .dd-status-pill {
            display: inline-block;
            padding: 0.22rem 0.6rem;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .dd-status-pill.pending { background: #fef3c7; color: #92400e; }
        .dd-status-pill.confirmed { background: #dbeafe; color: #1e40af; }
        .dd-status-pill.completed { background: #d1fae5; color: #065f46; }
        .dd-status-pill.canceled { background: #fee2e2; color: #991b1b; }

        .dd-new-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ef4444;
            display: inline-block;
            margin-right: 4px;
        }

        .dd-bar-chart { display: flex; flex-direction: column; gap: 0.65rem; }

        .dd-bar-row {
            display: grid;
            grid-template-columns: 90px 1fr 42px;
            align-items: center;
            gap: 0.65rem;
        }

        .dd-bar-label {
            font-size: 0.78rem;
            color: #6b7280;
            text-transform: capitalize;
        }

        .dd-bar-track {
            height: 8px;
            background: #f3f4f6;
            border-radius: 999px;
            overflow: hidden;
        }

        .dd-bar-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--dd-teal), var(--dd-teal-dark));
        }

        .dd-bar-count {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--dd-teal-dark);
            text-align: right;
        }

        .dd-revenue-bars {
            display: flex;
            align-items: flex-end;
            gap: 0.5rem;
            height: 120px;
            padding-top: 0.5rem;
        }

        .dd-revenue-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.35rem;
            min-width: 0;
        }

        .dd-revenue-bar {
            width: 100%;
            max-width: 36px;
            border-radius: 6px 6px 2px 2px;
            background: linear-gradient(180deg, var(--dd-gold), var(--dd-teal));
            min-height: 8px;
        }

        .dd-revenue-label {
            font-size: 0.65rem;
            color: #9ca3af;
            text-align: center;
        }

        .dd-member-chip {
            font-size: 0.68rem;
            padding: 0.15rem 0.45rem;
            border-radius: 999px;
            font-weight: 600;
        }

        .dd-member-chip.golden { background: var(--dd-gold-soft); color: #92680a; }
        .dd-member-chip.standard { background: rgba(17, 107, 131, 0.1); color: var(--dd-teal-dark); }
    </style>
    @endpush

    @section('content')
        <x-breadcrumb></x-breadcrumb>

        <div class="dd-dash-hero">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 position-relative" style="z-index:1;">
                <div>
                    <h2 style="color:#fff;">Welcome back, {{ auth()->user()->name ?? 'Admin' }}</h2>
                    <p>Degchi Dine control panel · {{ now()->format('l, F j, Y') }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('orders.index') }}" class="btn btn-light btn-sm fw-semibold"><i class="ri-shopping-cart-2-line me-1"></i> Orders</a>
                    <a href="{{ route('members.index') }}" class="btn btn-warning btn-sm fw-semibold text-dark"><i class="ri-user-star-line me-1"></i> Members</a>
                </div>
            </div>
        </div>

        {{-- Primary stats --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card dd-stat-card">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="dd-stat-icon teal"><i class="ri-shopping-bag-3-line"></i></div>
                        <div>
                            <div class="dd-stat-label">Total Orders</div>
                            <div class="dd-stat-value">{{ number_format($stats['orders_total']) }}</div>
                            <div class="dd-stat-meta">{{ $stats['orders_today'] }} today · {{ $stats['orders_new'] }} unread</div>
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
                        <div class="dd-stat-icon green"><i class="ri-user-heart-line"></i></div>
                        <div>
                            <div class="dd-stat-label">Members</div>
                            <div class="dd-stat-value">{{ number_format($stats['members_total']) }}</div>
                            <div class="dd-stat-meta">{{ $stats['members_golden'] }} golden · {{ $stats['members_pending'] }} pending approval</div>
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
        </div>

        {{-- Secondary stats --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card dd-stat-card">
                    <div class="card-body text-center py-3">
                        <div class="dd-stat-icon purple mx-auto mb-2"><i class="ri-restaurant-2-line"></i></div>
                        <div class="dd-stat-label">Menu Items</div>
                        <div class="dd-stat-value fs-4">{{ $stats['menu_items'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card dd-stat-card">
                    <div class="card-body text-center py-3">
                        <div class="dd-stat-icon teal mx-auto mb-2"><i class="ri-folder-3-line"></i></div>
                        <div class="dd-stat-label">Categories</div>
                        <div class="dd-stat-value fs-4">{{ $stats['categories'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card dd-stat-card">
                    <div class="card-body text-center py-3">
                        <div class="dd-stat-icon gold mx-auto mb-2"><i class="ri-price-tag-3-line"></i></div>
                        <div class="dd-stat-label">Active Offers</div>
                        <div class="dd-stat-value fs-4">{{ $stats['offers_active'] }}</div>
                    </div>
                </div>
            </div>
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
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card dd-stat-card">
                    <div class="card-body text-center py-3">
                        <div class="dd-stat-icon purple mx-auto mb-2"><i class="ri-store-2-line"></i></div>
                        <div class="dd-stat-label">Branches</div>
                        <div class="dd-stat-value fs-4">{{ $stats['branches'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
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
                                                @if(is_null($order->viewed_at))
                                                    <span class="dd-new-dot" title="New"></span>
                                                @endif
                                                #{{ $order->id }}
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $order->customer_name ?: 'Guest' }}</div>
                                                <small class="text-muted">{{ $order->customer_phone }}</small>
                                            </td>
                                            <td class="fw-semibold">৳ {{ number_format($order->final_amount, 2) }}</td>
                                            <td><span class="dd-status-pill {{ $order->status }}">{{ $order->status }}</span></td>
                                            <td><small>{{ $order->created_at->format('M d, H:i') }}</small></td>
                                            <td>
                                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-soft-info">View</a>
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

            {{-- Sidebar panels --}}
            <div class="col-xl-4">
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
                                        <div class="dd-bar-fill" style="width: {{ ($count / $maxStatus) * 100 }}%;"></div>
                                    </div>
                                    <span class="dd-bar-count">{{ $count }}</span>
                                </div>
                            @empty
                                <p class="text-muted mb-0 small">No order data.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="card dd-panel mb-3">
                    <div class="card-header">
                        <h5 class="card-title"><i class="ri-links-line me-1"></i> Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('orders.index') }}" class="dd-quick-link">
                            <span class="ql-icon"><i class="ri-shopping-cart-2-line"></i></span>
                            <span>Manage Orders</span>
                            @if($stats['orders_new'] > 0)
                                <span class="ql-badge">{{ $stats['orders_new'] }} new</span>
                            @endif
                        </a>
                        <a href="{{ route('members.index', ['approval' => 'pending']) }}" class="dd-quick-link">
                            <span class="ql-icon"><i class="ri-user-follow-line"></i></span>
                            <span>Student Approvals</span>
                            @if($stats['members_pending'] > 0)
                                <span class="ql-badge">{{ $stats['members_pending'] }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.reviews.index') }}" class="dd-quick-link">
                            <span class="ql-icon"><i class="ri-star-line"></i></span>
                            <span>Review Moderation</span>
                            @if($stats['reviews_pending'] > 0)
                                <span class="ql-badge">{{ $stats['reviews_pending'] }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.menu.index') }}" class="dd-quick-link">
                            <span class="ql-icon"><i class="ri-restaurant-line"></i></span>
                            <span>Menu Management</span>
                        </a>
                        <a href="{{ route('offers.index') }}" class="dd-quick-link">
                            <span class="ql-icon"><i class="ri-price-tag-3-line"></i></span>
                            <span>Offers &amp; Promotions</span>
                        </a>
                        <a href="{{ route('admin.branch.index') }}" class="dd-quick-link">
                            <span class="ql-icon"><i class="ri-store-2-line"></i></span>
                            <span>Branches</span>
                        </a>
                    </div>
                </div>

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
                                    <small class="text-muted">{{ $member->unique_card_number ?? $member->phone }}</small>
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
            </div>
        </div>

        @if($monthlyRevenue->isNotEmpty())
        <div class="row g-3 mt-1">
            <div class="col-12">
                <div class="card dd-panel">
                    <div class="card-header">
                        <h5 class="card-title"><i class="ri-bar-chart-grouped-line me-1"></i> Revenue (Last 6 Months)</h5>
                    </div>
                    <div class="card-body">
                        @php $maxRev = max(1, (float) $monthlyRevenue->max()); @endphp
                        <div class="dd-revenue-bars">
                            @foreach($monthlyRevenue as $monthKey => $total)
                                <div class="dd-revenue-col">
                                    <div class="dd-revenue-bar" style="height: {{ max(8, ($total / $maxRev) * 100) }}px;" title="৳ {{ number_format($total, 0) }}"></div>
                                    <span class="dd-revenue-label">{{ \Carbon\Carbon::createFromFormat('Y-m', $monthKey)->format('M') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endsection
</x-admin-master>
