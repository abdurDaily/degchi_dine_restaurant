<x-admin-master>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/libs/filepond/filepond.min.css') }}" type="text/css" />
        <link rel="stylesheet" href="{{ asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
    @endpush

    @section('title')
        SEO Settings
    @endsection

    @section('content')
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">SEO & Tracking</h4>
                        <div class="page-title-right">
                            <ol class="m-0 breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active">SEO Settings</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-1">Open Graph Image</h5>
                        <p class="text-muted mb-0 small">Recommended 1200×630px for Facebook, WhatsApp & Twitter previews.</p>
                    </div>
                    <div class="card-body text-center">
                        @if($ogImageUrl)
                            <img src="{{ $ogImageUrl }}" alt="Current OG image" class="img-fluid rounded mb-3 border" style="max-height: 180px; object-fit: cover;">
                        @endif
                        <div class="mx-auto avatar-xl col-sm-11">
                            <input type="file" class="filepond seo-og-upload" name="seo_og_image"
                                accept="image/png, image/jpeg, image/jpg, image/webp" />
                        </div>
                        <span class="text-danger small d-block mt-2">Max 1MB · Min 600×315px</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-1">Meta Tags & Pixels</h5>
                        <p class="text-muted mb-0 small">Controls site-wide SEO, social previews, analytics and ad pixels.</p>
                    </div>
                    <div class="card-body">
                        <form class="seo-setting-form" action="javascript:void(0)" method="post">
                            @csrf

                            <h6 class="text-uppercase text-muted fw-bold mb-3">Basic Meta</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Site Name</label>
                                    <input type="text" class="form-control" name="seo_site_name" value="{{ $settings->get('seo_site_name', 'Degchi Dine') }}" required>
                                    <span class="text-danger"></span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Default Title</label>
                                    <input type="text" class="form-control" name="seo_default_title" value="{{ $settings->get('seo_default_title') }}" required maxlength="160">
                                    <span class="text-danger"></span>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Default Meta Description</label>
                                    <textarea class="form-control" name="seo_default_description" rows="3" required maxlength="320">{{ $settings->get('seo_default_description') }}</textarea>
                                    <span class="text-danger"></span>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">Default Keywords</label>
                                    <input type="text" class="form-control" name="seo_default_keywords" value="{{ $settings->get('seo_default_keywords') }}" placeholder="restaurant, biriyani, chittagong">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Default Robots</label>
                                    <input type="text" class="form-control" name="seo_robots_default" value="{{ $settings->get('seo_robots_default', 'index, follow') }}">
                                </div>
                            </div>

                            <hr>
                            <h6 class="text-uppercase text-muted fw-bold mb-3">Social / Open Graph</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">OG Type</label>
                                    <input type="text" class="form-control" name="seo_og_type" value="{{ $settings->get('seo_og_type', 'website') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Twitter Card</label>
                                    <select class="form-control" name="seo_twitter_card">
                                        @foreach(['summary', 'summary_large_image'] as $card)
                                            <option value="{{ $card }}" {{ $settings->get('seo_twitter_card', 'summary_large_image') === $card ? 'selected' : '' }}>{{ $card }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Twitter Handle</label>
                                    <input type="text" class="form-control" name="seo_twitter_handle" value="{{ $settings->get('seo_twitter_handle') }}" placeholder="@degchidine">
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Canonical Base URL</label>
                                    <input type="url" class="form-control" name="seo_canonical_url" value="{{ $settings->get('seo_canonical_url', config('app.url')) }}" placeholder="{{ config('app.url') }}">
                                </div>
                            </div>

                            <hr>
                            <h6 class="text-uppercase text-muted fw-bold mb-3">Tracking Pixels</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Google Analytics (GA4)</label>
                                    <input type="text" class="form-control" name="seo_google_analytics_id" value="{{ $settings->get('seo_google_analytics_id') }}" placeholder="G-XXXXXXXXXX">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Google Tag Manager</label>
                                    <input type="text" class="form-control" name="seo_google_tag_manager_id" value="{{ $settings->get('seo_google_tag_manager_id') }}" placeholder="GTM-XXXXXXX">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Facebook Pixel ID</label>
                                    <input type="text" class="form-control" name="seo_facebook_pixel_id" value="{{ $settings->get('seo_facebook_pixel_id') }}" placeholder="123456789012345">
                                </div>
                            </div>

                            <hr>
                            <h6 class="text-uppercase text-muted fw-bold mb-3">Advanced</h6>
                            <div class="mb-3">
                                <label class="form-label">Custom Head Scripts</label>
                                <textarea class="form-control font-monospace" name="seo_head_scripts" rows="4" placeholder="Optional verification tags or custom scripts">{{ $settings->get('seo_head_scripts') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">robots.txt Content</label>
                                <textarea class="form-control font-monospace" name="seo_robots_txt" rows="5">{{ $settings->get('seo_robots_txt', "User-agent: *\nAllow: /\n\nSitemap: /sitemap.xml") }}</textarea>
                                <small class="text-muted">Live at <a href="{{ url('/robots.txt') }}" target="_blank">{{ url('/robots.txt') }}</a> · Sitemap at <a href="{{ url('/sitemap.xml') }}" target="_blank">{{ url('/sitemap.xml') }}</a></small>
                            </div>

                            <button type="submit" class="btn btn-primary">Save SEO Settings</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script src="{{ asset('assets/libs/filepond/filepond.min.js') }}"></script>
        <script src="{{ asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}"></script>
        <script src="{{ asset('assets/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js') }}"></script>
        <script>
            $(document).ready(function() {
                FilePond.registerPlugin(
                    FilePondPluginImagePreview,
                    FilePondPluginFileValidateSize
                );

                FilePond.create(document.querySelector('.seo-og-upload'), {
                    labelIdle: 'Upload OG image <span class="filepond--label-action">Browse</span>',
                    server: {
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        process: {
                            url: '{{ route('seo-setting-og-image.store') }}',
                            onload: (res) => {
                                const response = JSON.parse(res);
                                if (response.status === 'success') {
                                    toastr.success(response.message);
                                    setTimeout(() => location.reload(), 800);
                                } else {
                                    toastr.error(response.message ?? 'Upload failed');
                                }
                            },
                            onerror: (response) => {
                                const errorResponse = JSON.parse(response);
                                toastr.error(errorResponse.message ?? 'Invalid file upload');
                            }
                        }
                    },
                });

                $(document).on('submit', '.seo-setting-form', function(e) {
                    e.preventDefault();
                    $('#preloader').show();
                    $.ajax({
                        type: 'POST',
                        url: '{{ route('seo-setting.store') }}',
                        data: $(this).serialize(),
                        success: (data) => {
                            $('#preloader').hide();
                            if (data.status === 'success') {
                                toastr.success(data.message);
                            }
                        },
                        error: (err) => {
                            $('#preloader').hide();
                            if (err.status === 422) {
                                $.each(err.responseJSON.errors, function(i, error) {
                                    const el = $('.seo-setting-form [name="' + i + '"]');
                                    el.nextAll('span.text-danger').first().text(error[0]);
                                });
                                toastr.error('Please fix the highlighted fields.');
                            } else {
                                toastr.error('Something went wrong.');
                            }
                        }
                    });
                });
            });
        </script>
    @endpush
</x-admin-master>
