@extends('layouts.dashboard')
@section('title', 'Orders')

@push('styles')
<style>
    /* ─── Filter pill buttons ─── */
    .filter-btn-group .btn {
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.8rem;
        padding: 6px 16px;
        line-height: 1.4;
        transition: all 0.2s ease;
    }
    .filter-btn-group .btn .badge {
        font-size: 0.7rem;
        padding: 2px 7px;
        border-radius: 50px;
        margin-left: 4px;
        font-weight: 700;
    }
    .filter-btn-group .btn.active {
        box-shadow: 0 4px 12px rgba(0,0,0,.18);
        transform: translateY(-1px);
    }

    /* ─── Date range picker ─── */
    .drp-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
    }
    #dateRangePickerBtn {
        cursor: pointer;
        /* width: 50%;
        min-width: 190px; */
        background: #fff;
        border: 1.5px solid #d0d7df;
        border-radius: 5px;
        padding: 6px 32px 6px 34px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #344054;
        transition: border-color .2s, box-shadow .2s;
        white-space: nowrap;
        line-height: 1.4;
    }
    #dateRangePickerBtn:hover,
    #dateRangePickerBtn:focus {
        border-color: #4a90d9;
        box-shadow: 0 0 0 3px rgba(74,144,217,.12);
        outline: none;
    }
    .drp-wrapper .drp-icon {
        position: absolute;
        left: 12px;
        color: #4a90d9;
        font-size: 0.78rem;
        pointer-events: none;
    }
    #drpClear {
        position: absolute;
        right: 10px;
        color: #adb5bd;
        font-size: 1rem;
        line-height: 1;
        cursor: pointer;
        background: none;
        border: none;
        padding: 0;
        transition: color .15s;
    }
    #drpClear:hover { color: #dc3545; }

    /* ─── Search input matching pill style ─── */
    .orders-search-wrap {
        position: relative;
        display: inline-flex;
        align-items: center;
    }
    .orders-search-wrap .search-icon {
        position: absolute;
        left: 12px;
        color: #adb5bd;
        font-size: 0.78rem;
        pointer-events: none;
    }
    #ordersSearch {
        border-radius: 5px;
        border: 1.5px solid #d0d7df;
        padding: 6px 16px 6px 32px;
        font-size: 0.8rem;
        min-width: 190px;
        line-height: 1.4;
        transition: border-color .2s, box-shadow .2s;
    }
    #ordersSearch:focus {
        border-color: #4a90d9;
        box-shadow: 0 0 0 3px rgba(74,144,217,.12);
        outline: none;
    }

    /* ─── Unread / new-order rows ─── */
    tr.row-new-order { position: relative; }
    tr.row-new-order td {
        background: linear-gradient(90deg, #e8f4fd 0%, #f0f8ff 100%) !important;
        border-top: 1px solid #b8d9f5 !important;
        border-bottom: 1px solid #b8d9f5 !important;
    }
    tr.row-new-order td:first-child {
        border-left: 3px solid #3b9ede !important;
        padding-left: 10px !important;
    }
    tr.row-new-order td:first-child::after {
        content: 'NEW';
        display: inline-block;
        margin-left: 4px;
        padding: 1px 5px;
        font-size: 0.6rem;
        font-weight: 700;
        letter-spacing: .04em;
        color: #fff;
        background: #3b9ede;
        border-radius: 4px;
        vertical-align: middle;
        line-height: 1.6;
        animation: pulse-badge 1.6s ease-in-out infinite;
    }
    @keyframes pulse-badge {
        0%, 100% { opacity: 1; }
        50%       { opacity: .55; }
    }

    /* ─── Canceled order rows (member or admin) ─── */
    tr.row-canceled-order td {
        background: linear-gradient(90deg, #fde8e8 0%, #fff5f5 100%) !important;
        border-top: 1px solid #f5c2c7 !important;
        border-bottom: 1px solid #f5c2c7 !important;
        color: #842029;
    }
    tr.row-canceled-order td:first-child {
        border-left: 3px solid #dc3545 !important;
        padding-left: 10px !important;
    }
    tr.row-canceled-order:hover td {
        background: linear-gradient(90deg, #f8d7da 0%, #fde8e8 100%) !important;
    }

    /* Status chips — independent of Bootstrap text-* utility classes */
    .order-status-badge {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        padding: 0.35em 0.65em;
    }
    .order-status-canceled {
        background: #f8d7da !important;
        color: #842029 !important;
    }
    .order-status-pending {
        background: #fff3cd !important;
        color: #856404 !important;
    }
    .order-status-confirmed {
        background: #cff4fc !important;
        color: #055160 !important;
    }
    .order-status-completed {
        background: #d1e7dd !important;
        color: #0f5132 !important;
    }
    .order-branch-badge {
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.02em;
        padding: 0.35em 0.65em;
    }
    .order-branch-assigned {
        background: #0d5566 !important;
        color: #fff !important;
    }

    /* ─── Hide DataTable's own search box ─── */
    .dataTables_wrapper .dataTables_filter { display: none; }

    /* ─── Top customer month filter (matches pill styling) ─── */
    .top-customer-btn {
        background: #fff;
        border: 1.5px solid #d0d7df !important;
        border-radius: 5px;
        padding: 6px 16px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #344054;
        line-height: 1.4;
        transition: border-color .2s, box-shadow .2s;
    }
    .top-customer-btn:hover,
    .top-customer-btn:focus,
    .top-customer-btn.show {
        border-color: #4a90d9 !important;
        box-shadow: 0 0 0 3px rgba(74,144,217,.12);
        color: #344054 !important;
    }
    .top-customer-menu {
        min-width: 230px;
        border: 1.5px solid #e4e9ef;
        border-radius: 8px;
    }
    .top-customer-menu .dropdown-item {
        font-size: 0.82rem;
        border-radius: 6px;
    }
    .top-customer-menu .dropdown-item.active {
        background: #e8f4fd;
        color: #0a58ca;
    }

    /* ─── Top customers leaderboard panel ─── */
    .top-customers-card {
        border-top: 3px solid #4a90d9 !important;
    }
    .top-customers-card .top-rank {
        padding: 0.3em 0.55em;
        font-size: 0.72rem;
        font-weight: 700;
        border-radius: 50px;
    }
    .top-customers-card .rank-1 { background: #ffd700 !important; color: #221f0e !important; }
    .top-customers-card .rank-2 { background: #c0c7cf !important; color: #1f2937 !important; }
    .top-customers-card .rank-3 { background: #cd7f32 !important; color: #fff !important; }
</style>
@endpush

@section('content')
    <x-breadcrumb></x-breadcrumb>

    <div class="container-fluid py-4 admin-crud-page">
    <div class="row">
        <div class="col-12">
            <div class="card admin-crud-card shadow-sm">

                {{-- ══ Single toolbar row: pills + date picker + search ══ --}}
                <div class="card-header py-3" style="background:#f8f9fb; border-bottom:1px solid #e9ecef;">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">

                        {{-- Left: Status filter pills --}}
                        <div class="filter-btn-group d-flex flex-wrap align-items-center gap-2">
                            <button class="btn btn-dark active" data-filter="">
                                <i class="fas fa-list-ul me-1"></i>All
                                <span class="badge bg-white text-dark" id="count-all">{{ $counts['all'] ?? 0 }}</span>
                            </button>
                            <button class="btn btn-warning" data-filter="pending">
                                <i class="fas fa-clock me-1"></i>Pending
                                <span class="badge bg-white text-warning" id="count-pending">{{ $counts['pending'] ?? 0 }}</span>
                            </button>
                            <button class="btn btn-info text-white" data-filter="confirmed">
                                <i class="fas fa-check-circle me-1"></i>Confirmed
                                <span class="badge bg-white text-info" id="count-confirmed">{{ $counts['confirmed'] ?? 0 }}</span>
                            </button>
                            <button class="btn btn-success" data-filter="completed">
                                <i class="fas fa-check-double me-1"></i>Completed
                                <span class="badge bg-white text-success" id="count-completed">{{ $counts['completed'] ?? 0 }}</span>
                            </button>
                            <button class="btn btn-danger" data-filter="canceled">
                                <i class="fas fa-times-circle me-1"></i>Canceled
                                <span class="badge bg-white text-danger" id="count-canceled">{{ $counts['canceled'] ?? 0 }}</span>
                            </button>
                        </div>

                        {{-- Right: Top Customer month filter + Date picker + Search (same pill height) --}}
                        <div class="d-flex flex-wrap align-items-center gap-2">

                            {{-- Top Customers leaderboard: pick a month to see who bought the most --}}
                            <div class="dropdown position-relative">
                                <button class="btn top-customer-btn dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-trophy me-1"></i>
                                    <span id="topCustomerBtnLabel">Top Customer</span>
                                </button>
                                <div class="dropdown-menu top-customer-menu shadow dropdown-menu-end">
                                    <h6 class="dropdown-header">
                                        <i class="fas fa-calendar-alt me-1"></i>Select Month
                                    </h6>
                                    <button class="dropdown-item top-customer-month" type="button" data-month="">
                                        <i class="fas fa-times-circle me-1 text-muted"></i> All Customers
                                    </button>
                                    <div class="dropdown-divider"></div>
                                    @php($monthOptions = collect(range(0, 11))->map(fn ($i) => now()->copy()->subMonths($i)))
                                    @foreach($monthOptions as $i => $m)
                                        <button class="dropdown-item top-customer-month" type="button"
                                                data-month="{{ $m->format('Y-m') }}">
                                            @if($i === 0)
                                                <i class="fas fa-bolt me-1 text-warning"></i>
                                            @elseif($i === 1)
                                                <i class="fas fa-clock me-1 text-info"></i>
                                            @else
                                                <i class="fas fa-calendar-day me-1 text-muted"></i>
                                            @endif
                                            {{ $m->format('F Y') }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <div class="drp-wrapper">
                                <i class="fas fa-calendar-alt drp-icon"></i>
                                <input id="dateRangePickerBtn" type="text" readonly
                                       placeholder="Select date range" />
                                <button id="drpClear" class="d-none" title="Clear">&times;</button>
                            </div>

                            <div class="orders-search-wrap">
                                <i class="fas fa-search search-icon"></i>
                                <input id="ordersSearch" type="search"
                                       placeholder="Name, phone, card…" />
                            </div>

                        </div>

                    </div>
                </div>

                {{-- ══ Table ══ --}}
                <div class="card-body">
                    {{-- Top customers leaderboard (rendered by AJAX when a month is picked) --}}
                    <div id="topCustomersPanel" class="d-none mb-3"></div>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered yajra-datatable w-100 align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Phone</th>
                                    <th>Card No.</th>
                                    <th>Member</th>
                                    <th>Total</th>
                                    <th>Discount</th>
                                    <th>Final</th>
                                    <th>Status</th>
                                    <th>Branch</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    </div>

    {{-- ══ Order Details Modal ══ --}}
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header admin-modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-file-invoice me-2"></i>Order Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderDetailsModalBody">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted">Fetching order…</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
$(function () {

    /* ══════════════════════════════════════════════════
       1. DATE RANGE PICKER  — defaults to All Time
    ══════════════════════════════════════════════════ */
    const todayStr = moment().format('YYYY-MM-DD');
    let dateFrom   = '';
    let dateTo     = '';

    $('#dateRangePickerBtn').daterangepicker({
        startDate : moment('2020-01-01'),
        endDate   : moment(),
        opens     : 'left',
        locale: {
            cancelLabel : 'Clear',
            format      : 'MMM D, YYYY',
            separator   : '  →  ',
        },
        ranges: {
            'Today'       : [moment(), moment()],
            'Yesterday'   : [moment().subtract(1,'days'), moment().subtract(1,'days')],
            'Last 7 Days' : [moment().subtract(6,'days'), moment()],
            'Last 30 Days': [moment().subtract(29,'days'), moment()],
            'This Month'  : [moment().startOf('month'), moment().endOf('month')],
            'Last Month'  : [moment().subtract(1,'month').startOf('month'), moment().subtract(1,'month').endOf('month')],
            'All Time'    : [moment('2020-01-01'), moment()],
        }
    });

    $('#dateRangePickerBtn').on('apply.daterangepicker', function(ev, picker) {
        dateFrom = picker.startDate.format('YYYY-MM-DD');
        dateTo   = picker.endDate.format('YYYY-MM-DD');

        const isToday = dateFrom === todayStr && dateTo === todayStr;
        if (isToday) {
            $(this).val('Today');
            $('#drpClear').addClass('d-none');
        } else if (dateFrom === dateTo) {
            $(this).val(picker.startDate.format('MMM D, YYYY'));
            $('#drpClear').removeClass('d-none');
        } else {
            $(this).val(picker.startDate.format('MMM D') + '  →  ' + picker.endDate.format('MMM D, YYYY'));
            $('#drpClear').removeClass('d-none');
        }
        table.draw();
    });

    $('#dateRangePickerBtn').on('cancel.daterangepicker', function() {
        clearDateFilter();
    });

    $('#drpClear').on('click', function(e) {
        e.stopPropagation();
        clearDateFilter();
    });

    function clearDateFilter() {
        dateFrom = '';
        dateTo   = '';
        $('#dateRangePickerBtn').val('All Time');
        $('#drpClear').addClass('d-none');
        table.draw();
    }

    // Seed label for the default "All Time"
    $('#dateRangePickerBtn').val('All Time');

    /* ══════════════════════════════════════════════════
       2. DATATABLE
    ══════════════════════════════════════════════════ */
    let currentFilter = '';

    const table = $('.yajra-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('orders.index') }}',
            data: function (d) {
                d.search.value  = $('#ordersSearch').val();
                d.status_filter = currentFilter;
                d.date_from     = dateFrom;
                d.date_to       = dateTo;
            }
        },
        columns: [
            { data: 'DT_RowIndex',    name: 'DT_RowIndex',       orderable: false, searchable: false },
            { data: 'customer_name',  name: 'customer_name' },
            { data: 'customer_phone', name: 'customer_phone' },
            { data: 'card_number',    name: 'unique_card_number' },
            { data: 'member',         name: 'member.name',        orderable: false, searchable: false },
            { data: 'total',          name: 'total_amount' },
            { data: 'discount',       name: 'discount_amount' },
            { data: 'final',          name: 'final_amount' },
            { data: 'status_name',    name: 'status' },
            { data: 'branch_name',     orderable: false, searchable: false },
            { data: 'date',           name: 'created_at' },
            { data: 'action',         name: 'action',             orderable: false, searchable: false }
        ],
        order: [[10, 'desc']],

        rowCallback: function (row, data) {
            // Prefer the raw status field; never parse status_name HTML.
            const status = String(data.status || '').toLowerCase();
            const $row = $(row);

            $row.removeClass('row-new-order row-canceled-order');

            if (status === 'canceled') {
                // Canceled wins over "NEW" highlight so canceled rows stay clearly red.
                $row.addClass('row-canceled-order');
            } else if (parseInt(data.is_new) === 1) {
                $row.addClass('row-new-order');
            }
        },

        drawCallback: function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });

    $('#ordersSearch').on('keyup change clear', function () {
        table.search(this.value).draw();
    });

    /* ══════════════════════════════════════════════════
       3. STATUS FILTER BUTTONS
    ══════════════════════════════════════════════════ */
    $(document).on('click', '.filter-btn-group .btn', function () {
        $('.filter-btn-group .btn').removeClass('active');
        $(this).addClass('active');
        currentFilter = $(this).data('filter');
        table.draw();
    });

    /* ══════════════════════════════════════════════════
       4. OPEN ORDER MODAL  (marks row as viewed)
    ══════════════════════════════════════════════════ */
    $(document).on('click', '.view-order-btn', function () {
        const url  = $(this).data('url');
        const body = $('#orderDetailsModalBody');

        $(this).closest('tr').removeClass('row-new-order');

        body.html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Fetching order…</p></div>');
        $('#orderDetailsModal').modal('show');

        $.ajax({
            url, type: 'GET',
            success: function (html) { body.html(html); refreshCounts(); },
            error  : ()            => body.html('<div class="alert alert-danger m-3">Failed to load order details.</div>')
        });
    });

    /* ══════════════════════════════════════════════════
       5. STATUS UPDATE FORM
    ══════════════════════════════════════════════════ */
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
        const form      = $(this);
        const submitBtn = $('#saveOrderStatusBtn');
        const url       = form.data('action-url');

        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving…');

        $.ajax({
            url, type: 'POST', data: form.serialize(),
            success: function (res) {
                submitBtn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Changes');
                if (res.success) {
                    $('#orderDetailsModal').modal('hide');
                    table.draw(false);
                    refreshCounts();
                    if (typeof toastr !== 'undefined') toastr.success(res.message);
                }
            },
            error: function (xhr) {
                submitBtn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Changes');
                alert(xhr.responseJSON?.message || 'Failed to update status.');
            }
        });
    });

    /* ══════════════════════════════════════════════════
       6. REFRESH BUTTON COUNTS
    ══════════════════════════════════════════════════ */
    function refreshCounts() {
        $.get('{{ route('orders.index') }}', { counts_only: 1 }, function (res) {
            if (!res.counts) return;
            const c = res.counts;
            $('#count-all').text(c.all       ?? 0);
            $('#count-pending').text(c.pending   ?? 0);
            $('#count-confirmed').text(c.confirmed ?? 0);
            $('#count-completed').text(c.completed ?? 0);
            $('#count-canceled').text(c.canceled  ?? 0);
        });
    }
    window.refreshCounts = refreshCounts;

    /* ══════════════════════════════════════════════════
        8. REFRESH ON GLOBAL NEW-ORDER ALERT
    ══════════════════════════════════════════════════ */
    window.addEventListener('dd:new-order', function () {
        table.draw(false);
        refreshCounts();
    });

    /* ══════════════════════════════════════════════════
        9. TOP CUSTOMERS — month filter leaderboard
    ══════════════════════════════════════════════════ */
    let selectedTopMonth = '';

    $(document).on('click', '.top-customer-month', function () {
        selectedTopMonth = String($(this).data('month') || '');

        $('#topCustomerBtnLabel').text(
            selectedTopMonth ? moment(selectedTopMonth, 'YYYY-MM').format('MMM YYYY') : 'Top Customer'
        );

        $('.top-customer-menu .top-customer-month').removeClass('active');
        if (selectedTopMonth) {
            $('.top-customer-menu .top-customer-month[data-month="' + selectedTopMonth + '"]').addClass('active');
        }

        loadTopCustomers();
    });

    function loadTopCustomers() {
        const $panel = $('#topCustomersPanel');

        if (!selectedTopMonth) {
            $panel.addClass('d-none').empty();
            return;
        }

        $panel.removeClass('d-none').html(
            '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div>' +
            '<p class="mt-2 mb-0 text-muted small">Loading top customers…</p></div>'
        );

        $.get('{{ route('orders.index') }}', { top_customers: 1, month: selectedTopMonth }, function (res) {
            $panel.html(res.html || '');
        }).fail(function () {
            $panel.html('<div class="alert alert-danger m-0">Failed to load top customers.</div>');
        });
    }

    $(document).on('click', '#topCustomersClose', function () {
        selectedTopMonth = '';
        $('#topCustomerBtnLabel').text('Top Customer');
        $('.top-customer-menu .top-customer-month').removeClass('active');
        $('#topCustomersPanel').addClass('d-none').empty();
    });

});
</script>
<script src="{{ asset('assets/js/backend-order-items.js') }}"></script>
@endpush
