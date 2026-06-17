@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Watch Us on Facebook – Reels</h4>
                <div class="page-title-right">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Facebook Reels</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- CREATE FORM -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Add Facebook Reel</h5>
                </div>
                <div class="card-body">
                    <form id="reelForm" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Kitchen Rush">
                            <span class="text-danger error-text title_error"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Facebook Reel URL <span class="text-danger">*</span></label>
                            <input type="url" name="facebook_url" class="form-control" placeholder="https://www.facebook.com/share/r/...">
                            <span class="text-danger error-text facebook_url_error"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Thumbnail Image <small class="text-muted">(webp, png, jpg – portrait preferred)</small></label>
                            <input type="file" name="thumbnail" class="form-control" accept="image/webp,image/png,image/jpeg">
                            <span class="text-danger error-text thumbnail_error"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control input-number" value="0" min="0">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <button type="submit" id="submitBtn" class="btn btn-primary w-100 fw-bold">
                            <i class="ri-save-line me-1"></i> Save Reel
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- DATA TABLE -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table reel-datatable table-bordered nowrap align-middle w-100">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Thumbnail</th>
                                    <th>Title</th>
                                    <th>Facebook Link</th>
                                    <th>Sort</th>
                                    <th>Status</th>
                                    <th width="90px">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editReelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editReelForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Facebook Reel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="edit_title" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Facebook Reel URL <span class="text-danger">*</span></label>
                        <input type="url" name="facebook_url" id="edit_facebook_url" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Thumbnail <small class="text-muted">(Leave blank to keep current)</small></label>
                        <input type="file" name="thumbnail" class="form-control" accept="image/webp,image/png,image/jpeg">
                        <div id="edit_current_thumb" class="mt-1"></div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Sort Order</label>
                            <input type="number" name="sort_order" id="edit_sort_order" class="form-control input-number" min="0">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line me-1"></i> Update Reel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    const table = $('.reel-datatable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ route('admin.facebook-reels.index') }}",
            error: function(xhr, status, error) {
                console.error('DataTables error:', xhr, status, error);
                toastr.error('Failed to load reels. Check console.');
            }
        },
        columns: [
            { data: 'DT_RowIndex',       name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'thumbnail_preview', name: 'thumbnail',   orderable: false, searchable: false },
            { data: 'title',             name: 'title' },
            { data: 'facebook_link',     name: 'facebook_url', orderable: false, searchable: false },
            { data: 'sort_order',        name: 'sort_order' },
            { data: 'status',            name: 'status', orderable: false },
            { data: 'action',            name: 'action', orderable: false, searchable: false },
        ]
    });

    // CREATE
    $('#reelForm').on('submit', function (e) {
        e.preventDefault();
        $('.error-text').text('');
        const btn = $('#submitBtn');
        btn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: "{{ route('admin.facebook-reels.store') }}",
            method: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function (res) {
                Command: toastr[res.status](res.message);
                $('#reelForm')[0].reset();
                table.ajax.reload();
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    $.each(xhr.responseJSON.errors, function (key, val) {
                        $('.' + key + '_error').text(val[0]);
                    });
                } else {
                    Command: toastr['error']('Something went wrong.');
                }
            },
            complete: function () { btn.prop('disabled', false).text('Save Reel'); }
        });
    });

    // EDIT (fetch)
    $(document).on('click', '.edit-btn', function () {
        const id  = $(this).data('id');
        const url = "{{ route('admin.facebook-reels.edit', ':id') }}".replace(':id', id);

        $.get(url, function (data) {
            $('#edit_id').val(data.id);
            $('#edit_title').val(data.title);
            $('#edit_facebook_url').val(data.facebook_url);
            $('#edit_sort_order').val(data.sort_order);
            $('#edit_status').val(data.status ? '1' : '0');

            const thumbWrap = $('#edit_current_thumb');
            if (data.thumbnail) {
                thumbWrap.html(`<img src="/uploads/reels/${data.thumbnail}" height="60" class="rounded mt-1 object-fit-cover" alt="thumbnail" />`);
            } else {
                thumbWrap.html('<span class="text-muted small">No current thumbnail</span>');
            }

            $('#editReelModal').modal('show');
        });
    });

    // UPDATE
    $('#editReelForm').on('submit', function (e) {
        e.preventDefault();
        const id  = $('#edit_id').val();
        const url = "{{ route('admin.facebook-reels.update', ':id') }}".replace(':id', id);

        $.ajax({
            url: url,
            method: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function (res) {
                $('#editReelModal').modal('hide');
                Command: toastr[res.status](res.message);
                table.ajax.reload();
            },
            error: function () { Command: toastr['error']('Update failed.'); }
        });
    });

    // DELETE
    $(document).on('click', '.delete-btn', function () {
        const id  = $(this).data('id');
        const url = "{{ route('admin.facebook-reels.delete', ':id') }}".replace(':id', id);

        Swal.fire({
            title: 'Delete Reel?',
            text: 'The thumbnail and link will be removed.',
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
                    success: function (res) {
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
