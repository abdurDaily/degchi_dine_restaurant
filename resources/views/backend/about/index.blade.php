@extends('dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">About Section</h4>
                <div class="page-title-right">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">About Section</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Edit About Section</h5>
                </div>
                <div class="card-body">
                    @php
                        $val = fn($key) => $settings[$key]->value ?? '';
                    @endphp

                    <form id="aboutForm" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Kicker Text <small class="text-muted">(e.g. Our Heritage)</small></label>
                                <input type="text" name="about_kicker" class="form-control" value="{{ $val('about_kicker') }}" placeholder="e.g. Our Heritage">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Section Title <span class="text-danger">*</span></label>
                                <input type="text" name="about_title" class="form-control" value="{{ $val('about_title') }}" placeholder="e.g. The Story of Degchi Dine">
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Lead Paragraph <small class="text-muted">(Bold intro)</small></label>
                                <input type="text" name="about_lead" class="form-control" value="{{ $val('about_lead') }}" placeholder="Short bold intro paragraph...">
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Main Body Paragraph</label>
                                <input type="hidden" name="about_paragraph" id="aboutParagraphInput" value="{{ $val('about_paragraph') }}">
                                <div id="aboutParagraphEditor" class="border rounded" style="min-height: 220px;">{!! $val('about_paragraph') !!}</div>
                            </div>

                            <div class="col-12 mb-1">
                                <label class="form-label fw-semibold">Feature Highlights</label>
                            </div>
                            <div class="col-md-3 mb-3">
                                <input type="text" name="about_feature_1_icon" class="form-control" value="{{ $val('about_feature_1_icon') }}" placeholder="Icon class (e.g. bi bi-fire)">
                            </div>
                            <div class="col-md-9 mb-3">
                                <input type="text" name="about_feature_1_text" class="form-control" value="{{ $val('about_feature_1_text') }}" placeholder="Feature 1 text (e.g. Authentic Dum Style)">
                            </div>
                            <div class="col-md-3 mb-3">
                                <input type="text" name="about_feature_2_icon" class="form-control" value="{{ $val('about_feature_2_icon') }}" placeholder="Icon class (e.g. bi bi-patch-check-fill)">
                            </div>
                            <div class="col-md-9 mb-3">
                                <input type="text" name="about_feature_2_text" class="form-control" value="{{ $val('about_feature_2_text') }}" placeholder="Feature 2 text (e.g. Premium Ingredients)">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Experience Badge Number <small class="text-muted">(e.g. 10+)</small></label>
                                <input type="text" name="about_exp_number" class="form-control" value="{{ $val('about_exp_number') }}" placeholder="10+">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Experience Badge Text</label>
                                <input type="text" name="about_exp_text" class="form-control" value="{{ $val('about_exp_text') }}" placeholder="Years Of Culinary Craft">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-semibold">CTA Button URL <small class="text-muted">(Read Full Journey link)</small></label>
                                <input type="url" name="about_cta_url" class="form-control" value="{{ $val('about_cta_url') }}" placeholder="https://...">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    About Image
                                    <small class="text-muted">(webp, png, jpg – max 3MB)</small>
                                </label>
                                <input type="file" name="about_image" class="form-control" accept="image/webp,image/png,image/jpeg">
                                @if($val('about_image'))
                                    <div class="mt-2">
                                        <img src="{{ asset('uploads/about/' . $val('about_image')) }}"
                                             onerror="this.style.display='none'"
                                             class="rounded shadow-sm" height="120" alt="About Image" />
                                        <p class="text-muted small mt-1">Current image – upload new to replace</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-2">
                            <button type="submit" id="aboutSubmitBtn" class="btn btn-primary px-4">
                                <i class="ri-save-line me-1"></i> Save About Section
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{ asset('assets/libs/quill/quill.snow.css') }}" rel="stylesheet" />
@endpush

@push('scripts')
<script src="{{ asset('assets/libs/quill/quill.min.js') }}"></script>
<script>
$(document).ready(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    const aboutParagraphEditor = new Quill('#aboutParagraphEditor', {
        theme: 'snow',
        placeholder: 'Main about paragraph...'
    });
    const initialParagraph = $('#aboutParagraphInput').val();
    if (initialParagraph) {
        aboutParagraphEditor.clipboard.dangerouslyPasteHTML(initialParagraph);
    }

    $('#aboutForm').on('submit', function (e) {
        e.preventDefault();
        $('#aboutParagraphInput').val(aboutParagraphEditor.root.innerHTML);
        const btn = $('#aboutSubmitBtn');
        btn.prop('disabled', true).html('<i class="ri-loader-line me-1"></i> Saving...');

        $.ajax({
            url: "{{ route('admin.about.store') }}",
            method: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function (res) {
                console.log('Success response:', res);
                toastr[res.status](res.message);
            },
            error: function (xhr) {
                console.log('Error status:', xhr.status);
                console.log('Error response:', xhr.responseJSON);
                
                if (xhr.status === 422) {
                    $.each(xhr.responseJSON.errors, function (key, val) {
                        toastr['error'](val[0]);
                    });
                } else if (xhr.status === 500) {
                    const errorMsg = xhr.responseJSON?.message || 'Server error occurred';
                    console.error('Server error:', errorMsg);
                    toastr['error']('Error: ' + errorMsg);
                } else {
                    console.error('Full error object:', xhr);
                    toastr['error']('Something went wrong. Status: ' + xhr.status);
                }
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="ri-save-line me-1"></i> Save About Section');
            }
        });
    });
});
</script>
@endpush
