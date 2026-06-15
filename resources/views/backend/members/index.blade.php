@extends('dashboard')
@section('title', 'Members')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

@section('content')
    <x-breadcrumb></x-breadcrumb>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Members</h4>
                    <form method="GET" class="d-flex align-items-center" action="{{ route('members.index') }}">
                        @if(request('approval'))
                            <input type="hidden" name="approval" value="{{ request('approval') }}" />
                        @endif
                        <input type="search" name="search" value="{{ $search ?? '' }}" class="form-control me-2" placeholder="Search by card number, name, phone" style="width: 300px;" />
                        <button class="btn btn-primary" type="submit">Search</button>
                    </form>
                </div>

                <!-- Student Approval Filter Buttons -->
                <div class="card-body border-bottom py-3">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <span class="text-muted me-1" style="font-size: 0.85rem;">Filter student approvals:</span>
                        @php
                            $filterParams = fn ($status) => array_filter([
                                'approval' => $status,
                                'search' => $search ?? null,
                            ]);
                        @endphp
                        <a href="{{ route('members.index', $filterParams('pending')) }}" class="btn btn-sm {{ request('approval') === 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                            <i class="ri-time-line me-1"></i> Pending <span class="badge bg-white text-warning ms-1" id="count-pending">{{ $pendingCount }}</span>
                        </a>
                        <a href="{{ route('members.index', $filterParams('approved')) }}" class="btn btn-sm {{ request('approval') === 'approved' ? 'btn-success' : 'btn-outline-success' }}">
                            <i class="ri-check-line me-1"></i> Approved <span class="badge bg-white text-success ms-1" id="count-approved">{{ $approvedCount }}</span>
                        </a>
                        <a href="{{ route('members.index', $filterParams('rejected')) }}" class="btn btn-sm {{ request('approval') === 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
                            <i class="ri-close-line me-1"></i> Rejected <span class="badge bg-white text-danger ms-1" id="count-rejected">{{ $rejectedCount }}</span>
                        </a>
                        @if(request('approval'))
                            <a href="{{ route('members.index', array_filter(['search' => $search ?? null])) }}" class="btn btn-sm btn-link text-muted">Clear filter</a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Card Number</th>
                                    <th>Type</th>
                                    <th>Student</th>
                                    <th>Total Purchase</th>
                                    <th>Status</th>
                                    <th><i class="ri-more-fill"></i> Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($members as $member)
                                    <tr id="member-row-{{ $member->id }}">
                                        <td>{{ $member->id }}</td>
                                        <td>
                                            @if($member->profile_image_path)
                                                <img src="{{ asset('storage/' . $member->profile_image_path) }}" alt="{{ $member->name }}" class="rounded-circle" style="width:38px;height:38px;object-fit:cover;border:2px solid #e2e8f0;">
                                            @else
                                                <span class="avatar-sm rounded-circle bg-light d-inline-flex align-items-center justify-content-center" style="width:38px;height:38px;"><i class="ri-user-line text-muted fs-16"></i></span>
                                            @endif
                                        </td>
                                        <td>{{ $member->name }}</td>
                                        <td>{{ $member->phone }}</td>
                                        <td><code>{{ $member->unique_card_number }}</code></td>
                                        <td>
                                            @if($member->type === 'golden')
                                                <span class="badge bg-warning text-dark"><i class="ri-vip-crown-fill me-1"></i>Golden</span>
                                            @else
                                                <span class="badge bg-info">Membership</span>
                                            @endif
                                        </td>
<td>
    <span class="badge bg-{{ $member->is_student ? 'success' : 'secondary' }}">
        {{ $member->is_student ? 'Student (35% first-order)' : 'Non-Student (30% first-order)' }}
    </span>
</td>
<td>
    <span id="purchase-{{ $member->id }}">৳ {{ number_format($member->computed_total_purchase ?? 0, 2) }}</span>
    <small class="text-muted d-block">{{ $member->orders_count }} order{{ $member->orders_count !== 1 ? 's' : '' }}</small>
