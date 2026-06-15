@extends('components.admin-master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Reviews Management</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Reviews</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple Stats Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Reviews -->
        <div class="col-xl-3 col-sm-6">
            <div class="card simple-stat-card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-label mb-2">Total Reviews</p>
                            <h2 class="stat-number">{{ $reviews->total() }}</h2>
                        </div>
                        <div class="stat-icon primary">
                            <i class="ri-chat-3-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="col-xl-3 col-sm-6">
            <div class="card simple-stat-card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-label mb-2">Pending</p>
                            <h2 class="stat-number">{{ $pending }}</h2>
                        </div>
                        <div class="stat-icon warning">
                            <i class="ri-time-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approved -->
        <div class="col-xl-3 col-sm-6">
            <div class="card simple-stat-card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-label mb-2">Approved</p>
                            <h2 class="stat-number">{{ $approved }}</h2>
                        </div>
                        <div class="stat-icon success">
                            <i class="ri-check-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rejected -->
        <div class="col-xl-3 col-sm-6">
            <div class="card simple-stat-card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-label mb-2">Rejected</p>
                            <h2 class="stat-number">{{ $rejected }}</h2>
                        </div>
                        <div class="stat-icon danger">
                            <i class="ri-close-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">All Reviews</h5>
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="Search by name, email, or comment...">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="reviewsTable" class="table reviews-datatable table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Rating</th>
                                    <th>Title</th>
                                    <th>Comment</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center p-3">
                        <div id="paginationInfo" class="flex-grow-1"></div>
                    </div>
                    <ul id="reviewsPagination" class="pagination mb-3"></ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Review Modal -->
<div class="modal fade" id="viewReviewModal" tabindex="-1" role="dialog" aria-labelledby="viewReviewTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewReviewTitle">Review Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-3 text-center">
                        <img id="modalImage" src="" alt="Member Photo" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 1rem;" />
                    </div>
                    <div class="col-md-9">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Name:</strong>
                                <p id="modalName" class="text-muted"></p>
                            </div>
                            <div class="col-md-6">
                                <strong>Email:</strong>
                                <p id="modalEmail" class="text-muted"></p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Rating:</strong>
                                <p id="modalRating" class="text-muted"></p>
                            </div>
                            <div class="col-md-6">
                                <strong>Status:</strong>
                                <p id="modalStatus" class="text-muted"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Title:</strong>
                    <p id="modalTitle" class="text-muted"></p>
                </div>

                <div class="mb-3">
                    <strong>Comment:</strong>
                    <p id="modalComment" class="text-muted" style="line-height: 1.6;"></p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Submitted:</strong>
                        <p id="modalCreated" class="text-muted"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Approved:</strong>
                        <p id="modalApproved" class="text-muted"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="modalApproveBtn">Approve</button>
                <button type="button" class="btn btn-danger" id="modalRejectBtn">Reject</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.min.css">
<style>
    /* Simple & Light Color Design */
    :root {
        --primary: #5B5FFF;
        --warning: #FFA500;
        --success: #4CAF50;
        --danger: #FF4444;
        --light-bg: #F7F8FC;
        --card-bg: #FFFFFF;
        --text-dark: #2C2C2C;
        --text-muted: #7A8A99;
        --border: #E5E9F0;
    }

    .simple-stat-card {
        border: 1px solid var(--border);
        border-radius: 12px;
        background: var(--card-bg);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .simple-stat-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }

    .stat-label {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    .stat-number {
        font-size: 2.2rem;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
        line-height: 1.2;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        transition: all 0.3s ease;
    }

    .stat-icon.primary { background: rgba(91, 95, 255, 0.1); color: var(--primary); }
    .stat-icon.warning { background: rgba(255, 165, 0, 0.1); color: var(--warning); }
    .stat-icon.success { background: rgba(76, 175, 80, 0.1); color: var(--success); }
    .stat-icon.danger { background: rgba(255, 68, 68, 0.1); color: var(--danger); }

    .simple-stat-card:hover .stat-icon { transform: scale(1.1); }

    .table thead th {
        background: var(--light-bg);
        border: 1px solid var(--border);
        border-bottom: 2px solid var(--border);
        font-weight: 600;
        color: var(--text-dark);
        padding: 14px 12px;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table tbody td {
        vertical-align: middle;
        padding: 16px 12px;
        border-bottom: 1px solid var(--border);
        font-size: 0.95rem;
        color: var(--text-dark);
    }

    .table tbody tr:hover { background-color: var(--light-bg); }

    .badge { padding: 6px 12px; font-weight: 600; border-radius: 16px; font-size: 0.75rem; }
    .badge.bg-success { background-color: rgba(76, 175, 80, 0.15) !important; color: var(--success); }
    .badge.bg-warning { background-color: rgba(255, 165, 0, 0.15) !important; color: var(--warning); }
    .badge.bg-danger { background-color: rgba(255, 68, 68, 0.15) !important; color: var(--danger); }

    .btn-action {
        border: 1px solid var(--border);
        padding: 8px 12px;
        transition: all 0.2s ease;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        min-height: 38px;
    }

    .btn-view { color: var(--primary); border-color: var(--primary); }
    .btn-view:hover { background-color: rgba(91, 95, 255, 0.1); }

    .btn-approve { color: var(--success); border-color: var(--success); }
    .btn-approve:hover { background-color: rgba(76, 175, 80, 0.1); }

    .btn-delete { color: var(--danger); border-color: var(--danger); }
    .btn-delete:hover { background-color: rgba(255, 68, 68, 0.1); }

    .name-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid var(--border);
    }

    .rating-stars { display: flex; gap: 2px; font-size: 1.1rem; }
    .star-filled { color: #FFC107; }
    .star-empty { color: #DDD; }

    .email-link { color: var(--primary); text-decoration: none; }
    .email-link:hover { text-decoration: underline; }

    .search-box input {
        padding: 10px 14px;
        border: 1px solid var(--border);
        border-radius: 8px;
        background: var(--card-bg);
        color: var(--text-dark);
    }

    .search-box input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(91, 95, 255, 0.1);
    }

    #reviewsPagination {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 20px 0 0;
        justify-content: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    #reviewsPagination .page-link {
        padding: 8px 12px;
        border: 1px solid var(--border);
        color: var(--primary);
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
        background: var(--card-bg);
    }

    #reviewsPagination .page-link:hover {
        background-color: var(--light-bg);
        border-color: var(--primary);
    }

    #reviewsPagination .active .page-link {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
    }

    .modal-content {
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        border-bottom: 1px solid var(--border);
        background: var(--light-bg);
    }

    .modal-footer {
        border-top: 1px solid var(--border);
        background: var(--light-bg);
    }

    @media (max-width: 768px) {
        .stat-number { font-size: 1.8rem; }
        .table { font-size: 0.85rem; }
        .btn-action { padding: 6px 10px; min-width: 34px; min-height: 34px; }
        .name-avatar { width: 32px; height: 32px; }
    }

    @media (max-width: 576px) {
        .stat-number { font-size: 1.5rem; }
        .stat-label { font-size: 0.75rem; }
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let currentPage = 1;
    const perPage = 10;

    function loadReviews(page = 1, search = '') {
        currentPage = page;
        $.ajax({
            url: "{{ route('admin.reviews.index') }}",
            type: 'GET',
            data: { page: page, search: search },
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success && Array.isArray(response.data)) {
                    renderTable(response.data);
                    renderPagination(response.pagination);
                } else {
                    renderTable([]);
                }
            },
            error: function() {
                toastr.error('Error loading reviews');
            }
        });
    }

    function renderTable(data) {
        const tbody = $('#reviewsTable tbody');
        tbody.empty();

        if (data.length === 0) {
            tbody.html('<tr><td colspan="9"><div class="text-center py-5"><i class="ri-chat-off-line" style="font-size: 2rem; color: #ccc;"></i><p class="mt-2">No reviews found</p></div></td></tr>');
            return;
        }

        data.forEach(row => {
            const tr = $('<tr></tr>');
            tr.append(`<td><strong>${row.DT_RowIndex}</strong></td>`);
            tr.append(`<td>${row.name_with_image}</td>`);
            tr.append(`<td>${row.email_display}</td>`);
            tr.append(`<td><div class="rating-stars">${row.rating_display}</div></td>`);
            tr.append(`<td><span style="font-weight: 500;">${row.title}</span></td>`);
            tr.append(`<td><small style="color: #999;">${row.comment_preview}</small></td>`);
            tr.append(`<td>${row.status_badge}</td>`);
            tr.append(`<td><small>${row.created_at}</small></td>`);
            tr.append(`<td>${row.action}</td>`);
            tbody.append(tr);
        });
        bindActionHandlers();
    }

    function renderPagination(pagination) {
        const paginationContainer = $('#reviewsPagination');
        paginationContainer.empty();

        if (pagination.current_page > 1) {
            paginationContainer.append(`<li class="page-item"><a class="page-link" href="#" onclick="loadReviews(1, getSearchValue()); return false;">First</a></li>`);
            paginationContainer.append(`<li class="page-item"><a class="page-link" href="#" onclick="loadReviews(${pagination.current_page - 1}, getSearchValue()); return false;">Prev</a></li>`);
        }

        const maxPagesToShow = 5;
        let startPage = Math.max(1, pagination.current_page - Math.floor(maxPagesToShow / 2));
        let endPage = Math.min(pagination.total_pages, startPage + maxPagesToShow - 1);
        if (endPage - startPage < maxPagesToShow - 1) {
            startPage = Math.max(1, endPage - maxPagesToShow + 1);
        }

        for (let i = startPage; i <= endPage; i++) {
            const activeClass = i === pagination.current_page ? 'active' : '';
            paginationContainer.append(`<li class="page-item ${activeClass}"><a class="page-link" href="#" onclick="loadReviews(${i}, getSearchValue()); return false;">${i}</a></li>`);
        }

        if (pagination.current_page < pagination.total_pages) {
            paginationContainer.append(`<li class="page-item"><a class="page-link" href="#" onclick="loadReviews(${pagination.current_page + 1}, getSearchValue()); return false;">Next</a></li>`);
            paginationContainer.append(`<li class="page-item"><a class="page-link" href="#" onclick="loadReviews(${pagination.total_pages}, getSearchValue()); return false;">Last</a></li>`);
        }

        $('#paginationInfo').text(`Showing ${pagination.from} to ${pagination.to} of ${pagination.total} reviews`);
    }

    function getSearchValue() {
        return $('#searchInput').val();
    }

    function bindActionHandlers() {
        $(document).off('click', '.btn-view').on('click', '.btn-view', function() {
            const btn = $(this);
            $('#modalImage').attr('src', btn.data('image') || 'https://i.pravatar.cc/80?u=' + encodeURIComponent(btn.data('name')));
            $('#modalName').text(btn.data('name'));
            $('#modalEmail').text(btn.data('email'));
            $('#modalTitle').text(btn.data('title') || '-');
            $('#modalComment').text(btn.data('comment'));
            $('#modalCreated').text(btn.data('created'));
            $('#modalApproved').text(btn.data('approved') || 'Not yet approved');
            
            const rating = btn.data('rating');
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                stars += i <= rating ? '<i class="ri-star-fill star-filled"></i>' : '<i class="ri-star-line star-empty"></i>';
            }
            $('#modalRating').html(stars);
            
            const status = btn.data('status');
            let statusBadge = 'bg-warning';
            if (status === 'approved') statusBadge = 'bg-success';
            if (status === 'rejected') statusBadge = 'bg-danger';
            $('#modalStatus').html('<span class="badge ' + statusBadge + '">' + status.toUpperCase() + '</span>');
            
            $('#modalApproveBtn').data('id', btn.data('id'));
            $('#modalRejectBtn').data('id', btn.data('id'));
            $('#modalApproveBtn').prop('disabled', status === 'approved').css('opacity', status === 'approved' ? 0.6 : 1);
            $('#modalRejectBtn').prop('disabled', status === 'rejected').css('opacity', status === 'rejected' ? 0.6 : 1);
            
            new bootstrap.Modal(document.getElementById('viewReviewModal')).show();
        });

        $(document).off('click', '#modalApproveBtn').on('click', '#modalApproveBtn', function() {
            const id = $(this).data('id');
            $.ajax({
                url: '{{ route("admin.reviews.approve", ":id") }}'.replace(':id', id),
                type: 'POST',
                data: {_token: '{{ csrf_token() }}'},
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        bootstrap.Modal.getInstance(document.getElementById('viewReviewModal')).hide();
                        loadReviews(currentPage, getSearchValue());
                    }
                },
                error: function() { toastr.error('Failed to approve'); }
            });
        });

        $(document).off('click', '#modalRejectBtn').on('click', '#modalRejectBtn', function() {
            const id = $(this).data('id');
            $.ajax({
                url: '{{ route("admin.reviews.reject", ":id") }}'.replace(':id', id),
                type: 'POST',
                data: {_token: '{{ csrf_token() }}'},
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        bootstrap.Modal.getInstance(document.getElementById('viewReviewModal')).hide();
                        loadReviews(currentPage, getSearchValue());
                    }
                },
                error: function() { toastr.error('Failed to reject'); }
            });
        });

        $(document).off('click', '.btn-delete').on('click', '.btn-delete', function(e) {
            e.preventDefault();
            if (!confirm('Are you sure?')) return;
            const id = $(this).data('id');
            $.ajax({
                url: '{{ route("admin.reviews.delete", ":id") }}'.replace(':id', id),
                type: 'DELETE',
                data: {_token: '{{ csrf_token() }}'},
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        loadReviews(currentPage, getSearchValue());
                    }
                },
                error: function() { toastr.error('Failed to delete'); }
            });
        });
    }

    $(document).ready(function() {
        loadReviews(1);
        let searchTimeout;
        $('#searchInput').on('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => loadReviews(1, $(this).val()), 300);
        });
    });
</script>
@endpush

@endsection
