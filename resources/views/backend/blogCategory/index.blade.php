@extends('layouts.dashboard')

@section('title', 'Blog Category Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-crud.css') }}">
    <style>
        .status-badge { padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 500; }
        .status-badge.active { background: #d4edda; color: #155724; }
        .status-badge.inactive { background: #f8d7da; color: #721c24; }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4 admin-crud-page">
        <div class="admin-crud-header">
            <div>
                <h3 class="admin-crud-header__title">Blog Category Management</h3>
                <p class="admin-crud-header__lead">Add, edit, and manage blog categories</p>
            </div>
            <div class="admin-crud-header__actions">
                <button type="button" class="admin-crud-btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="ri-add-line"></i>Add New Category
                </button>
            </div>
        </div>

        <div class="admin-crud-card">
            <div class="admin-crud-card__head">
                <h5><i class="ri-price-tag-3-line me-1"></i> All Categories</h5>
            </div>
            <div class="admin-crud-card__body admin-crud-card__body--flush">
                <div class="admin-crud-table-wrap">
                    <table class="table admin-datatable category-datatable table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="60">No</th>
                                <th>Category Name</th>
                                <th>Slug</th>
                                <th width="100">Status</th>
                                <th>Created At</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header admin-modal-header">
                    <h5 class="modal-title">
                        <i class="ri-price-tag-3-fill me-2"></i>
                        <span id="modalTitle">Add Category</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="categoryForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Recipes" required>
                            <span class="text-danger error-text name_error d-block mt-1" style="font-size: 0.85rem;"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" class="form-control" placeholder="Auto-generated if empty">
                            <span class="text-danger error-text slug_error d-block mt-1" style="font-size: 0.85rem;"></span>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="categoryIsActive" name="is_active" value="1" checked>
                            <label class="form-check-label" for="categoryIsActive">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-2"></i>Save Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let table;
    let currentEditId = null;

    $(document).ready(function() {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        table = $('.category-datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            pageLength: 25,
            order: [[1, 'asc']],
            ajax: "{{ route('admin.blogCategories.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '60px' },
                { data: 'name', name: 'name' },
                { data: 'slug', name: 'slug' },
                { data: 'status', name: 'is_active', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false, width: '120px' }
            ]
        });

        $('#categoryForm').on('submit', function(e) {
            e.preventDefault();
            $('.error-text').text('');

            let url = currentEditId
                ? "{{ route('admin.blogCategories.update', ':id') }}".replace(':id', currentEditId)
                : "{{ route('admin.blogCategories.store') }}";

            let formData = new FormData(this);
            formData.set('is_active', $('#categoryIsActive').is(':checked') ? 1 : 0);
            formData.append('_method', currentEditId ? 'PUT' : 'POST');

            $.ajax({
                url: url,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.message);
                        $('#addCategoryModal').modal('hide');
                        resetForm();
                        table.ajax.reload();
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        $.each(xhr.responseJSON.errors, function(key, val) {
                            $('.' + key + '_error').text(val[0]);
                        });
                    } else {
                        toastr.error('Error saving category');
                    }
                }
            });
        });

        $(document).on('click', '.edit-btn', function() {
            let id = $(this).data('id');
            $.get("{{ route('admin.blogCategories.edit', ':id') }}".replace(':id', id), function(data) {
                currentEditId = id;
                $('#modalTitle').text('Edit Category');
                $('input[name="name"]').val(data.name);
                $('input[name="slug"]').val(data.slug);
                $('#categoryIsActive').prop('checked', !!data.is_active);
                $('#addCategoryModal').modal('show');
            });
        });

        $(document).on('click', '.delete-btn', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');

            Swal.fire({
                title: 'Delete Category?',
                text: "Delete '" + name + "'? This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.blogCategories.delete', ':id') }}".replace(':id', id),
                        method: 'DELETE',
                        success: function(res) {
                            if (res.success) {
                                toastr.success(res.message);
                                table.ajax.reload();
                            }
                        },
                        error: function() {
                            toastr.error('Error deleting category');
                        }
                    });
                }
            });
        });

        $('#addCategoryModal').on('hidden.bs.modal', resetForm);
    });

    function resetForm() {
        $('#categoryForm')[0].reset();
        $('.error-text').text('');
        currentEditId = null;
        $('#modalTitle').text('Add Category');
        $('#categoryIsActive').prop('checked', true);
    }
</script>
@endpush
