@extends('layouts.dashboard')

@section('title', 'Coupon Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-crud.css') }}">
@endpush

@section('content')
    <div class="container-fluid py-4 admin-crud-page">
        <div class="admin-crud-header">
            <div>
                <h3 class="admin-crud-header__title">Coupon Management</h3>
                <p class="admin-crud-header__lead">Create and manage coupon codes, discount rates, and usage limits</p>
            </div>
            <div class="admin-crud-header__actions">
                @can('coupon-create')
                <button type="button" class="admin-crud-btn-primary" data-bs-toggle="modal" data-bs-target="#addCouponModal">
                    <i class="ri-add-line"></i>Add New Coupon
                </button>
                @endcan
            </div>
        </div>

        <div class="admin-crud-card">
            <div class="admin-crud-card__head">
                <h5><i class="ri-ticket-2-line me-1"></i> All Coupons</h5>
            </div>
            <div class="admin-crud-card__body admin-crud-card__body--flush">
                <div class="admin-crud-table-wrap">
                    <table class="table admin-datatable coupon-datatable table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="60">No</th>
                                <th>Coupon Name</th>
                                <th>Coupon Code</th>
                                <th>Discount Type</th>
                                <th>Discount Amount</th>
                                <th>Usage (Used/Limit)</th>
                                <th>Expiry Date</th>
                                <th width="100">Status</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    <!-- Add/Edit Coupon Modal -->
    <div class="modal fade" id="addCouponModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header admin-modal-header">
                    <h5 class="modal-title">
                        <i class="ri-ticket-2-fill me-2"></i><span id="modalTitle">Add Coupon Details</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="couponForm">
                    @csrf
                    <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Coupon Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Summer Special" required>
                                <span class="text-danger error-text name_error d-block mt-1" style="font-size: 0.85rem;"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Coupon Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control" placeholder="e.g. SUMMER50" required style="text-transform: uppercase;">
                                <span class="text-danger error-text code_error d-block mt-1" style="font-size: 0.85rem;"></span>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Discount Type <span class="text-danger">*</span></label>
                                <select name="discount_type" class="form-select" required>
                                    <option value="flat">Flat Amount</option>
                                    <option value="percentage">Percentage (%)</option>
                                </select>
                                <span class="text-danger error-text discount_type_error d-block mt-1" style="font-size: 0.85rem;"></span>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Discount Amount <span class="text-danger">*</span></label>
                                <input type="number" name="discount_amount" class="form-control" placeholder="e.g. 50" min="0" step="0.01" required>
                                <span class="text-danger error-text discount_amount_error d-block mt-1" style="font-size: 0.85rem;"></span>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Minimum Order Amount</label>
                                <input type="number" name="min_order_amount" class="form-control" placeholder="e.g. 100" min="0" step="0.01">
                                <span class="text-danger error-text min_order_amount_error d-block mt-1" style="font-size: 0.85rem;"></span>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">How many times it can be used? <span class="text-muted">(Usage Limit)</span></label>
                                <input type="number" name="usage_limit" class="form-control" placeholder="e.g. 100 (Leave blank for unlimited)" min="1">
                                <span class="text-danger error-text usage_limit_error d-block mt-1" style="font-size: 0.85rem;"></span>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Expiry Date</label>
                                <input type="date" name="expires_at" class="form-control">
                                <span class="text-danger error-text expires_at_error d-block mt-1" style="font-size: 0.85rem;"></span>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="statusCheck" name="is_active" value="1" checked>
                                    <label class="form-check-label" for="statusCheck">
                                        Active (usable by customers)
                                    </label>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Describe the coupon rules/terms..."></textarea>
                                <span class="text-danger error-text description_error d-block mt-1" style="font-size: 0.85rem;"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-2"></i>Save Coupon
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let currentEditId = null;

            // Initialize DataTable
            const table = $('.coupon-datatable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                ajax: "{{ route('admin.coupon.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'code', name: 'code' },
                    { data: 'discount_type', name: 'discount_type' },
                    { data: 'discount_amount', name: 'discount_amount' },
                    { data: 'usage', name: 'usage' },
                    { data: 'expires_at', name: 'expires_at' },
                    { data: 'is_active', name: 'is_active' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            // Convert code to uppercase automatically on input
            $('input[name="code"]').on('input', function() {
                $(this).val($(this).val().toUpperCase());
            });

            // Form Submit (Store or Update)
            $('#couponForm').on('submit', function(e) {
                e.preventDefault();
                $('.error-text').text('');

                let id = currentEditId;
                let url = "{{ route('admin.coupon.store') }}";
                
                if (id) {
                    url = "{{ route('admin.coupon.update', ':id') }}".replace(':id', id);
                }

                let formData = new FormData(this);

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status === 'success') {
                            toastr.success(res.message, 'Success');
                            $('#addCouponModal').modal('hide');
                            resetForm();
                            table.ajax.reload();
                        } else {
                            toastr.error(res.message || 'Something went wrong', 'Error');
                        }
                    },
                    error: function(err) {
                        if (err.status === 422) {
                            let errors = err.responseJSON.errors;
                            $.each(errors, function(key, val) {
                                $('.' + key + '_error').text(val[0]);
                            });
                            toastr.error('Please fix validation errors', 'Validation Error');
                        } else {
                            toastr.error(err.responseJSON?.message || 'Server Error', 'Error');
                        }
                    }
                });
            });

            // Edit Button Click
            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                currentEditId = id;

                $.get("{{ route('admin.coupon.edit', ':id') }}".replace(':id', id), function(data) {
                    $('#modalTitle').text('Edit Coupon Details');
                    $('input[name="name"]').val(data.name);
                    $('input[name="code"]').val(data.code);
                    $('select[name="discount_type"]').val(data.discount_type);
                    $('input[name="discount_amount"]').val(data.discount_amount);
                    $('input[name="min_order_amount"]').val(data.min_order_amount);
                    $('input[name="usage_limit"]').val(data.usage_limit);
                    
                    if (data.expires_at) {
                        let dateOnly = data.expires_at.split('T')[0];
                        $('input[name="expires_at"]').val(dateOnly);
                    } else {
                        $('input[name="expires_at"]').val('');
                    }

                    $('textarea[name="description"]').val(data.description || '');
                    $('input[name="is_active"]').prop('checked', data.is_active == 1);

                    $('#addCouponModal').modal('show');
                });
            });

            // Delete Button Click
            $(document).on('click', '.delete-btn', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');

                Swal.fire({
                    title: 'Delete Coupon?',
                    text: "Delete '" + name + "'? This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.coupon.delete', ':id') }}".replace(':id', id),
                            method: 'DELETE',
                            success: function(res) {
                                if (res.status === 'success') {
                                    toastr.success(res.message, 'Deleted');
                                    table.ajax.reload();
                                } else {
                                    toastr.error(res.message, 'Error');
                                }
                            },
                            error: function() {
                                toastr.error('Error deleting coupon', 'Error');
                            }
                        });
                    }
                });
            });

            // Reset form when modal is hidden
            $('#addCouponModal').on('hidden.bs.modal', function() {
                resetForm();
            });

            function resetForm() {
                $('#couponForm')[0].reset();
                currentEditId = null;
                $('#modalTitle').text('Add Coupon Details');
                $('.error-text').text('');
            }
        });
    </script>
@endpush
