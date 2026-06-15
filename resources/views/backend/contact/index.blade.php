@extends('dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Contact / Location Section</h4>
                <div class="page-title-right">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Contact Section</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Edit Contact / Location Section</h5>
                </div>
                <div class="card-body">
                    @php
                        $val = fn($key) => $settings[$key]->value ?? '';
                    @endphp

                    <form id="contactForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Section Title <small class="text-muted">(e.g. Visit Us)</small></label>
                                <input type="text" name="contact_section_title" class="form-control" value="{{ $val('contact_section_title') }}" placeholder="Visit Us">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Section Subtitle</label>
                                <input type="text" name="contact_section_subtitle" class="form-control" value="{{ $val('contact_section_subtitle') }}" placeholder="We look forward to welcoming you">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Restaurant Name</label>
                                <input type="text" name="contact_restaurant_name" class="form-control" value="{{ $val('contact_restaurant_name') }}" placeholder="Degchi Dine">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Phone / Reservation Number</label>
                                <input type="text" name="contact_phone" class="form-control" value="{{ $val('contact_phone') }}" placeholder="+880 1234 567 890">
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Address</label>
                                <textarea name="contact_address" class="form-control" rows="2" placeholder="Boropool Circle, Kaptan Villa, Halishahar, Chittagong.">{{ $val('contact_address') }}</textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Opening Hours</label>
                                <input type="text" name="contact_hours" class="form-control" value="{{ $val('contact_hours') }}" placeholder="Mon - Sun: 11:00 AM - 11:00 PM">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Email Address</label>
                                <input type="email" name="contact_email" class="form-control" value="{{ $val('contact_email') }}" placeholder="info@example.com">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Facebook Page URL</label>
                                <input type="url" name="contact_facebook_url" class="form-control" value="{{ $val('contact_facebook_url') }}" placeholder="https://www.facebook.com/DegchiDine">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Instagram Page URL</label>
                                <input type="url" name="contact_instagram_url" class="form-control" value="{{ $val('contact_instagram_url') }}" placeholder="https://www.instagram.com/...">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-semibold">Google Maps "Get Directions" Link</label>
                                <input type="url" name="contact_map_link" class="form-control" value="{{ $val('contact_map_link') }}" placeholder="https://maps.google.com/?q=...">
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">
                                    Google Maps Embed Iframe src
                                    <small class="text-muted">(paste only the src="..." URL from Google Maps embed code)</small>
                                </label>
                                <textarea name="contact_map_embed" class="form-control font-monospace" rows="4" placeholder="https://www.google.com/maps/embed?pb=...">{{ $val('contact_map_embed') }}</textarea>
                                <small class="text-muted">Go to Google Maps → Share → Embed a map → copy only the URL inside <code>src="..."</code></small>
                            </div>
                        </div>

                        <div class="mt-2">
                            <button type="submit" id="contactSubmitBtn" class="btn btn-primary px-4">
                                <i class="ri-save-line me-1"></i> Save Contact Section
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    $('#contactForm').on('submit', function (e) {
        e.preventDefault();
        const btn = $('#contactSubmitBtn');
        btn.prop('disabled', true).html('<i class="ri-loader-line me-1"></i> Saving...');

        $.ajax({
            url: "{{ route('admin.contact.store') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function (res) {
                Command: toastr[res.status](res.message);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    $.each(xhr.responseJSON.errors, function (key, val) {
                        Command: toastr['error'](val[0]);
                    });
                } else {
                    Command: toastr['error']('Something went wrong.');
                }
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="ri-save-line me-1"></i> Save Contact Section');
            }
        });
    });
});
</script>
@endpush
