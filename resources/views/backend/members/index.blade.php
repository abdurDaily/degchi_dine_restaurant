@extends('layouts.dashboard')
@section('title', 'Members')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

@section('content')
    <x-breadcrumb></x-breadcrumb>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h4 class="card-title mb-0">Members</h4>
                    </div>
                    <form method="GET" action="{{ route('members.index') }}" id="membersFilterForm" class="row g-2 align-items-center">
                        <div class="col-auto">
                            <select name="status" class="form-select form-select-sm members-filter-select" style="min-width: 130px;">
                                <option value="active" @selected(($statusFilter ?? 'active') === 'active')>Active</option>
                                <option value="pending" @selected(($statusFilter ?? '') === 'pending')>Pending</option>
                                <option value="suspended" @selected(($statusFilter ?? '') === 'suspended')>Suspended</option>
                                <option value="all" @selected(($statusFilter ?? '') === 'all')>All Status</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <select name="type" class="form-select form-select-sm members-filter-select" style="min-width: 140px;">
                                <option value="all" @selected(($typeFilter ?? 'all') === 'all')>All Type</option>
                                <option value="membership" @selected(($typeFilter ?? '') === 'membership')>Membership</option>
                                <option value="golden" @selected(($typeFilter ?? '') === 'golden')>Golden</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <select name="student" class="form-select form-select-sm members-filter-select" style="min-width: 140px;">
                                <option value="all" @selected(($studentFilter ?? 'all') === 'all')>All Members</option>
                                <option value="yes" @selected(($studentFilter ?? '') === 'yes')>Student</option>
                                <option value="no" @selected(($studentFilter ?? '') === 'no')>Non-Student</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <select name="approval" class="form-select form-select-sm members-filter-select" style="min-width: 150px;">
                                <option value="all" @selected(($approvalFilter ?? 'all') === 'all')>All Approvals</option>
                                <option value="pending" @selected(($approvalFilter ?? '') === 'pending')>Student Pending</option>
                                <option value="approved" @selected(($approvalFilter ?? '') === 'approved')>Student Approved</option>
                                <option value="rejected" @selected(($approvalFilter ?? '') === 'rejected')>Student Rejected</option>
                            </select>
                        </div>
                        <div class="col">
                            <input type="search" name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm" placeholder="Search card, name, phone, email" />
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary btn-sm" type="submit">Search</button>
                        </div>
                        <div class="col-auto ms-auto">
                            <a href="{{ route('members.index', ['status' => 'active', 'type' => 'all', 'student' => 'all', 'approval' => 'all']) }}" class="btn btn-outline-secondary btn-sm">Clear filter</a>
                        </div>
                    </form>
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
                                            <select
                                                class="form-select form-select-sm member-status-select"
                                                data-id="{{ $member->id }}"
                                                data-url="{{ route('members.updateStatus', $member->id) }}"
                                                style="min-width: 120px;"
                                            >
                                                <option value="pending" @selected($member->status === 'pending')>Pending</option>
                                                <option value="active" @selected($member->status === 'active')>Active</option>
                                                <option value="suspended" @selected($member->status === 'suspended')>Suspended</option>
                                            </select>
                                            <span id="status-badge-{{ $member->id }}" class="badge mt-1 bg-{{ $member->status === 'active' ? 'success' : ($member->status === 'suspended' ? 'danger' : 'secondary') }}">
                                                {{ ucfirst($member->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary view-member-btn" data-id="{{ $member->id }}" data-url="{{ route('members.show', $member->id) }}" title="View Details" data-bs-toggle="tooltip">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            @if($member->type !== 'golden')
                                                <button type="button" class="btn btn-sm btn-outline-warning upgrade-golden-btn" data-id="{{ $member->id }}" data-url="{{ route('members.upgradeGolden', $member->id) }}" title="Upgrade to Golden (10% every order)" data-bs-toggle="tooltip">
                                                    <i class="ri-vip-crown-line"></i>
                                                </button>
                                            @endif
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
                    <div class="ms-auto d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-warning btn-sm d-none" id="modalUpgradeGoldenBtn">
                            <i class="ri-vip-crown-fill me-1"></i> Upgrade to Golden
                        </button>
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
    var currentModalMember = null;

    // Auto-apply filters when a select changes
    $(document).on('change', '.members-filter-select', function () {
        $('#membersFilterForm').trigger('submit');
    });

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
                currentModalMember = m;
                var typeBadge = m.type === 'golden'
                    ? '<span class="badge bg-warning text-dark"><i class="ri-vip-crown-fill me-1"></i>Golden</span>'
                    : '<span class="badge bg-info">Membership</span>';
                var statusBadge = '<span class="badge bg-' + (m.status === 'active' ? 'success' : (m.status === 'suspended' ? 'danger' : 'secondary')) + '">' + m.status.charAt(0).toUpperCase() + m.status.slice(1) + '</span>';
                var statusSelectHtml =
                    '<select class="form-select form-select-sm member-status-select mt-1" data-id="' + m.id + '" data-url="{{ url("/members") }}/' + m.id + '/update-status" style="max-width:160px;">' +
                        '<option value="pending"' + (m.status === 'pending' ? ' selected' : '') + '>Pending</option>' +
                        '<option value="active"' + (m.status === 'active' ? ' selected' : '') + '>Active</option>' +
                        '<option value="suspended"' + (m.status === 'suspended' ? ' selected' : '') + '>Suspended</option>' +
                    '</select>';
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
                updateModalGoldenUpgrade(m);

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
                            '<tr><th>Status</th><td><div id="modalStatusBadgeWrap">' + statusBadge + '</div>' + statusSelectHtml + '</td></tr>' +
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
                $('#memberDetailBody .member-status-select').each(function () {
                    $(this).data('previous', $(this).val());
                });
            }
        }).fail(function () {
            $('#memberDetailBody').html('<div class="alert alert-danger">Failed to load member details.</div>');
        });
    });

    // ---- Update Status (pending / active / suspended) ----
    function statusBadgeClass(status) {
        if (status === 'active') return 'bg-success';
        if (status === 'suspended') return 'bg-danger';
        return 'bg-secondary';
    }

    function updateStatusCounts(counts) {
        if (!counts) return;
        $('#count-status-pending').text(counts.pending ?? 0);
        $('#count-status-active').text(counts.active ?? 0);
        $('#count-status-suspended').text(counts.suspended ?? 0);
    }

    $(document).on('change', '.member-status-select', function () {
        var select = $(this);
        var memberId = select.data('id');
        var url = select.data('url');
        var newStatus = select.val();
        var previous = select.data('previous');

        if (!confirm('Change this member\'s status to "' + newStatus + '"?')) {
            select.val(previous);
            return;
        }

        select.prop('disabled', true);

        $.ajax({
            url: url,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            data: { status: newStatus },
            success: function (res) {
                select.prop('disabled', false);
                if (res.success) {
                    select.data('previous', res.new_status);
                    $('#status-badge-' + memberId)
                        .removeClass('bg-success bg-danger bg-secondary')
                        .addClass(statusBadgeClass(res.new_status))
                        .text(res.new_status.charAt(0).toUpperCase() + res.new_status.slice(1));

                    // Keep other selects for same member in sync
                    $('.member-status-select[data-id="' + memberId + '"]').val(res.new_status).data('previous', res.new_status);

                    var modalBadge = $('#modalStatusBadgeWrap .badge');
                    if (modalBadge.length) {
                        modalBadge
                            .removeClass('bg-success bg-danger bg-secondary')
                            .addClass(statusBadgeClass(res.new_status))
                            .text(res.new_status.charAt(0).toUpperCase() + res.new_status.slice(1));
                    }

                    updateStatusCounts(res.counts);
                    toastr ? toastr.success(res.message) : alert(res.message);
                }
            },
            error: function (xhr) {
                select.prop('disabled', false);
                select.val(previous);
                var errorMsg = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'Failed to update status.';
                toastr ? toastr.error(errorMsg) : alert(errorMsg);
            }
        });
    });

    // Store initial status for cancel/revert
    $('.member-status-select').each(function () {
        $(this).data('previous', $(this).val());
    });

    // ---- Toggle Status (legacy) ----
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
                    $('#status-badge-' + memberId)
                        .removeClass('bg-success bg-danger bg-secondary')
                        .addClass(statusBadgeClass(res.new_status))
                        .text(res.new_status.charAt(0).toUpperCase() + res.new_status.slice(1));
                    $('.member-status-select[data-id="' + memberId + '"]').val(res.new_status).data('previous', res.new_status);
                    updateStatusCounts(res.counts);
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

    function updateModalGoldenUpgrade(member) {
        var footer = $('#memberApprovalFooter');
        var upgradeBtn = $('#modalUpgradeGoldenBtn');

        footer.removeClass('d-none');
        upgradeBtn.data('id', member.id).data('url', '{{ url("/members") }}/' + member.id + '/upgrade-golden');

        if (member.type === 'golden') {
            upgradeBtn.addClass('d-none');
            return;
        }

        upgradeBtn.removeClass('d-none');
    }

    function updateMemberTypeBadge(memberId) {
        var row = $('#member-row-' + memberId);
        if (!row.length) return;

        row.find('td').eq(5).html('<span class="badge bg-warning text-dark"><i class="ri-vip-crown-fill me-1"></i>Golden</span>');
        row.find('.upgrade-golden-btn').remove();
    }

    function updateModalApprovalActions(member) {
        var footer = $('#memberApprovalFooter');
        var approveBtn = $('#modalApproveBtn');
        var rejectBtn = $('#modalRejectBtn');
        var statusBadge = $('#modalApprovalStatusBadge');

        if (!member.is_student) {
            $('#modalApproveBtn, #modalRejectBtn').addClass('d-none');
            $('#modalApprovalStatusBadge').empty();
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

    // ---- Upgrade to Golden ----
    function handleGoldenUpgrade(url, memberId, onSuccess) {
        if (!confirm('Upgrade this member to Golden Card? They will receive 10% off every order for 5 years.')) {
            return;
        }

        $.ajax({
            url: url,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function (res) {
                if (res.success) {
                    updateMemberTypeBadge(memberId);
                    if (typeof onSuccess === 'function') {
                        onSuccess(res);
                    }
                    toastr ? toastr.success(res.message) : alert(res.message);
                }
            },
            error: function (xhr) {
                var errorMsg = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'Failed to upgrade member.';
                toastr ? toastr.error(errorMsg) : alert(errorMsg);
            }
        });
    }

    $(document).on('click', '.upgrade-golden-btn', function () {
        var btn = $(this);
        handleGoldenUpgrade(btn.data('url'), btn.data('id'));
    });

    $('#modalUpgradeGoldenBtn').on('click', function () {
        var btn = $(this);
        handleGoldenUpgrade(btn.data('url'), btn.data('id'), function (res) {
            if (currentModalMember) {
                currentModalMember.type = 'golden';
                currentModalMember.expires_at = res.expires_at || currentModalMember.expires_at;
                updateModalGoldenUpgrade(currentModalMember);
            }

            var typeCell = $('#memberDetailBody').find('tr').filter(function () {
                return $(this).find('th').text() === 'Type';
            }).find('td');
            typeCell.html('<span class="badge bg-warning text-dark"><i class="ri-vip-crown-fill me-1"></i>Golden</span>');

            if (res.expires_at) {
                var expiresRow = $('#memberDetailBody').find('tr').filter(function () {
                    return $(this).find('th').text() === 'Expires At';
                }).find('td');
                expiresRow.text(res.expires_at);
            }
        });
    });

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
