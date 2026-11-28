@extends('layouts.dashboard')
@section('title', 'Offers')

@section('content')
    <x-breadcrumb></x-breadcrumb>

    <div class="container-fluid py-4 admin-crud-page">
        <div class="row">
            <div class="col-12">
                <div class="card admin-crud-card shadow-sm">
                    <div class="card-header d-flex align-items-center justify-content-between py-3">
                        <h5 class="mb-0 fw-bold"><i class="ri-price-tag-3-line me-2 text-primary"></i>Offers & Promotions</h5>
                        <a href="{{ route('offers.create') }}" class="btn btn-primary btn-sm">
                            <i class="ri-add-line me-1"></i> New Offer
                        </a>
                    </div>

                    <div class="card-body p-0">
                        <div class="alert alert-info border-0 rounded-0 mb-0 py-3 px-4">
                            <strong><i class="ri-information-line me-1"></i> How discounts work</strong>
                            <ul class="mb-0 mt-2 small ps-3">
                                <li><strong>Food item offers</strong> (Applies To: <em>All</em>) — All food items or
                                    Specific items. Largest % wins on the card.</li>
                                <li><strong>Membership</strong> + <strong>First Order Only</strong> —
                                    @if ($systemOffers['membership_first']['percent'])
                                        {{ $systemOffers['membership_first']['percent'] }}% on the member’s
                                        <em>first order only</em>. After that order: no membership discount.
                                    @else
                                        <em>currently switched off — no membership first-order amount is shown.</em>
                                    @endif
                                </li>
                                <li><strong>Student</strong> + <strong>First Order Only</strong> —
                                    @if ($systemOffers['student_first']['percent'])
                                        {{ $systemOffers['student_first']['percent'] }}% on an approved
                                        student’s <em>first order only</em>. After that order: no student discount.
                                    @else
                                        <em>currently switched off — no student first-order amount is shown.</em>
                                    @endif
                                </li>
                                <li><strong>Golden</strong> —
                                    @if ($systemOffers['golden']['active'] && $systemOffers['golden']['percent'] > 0)
                                        {{ $systemOffers['golden']['percent'] }}% on every order after upgrade
                                        @if ($systemOffers['golden']['threshold'] !== null)
                                            (upgrade threshold ৳{{ number_format($systemOffers['golden']['threshold'], 2) }})
                                        @endif
                                        . Not first-order limited.
                                    @else
                                        <em>currently switched off — no Golden amount or threshold is shown.</em>
                                    @endif
                                </li>
                                <li><strong>New Member Welcome</strong> —
                                    @if ($systemOffers['welcome']['percent'])
                                        every order gets {{ $systemOffers['welcome']['percent'] }}% for
                                        {{ $systemOffers['welcome']['months'] }} months after that member's own
                                        registration date, then it expires automatically. If the first-order discount
                                        has a higher %, the first order still uses that one.
                                    @else
                                        <em>currently switched off — no welcome amount is shown.</em>
                                    @endif
                                </li>
                                <li>Checkout takes the <strong>higher of</strong> food-item offer vs member-card benefit
                                    (not stacked).</li>
                                <li><strong>Min Order Total</strong> is a <em>whole-cart</em> threshold, not a per-item one.
                                    An offer only applies once the order subtotal (before discounts) reaches that amount.
                                    Below it the discount is not applied, and the item is billed at full price. A blank
                                    value means no minimum.</li>
                            </ul>
                        </div>

                        {{-- Built-in offers: same rows as the table below, summarised live --}}
                        <div class="border-bottom py-3 px-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="mb-0 fw-bold small text-uppercase text-muted">
                                    <i class="ri-settings-3-line me-1"></i> Built-in offers
                                </h6>
                                <span class="small text-muted">Edit any value here; inactive rows hide their amounts on the website.</span>
                            </div>
                            <div class="row g-2">
                                @foreach ($systemOffers as $setting)
                                    <div class="col-12 col-md-6 col-xl-3">
                                        <div class="border rounded p-3 h-100">
                                            <div class="d-flex align-items-start justify-content-between gap-2">
                                                <strong class="small">{{ $setting['label'] }}</strong>
                                                <span class="badge {{ $setting['active'] ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                                    {{ $setting['active'] ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                            <div class="small text-muted mt-2">
                                                @if (! $setting['active'])
                                                    No amount shown on the website.
                                                @elseif ($setting['key'] === 'golden')
                                                    {{ $setting['percent'] }}% benefit
                                                    @if ($setting['threshold'] !== null)
                                                        · ৳{{ number_format($setting['threshold'], 2) }} threshold
                                                    @endif
                                                @elseif ($setting['key'] === 'welcome')
                                                    {{ $setting['percent'] }}% for {{ $setting['months'] }} months
                                                @else
                                                    {{ $setting['percent'] }}% first order
                                                @endif
                                            </div>
                                            <div class="mt-2">
                                                @if ($setting['offer_id'])
                                                    <a href="{{ route('offers.edit', $setting['offer_id']) }}" class="small text-decoration-none">
                                                        <i class="ri-edit-line me-1"></i>Edit offer
                                                    </a>
                                                @else
                                                    <span class="small text-muted">Not configured</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @if ($offers->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="ri-price-tag-3-line" style="font-size:3rem;opacity:.3;"></i>
                                <p class="mt-2">No offers yet. <a href="{{ route('offers.create') }}">Create one</a>.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Image</th>
                                            <th>Name</th>
                                            <th class="text-center">Discount</th>
                                            <th>Applies To</th>
                                            <th class="text-center">Min Order</th>
                                            <th class="text-center">Popup</th>
                                            <th>Expires</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($offers as $i => $offer)
                                            <tr>
                                                <td class="text-muted small">{{ $i + 1 }}</td>
                                                <td>
                                                    <img src="{{ $offer->popup_image ? asset('storage/' . $offer->popup_image) : asset('assets/placeholder/placeholder.png') }}"
                                                        alt="{{ $offer->name }}" class="admin-thumb"
                                                        style="width:52px;height:40px;object-fit:cover;border-radius:6px;"
                                                        onerror="this.onerror=null;this.src='{{ asset('assets/placeholder/placeholder.png') }}';">
                                                </td>
                                                <td>
                                                    <div class="fw-semibold">{{ $offer->name }}</div>
                                                    @if ($offer->popup_badge)
                                                        <span class="badge bg-danger-subtle text-danger"
                                                            style="font-size:.7rem;">{{ $offer->popup_badge }}</span>
                                                    @endif
                                                    @if ($offer->description)
                                                        <div class="text-muted small"
                                                            style="max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                                            {{ $offer->description }}</div>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($offer->discount_percent > 0)
                                                        <span
                                                            class="badge bg-success fs-6 fw-bold">{{ $offer->discount_percent }}%</span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>
                            @if ($offer->applicable_to === 'welcome')
                                <span class="badge bg-success-subtle text-success"
                                      title="Every order gets this discount until each member's own registration anniversary">
                                    Welcome {{ (int) ($offer->welcome_duration_months ?: \App\Models\Member::WELCOME_DEFAULT_DURATION_MONTHS) }} mo
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary text-capitalize">{{ $offer->applicable_to }}</span>
                            @endif
                        </td>
                                                <td class="text-center">
                                                    @php $minTotal = $offer->minimumTotal(); @endphp
                                                    @if ($minTotal !== null)
                                                        <span class="fw-semibold" title="Applies only when the order subtotal reaches this amount">৳{{ number_format($minTotal, 2) }}</span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($offer->show_as_popup)
                                                        <span class="badge bg-primary-subtle text-primary"><i
                                                                class="ri-megaphone-line me-1"></i>Yes</span>
                                                    @else
                                                        <span class="text-muted small">No</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($offer->popup_expires_at)
                                                        <span
                                                            class="{{ $offer->popup_expires_at->isPast() ? 'text-danger' : 'text-success' }} small fw-semibold">
                                                            {{ $offer->popup_expires_at->format('M d, Y') }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted small">No expiry</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check form-switch d-inline-block m-0">
                                                        <input type="checkbox" class="form-check-input status-toggle"
                                                            data-id="{{ $offer->id }}"
                                                            data-url="{{ route('offers.toggle', $offer->id) }}"
                                                            {{ $offer->is_active ? 'checked' : '' }}
                                                            style="cursor:pointer;">
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex gap-1 justify-content-center">
                                                        <a href="{{ route('offers.edit', $offer->id) }}"
                                                            class="btn btn-sm btn-outline-warning" title="Edit">
                                                            <i class="ri-edit-line"></i>
                                                        </a>
                                                        <button class="btn btn-sm btn-outline-danger delete-offer-btn"
                                                            data-id="{{ $offer->id }}"
                                                            data-url="{{ route('offers.destroy', $offer->id) }}"
                                                            title="Delete">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            // Toggle active status
            $(document).on('change', '.status-toggle', function() {
                const url = $(this).data('url');
                const cb = this;
                $.post(url, {
                    _token: '{{ csrf_token() }}'
                }, function(res) {
                    if (res.success) {
                        toastr.success('Status updated.');
                    }
                }).fail(function() {
                    cb.checked = !cb.checked;
                    toastr.error('Failed to update status.');
                });
            });

            // Delete
            $(document).on('click', '.delete-offer-btn', function() {
                const url = $(this).data('url');
                const row = $(this).closest('tr');
                Swal.fire({
                    title: 'Delete this offer?',
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Yes, delete',
                }).then(res => {
                    if (res.isConfirmed) {
                        $.ajax({
                            url,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(r) {
                                if (r.success) {
                                    row.fadeOut(300, () => row.remove());
                                    toastr.success(r.message);
                                }
                            },
                            error: () => toastr.error('Failed to delete.')
                        });
                    }
                });
            });
        });
    </script>
@endpush
