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

    /* ─── Toast ─── */
    #newOrderToast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 9999;
        min-width: 300px;
        border-radius: 10px;
        overflow: hidden;
    }

    /* ─── Sound toggle ─── */
    #soundToggleBtn {
        position: fixed;
        bottom: 24px;
        left: 24px;
        z-index: 9999;
        border-radius: 50%;
        width: 46px;
        height: 46px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        box-shadow: 0 3px 12px rgba(0,0,0,.2);
    }

    /* ─── Hide DataTable's own search box ─── */
    .dataTables_wrapper .dataTables_filter { display: none; }
</style>
@endpush

@section('content')
    <x-breadcrumb></x-breadcrumb>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">

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

                        {{-- Right: Date picker + Search (same pill height) --}}
                        <div class="d-flex flex-wrap align-items-center gap-2">

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
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered yajra-datatable w-100 align-middle">
                            <thead class="table-dark">
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

    {{-- ══ Order Details Modal ══ --}}
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-file-invoice me-2"></i>Order Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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

    {{-- ══ New-order toast ══ --}}
    <div id="newOrderToast" class="toast align-items-center border-0 shadow"
         role="alert" aria-live="assertive" aria-atomic="true"
         data-bs-autohide="true" data-bs-delay="7000"
         style="background:linear-gradient(135deg,#1abc9c,#16a085);color:#fff;">
        <div class="d-flex">
            <div class="toast-body">
                <span style="font-size:1.3rem;">🛎️</span>
                <strong class="ms-1">New Order!</strong>
                <div class="mt-1 small" id="newOrderCount"></div>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>

    {{-- ══ Sound toggle ══ --}}
    <button id="soundToggleBtn" class="btn btn-secondary" title="Toggle new order notification sound">
        <i class="fas fa-bell" id="soundIcon"></i>
    </button>
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
            { data: 'date',           name: 'created_at' },
            { data: 'action',         name: 'action',             orderable: false, searchable: false }
        ],
        order: [[9, 'desc']],

        rowCallback: function (row, data) {
            if (parseInt(data.is_new) === 1) {
                $(row).addClass('row-new-order');
            } else {
                $(row).removeClass('row-new-order');
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

    /* ══════════════════════════════════════════════════
       7. NEW ORDER POLLING + SOUND  (every 10 s)
    ══════════════════════════════════════════════════ */
    let soundEnabled = true;
    let lastKnownId  = 0;

    $.get('{{ route('orders.latestId') }}', function (res) {
        lastKnownId = res.latest_id || 0;
    });

    function playBeep() {
        try {
            // Use a simple beep sound (base64 encoded WAV file - short beep)
            const beepSound = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAAB9AAACABAAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj==');
            beepSound.volume = 0.7;
            beepSound.play().catch(err => {
                console.log('Beep sound playback error (trying fallback):', err);
                // Fallback: Try Web Audio API
                try {
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    if (AudioContext) {
                        const ctx = new AudioContext();
                        if (ctx.state === 'suspended') {
                            ctx.resume();
                        }
                        const gain = ctx.createGain();
                        gain.gain.setValueAtTime(0.3, ctx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.5);
                        gain.connect(ctx.destination);
                        
                        const osc = ctx.createOscillator();
                        osc.frequency.setValueAtTime(1000, ctx.currentTime);
                        osc.connect(gain);
                        osc.start(ctx.currentTime);
                        osc.stop(ctx.currentTime + 0.3);
                    }
                } catch (e) {
                    console.warn('Web Audio fallback also failed:', e);
                }
            });
        } catch (e) {
            console.warn('Beep sound error:', e);
        }
    }

    setInterval(function () {
        $.get('{{ route('orders.latestId') }}', function (res) {
            const newId = res.latest_id || 0;
            if (lastKnownId > 0 && newId > lastKnownId) {
                const diff = newId - lastKnownId;
                if (soundEnabled) playBeep();
                $('#newOrderCount').text(diff + ' new order' + (diff > 1 ? 's' : '') + ' just arrived.');
                new bootstrap.Toast(document.getElementById('newOrderToast')).show();
                table.draw(false);
                refreshCounts();
            }
            lastKnownId = newId;
        });
    }, 3000);  // Check every 3 seconds instead of 10

    /* ══════════════════════════════════════════════════
       8. SOUND TOGGLE
    ══════════════════════════════════════════════════ */
    $('#soundToggleBtn').on('click', function () {
        soundEnabled = !soundEnabled;
        $('#soundIcon')
            .toggleClass('fa-bell',      soundEnabled)
            .toggleClass('fa-bell-slash', !soundEnabled);
        $(this).toggleClass('btn-secondary', soundEnabled).toggleClass('btn-danger', !soundEnabled);
        toastr.info('Order sound ' + (soundEnabled ? 'on' : 'off'));
    });

});
</script>
@endpush