</td>
                                        <td>
                                            <span id="status-badge-{{ $member->id }}" class="badge bg-{{ $member->status === 'active' ? 'success' : ($member->status === 'suspended' ? 'danger' : 'secondary') }}">
                                                {{ ucfirst($member->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary view-member-btn" data-id="{{ $member->id }}" data-url="{{ route('members.show', $member->id) }}" title="View Details" data-bs-toggle="tooltip">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No members found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $members->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Member Detail Modal --}}
    <div class="modal fade" id="memberDetailModal" tabindex="-1" aria-labelledby="memberDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="memberDetailModalLabel">Member Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="memberDetailBody">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-none" id="memberApprovalFooter">
                    <span id="modalApprovalStatusBadge"></span>
                    <div class="ms-auto d-flex gap-2">
                        <button type="button" class="btn btn-success btn-sm d-none" id="modalApproveBtn">
                            <i class="ri-check-line me-1"></i> Approve
                        </button>
                        <button type="button" class="btn btn-danger btn-sm d-none" id="modalRejectBtn">
                            <i class="ri-close-line me-1"></i> Reject
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
$(function () {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    // ---- View Member Detail ----
    $(document).on('click', '.view-member-btn', function () {
        var url = $(this).data('url');
        var modal = new bootstrap.Modal(document.getElementById('memberDetailModal'));
        $('#memberDetailBody').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        $('#memberApprovalFooter').addClass('d-none');
        modal.show();

        $.getJSON(url, function (res) {
            if (res.success) {
                var m = res.member;
                var typeBadge = m.type === 'golden'
                    ? '<span class="badge bg-warning text-dark"><i class="ri-vip-crown-fill me-1"></i>Golden</span>'
                    : '<span class="badge bg-info">Membership</span>';
                var statusBadge = '<span class="badge bg-' + (m.status === 'active' ? 'success' : 'danger') + '">' + m.status.charAt(0).toUpperCase() + m.status.slice(1) + '</span>';
                var studentBadge = m.is_student
                    ? '<span class="badge bg-success"><i class="ri-graduation-cap-line me-1"></i>Student (35% first-order)</span>'
                    : '<span class="badge bg-secondary">Non-Student (30% first-order)</span>';

                var approvalBadgeHtml = '';
                if (m.is_student) {
                    if (m.approval_status === 'approved') {
                        approvalBadgeHtml = '<span id="modalApprovalBodyBadge" class="badge bg-success"><i class="ri-check-line me-1"></i>Approved</span>';
                    } else if (m.approval_status === 'rejected') {
                        approvalBadgeHtml = '<span id="modalApprovalBodyBadge" class="badge bg-danger"><i class="ri-close-line me-1"></i>Rejected</span>';
                    } else {
                        approvalBadgeHtml = '<span id="modalApprovalBodyBadge" class="badge bg-warning"><i class="ri-time-line me-1"></i>Pending</span>';
                    }
                }

                updateModalApprovalActions(m);

                var profileImageHtml = '';
                if (m.profile_image_url) {
                    profileImageHtml = '<div class="text-center mb-3"><img src="' + m.profile_image_url + '" alt="' + m.name + '" class="rounded-circle" style="width:80px;height:80px;object-fit:cover;border:3px solid #e2e8f0;"></div>';
                }

                var studentCardHtml = '';
                if (m.student_card_url) {
                    // Check if it's a PDF file
                    if (m.student_card_url.toLowerCase().endsWith('.pdf')) {
                        studentCardHtml = '<div class="mt-3"><h6 class="fw-bold">Student Card (PDF)</h6>' +
                            '<a href="' + m.student_card_url + '" target="_blank" class="btn btn-primary btn-sm">' +
                            '<i class="ri-file-pdf-line me-1"></i> View PDF Document</a></div>';
                    } else {
                        // Image file - show thumbnail
                        studentCardHtml = '<div class="mt-3"><h6 class="fw-bold">Student Card</h6>' +
                            '<a href="' + m.student_card_url + '" target="_blank">' +
                            '<img src="' + m.student_card_url + '" alt="Student Card" class="img-fluid rounded border" style="max-height:200px;"></a></div>';
                    }
                }

                var ordersHtml = '';
                if (m.recent_orders && m.recent_orders.length > 0) {
                    ordersHtml = '<div class="mt-3"><h6 class="fw-bold">Recent Orders</h6><table class="table table-sm table-bordered"><thead><tr><th>#</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead><tbody>';
                    $.each(m.recent_orders, function (i, o) {
                        var sBadge = o.status === 'completed' ? 'success' : (o.status === 'confirmed' ? 'info' : (o.status === 'canceled' ? 'danger' : 'warning'));
                        ordersHtml += '<tr><td>' + o.id + '</td><td>৳ ' + o.final_amount + '</td><td><span class="badge bg-' + sBadge + '">' + o.status.charAt(0).toUpperCase() + o.status.slice(1) + '</span></td><td>' + o.date + '</td></tr>';
                    });
                    ordersHtml += '</tbody></table></div>';
                }

                var html = profileImageHtml +
                    '<div class="row">' +
                    '<div class="col-md-6">' +
                        '<table class="table table-borderless mb-0">' +
                            '<tr><th width="40%">Name</th><td>' + m.name + '</td></tr>' +
                            '<tr><th>Phone</th><td>' + m.phone + '</td></tr>' +
                            '<tr><th>Email</th><td>' + (m.email || '<span class="text-muted">N/A</span>') + '</td></tr>' +
                            '<tr><th>Card Number</th><td><code>' + m.unique_card_number + '</code></td></tr>' +
                            '<tr><th>Type</th><td>' + typeBadge + '</td></tr>' +
                            '<tr><th>Status</th><td>' + statusBadge + '</td></tr>' +
                            '<tr><th>Student</th><td>' + studentBadge + '</td></tr>' +
                            (approvalBadgeHtml ? '<tr><th>Approval</th><td>' + approvalBadgeHtml + '</td></tr>' : '') +
                        '</table>' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<table class="table table-borderless mb-0">' +
                            '<tr><th width="40%">Date of Birth</th><td>' + (m.dob || '<span class="text-muted">N/A</span>') + '</td></tr>' +
                            '<tr><th>Marriage Date</th><td>' + (m.marriage_date || '<span class="text-muted">N/A</span>') + '</td></tr>' +
                            '<tr><th>Address</th><td>' + (m.address || '<span class="text-muted">N/A</span>') + '</td></tr>' +
                            '<tr><th>Total Purchase</th><td><strong class="text-success">৳ ' + m.total_purchase.toFixed(2) + '</strong></td></tr>' +
                            '<tr><th>Total Orders</th><td>' + m.orders_count + '</td></tr>' +
                            '<tr><th>Discount Used</th><td>' + (m.first_order_discount_used ? '<span class="badge bg-secondary">Used</span>' : '<span class="badge bg-primary">Available</span>') + '</td></tr>' +
                            '<tr><th>Expires At</th><td>' + (m.expires_at || '<span class="text-muted">N/A</span>') + '</td></tr>' +
                        '</table>' +
                    '</div>' +
                '</div>' +
                studentCardHtml +
                ordersHtml;

                $('#memberDetailBody').html(html);
            }
        }).fail(function () {
            $('#memberDetailBody').html('<div class="alert alert-danger">Failed to load member details.</div>');
        });
    });

    // ---- Toggle Status ----
    $(document).on('click', '.toggle-status-btn', function () {
        var btn = $(this);
        var memberId = btn.data('id');
        var url = btn.data('url');

        if (!confirm('Are you sure you want to change this member\'s status?')) return;

        $.ajax({
            url: url,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function (res) {
                if (res.success) {
                    var isActive = res.new_status === 'active';
                    // Update status badge
                    $('#status-badge-' + memberId)
                        .removeClass('bg-success bg-danger bg-secondary')
                        .addClass(isActive ? 'bg-success' : 'bg-danger')
                        .text(res.new_status.charAt(0).toUpperCase() + res.new_status.slice(1));
                    // Update toggle button
                    btn.removeClass('btn-outline-warning btn-outline-success')
                        .addClass(isActive ? 'btn-outline-warning' : 'btn-outline-success')
                        .attr('title', isActive ? 'Suspend' : 'Activate')
                        .html('<i class="ri-' + (isActive ? 'forbid-line' : 'checkbox-circle-line') + '"></i>');

                    toastr ? toastr.success(res.message) : alert(res.message);
                }
            },
            error: function () {
                toastr ? toastr.error('Failed to toggle status.') : alert('Failed to toggle status.');
            }
        });
    });

    // ---- Sync Purchase ----
    $(document).on('click', '.sync-purchase-btn', function () {
        var btn = $(this);
        var memberId = btn.data('id');
        var url = btn.data('url');
        btn.prop('disabled', true).find('i').addClass('fa-spin');

        $.ajax({
            url: url,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function (res) {
                btn.prop('disabled', false).find('i').removeClass('fa-spin');
                if (res.success) {
                    $('#purchase-' + memberId).text('৳ ' + res.total_purchase.toFixed(2));
                    toastr ? toastr.success(res.message) : alert(res.message);
                }
            },
            error: function () {
                btn.prop('disabled', false).find('i').removeClass('fa-spin');
                toastr ? toastr.error('Failed to sync purchase.') : alert('Failed to sync purchase.');
            }
        });
    });

    function updateModalApprovalActions(member) {
        var footer = $('#memberApprovalFooter');
        var approveBtn = $('#modalApproveBtn');
        var rejectBtn = $('#modalRejectBtn');
        var statusBadge = $('#modalApprovalStatusBadge');

        if (!member.is_student) {
            footer.addClass('d-none');
            return;
        }

        footer.removeClass('d-none');
        approveBtn.data('id', member.id).data('url', '{{ url("/members") }}/' + member.id + '/approve');
        rejectBtn.data('id', member.id).data('url', '{{ url("/members") }}/' + member.id + '/reject');

        var badgeClass = 'bg-warning';
        var badgeText = 'Pending';
        var badgeIcon = 'ri-time-line';

        if (member.approval_status === 'approved') {
            badgeClass = 'bg-success';
            badgeText = 'Approved';
            badgeIcon = 'ri-check-line';
        } else if (member.approval_status === 'rejected') {
            badgeClass = 'bg-danger';
            badgeText = 'Rejected';
            badgeIcon = 'ri-close-line';
        }

        statusBadge.html('<span class="badge ' + badgeClass + '"><i class="' + badgeIcon + ' me-1"></i>' + badgeText + '</span>');

        var bodyBadge = $('#modalApprovalBodyBadge');
        if (bodyBadge.length) {
            bodyBadge.removeClass('bg-warning bg-success bg-danger')
                .addClass(badgeClass)
                .html('<i class="' + badgeIcon + ' me-1"></i>' + badgeText);
        }

        approveBtn.toggleClass('d-none', member.approval_status === 'approved').prop('disabled', member.approval_status === 'approved');
        rejectBtn.toggleClass('d-none', member.approval_status === 'rejected').prop('disabled', member.approval_status === 'rejected');
    }

    function updateFilterCounts(counts) {
        if (!counts) return;
        $('#count-pending').text(counts.pending ?? 0);
        $('#count-approved').text(counts.approved ?? 0);
        $('#count-rejected').text(counts.rejected ?? 0);
    }

    // ---- Approve Student Member ----
    $('#modalApproveBtn').on('click', function () {
        var btn = $(this);
        var memberId = btn.data('id');
        var url = btn.data('url');

        btn.prop('disabled', true);

        $.ajax({
            url: url,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function (res) {
                if (res.success) {
                    updateModalApprovalActions({ id: memberId, is_student: true, approval_status: 'approved' });
                    updateFilterCounts(res.counts);
                    toastr ? toastr.success(res.message) : alert(res.message);
                }
                btn.prop('disabled', false);
            },
            error: function (xhr) {
                btn.prop('disabled', false);
                var errorMsg = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'Failed to approve member.';
                toastr ? toastr.error(errorMsg) : alert(errorMsg);
            }
        });
    });

    // ---- Reject Student Member ----
    $('#modalRejectBtn').on('click', function () {
        var btn = $(this);
        var memberId = btn.data('id');
        var url = btn.data('url');

        btn.prop('disabled', true);

        $.ajax({
            url: url,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function (res) {
                if (res.success) {
                    updateModalApprovalActions({ id: memberId, is_student: true, approval_status: 'rejected' });
                    updateFilterCounts(res.counts);
                    toastr ? toastr.success(res.message) : alert(res.message);
                }
                btn.prop('disabled', false);
            },
            error: function (xhr) {
                btn.prop('disabled', false);
                var errorMsg = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'Failed to reject member.';
                toastr ? toastr.error(errorMsg) : alert(errorMsg);
            }
        });
    });
});
</script>
@endpush
