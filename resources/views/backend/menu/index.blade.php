@extends('dashboard')

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

        .menu-datatable {
            margin-bottom: 0;
        }

        .menu-datatable thead th {
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            color: #495057;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1rem;
        }

        .menu-datatable tbody td {
            padding: 0.9rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
        }

        .menu-datatable tbody tr:hover {
            background-color: #f8f9fa;
        }

        .menu-datatable tbody tr {
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

        .variation-row {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 1.2rem;
            margin-bottom: 1rem;
            position: relative;
            transition: all 0.3s;
        }

        .variation-row:hover {
            border-color: #667eea;
            background: #f0f3ff;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
        }

        .variation-row.first {
            background: linear-gradient(135deg, #667eea10 0%, #764ba210 100%);
            border: 1px solid #667eea;
        }

        .remove-variation {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            z-index: 10;
        }

        .variation-index {
            display: inline-block;
            background: #667eea;
            color: white;
            width: 28px;
            height: 28px;
            line-height: 28px;
            text-align: center;
            border-radius: 50%;
            font-weight: 600;
            font-size: 0.85rem;
            margin-right: 0.5rem;
        }

        .add-variation-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.6rem 1rem;
            font-weight: 500;
        }

        .add-variation-btn:hover {
            background: linear-gradient(135deg, #5568d3 0%, #68428b 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
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

        .image-placeholder {
            background: #e9ecef;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 280px;
            color: #6c757d;
            font-size: 0.9rem;
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
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="header-actions">
                    <div class="page-header">
                        <h3 class="mb-0">Menu Items Management</h3>
                        <p class="text-muted mb-0">Add, edit, and manage your restaurant menu items and pricing variations</p>
                    </div>
                    <button type="button" class="btn btn-primary add-new-btn" data-bs-toggle="modal" data-bs-target="#addMenuModal">
                        <i class="ri-add-line me-2"></i>Add New Menu Item
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
                            <table class="table menu-datatable table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th width="60">No</th>
                                        <th width="70">Image</th>
                                        <th>Item Name</th>
                                        <th>Category</th>
                                        <th width="140">Price Range</th>
                                        <th width="100">Variations</th>
                                        <th width="100">Status</th>
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

    <!-- Add/Edit Menu Item Modal -->
    <div class="modal fade" id="addMenuModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ri-restaurant-2-fill me-2"></i>Add Menu Item & Variations
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="menuForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">-- Select Category --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-text category_id_error d-block mt-1" style="font-size: 0.85rem;"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Item Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Mutton Kacchi" required>
                                <span class="text-danger error-text name_error d-block mt-1" style="font-size: 0.85rem;"></span>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Short Description</label>
                                <textarea name="description" class="form-control" rows="2" placeholder="Brief description of the menu item..."></textarea>
                                <span class="text-danger error-text description_error d-block mt-1" style="font-size: 0.85rem;"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Availability</label>
                                <select name="is_available" class="form-select">
                                    <option value="1">Available</option>
                                    <option value="0">Out of Stock</option>
                                </select>
                            </div>
                            <div class="col-md-6"></div>

                            <!-- Variations Section -->
                            <div class="col-12">
                                <hr class="my-2">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <label class="form-label mb-0">Price Variations <span class="text-danger">*</span></label>
                                        <small class="text-muted d-block">Add different sizes/types with their prices</small>
                                    </div>
                                    <button type="button" class="btn btn-sm add-variation-btn" id="add_variation">
                                        <i class="ri-add-line me-1"></i>Add Variation
                                    </button>
                                </div>
                                <div id="variation_wrapper"></div>
                                <span class="text-danger error-text variations_error d-block mt-1" style="font-size: 0.85rem;"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-2"></i>Save Menu Item
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
                        <i class="ri-file-info-fill me-2"></i>Menu Item Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">
                    <div class="row g-4">
                        <!-- Image Section -->
                        <div class="col-md-5">
                            <div class="image-placeholder">
                                <img id="detail_image" src="" alt="Item Image" class="img-fluid rounded" style="max-height: 280px; display: none;">
                                <span id="no_image">No Image Available</span>
                            </div>
                        </div>

                        <!-- Details Section -->
                        <div class="col-md-7">
                            <div class="detail-section">
                                <div class="detail-label">Item Name</div>
                                <h4 id="detail_name" class="detail-value mb-0"></h4>
                            </div>

                            <div class="detail-section">
                                <div class="detail-label">Category</div>
                                <p id="detail_category" class="detail-value mb-0"></p>
                            </div>

                            <div class="detail-section">
                                <div class="detail-label">Item Slug</div>
                                <p id="detail_slug" class="detail-value font-monospace mb-0" style="font-size: 0.9rem; background: white; padding: 0.5rem; border-radius: 4px; border-left: 3px solid #667eea;"></p>
                            </div>

                            <div class="detail-section">
                                <div class="detail-label">Description</div>
                                <p id="detail_description" class="detail-value mb-0 text-muted" style="font-size: 0.95rem; line-height: 1.5;"></p>
                            </div>

                            <div class="detail-section">
                                <div class="detail-label">Status</div>
                                <div id="detail_status" class="detail-value mb-0"></div>
                            </div>

                            <div class="detail-section">
                                <div class="detail-label">Price Variations</div>
                                <div id="detail_variations" class="mb-0">
                                    <p class="text-muted">No variations</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="edit_from_detail">
                        <i class="ri-pencil-line me-2"></i>Edit Item
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
        let vIndex = 0;
        let table;
        let currentEditId = null;

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize DataTable
            table = $('.menu-datatable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                pageLength: 25,
                ajax: "{{ route('admin.menu.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', width: '60px' },
                    { data: 'image_preview', name: 'image_preview', orderable: false, searchable: false, width: '70px' },
                    { data: 'name', name: 'name' },
                    { data: 'category_name', name: 'category.name' },
                    { data: 'price_range', name: 'price_range', orderable: false, searchable: false, width: '140px' },
                    { data: 'variations_count', name: 'variations_count', orderable: false, searchable: false, width: '100px' },
                    { data: 'status', name: 'status', width: '100px' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, width: '120px' }
                ]
            });

            // Add Variation Row
            $('#add_variation').click(function() {
                addVariationRow();
            });

            // Remove Variation Row
            $(document).on('click', '.remove-variation', function() {
                $(this).closest('.variation-row').fadeOut(300, function() {
                    $(this).remove();
                });
            });

            // Form Submit
            $('#menuForm').on('submit', function(e) {
                e.preventDefault();
                $('.error-text').text('');

                currentEditId = $(this).attr('data-edit-id');
                let url = currentEditId ? "{{ route('admin.menu.update', ':id') }}".replace(':id', currentEditId) : "{{ route('admin.menu.store') }}";

                let formData = new FormData(this);

                $.ajax({
                    url: url,
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        toastr.success(res.message, 'Success', { timeOut: 3000 });
                        $('#addMenuModal').modal('hide');
                        resetForm();
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            $.each(xhr.responseJSON.errors, function(key, val) {
                                $('.' + key + '_error').text(val[0]);
                            });
                        } else {
                            toastr.error('Error saving menu item', 'Error', { timeOut: 3000 });
                        }
                    }
                });
            });

            // Edit Button
            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                $.get("{{ route('admin.menu.edit', ':id') }}".replace(':id', id), function(data) {
                    currentEditId = id;
                    $('#menuForm').attr('data-edit-id', id);
                    $('select[name="category_id"]').val(data.category_id);
                    $('input[name="name"]').val(data.name);
                    $('textarea[name="description"]').val(data.description);
                    $('select[name="is_available"]').val(data.is_available);

                    $('#variation_wrapper').html('');
                    vIndex = 0;
                    data.variations.forEach((v, index) => {
                        addVariationRow(v, index);
                    });

                    $('#addMenuModal').modal('show');
                }).fail(function() {
                    toastr.error('Failed to load menu item', 'Error');
                });
            });

            // View Details Button
            $(document).on('click', '.view-details-btn', function() {
                let id = $(this).data('id');
                $.get("{{ route('admin.menu.edit', ':id') }}".replace(':id', id), function(data) {
                    currentEditId = id;

                    $('#detail_name').text(data.name);
                    $('#detail_category').text(data.category.name || 'N/A');
                    $('#detail_slug').text(data.slug || 'N/A');
                    $('#detail_description').text(data.description || 'No description provided');
                    
                    $('#detail_status').html(data.is_available 
                        ? '<span class="badge bg-success"><i class="ri-check-line me-1"></i>Available</span>'
                        : '<span class="badge bg-danger"><i class="ri-close-line me-1"></i>Out of Stock</span>');

                    // Variations
                    if (data.variations.length > 0) {
                        let variationsHtml = '';
                        data.variations.forEach((v, index) => {
                            const image = v.image ? (v.image.includes('http') ? v.image : '{{ asset("") }}' + v.image) : null;
                            variationsHtml += `
                                <div class="badge bg-soft-primary text-primary me-2 mb-2" style="padding: 0.6rem 0.9rem; font-size: 0.85rem;">
                                    <strong>${v.name}</strong> - <strong style="color: #667eea;">৳${parseFloat(v.price).toFixed(2)}</strong>
                                </div>`;
                        });
                        $('#detail_variations').html(variationsHtml);
                    } else {
                        $('#detail_variations').html('<p class="text-muted">No variations added</p>');
                    }

                    // Show image if exists
                    if (data.variations.length > 0 && data.variations[0].image) {
                        const imgUrl = data.variations[0].image.includes('http') ? data.variations[0].image : '{{ asset("") }}' + data.variations[0].image;
                        $('#detail_image').attr('src', imgUrl).show();
                        $('#no_image').hide();
                    } else {
                        $('#detail_image').hide();
                        $('#no_image').show();
                    }

                    $('#viewDetailsModal').modal('show');
                }).fail(function() {
                    toastr.error('Failed to load menu item details', 'Error');
                });
            });

            // Edit from detail modal
            $(document).on('click', '#edit_from_detail', function() {
                $('#viewDetailsModal').modal('hide');
                let id = currentEditId;
                $.get("{{ route('admin.menu.edit', ':id') }}".replace(':id', id), function(data) {
                    $('#menuForm').attr('data-edit-id', id);
                    $('select[name="category_id"]').val(data.category_id);
                    $('input[name="name"]').val(data.name);
                    $('textarea[name="description"]').val(data.description);
                    $('select[name="is_available"]').val(data.is_available);

                    $('#variation_wrapper').html('');
                    vIndex = 0;
                    data.variations.forEach((v, index) => {
                        addVariationRow(v, index);
                    });

                    $('#addMenuModal').modal('show');
                });
            });

            // Delete Button
            $(document).on('click', '.delete-btn', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Delete Menu Item?',
                    text: "This action cannot be undone. All variations will be deleted.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.menu.delete', ':id') }}".replace(':id', id),
                            method: 'DELETE',
                            data: { _token: "{{ csrf_token() }}" },
                            success: function(res) {
                                toastr.success(res.message, 'Deleted', { timeOut: 3000 });
                                table.ajax.reload();
                            },
                            error: function() {
                                toastr.error('Error deleting item', 'Error');
                            }
                        });
                    }
                });
            });

            // Reset form when modal is hidden
            $('#addMenuModal').on('hidden.bs.modal', function() {
                resetForm();
            });
        });

        function addVariationRow(data = null, index = null) {
            let i = index !== null ? index : vIndex++;
            let name = data ? data.name : '';
            let price = data ? data.price : '';
            let oldImage = data ? `<input type="hidden" name="variations[${i}][old_image]" value="${data.image}">` : '';
            let isFirst = i === 0;

            let html = `
                <div class="variation-row ${isFirst ? 'first' : ''}" style="position: relative;">
                    ${i > 0 ? '<button type="button" class="btn btn-sm btn-danger remove-variation"><i class="ri-close-line"></i></button>' : ''}
                    ${oldImage}
                    <div class="row g-3">
                        <div class="col-12 mb-2">
                            <small class="text-muted fw-600">
                                <span class="variation-index">${i + 1}</span>
                                <strong>Variation ${i + 1}</strong>
                            </small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Size/Type <span class="text-danger">*</span></label>
                            <input type="text" name="variations[${i}][name]" value="${name}" class="form-control form-control-sm" placeholder="e.g. Small, Medium, Large" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Price (৳) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="variations[${i}][price]" value="${price}" class="form-control form-control-sm" placeholder="0.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Image</label>
                            <input type="file" name="variations[${i}][image]" class="form-control form-control-sm" accept="image/*">
                            <small class="text-muted d-block mt-1">Recommended: 500x500px</small>
                        </div>
                    </div>
                </div>`;
            $('#variation_wrapper').append(html);
        }

        function resetForm() {
            $('#menuForm')[0].reset();
            $('#menuForm').removeAttr('data-edit-id');
            $('#variation_wrapper').html('');
            vIndex = 0;
            addVariationRow();
            $('.error-text').text('');
            currentEditId = null;
        }
    </script>
@endpush
