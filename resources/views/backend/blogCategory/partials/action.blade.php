<div class="admin-crud-actions">
    <button type="button" class="admin-crud-actions__btn admin-crud-actions__btn--view edit-btn"
            data-id="{{ $category->id }}" title="Edit">
        <i class="ri-pencil-line"></i>
    </button>
    <button type="button" class="admin-crud-actions__btn admin-crud-actions__btn--delete delete-btn"
            data-id="{{ $category->id }}" data-name="{{ $category->name }}" title="Delete">
        <i class="ri-delete-bin-line"></i>
    </button>
</div>
