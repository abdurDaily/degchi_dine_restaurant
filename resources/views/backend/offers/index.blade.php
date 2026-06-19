@extends('layouts.dashboard')
@section('title', 'Offers')

@section('content')
<x-breadcrumb></x-breadcrumb>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between py-3">
                <h5 class="mb-0 fw-bold"><i class="ri-price-tag-3-line me-2 text-primary"></i>Offers & Promotions</h5>
                <a href="{{ route('offers.create') }}" class="btn btn-primary btn-sm">
                    <i class="ri-add-line me-1"></i> New Offer
                </a>
            </div>

            <div class="card-body p-0">
                <div class="alert alert-info border-0 rounded-0 mb-0 py-3 px-4">
                    <strong><i class="ri-information-line me-1"></i> First-order offers</strong>
                    <ul class="mb-0 mt-2 small ps-3">
                        <li><strong>Membership First Order (30%)</strong> — non-student members only, first order.</li>
                        <li><strong>Student Membership First Order (35%)</strong> — approved students only. Pending or rejected students never receive this offer.</li>
                        <li>At checkout, the system applies the <strong>highest discount among eligible offers only</strong> — not the global maximum.</li>
                    </ul>
                </div>
                @if($offers->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="ri-price-tag-3-line" style="font-size:3rem;opacity:.3;"></i>
                        <p class="mt-2">No offers yet. <a href="{{ route('offers.create') }}">Create one</a>.</p>
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th class="text-center">Discount</th>
                                <th>Applies To</th>
                                <th class="text-center">Popup</th>
                                <th>Expires</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($offers as $i => $offer)
                            <tr>
                                <td class="text-muted small">{{ $i + 1 }}</td>
                                <td>
                                    @if($offer->popup_image)
                                        <img src="{{ asset('storage/' . $offer->popup_image) }}"
                                             alt="{{ $offer->name }}"
                                             style="width:52px;height:40px;object-fit:cover;border-radius:6px;">
                                    @else
                                        <div style="width:52px;height:40px;background:#f0f0f0;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#bbb;">
                                            <i class="ri-image-line"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $offer->name }}</div>
                                    @if($offer->popup_badge)
                                        <span class="badge bg-danger-subtle text-danger" style="font-size:.7rem;">{{ $offer->popup_badge }}</span>
                                    @endif
                                    @if($offer->description)
                                        <div class="text-muted small" style="max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $offer->description }}</div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($offer->discount_percent > 0)
                                        <span class="badge bg-success fs-6 fw-bold">{{ $offer->discount_percent }}%</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-secondary-subtle text-secondary text-capitalize">{{ $offer->applicable_to }}</span></td>
                                <td class="text-center">
                                    @if($offer->show_as_popup)
                                        <span class="badge bg-primary-subtle text-primary"><i class="ri-megaphone-line me-1"></i>Yes</span>
                                    @else
                                        <span class="text-muted small">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($offer->popup_expires_at)
                                        <span class="{{ $offer->popup_expires_at->isPast() ? 'text-danger' : 'text-success' }} small fw-semibold">
                                            {{ $offer->popup_expires_at->format('M d, Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted small">No expiry</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch d-inline-block m-0">
                                        <input type="checkbox" class="form-check-input status-toggle"
                                               data-id="{{ $offer->id }}"
                                               data-url="{{ route('offers.toggle', $offer->id) }}"
                                               {{ $offer->is_active ? 'checked' : '' }}
                                               style="cursor:pointer;">
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="{{ route('offers.edit', $offer->id) }}"
                                           class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger delete-offer-btn"
                                                data-id="{{ $offer->id }}"
                                                data-url="{{ route('offers.destroy', $offer->id) }}"
                                                title="Delete">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function(){
    // Toggle active status
    $(document).on('change', '.status-toggle', function(){
        const url = $(this).data('url');
        const cb  = this;
        $.post(url, {_token: '{{ csrf_token() }}'}, function(res){
            if(res.success){
                toastr.success('Status updated.');
            }
        }).fail(function(){
            cb.checked = !cb.checked;
            toastr.error('Failed to update status.');
        });
    });

    // Delete
    $(document).on('click', '.delete-offer-btn', function(){
        const url = $(this).data('url');
        const row = $(this).closest('tr');
        Swal.fire({
            title: 'Delete this offer?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Yes, delete',
        }).then(res => {
            if(res.isConfirmed){
                $.ajax({
                    url, method: 'DELETE',
                    data: {_token: '{{ csrf_token() }}'},
                    success: function(r){
                        if(r.success){
                            row.fadeOut(300, () => row.remove());
                            toastr.success(r.message);
                        }
                    },
                    error: () => toastr.error('Failed to delete.')
                });
            }
        });
    });
});
</script>
@endpush
