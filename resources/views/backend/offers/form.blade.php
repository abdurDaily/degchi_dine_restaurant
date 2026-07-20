@extends('layouts.dashboard')
@section('title', isset($offer->id) ? 'Edit Offer' : 'Create Offer')

@section('content')
<x-breadcrumb></x-breadcrumb>

@php $isEdit = isset($offer->id); @endphp

<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-10">
        <div class="card shadow-sm">
            <div class="card-header py-3 d-flex align-items-center gap-2">
                <i class="ri-price-tag-3-line text-primary fs-5"></i>
                <h5 class="mb-0 fw-bold">{{ $isEdit ? 'Edit Offer' : 'Create New Offer' }}</h5>
                <a href="{{ route('offers.index') }}" class="btn btn-sm btn-outline-secondary ms-auto">
                    <i class="ri-arrow-left-line me-1"></i> Back
                </a>
            </div>

            <div class="card-body">
                <form method="POST"
                      action="{{ $isEdit ? route('offers.update', $offer->id) : route('offers.store') }}"
                      enctype="multipart/form-data">
                    @csrf
                    @if($isEdit) @method('PUT') @endif

                    {{-- Name + Discount --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Offer Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $offer->name) }}" placeholder="e.g. Eid Special Offer" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Discount %</label>
                            <div class="input-group">
                                <input type="number" name="discount_percent" min="0" max="100"
                                       class="form-control @error('discount_percent') is-invalid @enderror"
                                       value="{{ old('discount_percent', $offer->discount_percent ?? 0) }}" required>
                                <span class="input-group-text">%</span>
                            </div>
                            @error('discount_percent')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" rows="3"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Describe the offer…">{{ old('description', $offer->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Offer Type --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Offer Type <span class="text-danger">*</span></label>
                        <select name="offer_type" id="offerType" class="form-select @error('offer_type') is-invalid @enderror">
                            <option value="all_items" {{ old('offer_type', $offer->offer_type ?? 'all_items') === 'all_items' ? 'selected' : '' }}>
                                All Food Items
                            </option>
                            <option value="specific_items" {{ old('offer_type', $offer->offer_type) === 'specific_items' ? 'selected' : '' }}>
                                Specific Food Items
                            </option>
                        </select>
                        @error('offer_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Menu Items Selection (shown only for specific_items) --}}
                    <div id="menuItemsSection" class="mb-3" style="display: {{ old('offer_type', $offer->offer_type ?? 'all_items') === 'specific_items' ? 'block' : 'none' }}">
                        <label class="form-label fw-semibold">Select Food Items <span class="text-danger">*</span></label>
                        <select name="menu_variations[]" id="menuVariations" class="form-select select2-menu-items @error('menu_variations') is-invalid @enderror" multiple>
                            @foreach(\App\Models\Menu::with('variations')->where('is_available', 1)->orderBy('name')->get() as $menu)
                                @foreach($menu->variations as $variation)
                                    <option value="{{ $variation->id }}" 
                                        data-category="{{ $menu->category->name ?? 'Uncategorized' }}"
                                        {{ (is_array(old('menu_variations')) && in_array($variation->id, old('menu_variations'))) || 
                                           (isset($offer) && $offer->menuVariations->contains($variation->id)) ? 'selected' : '' }}>
                                        {{ $menu->name }} - {{ $variation->name }} (৳{{ number_format($variation->price, 2) }})
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                        <div class="form-text">
                            <i class="ri-search-line me-1"></i> Type to search for food items by name or category
                        </div>
                        @error('menu_variations')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Applicable To + Min Total --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Applicable To</label>
                            <select name="applicable_to" class="form-select @error('applicable_to') is-invalid @enderror">
                                @foreach(['all','membership','student','golden'] as $opt)
                                    <option value="{{ $opt }}" {{ old('applicable_to', $offer->applicable_to ?? 'all') === $opt ? 'selected' : '' }}>
                                        {{ $opt === 'all' ? 'All customers (public promo)' : ucfirst($opt) }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                <strong>All</strong> = food menu promo (menu badges). 
                                <strong>Membership / Student</strong> = checkout member-card discount (use the built-in First Order rows). 
                                <strong>Golden</strong> = golden card benefit / threshold.
                            </div>
                            @error('applicable_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Min Order Total (৳)</label>
                            <input type="number" name="min_total" min="0" step="0.01"
                                   class="form-control @error('min_total') is-invalid @enderror"
                                   value="{{ old('min_total', $offer->min_total) }}"
                                   placeholder="Leave blank for no minimum">
                            @error('min_total')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size:.78rem;letter-spacing:.07em;">
                        <i class="ri-megaphone-line me-1"></i> Popup Ad Settings
                    </h6>

                    {{-- Show as Popup toggle --}}
                    <div class="mb-3 d-flex align-items-center gap-3">
                        <div class="form-check form-switch mb-0">
                            <input type="checkbox" class="form-check-input" name="show_as_popup" id="showAsPopup"
                                   value="1" {{ old('show_as_popup', $offer->show_as_popup ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="showAsPopup">
                                Show as popup on home page
                            </label>
                        </div>
                        <span class="badge bg-info-subtle text-info small">Visible to all visitors on the website</span>
                    </div>

                    {{-- Popup image --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Popup Image <span class="text-muted fw-normal">(WebP, PNG, JPG — max 2MB)</span></label>

                        {{-- Current image preview --}}
                        @if($isEdit && $offer->popup_image)
                        <div id="currentImgWrap" class="mb-2">
                            <img src="{{ asset('storage/' . $offer->popup_image) }}"
                                 alt="Current" id="currentImg"
                                 style="max-height:160px;border-radius:10px;border:1.5px solid #dee2e6;">
                            <div class="text-muted small mt-1">Current image — upload a new one to replace it</div>
                        </div>
                        @endif

                        <input type="file" name="popup_image" id="popupImageInput"
                               class="form-control @error('popup_image') is-invalid @enderror"
                               accept="image/webp,image/png,image/jpeg">
                        @error('popup_image')<div class="invalid-feedback">{{ $message }}</div>@enderror

                        {{-- New image preview --}}
                        <div id="newImgPreviewWrap" class="d-none mt-2">
                            <img id="newImgPreview" src="" alt="Preview"
                                 style="max-height:160px;border-radius:10px;border:1.5px solid #28a745;">
                            <div class="text-muted small mt-1">New image preview</div>
                        </div>
                    </div>

                    {{-- Badge + Expires --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Popup Badge Label</label>
                            <input type="text" name="popup_badge"
                                   class="form-control @error('popup_badge') is-invalid @enderror"
                                   value="{{ old('popup_badge', $offer->popup_badge) }}"
                                   placeholder="e.g. Eid Special, Flash Sale">
                            <div class="form-text">Small badge shown on the popup image corner</div>
                            @error('popup_badge')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Popup Expires On</label>
                            <input type="date" name="popup_expires_at"
                                   class="form-control datepicker @error('popup_expires_at') is-invalid @enderror"
                                   value="{{ old('popup_expires_at', $offer->popup_expires_at?->format('Y-m-d')) }}">
                            <div class="form-text">Popup auto-hides after this date (leave blank = always show)</div>
                            @error('popup_expires_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- Flags --}}
                    <div class="d-flex flex-wrap gap-4 mb-4">
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input" name="is_active" id="isActive"
                                   value="1" {{ old('is_active', $offer->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isActive">Active</label>
                        </div>
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input" name="is_first_order" id="isFirstOrder"
                                   value="1" {{ old('is_first_order', $offer->is_first_order ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isFirstOrder">First Order Only</label>
                            <div class="form-text">
                                <strong>Membership / Student:</strong> discount applies on that member’s <em>first order only</em>.
                                After they place one order, they get <strong>no more</strong> membership/student discount
                                (until Golden). <strong>All (food promo):</strong> requires member login; badge hides after their first order.
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="ri-save-line me-1"></i>
                            {{ $isEdit ? 'Save Changes' : 'Create Offer' }}
                        </button>
                        <a href="{{ route('offers.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function(){
    // Image preview
    $('#popupImageInput').on('change', function(){
        const file = this.files[0];
        if(!file){ $('#newImgPreviewWrap').addClass('d-none'); return; }
        const reader = new FileReader();
        reader.onload = e => {
            $('#newImgPreview').attr('src', e.target.result);
            $('#newImgPreviewWrap').removeClass('d-none');
        };
        reader.readAsDataURL(file);
    });

    // Toggle menu items section based on offer type
    $('#offerType').on('change', function() {
        if ($(this).val() === 'specific_items') {
            $('#menuItemsSection').show();
        } else {
            $('#menuItemsSection').hide();
        }
    });

    // Initialize Select2 for searchable menu items
    $('.select2-menu-items').select2({
        placeholder: 'Search and select food items...',
        allowClear: true,
        width: '100%',
        templateResult: formatMenuItem,
        templateSelection: formatMenuSelection
    });

    function formatMenuItem(item) {
        if (!item.id) return item.text;
        
        const category = $(item.element).data('category');
        const $item = $(
            '<div class="d-flex flex-column">' +
                '<div class="fw-semibold">' + item.text.split(' (')[0] + '</div>' +
                '<small class="text-muted"><i class="ri-bookmark-line"></i> ' + category + '</small>' +
            '</div>'
        );
        return $item;
    }

    function formatMenuSelection(item) {
        return item.text.split(' (')[0]; // Show only name without price in selection
    }
});
</script>
@endpush
