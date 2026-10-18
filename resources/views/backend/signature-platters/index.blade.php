@extends('layouts.dashboard')

@push('styles')
    <style>
        table.dataTable {
            width: 100% !important;
            margin: 0 auto;
            clear: both;
            border-collapse: collapse !important;
        }

        .platter-datatable td {
            white-space: normal !important;
            word-wrap: break-word;
            max-width: 200px;
        }

        @media (max-width: 1200px) {
            .col-xl-4 {
                margin-bottom: 20px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4 admin-crud-page">
        <div class="row">
            <div class="col-xl-4 col-lg-5">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Add New Signature Platter</h5>
                    </div>
                    <div class="card-body">
                        <form id="plateForm" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold">Title</label>
                                <input type="text" name="title" class="form-control" placeholder="Enter platter title">
                                <span class="text-danger error-text title_error" style="font-size: 13px;"></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Subtitle</label>
                                <input type="text" name="subtitle" class="form-control" placeholder="Enter subtitle">
                                <span class="text-danger error-text subtitle_error" style="font-size: 13px;"></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Description</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Enter description"></textarea>
                                <span class="text-danger error-text description_error" style="font-size: 13px;"></span>
                            </div>
                            <div class="mb-3">
                                <hr class="my-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <label class="form-label mb-0 fw-bold">Features</label>
                                        <small class="text-muted d-block">Highlighted points shown on the homepage (first 2)</small>
                                    </div>
                                    <button type="button" class="btn btn-sm admin-add-feature-btn add-feature" data-target="#featureList">
                                        <i class="ri-add-line me-1"></i>Add Feature
                                    </button>
                                </div>
                                <div id="featureList" class="feature-list"></div>
                                <span class="text-danger error-text features_error d-block mt-1" style="font-size: 0.85rem;"></span>
                            </div>
                            <div class="admin-file-field mb-3">
                                <label class="form-label fw-bold">Thumbnail Image <i class="ri-image-add-line"></i></label>
                                <input type="file" name="thumbnail_image" class="form-control" accept="image/*">
                                <span class="admin-file-field__hint">For slider (500x500px)</span>
                                <span class="text-danger error-text thumbnail_image_error" style="font-size: 13px;"></span>
                            </div>
                            <div class="admin-file-field mb-3">
                                <label class="form-label fw-bold">Menu Card Image <i class="ri-image-add-line"></i></label>
                                <input type="file" name="menu_card_image" class="form-control" accept="image/*">
                                <span class="admin-file-field__hint">For popup (1200x1600px)</span>
                                <span class="text-danger error-text menu_card_image_error" style="font-size: 13px;"></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <select name="status" class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <span class="text-danger error-text status_error" style="font-size: 13px;"></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" value="0" min="0">
                                <span class="text-danger error-text sort_order_error" style="font-size: 13px;"></span>
                            </div>
                            <button type="submit" id="submitBtn" class="btn btn-primary w-100 py-2">
                                <i class="fas fa-save me-1"></i> Save Platter
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-8 col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table platter-datatable table-hover table-bordered align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Thumbnail</th>
                                        <th>Title</th>
                                        <th>Subtitle</th>
                                        <th>Status</th>
                                        <th width="100">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editPlatterModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content border-0">
                <form id="editPlatterForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="edit_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Signature Platter</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" id="edit_title" name="title" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subtitle</label>
                            <input type="text" id="edit_subtitle" name="subtitle" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea id="edit_description" name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <label class="form-label mb-0">Features</label>
                                    <small class="text-muted d-block">Highlighted points shown on the homepage (first 2)</small>
                                </div>
                                <button type="button" class="btn btn-sm admin-add-feature-btn add-feature" data-target="#editFeatureList">
                                    <i class="ri-add-line me-1"></i>Add Feature
                                </button>
                            </div>
                            <div id="editFeatureList" class="feature-list"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="admin-file-field mb-3">
                                    <label class="form-label">Thumbnail Image <i class="ri-image-add-line"></i></label>
                                    <input type="file" id="edit_thumbnail_image" name="thumbnail_image" class="form-control" accept="image/*">
                                    <span class="admin-file-field__hint">Leave empty to keep current</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="admin-file-field mb-3">
                                    <label class="form-label">Menu Card Image <i class="ri-image-add-line"></i></label>
                                    <input type="file" id="edit_menu_card_image" name="menu_card_image" class="form-control" accept="image/*">
                                    <span class="admin-file-field__hint">Leave empty to keep current</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select id="edit_status" name="status" class="form-control">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Sort Order</label>
                                    <input type="number" id="edit_sort_order" name="sort_order" class="form-control" min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Platter</button>
                    </div>
                </form>
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

            function featureRowHtml(i) {
                i = i || 0;
                return `
                    <div class="admin-feature-row" data-f-index="${i}">
                        <button type="button" class="btn btn-sm btn-danger remove-feature" title="Remove feature">
                            <i class="ri-close-line"></i>
                        </button>
                        <div class="row g-3">
                            <div class="col-12">
                                <small class="text-muted d-flex align-items-center">
                                    <span class="admin-feature-index">${i + 1}</span>
                                    <strong>Feature <span class="feature-num">${i + 1}</span></strong>
                                </small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Label <span class="text-muted fw-normal">(optional)</span></label>
                                <input type="text" class="form-control form-control-sm feature-label"
                                    placeholder="e.g. Veg Lunch Special">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Icon <span class="text-muted fw-normal">(optional)</span></label>
                                <input type="text" class="form-control form-control-sm feature-icon"
                                    placeholder="e.g. fa-leaf" title="Font Awesome icon class (without fa-solid)">
                            </div>
                            <div class="col-12">
                                <label class="form-label small">Text <span class="text-danger">*</span></label>
                                <textarea class="form-control form-control-sm feature-text" rows="2"
                                    placeholder="e.g. Paneer Butter Masala, Dal Tadka served with Basmati Rice &amp; Naan"></textarea>
                            </div>
                        </div>
                    </div>`;
            }

            function syncFeatureRows(container) {
                const $rows = container.find('.admin-feature-row');

                $rows.each(function(i) {
                    const $row = $(this);
                    const $label = $row.find('.feature-label');
                    const $text = $row.find('.feature-text');
                    const $icon = $row.find('.feature-icon');
                    const label = String($label.val() || '').trim();
                    const icon = String($icon.val() || '').trim();

                    $row.attr('data-f-index', i);
                    $row.find('.admin-feature-index, .feature-num').text(i + 1);

                    if (label || icon) {
                        label ? $label.attr('name', 'features[' + i + '][label]') : $label.removeAttr('name');
                        $text.attr('name', 'features[' + i + '][text]');
                        icon ? $icon.attr('name', 'features[' + i + '][icon]') : $icon.removeAttr('name');
                    } else {
                        $label.removeAttr('name');
                        $text.attr('name', 'features[' + i + ']');
                        $icon.removeAttr('name');
                    }
                });

                $rows.removeClass('first').first().addClass('first');
                $rows.find('.remove-feature').toggle($rows.length > 1);
            }

            function populateFeatureRows(container, features) {
                container.empty();
                const list = (Array.isArray(features) && features.length) ? features : [null];

                list.forEach(function(feature, i) {
                    const $row = $(featureRowHtml(i));

                    if (feature && typeof feature === 'object') {
                        $row.find('.feature-label').val(feature.label || '');
                        $row.find('.feature-text').val(feature.text || '');
                        $row.find('.feature-icon').val(feature.icon || '');
                    } else if (feature) {
                        $row.find('.feature-text').val(String(feature));
                    }

                    container.append($row);
                });

                syncFeatureRows(container);
            }

            $(document).on('click', '.add-feature', function() {
                const $container = $($(this).data('target'));
                $container.append(featureRowHtml($container.children('.admin-feature-row').length));
                syncFeatureRows($container);
            });

            $(document).on('click', '.remove-feature', function() {
                const $container = $(this).closest('.feature-list');
                $(this).closest('.admin-feature-row').remove();

                if (!$container.children('.admin-feature-row').length) {
                    $container.append(featureRowHtml(0));
                }

                syncFeatureRows($container);
            });

            populateFeatureRows($('#featureList'), []);

            const table = $('.platter-datatable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('admin.signature-platters.index') }}",
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'thumbnail_preview',
                        name: 'thumbnail_preview',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'subtitle',
                        name: 'subtitle'
                    },
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
                ],
                columnDefs: [
                    {
                        responsivePriority: 1,
                        targets: 0
                    },
                    {
                        responsivePriority: 2,
                        targets: 5
                    }
                ]
            });

            // CREATE
            $('#plateForm').on('submit', function(e) {
                e.preventDefault();
                $('.error-text').text('');
                syncFeatureRows($('#featureList'));
                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('admin.signature-platters.store') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('#submitBtn').prop('disabled', true).text('Processing...');
                    },
                    success: function(res) {
                        toastr.success(res.message);
                        $('#plateForm')[0].reset();
                        populateFeatureRows($('#featureList'), []);
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            $.each(xhr.responseJSON.errors, function(key, val) {
                                $('.' + key + '_error').text(val[0]);
                            });
                        }
                    },
                    complete: function() {
                        $('#submitBtn').prop('disabled', false).text('Save Platter');
                    }
                });
            });

            // EDIT (Fetch)
            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                let url = "{{ route('admin.signature-platters.edit', ':id') }}".replace(':id', id);
                $.get(url, function(data) {
                    $('#edit_id').val(data.id);
                    $('#edit_title').val(data.title);
                    $('#edit_subtitle').val(data.subtitle);
                    $('#edit_description').val(data.description);
                    $('#edit_status').val(data.status);
                    $('#edit_sort_order').val(data.sort_order);
                    populateFeatureRows($('#editFeatureList'), data.features);
                    $('#editPlatterModal').modal('show');
                });
            });

            // UPDATE
            $('#editPlatterForm').on('submit', function(e) {
                e.preventDefault();
                let id = $('#edit_id').val();
                let url = "{{ route('admin.signature-platters.update', ':id') }}".replace(':id', id);
                syncFeatureRows($('#editFeatureList'));
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        $('#editPlatterModal').modal('hide');
                        toastr.success(res.message);
                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        toastr.error('Error updating platter');
                    }
                });
            });

            // DELETE
            $(document).on('click', '.delete-btn', function() {
                let id = $(this).data('id');
                let url = "{{ route('admin.signature-platters.delete', ':id') }}".replace(':id', id);

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            method: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                if (res.status === 'success') {
                                    Swal.fire(
                                        'Deleted!',
                                        res.message,
                                        'success'
                                    );
                                    table.ajax.reload();
                                }
                            },
                            error: function() {
                                Swal.fire(
                                    'Error!',
                                    'Something went wrong while deleting.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
