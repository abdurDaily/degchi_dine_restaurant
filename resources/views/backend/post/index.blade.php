@extends('layouts.dashboard')

@section('title', 'Blog Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-crud.css') }}">
    <style>
        .nav-tabs .nav-link { color: #495057; border: none; padding: 0.75rem 1.25rem; font-weight: 500; border-radius: 8px 8px 0 0; }
        .nav-tabs .nav-link.active { color: #fff; background: var(--brand); }
        .post-image-preview { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; }
        .comment-text-preview { max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .status-badge { padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 500; }
        .status-badge.active, .status-badge.enabled { background: #d4edda; color: #155724; }
        .status-badge.inactive { background: #f8d7da; color: #721c24; }
        .status-badge.disabled { background: #fff3cd; color: #856404; }
        .image-preview-container { position: relative; display: inline-block; }
        .image-preview-container img { max-height: 100px; border-radius: 8px; border: 1px solid #dee2e6; }
        .image-preview-container .remove-image-btn {
            position: absolute; top: -8px; right: -8px; background: #dc3545; color: #fff;
            border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer;
        }
        .admin-crud-actions__btn--toggle { background: var(--brand-teal-light); color: var(--brand); }
        .admin-crud-actions__btn--hide { background: #fff3cd; color: #856404; }
        .admin-crud-actions__btn--show { background: #d4edda; color: #155724; }
    </style>
@endpush

@section('content')
<div class="container-fluid py-4 admin-crud-page">
    <div class="admin-crud-header">
        <div>
            <h3 class="admin-crud-header__title">Blog Management</h3>
            <p class="admin-crud-header__lead">Manage posts, categories, and comments</p>
        </div>
    </div>

    <ul class="nav nav-tabs" id="blogTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="posts-tab" data-bs-toggle="tab" data-bs-target="#posts-pane" type="button" role="tab">
                <i class="ri-file-text-line"></i> Posts
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories-pane" type="button" role="tab">
                <i class="ri-price-tag-3-line"></i> Categories
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="comments-tab" data-bs-toggle="tab" data-bs-target="#comments-pane" type="button" role="tab">
                <i class="ri-chat-3-line"></i> Comments
            </button>
        </li>
    </ul>

    <div class="tab-content" id="blogTabsContent">
        <div class="tab-pane fade show active" id="posts-pane" role="tabpanel">
            <div class="admin-crud-card">
                <div class="admin-crud-card__head d-flex justify-content-between align-items-center">
                    <h5><i class="ri-file-text-line me-1"></i> All Posts</h5>
                    <button type="button" class="admin-crud-btn-primary" data-bs-toggle="modal" data-bs-target="#addPostModal">
                        <i class="ri-add-line"></i>Create Post
                    </button>
                </div>
                <div class="admin-crud-card__body admin-crud-card__body--flush">
                    <div class="admin-crud-table-wrap">
                        <table class="table admin-datatable posts-datatable table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th width="60">No</th>
                                    <th width="70">Image</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Author</th>
                                    <th width="100">Status</th>
                                    <th width="100">Comments</th>
                                    <th width="100">Views</th>
                                    <th width="140">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="categories-pane" role="tabpanel">
            <div class="admin-crud-card">
                <div class="admin-crud-card__head d-flex justify-content-between align-items-center">
                    <h5><i class="ri-price-tag-3-line me-1"></i> All Categories</h5>
                    <button type="button" class="admin-crud-btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="ri-add-line"></i>Add Category
                    </button>
                </div>
                <div class="admin-crud-card__body admin-crud-card__body--flush">
                    <div class="admin-crud-table-wrap">
                        <table class="table admin-datatable categories-datatable table-hover align-middle mb-0">
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

        <div class="tab-pane fade" id="comments-pane" role="tabpanel">
            <div class="admin-crud-card">
                <div class="admin-crud-card__head">
                    <h5><i class="ri-chat-3-line me-1"></i> All Comments</h5>
                </div>
                <div class="admin-crud-card__body admin-crud-card__body--flush">
                    <div class="admin-crud-table-wrap">
                        <table class="table admin-datatable comments-datatable table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th width="60">No</th>
                                    <th>Comment</th>
                                    <th>Post</th>
                                    <th>Member</th>
                                    <th width="100">Status</th>
                                    <th width="80">Replies</th>
                                    <th width="80">Likes</th>
                                    <th width="80">Dislikes</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Post Modal --}}
    <div class="modal fade" id="addPostModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header admin-modal-header">
                    <h5 class="modal-title">
                        <i class="ri-file-text-fill me-2"></i><span id="postModalTitle">Create New Post</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="postForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="remove_image" value="0">
                    <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required>
                                <span class="text-danger error-text title_error d-block mt-1" style="font-size: 0.85rem;"></span>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Category</label>
                                <select name="blog_category_id" class="form-select" id="postCategorySelect">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-text blog_category_id_error d-block mt-1" style="font-size: 0.85rem;"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Author</label>
                                <select name="author_id" class="form-select" id="postAuthorSelect">
                                    @foreach($authors as $author)
                                        <option value="{{ $author->id }}" @selected($author->id === auth()->id())>{{ $author->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-text author_id_error d-block mt-1" style="font-size: 0.85rem;"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Featured Image</label>
                                <input type="file" name="image" id="postImageInput" class="form-control"
                                    accept="image/jpeg,image/png,image/webp,image/gif">
                                <span class="text-danger error-text image_error d-block mt-1" style="font-size: 0.85rem;"></span>
                                <small class="text-muted d-block mt-1">Allowed: jpg, jpeg, png, gif, webp. Max size: 3MB.</small>
                                <div id="currentImagePreview" style="display:none;" class="mt-2">
                                    <div class="image-preview-container">
                                        <img id="currentImage" src="" alt="Current image">
                                        <button type="button" class="remove-image-btn" id="removeImageBtn" title="Remove image">×</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Content <span class="text-danger">*</span></label>
                                <textarea name="content" rows="8" class="form-control" required></textarea>
                                <span class="text-danger error-text content_error d-block mt-1" style="font-size: 0.85rem;"></span>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="postIsActive" name="is_active" value="1" checked>
                                    <label class="form-check-label" for="postIsActive">Published</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="postCommentsEnabled" name="comments_enabled" value="1" checked>
                                    <label class="form-check-label" for="postCommentsEnabled">Allow Comments</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-2"></i><span id="postSubmitText">Save Post</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Category Modal --}}
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header admin-modal-header">
                    <h5 class="modal-title">
                        <i class="ri-price-tag-3-fill me-2"></i><span id="categoryModalTitle">Add Category</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="categoryForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                            <span class="text-danger error-text name_error d-block mt-1" style="font-size: 0.85rem;"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" class="form-control">
                            <span class="text-danger error-text slug_error d-block mt-1" style="font-size: 0.85rem;"></span>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="tabCategoryIsActive" name="is_active" value="1" checked>
                            <label class="form-check-label" for="tabCategoryIsActive">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-2"></i><span id="categorySubmitText">Save Category</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let postsTable, categoriesTable, commentsTable;
    let currentPostId = null;
    let currentCategoryId = null;
    const authUserId = Number('{{ auth()->id() }}') || null;
    const postImageBaseUrl = @json(asset('uploads/posts'));

    $(document).ready(function() {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        initPostsTable();
        initCategoriesTable();
        initCommentsTable();

        $('#postForm').on('submit', function(e) {
            e.preventDefault();
            $('.error-text').text('');

            const url = currentPostId
                ? "{{ route('admin.posts.update', ':id') }}".replace(':id', currentPostId)
                : "{{ route('admin.posts.store') }}";

            const formData = new FormData(this);
            formData.set('is_active', $('#postIsActive').is(':checked') ? '1' : '0');
            formData.set('comments_enabled', $('#postCommentsEnabled').is(':checked') ? '1' : '0');
            formData.set('remove_image', $('input[name="remove_image"]').val() || '0');

            if (currentPostId) {
                formData.append('_method', 'PUT');
            }

            const $submitBtn = $('#postSubmitText').closest('button');
            $submitBtn.prop('disabled', true);

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res && res.success) {
                        toastr.success(res.message);
                        $('#addPostModal').modal('hide');
                        resetPostForm();
                        postsTable.ajax.reload(null, false);
                    } else {
                        // Surface the real reason instead of a blank generic message
                        console.error('Post save returned success:false ->', res);
                        toastr.error((res && res.message) ? res.message : 'Error saving post (see console for details)');
                    }
                },
                error: function(xhr) {
                    // Always log the raw response so the real cause is visible in DevTools
                    console.error('Post save failed. Status:', xhr.status, 'Response:', xhr.responseText);

                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(key, val) {
                            $('.' + key + '_error').text(Array.isArray(val) ? val[0] : val);
                        });
                        toastr.error('Please fix the highlighted fields.');
                        return;
                    }

                    if (xhr.status === 413) {
                        toastr.error('The selected image is too large for the server upload limit.');
                        return;
                    }

                    if (xhr.status === 419) {
                        toastr.error('Your session expired. Please refresh the page and try again.');
                        return;
                    }

                    const msg = (xhr.responseJSON && xhr.responseJSON.message)
                        ? xhr.responseJSON.message
                        : ('Error saving post (status ' + xhr.status + ') — check console for details');

                    toastr.error(msg);
                },
                complete: function() {
                    $submitBtn.prop('disabled', false);
                }
            });
        });

        $(document).on('click', '.edit-post-btn', function() {
            const id = $(this).data('id');
            $.get("{{ route('admin.posts.edit', ':id') }}".replace(':id', id), function(data) {
                currentPostId = id;
                $('#postModalTitle').text('Edit Post');
                $('#postSubmitText').text('Update Post');
                $('input[name="title"]').val(data.title);
                $('#postCategorySelect').val(data.blog_category_id || '');
                $('#postAuthorSelect').val(data.author_id || authUserId);
                $('textarea[name="content"]').val(data.content);
                $('#postIsActive').prop('checked', !!data.is_active);
                $('#postCommentsEnabled').prop('checked', !!data.comments_enabled);
                $('input[name="remove_image"]').val('0');
                $('#postImageInput').val('');

                if (data.image_url || data.image) {
                    $('#currentImagePreview').show();
                    $('#currentImage').attr('src', data.image_url || (postImageBaseUrl + '/' + data.image));
                } else {
                    $('#currentImagePreview').hide();
                    $('#currentImage').attr('src', '');
                }

                $('#addPostModal').modal('show');
            }).fail(function(xhr) {
                console.error('Failed to load post', xhr.status, xhr.responseText);
                toastr.error('Failed to load post');
            });
        });

        $(document).on('click', '#removeImageBtn', function() {
            $('#currentImagePreview').hide();
            $('#currentImage').attr('src', '');
            $('input[name="remove_image"]').val('1');
            $('#postImageInput').val('');
        });

        $(document).on('click', '.toggle-comments-btn', function() {
            const id = $(this).data('id');
            const status = $(this).data('status') == 1 ? 0 : 1;
            $.post("{{ route('admin.posts.toggle-comments', ':id') }}".replace(':id', id), { status: status })
                .done(function(res) {
                    if (res.success) {
                        toastr.success(res.message);
                        postsTable.ajax.reload(null, false);
                    }
                })
                .fail(function() { toastr.error('Error toggling comments'); });
        });

        $(document).on('click', '.delete-post-btn', function() {
            const id = $(this).data('id');
            const title = $(this).data('title');
            Swal.fire({
                title: 'Delete Post?',
                text: "Delete '" + title + "'? This cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Yes, Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.posts.delete', ':id') }}".replace(':id', id),
                        method: 'DELETE',
                        success: function(res) {
                            if (res.success) {
                                toastr.success(res.message);
                                postsTable.ajax.reload();
                            }
                        },
                        error: function() { toastr.error('Error deleting post'); }
                    });
                }
            });
        });

        $('#categoryForm').on('submit', function(e) {
            e.preventDefault();
            $('.error-text').text('');

            const url = currentCategoryId
                ? "{{ route('admin.blogCategories.update', ':id') }}".replace(':id', currentCategoryId)
                : "{{ route('admin.blogCategories.store') }}";

            const formData = new FormData(this);
            formData.set('is_active', $('#tabCategoryIsActive').is(':checked') ? '1' : '0');
            formData.append('_method', currentCategoryId ? 'PUT' : 'POST');

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.message);
                        $('#addCategoryModal').modal('hide');
                        resetCategoryForm();
                        categoriesTable.ajax.reload();
                        location.reload();
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(key, val) {
                            $('.' + key + '_error').text(Array.isArray(val) ? val[0] : val);
                        });
                    } else {
                        toastr.error('Error saving category');
                    }
                }
            });
        });

        $(document).on('click', '.edit-category-btn', function() {
            const id = $(this).data('id');
            $.get("{{ route('admin.blogCategories.edit', ':id') }}".replace(':id', id), function(data) {
                currentCategoryId = id;
                $('#categoryModalTitle').text('Edit Category');
                $('#categorySubmitText').text('Update Category');
                $('#categoryForm input[name="name"]').val(data.name);
                $('#categoryForm input[name="slug"]').val(data.slug);
                $('#tabCategoryIsActive').prop('checked', !!data.is_active);
                $('#addCategoryModal').modal('show');
            });
        });

        $(document).on('click', '.delete-category-btn', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            Swal.fire({
                title: 'Delete Category?',
                text: "Delete '" + name + "'?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Yes, Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.blogCategories.delete', ':id') }}".replace(':id', id),
                        method: 'DELETE',
                        success: function(res) {
                            if (res.success) {
                                toastr.success(res.message);
                                categoriesTable.ajax.reload();
                                location.reload();
                            }
                        },
                        error: function() { toastr.error('Error deleting category'); }
                    });
                }
            });
        });

        $(document).on('click', '.toggle-comment-btn', function() {
            $.post("{{ route('admin.comments.toggle', ':id') }}".replace(':id', $(this).data('id')))
                .done(function(res) {
                    if (res.success) {
                        toastr.success(res.message);
                        commentsTable.ajax.reload(null, false);
                    }
                })
                .fail(function() { toastr.error('Error toggling comment'); });
        });

        $(document).on('click', '.delete-comment-btn', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Delete Comment?',
                text: 'This cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Yes, Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.comments.delete', ':id') }}".replace(':id', id),
                        method: 'DELETE',
                        success: function(res) {
                            if (res.success) {
                                toastr.success(res.message);
                                commentsTable.ajax.reload();
                            }
                        },
                        error: function() { toastr.error('Error deleting comment'); }
                    });
                }
            });
        });

        $('#addPostModal').on('hidden.bs.modal', resetPostForm);
        $('#addCategoryModal').on('hidden.bs.modal', resetCategoryForm);
    });

    function initPostsTable() {
        postsTable = $('.posts-datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            pageLength: 25,
            order: [[2, 'asc']],
            ajax: "{{ route('admin.posts.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '60px' },
                {
                    data: 'image',
                    name: 'image',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        if (!data) {
                            return '<span class="text-muted">No image</span>';
                        }

                        const imageUrl = String(data).startsWith('http')
                            ? data
                            : (postImageBaseUrl + '/' + String(data).replace(/^uploads\/posts\//, ''));

                        return '<img src="' + imageUrl + '" class="post-image-preview" alt="Post image">';
                    }
                },
                { data: 'title', name: 'title' },
                { data: 'blog_category.name', name: 'blogCategory.name', defaultContent: '-', orderable: false },
                { data: 'author.name', name: 'author.name', defaultContent: 'Unknown', orderable: false },
                {
                    data: 'is_active',
                    name: 'is_active',
                    render: function(data) {
                        return '<span class="status-badge ' + (data ? 'active' : 'inactive') + '">' + (data ? 'Published' : 'Draft') + '</span>';
                    }
                },
                {
                    data: 'comments_enabled',
                    name: 'comments_enabled',
                    render: function(data) {
                        return '<span class="status-badge ' + (data ? 'enabled' : 'disabled') + '">' + (data ? 'Enabled' : 'Disabled') + '</span>';
                    }
                },
                { data: 'view_count', name: 'view_count' },
                {
                    data: 'id',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="admin-crud-actions">
                                <button class="admin-crud-actions__btn admin-crud-actions__btn--view edit-post-btn" data-id="${data}" title="Edit">
                                    <i class="ri-pencil-line"></i>
                                </button>
                                <button class="admin-crud-actions__btn admin-crud-actions__btn--toggle toggle-comments-btn" data-id="${data}" data-status="${row.comments_enabled ? 1 : 0}" title="Toggle Comments">
                                    <i class="ri-chat-${row.comments_enabled ? '3' : 'off'}-line"></i>
                                </button>
                                <button class="admin-crud-actions__btn admin-crud-actions__btn--delete delete-post-btn" data-id="${data}" data-title="${$('<div>').text(row.title).html()}" title="Delete">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>`;
                    }
                }
            ]
        });
    }

    function initCategoriesTable() {
        categoriesTable = $('.categories-datatable').DataTable({
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
                {
                    data: 'id',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="admin-crud-actions">
                                <button class="admin-crud-actions__btn admin-crud-actions__btn--view edit-category-btn" data-id="${data}" title="Edit">
                                    <i class="ri-pencil-line"></i>
                                </button>
                                <button class="admin-crud-actions__btn admin-crud-actions__btn--delete delete-category-btn" data-id="${data}" data-name="${$('<div>').text(row.name).html()}" title="Delete">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>`;
                    }
                }
            ]
        });
    }

    function initCommentsTable() {
        commentsTable = $('.comments-datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            pageLength: 25,
            order: [[1, 'asc']],
            ajax: "{{ route('admin.comments.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '60px' },
                {
                    data: 'comment',
                    name: 'comment',
                    render: function(data) {
                        return '<div class="comment-text-preview">' + $('<div>').text(data || '').html() + '</div>';
                    }
                },
                { data: 'post_title', name: 'post_title', orderable: false, searchable: false },
                { data: 'member_name', name: 'member_name', orderable: false, searchable: false },
                {
                    data: 'is_active',
                    name: 'is_active',
                    render: function(data) {
                        return '<span class="status-badge ' + (data ? 'active' : 'inactive') + '">' + (data ? 'Active' : 'Hidden') + '</span>';
                    }
                },
                { data: 'replies_count', name: 'replies_count', orderable: false, searchable: false },
                { data: 'likes_count', name: 'likes_count', orderable: false, searchable: false },
                { data: 'dislikes_count', name: 'dislikes_count', orderable: false, searchable: false },
                {
                    data: 'id',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let statusIcon = row.is_active ? 'ri-eye-off-line' : 'ri-eye-line';
                        let statusClass = row.is_active ? 'hide' : 'show';
                        return `
                            <div class="admin-crud-actions">
                                <button class="admin-crud-actions__btn admin-crud-actions__btn--${statusClass} toggle-comment-btn" data-id="${data}" title="${row.is_active ? 'Hide' : 'Show'}">
                                    <i class="${statusIcon}"></i>
                                </button>
                                <button class="admin-crud-actions__btn admin-crud-actions__btn--delete delete-comment-btn" data-id="${data}" title="Delete">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>`;
                    }
                }
            ]
        });
    }

    function resetPostForm() {
        $('#postForm')[0].reset();
        $('input[name="remove_image"]').val('0');
        $('#postImageInput').val('');
        $('#currentImagePreview').hide();
        $('#currentImage').attr('src', '');
        $('.error-text').text('');
        currentPostId = null;
        $('#postModalTitle').text('Create New Post');
        $('#postSubmitText').text('Save Post');
        $('#postIsActive').prop('checked', true);
        $('#postCommentsEnabled').prop('checked', true);
        $('#postAuthorSelect').val(authUserId);
    }

    function resetCategoryForm() {
        $('#categoryForm')[0].reset();
        $('.error-text').text('');
        currentCategoryId = null;
        $('#categoryModalTitle').text('Add Category');
        $('#categorySubmitText').text('Save Category');
        $('#tabCategoryIsActive').prop('checked', true);
    }
</script>
@endsection