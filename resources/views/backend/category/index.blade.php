@extends('dashboard')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- CREATE CATEGORY FORM -->
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Add Category</h5>
                    </div>
                    <div class="card-body">
                        <form id="categoryForm" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Select Branch</label>
                                <select name="branch_id" class="form-select">
                                    <option value="">-- Choose Branch --</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-text branch_id_error"></span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Category Name</label>
                                <input type="text" name="name" id="cat_name" class="form-control"
                                    placeholder="e.g. Fast Food">
                                <span class="text-danger error-text name_error"></span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Category Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <span class="text-danger error-text image_error"></span>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" class="form-select">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            <button type="submit" id="submitBtn" class="btn btn-primary w-100 fw-bold">Save
                                Category</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- CATEGORY DATA TABLE -->
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table category-datatable table-bordered nowrap align-middle w-100">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Image</th>
                                        <th>Category</th>
                                        <th>Branch</th>
                                        <th>Status</th>
                                        <th width="80px">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- EDIT CATEGORY MODAL -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editCategoryForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="edit_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Branch</label>
                            <select name="branch_id" id="edit_branch_id" class="form-select">
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" id="edit_name" name="name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Image (Leave blank to keep current)</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                        <!-- Inside your Edit Modal -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                ajax: "{{ route('admin.category.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'image',
                        name: 'image',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'branch_name',
                        name: 'branch.name'
                    }, // Links to branch relationship
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // CREATE
            $('#categoryForm').on('submit', function(e) {
                e.preventDefault();

                // Clear previous errors
                $('.error-text').text('');
                let submitBtn = $('#submitBtn');
                submitBtn.prop('disabled', true).text('Saving...');

                // Use FormData for files/images
                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('admin.category.store') }}",
                    method: "POST",
                    data: formData,
                    processData: false, // Required for FormData
                    contentType: false, // Required for FormData
                    success: function(res) {
                        toastr.success(res.message);
                        $('#categoryForm')[0].reset();
                        $('.category-datatable').DataTable().ajax.reload(); // Reload Table
                        submitBtn.prop('disabled', false).text('Save Category');
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).text('Save Category');
                        if (xhr.status === 422) {
                            // Validation Errors
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

            // EDIT (Fetch)
            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                let url = "{{ route('admin.category.edit', ':id') }}".replace(':id', id);

                $.get(url, function(data) {
                    $('#edit_id').val(data.id);
                    $('#edit_name').val(data.name);
                    $('#edit_branch_id').val(data.branch_id);

                    // ADD THIS LINE TO SHOW THE STATUS
                    $('#edit_status').val(data.status);

                    $('#editCategoryModal').modal('show');
                });
            });

            // UPDATE (Use FormData for potential Image Upload)
            $('#editCategoryForm').on('submit', function(e) {
                e.preventDefault();
                let id = $('#edit_id').val();
                let url = "{{ route('admin.category.update', ':id') }}".replace(':id', id);
                let formData = new FormData(this);

                $.ajax({
                    url: url,
                    method: 'POST', // Using POST with _method or direct route
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

            // DELETE (SweetAlert2)
            $(document).on('click', '.delete-btn', function() {
                let id = $(this).data('id');
                let url = "{{ route('admin.category.delete', ':id') }}".replace(':id', id);

                Swal.fire({
                    title: 'Delete Category?',
                    text: "This will affect menus under this category!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
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
