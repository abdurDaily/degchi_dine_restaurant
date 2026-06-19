@extends('layouts.dashboard')

@section('title', 'Category Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-crud.css') }}">
@endpush

@section('content')
    <div class="container-fluid py-4 admin-crud-page">
        <div class="admin-crud-header">
            <div>
                <h3 class="admin-crud-header__title">Category Management</h3>
                <p class="admin-crud-header__lead">Organize menu items by branch and category</p>
            </div>
        </div>

        <div class="row admin-crud-grid g-4">
            <div class="col-xl-4 col-lg-5">
                <div class="admin-crud-card admin-crud-form-panel">
                    <div class="admin-crud-card__head">
                        <h5><i class="ri-folder-add-line me-1"></i> Add Category</h5>
                    </div>
                    <div class="admin-crud-card__body">
                        <form id="categoryForm" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Select Branch</label>
                                <select name="branch_id" class="form-select">
                                    <option value="">-- Choose Branch --</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-text branch_id_error d-block mt-1" style="font-size:0.82rem;"></span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Category Name</label>
                                <input type="text" name="name" id="cat_name" class="form-control" placeholder="e.g. Fast Food">
                                <span class="text-danger error-text name_error d-block mt-1" style="font-size:0.82rem;"></span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Category Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <span class="text-danger error-text image_error d-block mt-1" style="font-size:0.82rem;"></span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            <button type="submit" id="submitBtn" class="admin-crud-btn-primary">Save Category</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-8 col-lg-7">
                <div class="admin-crud-card">
                    <div class="admin-crud-card__head">
                        <h5><i class="ri-list-check-2 me-1"></i> All Categories</h5>
                    </div>
                    <div class="admin-crud-card__body admin-crud-card__body--flush">
                        <div class="admin-crud-table-wrap">
                            <table class="table admin-datatable category-datatable table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Image</th>
                                        <th>Category</th>
                                        <th>Branch</th>
                                        <th>Status</th>
                                        <th width="80">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <form id="editCategoryForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="edit_id">
                    <div class="modal-header admin-modal-header">
                        <h5 class="modal-title"><i class="ri-pencil-line me-1"></i> Edit Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Branch</label>
                            <select name="branch_id" id="edit_branch_id" class="form-select">
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" id="edit_name" name="name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image <span class="text-muted fw-normal">(leave blank to keep current)</span></label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Status</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="admin-crud-btn-primary">Update Category</button>
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
            const table = $('.category-datatable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                pageLength: 25,
                ajax: "{{ route('admin.category.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        width: '50px'
                    },
                    {
                        data: 'image',
                        name: 'image',
                        orderable: false,
                        searchable: false,
                        width: '70px'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'branch_name',
                        name: 'branch.name'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        width: '90px'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '80px'
                    },
                ]
            });

            $('#categoryForm').on('submit', function(e) {
                e.preventDefault();
                $('.error-text').text('');
                let submitBtn = $('#submitBtn');
                submitBtn.prop('disabled', true).text('Saving...');
                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('admin.category.store') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        toastr.success(res.message);
                        $('#categoryForm')[0].reset();
                        table.ajax.reload();
                        submitBtn.prop('disabled', false).text('Save Category');
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).text('Save Category');
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, val) {
                                $('.' + key + '_error').text(val[0]);
                            });
                        } else {
                            toastr.error("Something went wrong on the server.");
                        }
                    }
                });
            });

            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                let url = "{{ route('admin.category.edit', ':id') }}".replace(':id', id);

                $.get(url, function(data) {
                    $('#edit_id').val(data.id);
                    $('#edit_name').val(data.name);
                    $('#edit_branch_id').val(data.branch_id);
                    $('#edit_status').val(data.status);
                    $('#editCategoryModal').modal('show');
                });
            });

            $('#editCategoryForm').on('submit', function(e) {
                e.preventDefault();
                let id = $('#edit_id').val();
                let url = "{{ route('admin.category.update', ':id') }}".replace(':id', id);
                let formData = new FormData(this);

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        $('#editCategoryModal').modal('hide');
                        toastr.success(res.message);
                        table.ajax.reload();
                    }
                });
            });

            $(document).on('click', '.delete-btn', function() {
                let id = $(this).data('id');
                let url = "{{ route('admin.category.delete', ':id') }}".replace(':id', id);

                Swal.fire({
                    title: 'Delete Category?',
                    text: "This will affect menus under this category!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#116b83',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            method: 'DELETE',
                            success: function(res) {
                                Swal.fire('Deleted!', res.message, 'success');
                                table.ajax.reload();
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
