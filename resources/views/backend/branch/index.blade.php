@extends('layouts.dashboard')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .datatable-wrapper {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        .branch-datatable {
            margin-bottom: 0;
        }

        .branch-datatable thead th {
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            color: #495057;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1rem;
        }

        .branch-datatable tbody td {
            padding: 0.9rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
        }

        .branch-datatable tbody tr:hover {
            background-color: #f8f9fa;
        }

        .branch-datatable tbody tr {
            transition: background-color 0.2s;
        }

        .badge {
            font-weight: 500;
            padding: 0.5rem 0.75rem;
            font-size: 0.8rem;
        }

        .btn-sm {
            padding: 0.4rem 0.7rem;
            font-size: 0.8rem;
        }

        .add-new-btn {
            padding: 0.6rem 1.2rem;
            font-weight: 500;
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .modal-header .modal-title {
            color: white;
            font-weight: 600;
        }

        .modal-header .btn-close {
            filter: invert(1);
        }

        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .detail-section {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }

        .detail-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.3rem;
        }

        .detail-value {
            font-size: 1rem;
            color: #212529;
            font-weight: 500;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-header h3 {
            font-weight: 700;
            color: #212529;
            margin-bottom: 0.25rem;
        }

        .page-header .text-muted {
            font-size: 0.95rem;
        }

        .header-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        table.dataTable {
            width: 100% !important;
        }

        .delivery-service {
            background: #f0f3ff;
            border-left: 3px solid #667eea;
            padding: 0.8rem;
            margin-bottom: 0.8rem;
            border-radius: 4px;
        }

        .delivery-service-label {
            font-weight: 600;
            color: #667eea;
            font-size: 0.9rem;
        }

        .delivery-service-value {
            color: #212529;
            word-break: break-all;
            margin-top: 0.3rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="header-actions">
                    <div class="page-header">
                        <h3 class="mb-0">Branch Management</h3>
                        <p class="text-muted mb-0">Add, edit, and manage your restaurant branches with delivery service details</p>
                    </div>
                    <button type="button" class="btn btn-primary add-new-btn" data-bs-toggle="modal" data-bs-target="#addBranchModal">
                        <i class="ri-add-line me-2"></i>Add New Branch
                    </button>
                </div>
            </div>
        </div>

        <!-- DataTable Card -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive datatable-wrapper">
                            <table class="table branch-datatable table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th width="60">No</th>
                                        <th>Branch Name</th>
                                        <th>Phone</th>
                                        <th>Location</th>
                                        <th width="100">Delivery Services</th>
                                        <th width="120">Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Branch Modal -->
    <div class="modal fade" id="addBranchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ri-store-2-fill me-2"></i><span id="modalTitle">Add Branch Details</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="branchForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Branch Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Downtown Branch" required>
                                <span class="text-danger error-text name_error d-block mt-1" style="font-size: 0.85rem;"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" placeholder="e.g. +8801234567890" required>
                                <span class="text-danger error-text phone_error d-block mt-1" style="font-size: 0.85rem;"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="statusCheck" name="status" value="1" checked>
                                    <label class="form-check-label" for="statusCheck">
                                        Active (visible on frontend)
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Location <span class="text-danger">*</span></label>
                                <input type="text" name="location" class="form-control" placeholder="e.g. 123 Main Street, Downtown" required>
                                <span class="text-danger error-text location_error d-block mt-1" style="font-size: 0.85rem;"></span>
                            </div>

                            <!-- Delivery Services Section -->
                            <div class="col-12">
                                <hr class="my-2">
                                <label class="form-label mb-3">
                                    <strong>Delivery Services (Optional)</strong>
                                    <small class="text-muted d-block">Add delivery partner links and logos</small>
                                </label>

                                <!-- FoodPanda -->
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label small">FoodPanda URL</label>
                                        <input type="url" name="foodpanda_url" class="form-control form-control-sm" placeholder="https://www.foodpanda.com/...">
                                        <span class="text-danger error-text foodpanda_url_error d-block mt-1" style="font-size: 0.85rem;"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">FoodPanda Logo</label>
                                        <input type="file" name="foodpanda_logo" class="form-control form-control-sm logo-file-input" accept="image/*" data-max-size="5120">
                                        <small class="text-muted d-block mt-1">Max: 5MB (PNG, JPG, GIF, SVG)</small>
                                        <span class="text-danger error-text foodpanda_logo_error d-block mt-1" style="font-size: 0.85rem;"></span>
                                    </div>
                                </div>

                                <!-- Pathao -->
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label small">Pathao URL</label>
                                        <input type="url" name="pathao_url" class="form-control form-control-sm" placeholder="https://www.pathao.com/...">
                                        <span class="text-danger error-text pathao_url_error d-block mt-1" style="font-size: 0.85rem;"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Pathao Logo</label>
                                        <input type="file" name="pathao_logo" class="form-control form-control-sm logo-file-input" accept="image/*" data-max-size="5120">
                                        <small class="text-muted d-block mt-1">Max: 5MB (PNG, JPG, GIF, SVG)</small>
                                        <span class="text-danger error-text pathao_logo_error d-block mt-1" style="font-size: 0.85rem;"></span>
                                    </div>
                                </div>

                                <!-- Foodi -->
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label small">Foodi URL</label>
                                        <input type="url" name="foodi_url" class="form-control form-control-sm" placeholder="https://www.foodiapp.com/...">
                                        <span class="text-danger error-text foodi_url_error d-block mt-1" style="font-size: 0.85rem;"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Foodi Logo</label>
                                        <input type="file" name="foodi_logo" class="form-control form-control-sm logo-file-input" accept="image/*" data-max-size="5120">
                                        <small class="text-muted d-block mt-1">Max: 5MB (PNG, JPG, GIF, SVG)</small>
                                        <span class="text-danger error-text foodi_logo_error d-block mt-1" style="font-size: 0.85rem;"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-2"></i>Save Branch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Details Modal -->
    <div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ri-file-info-fill me-2"></i>Branch Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="detail-section">
                                <div class="detail-label">Branch Name</div>
                                <h4 id="detail_name" class="detail-value mb-0"></h4>
                            </div>

                            <div class="detail-section">
                                <div class="detail-label">Phone</div>
                                <p id="detail_phone" class="detail-value mb-0"></p>
                            </div>

                            <div class="detail-section">
                                <div class="detail-label">Location</div>
                                <p id="detail_location" class="detail-value mb-0"></p>
                            </div>

                            <div class="detail-section">
                                <div class="detail-label">Delivery Services</div>
                                <div id="detail_services" class="mb-0">
                                    <p class="text-muted">No delivery services configured</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="edit_from_detail">
                        <i class="ri-pencil-line me-2"></i>Edit Branch
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let table;
        let currentEditId = null;

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // File size validation on file input change
            $(document).on('change', '.logo-file-input', function() {
                const file = this.files[0];
                if (file) {
                    const maxSize = $(this).data('max-size') * 1024; // Convert KB to bytes
                    const fileSizeInKB = Math.round(file.size / 1024);
                    const fieldName = $(this).attr('name');
                    const $errorElement = $('.' + fieldName + '_error');

                    if (file.size > maxSize) {
                        const maxSizeInMB = $(this).data('max-size') / 1024;
                        const errorMsg = `Image size is ${fileSizeInKB}KB. Maximum allowed size is ${maxSizeInMB}MB.`;
                        $errorElement.text(errorMsg).show();
                        
                        // Show alert to user
                        Swal.fire({
                            icon: 'warning',
                            title: 'File Too Large',
                            text: errorMsg,
                            confirmButtonColor: '#667eea'
                        });
                        
                        // Clear the input
                        $(this).val('');
                    } else {
                        // Check if file is image
                        const validMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'];
                        if (!validMimes.includes(file.type)) {
                            const errorMsg = 'Please select a valid image file (PNG, JPG, GIF, SVG).';
                            $errorElement.text(errorMsg).show();
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid File Type',
                                text: errorMsg,
                                confirmButtonColor: '#667eea'
                            });
                            
                            $(this).val('');
                        } else {
                            $errorElement.text('').hide();
                        }
                    }
                }
            });

            // Reset form when modal is being shown for ADD
            $('#addBranchModal').on('show.bs.modal', function() {
                if (!currentEditId || currentEditId <= 0) {
                    $('#modalTitle').text('Add Branch Details');
                }
            });

            // Initialize DataTable
            table = $('.branch-datatable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                pageLength: 25,
                ajax: "{{ route('admin.branch.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', width: '60px' },
                    { data: 'name', name: 'name' },
                    { data: 'phone', name: 'phone' },
                    { data: 'location', name: 'location' },
                    { 
                        data: null, 
                        name: 'services',
                        orderable: false,
                        searchable: false,
                        width: '100px',
                        render: function(data) {
                            let count = 0;
                            if (data.foodpanda_url) count++;
                            if (data.pathao_url) count++;
                            if (data.foodi_url) count++;
                            return count > 0 ? '<span class="badge bg-success">' + count + ' Services</span>' : '<span class="badge bg-secondary">None</span>';
                        }
                    },
                    { data: 'action', name: 'action', orderable: false, searchable: false, width: '120px' }
                ]
            });

            // Form Submit
            $('#branchForm').on('submit', function(e) {
                e.preventDefault();
                $('.error-text').text('');

                // Check if we're editing or creating based on currentEditId
                let url;
                if (currentEditId && currentEditId > 0) {
                    url = "{{ url('/admin/branch') }}/" + currentEditId;
                    console.log('UPDATE MODE - URL:', url, 'ID:', currentEditId);
                } else {
                    url = "{{ url('/admin/branch') }}";
                    console.log('CREATE MODE - URL:', url);
                }

                let formData = new FormData(this);
                
                // Handle status checkbox - if checked, set to 1, otherwise 0
                formData.delete('status');
                formData.append('status', $('#statusCheck').is(':checked') ? 1 : 0);
                
                console.log('=== FORM SUBMIT ===');
                console.log('Mode:', currentEditId ? 'UPDATE' : 'CREATE');
                console.log('Final URL:', url);
                console.log('Current Edit ID:', currentEditId);

                // Show loading indicator
                toastr.info('Processing...', '', { hideDuration: 10000, timeOut: 10000 });

                $.ajax({
                    url: url,
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        console.log('=== SUCCESS ===');
                        console.log('Response:', res);
                        
                        if (res && res.status === 'success') {
                            toastr.success(res.message);
                            $('#addBranchModal').modal('hide');
                            resetForm();
                            
                            setTimeout(function() {
                                if (typeof table !== 'undefined') {
                                    table.ajax.reload();
                                }
                            }, 300);
                        } else {
                            toastr.error(res?.message || 'Unknown error');
                        }
                    },
                    error: function(xhr) {
                        console.log('=== ERROR ===');
                        console.log('Status:', xhr.status);
                        console.log('Response:', xhr.responseJSON);
                        
                        let errorMsg = 'Error';
                        if (xhr.status === 422) {
                            errorMsg = 'Validation error - please check your input';
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, val) {
                                $('.' + key + '_error').text(val[0]);
                            });
                        } else if (xhr.status === 400) {
                            errorMsg = xhr.responseJSON?.message || 'Bad request';
                        } else if (xhr.status === 404) {
                            errorMsg = 'Branch not found';
                        } else if (xhr.status === 500) {
                            errorMsg = 'Server error: ' + (xhr.responseJSON?.message || '');
                        }
                        
                        toastr.error(errorMsg);
                    }
                });
            });

            // Edit Button
            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                console.log('Loading branch for edit, ID:', id);
                $.get("{{ route('admin.branch.edit', ':id') }}".replace(':id', id), function(data) {
                    console.log('Branch data loaded:', data);
                    currentEditId = id;
                    $('#branchForm').attr('data-edit-id', id);
                    $('#modalTitle').text('Edit Branch Details');
                    
                    $('input[name="name"]').val(data.name || '');
                    $('input[name="phone"]').val(data.phone || '');
                    $('input[name="location"]').val(data.location || '');
                    $('input[name="foodpanda_url"]').val(data.foodpanda_url || '');
                    $('input[name="pathao_url"]').val(data.pathao_url || '');
                    $('input[name="foodi_url"]').val(data.foodi_url || '');
                    $('input[name="status"]').prop('checked', data.status == 1);

                    $('#addBranchModal').modal('show');
                }).fail(function(xhr) {
                    console.error('Failed to load branch:', xhr);
                    toastr.error('Failed to load branch', 'Error');
                });
            });

            // View Details Button
            $(document).on('click', '.view-details-btn', function() {
                let id = $(this).data('id');
                $.get("{{ route('admin.branch.edit', ':id') }}".replace(':id', id), function(data) {
                    currentEditId = id;

                    $('#detail_name').text(data.name);
                    $('#detail_phone').text(data.phone);
                    $('#detail_location').text(data.location);

                    // Build delivery services HTML
                    let servicesHtml = '';
                    if (data.foodpanda_url || data.pathao_url || data.foodi_url) {
                        if (data.foodpanda_url) {
                            servicesHtml += `<div class="delivery-service">
                                <div class="delivery-service-label">🍔 FoodPanda</div>
                                <div class="delivery-service-value"><a href="${data.foodpanda_url}" target="_blank">${data.foodpanda_url}</a></div>
                            </div>`;
                        }
                        if (data.pathao_url) {
                            servicesHtml += `<div class="delivery-service">
                                <div class="delivery-service-label">🚚 Pathao</div>
                                <div class="delivery-service-value"><a href="${data.pathao_url}" target="_blank">${data.pathao_url}</a></div>
                            </div>`;
                        }
                        if (data.foodi_url) {
                            servicesHtml += `<div class="delivery-service">
                                <div class="delivery-service-label">🛵 Foodi</div>
                                <div class="delivery-service-value"><a href="${data.foodi_url}" target="_blank">${data.foodi_url}</a></div>
                            </div>`;
                        }
                        $('#detail_services').html(servicesHtml);
                    } else {
                        $('#detail_services').html('<p class="text-muted">No delivery services configured</p>');
                    }

                    $('#viewDetailsModal').modal('show');
                }).fail(function() {
                    toastr.error('Failed to load branch details', 'Error');
                });
            });

            // Edit from detail modal
            $(document).on('click', '#edit_from_detail', function() {
                $('#viewDetailsModal').modal('hide');
                let id = currentEditId;
                $.get("{{ route('admin.branch.edit', ':id') }}".replace(':id', id), function(data) {
                    $('#branchForm').attr('data-edit-id', id);
                    $('#modalTitle').text('Edit Branch Details');
                    $('input[name="name"]').val(data.name);
                    $('input[name="phone"]').val(data.phone);
                    $('input[name="location"]').val(data.location);
                    $('input[name="foodpanda_url"]').val(data.foodpanda_url || '');
                    $('input[name="pathao_url"]').val(data.pathao_url || '');
                    $('input[name="foodi_url"]').val(data.foodi_url || '');
                    $('input[name="status"]').prop('checked', data.status == 1);

                    $('#addBranchModal').modal('show');
                });
            });

            // Copy Link Button
            $(document).on('click', '.copy-link-btn', function() {
                let slug = $(this).data('slug');
                let linkUrl = "{{ url('/branches') }}/" + slug;
                
                // Copy to clipboard
                navigator.clipboard.writeText(linkUrl).then(function() {
                    toastr.success('Link copied to clipboard!', 'Success', { timeOut: 2000 });
                }).catch(function() {
                    // Fallback for older browsers
                    let tempInput = $('<input>');
                    $('body').append(tempInput);
                    tempInput.val(linkUrl).select();
                    document.execCommand('copy');
                    tempInput.remove();
                    toastr.success('Link copied to clipboard!', 'Success', { timeOut: 2000 });
                });
            });

            // Delete Button
            $(document).on('click', '.delete-btn', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');
                Swal.fire({
                    title: 'Delete Branch?',
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
                            url: "{{ route('admin.branch.delete', ':id') }}".replace(':id', id),
                            method: 'DELETE',
                            data: { _token: "{{ csrf_token() }}" },
                            success: function(res) {
                                if (res.status === 'success') {
                                    toastr.success(res.message, 'Deleted', { timeOut: 3000 });
                                    setTimeout(function() {
                                        console.log('Reloading DataTable after delete...');
                                        table.ajax.reload(function() {
                                            console.log('DataTable reloaded after delete');
                                        });
                                    }, 300);
                                }
                            },
                            error: function() {
                                toastr.error('Error deleting branch', 'Error');
                            }
                        });
                    }
                });
            });

            // Reset form when modal is hidden
            $('#addBranchModal').on('hidden.bs.modal', function() {
                resetForm();
            });
        });

        function resetForm() {
            $('#branchForm')[0].reset();
            
            // CRITICAL: Remove ALL traces of edit ID
            $('#branchForm').removeAttr('data-edit-id');
            $('#branchForm')[0].removeAttribute('data-edit-id');
            delete $('#branchForm')[0].dataset.editId;
            
            // Make absolutely sure it's null
            currentEditId = null;
            
            $('.error-text').text('');
            
            console.log('Form reset. currentEditId is now:', currentEditId);
        }
    </script>
@endpush
