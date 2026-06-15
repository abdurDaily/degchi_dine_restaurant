<x-admin-master>
    @push('styles')
        <!-- dropzone css -->
        <link rel="stylesheet" href="{{ asset('assets/libs/dropzone/dropzone.css') }}" type="text/css" />
        <!-- Filepond css -->
        <link rel="stylesheet" href="{{ asset('assets/libs/filepond/filepond.min.css') }}" type="text/css" />
        <link rel="stylesheet"
            href="{{ asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
    @endpush
    @section('title')
        General Setting
    @endsection
    @section('content')
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">General Setting</h4>

                        <div class="page-title-right">
                            <ol class="m-0 breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active">General Setting</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Upload Logo</h4>
                    </div>
                    <div class="text-center card-body">
                        <div class="mx-auto avatar-xl col-sm-9">
                            <input type="file" class="filepond logo-upload" name="logo"
                                accept="image/png, image/jpeg, image/gif" />
                        </div>
                        <span class="text-danger">Must be within 200KB</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Favicon Logo</h4>
                    </div>
                    <div class="text-center card-body">
                        <div class="mx-auto avatar-xl col-sm-9">
                            <input type="file" class="filepond favicon-upload" name="favicon"
                                accept="image/png, image/jpeg, image/gif" />
                        </div>
                        <span class="text-danger">Must be within 100KB. dont upload .ico file</span>
                    </div>
                </div>
            </div>
            <hr>
            <form class="general-setting-form row" action="javascript:void(0)" method="post" enctype="multipart/form-data">
                @csrf
                <div class="col-md-6">
                    <div class="mb-3 row">
                        <label for="companyName" class="col-sm-2 col-form-label">Company</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="companyName" name="company"
                                value="{{ Session::get('company') }}" placeholder="Example Company">
                            <span class="text-danger"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3 row">
                        <label for="tagLine" class="col-sm-2 col-form-label">Tagline</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="tagLine" name="tagline"
                                value="{{ Session::get('tagline') }}" placeholder="Your tagline">
                            <span class="text-danger"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3 row">
                        <label for="currency" class="col-sm-2 col-form-label">Currency</label>
                        <div class="col-sm-10">
                            <select name="currency" id="currency" class="form-control select2">
                                @foreach ($currency_list as $currency_data)
                                    <option value="{{ $currency_data->id }}"
                                        {{ Session::get('currency') == $currency_data->id ? 'selected' : '' }}>
                                        {{ $currency_data->code }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger"></span>
                        </div>
                    </div>
                </div>
                <div class="mb-3 col-12">
                    <button type="submit" class="btn btn-primary">Save Setting</button>
                </div>
            </form>
        </div>
    @endsection
    @push('scripts')
        <!-- dropzone min -->
        <script src="{{ asset('assets/libs/dropzone/dropzone-min.js') }}"></script>
        <!-- filepond js -->
        <script src="{{ asset('assets/libs/filepond/filepond.min.js') }}"></script>
        <script src="{{ asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}"></script>
        <script src="{{ asset('assets/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js') }}">
        </script>
        <script
            src="{{ asset('assets/libs/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js') }}">
        </script>
        <script src="{{ asset('assets/libs/filepond-plugin-file-encode/filepond-plugin-file-encode.min.js') }}"></script>
        <script>
            $(document).ready(function() {
                FilePond.registerPlugin(
                    FilePondPluginImagePreview,
                    FilePondPluginImageExifOrientation,
                    FilePondPluginFileValidateSize
                );

                // Create a FilePond instance
                let logoPond = FilePond.create(document.querySelector(".logo-upload"), {
                    labelIdle: 'Drag & Drop logo or <span class="filepond--label-action">Browse</span>',
                    server: {
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        process: {
                            url: '{{ route('general-setting-logo.store') }}',
                            onload: (res) => {
                                let response = JSON.parse(res);
                                if (response.status == 'success') {
                                    Command: toastr["success"]('Logo uploaded successfully');
                                }
                                else {
                                    Command: toastr["error"](response.message ?? 'Something went wrong');
                                }
                            },
                            onerror: (response) => {
                                let errorResponse = JSON.parse(response);
                                Command: toastr["error"](errorResponse.message ?? 'Invalid file upload');
                            }
                        }
                    },
                });

                let faviconPond = FilePond.create(document.querySelector(".favicon-upload"), {
                    labelIdle: 'Drag & Drop favicon or <span class="filepond--label-action">Browse</span>',
                    server: {
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        process: {
                            url: '{{ route('general-setting-favicon.store') }}',
                            onload: (res) => {
                                let response = JSON.parse(res);
                                if (response.status == 'success') {
                                    Command: toastr["success"]('Favicon uploaded successfully');
                                }
                                else {
                                    Command: toastr["error"](response.message ?? 'Something went wrong');
                                }
                            },
                            onerror: (response) => {
                                let errorResponse = JSON.parse(response);
                                Command: toastr["error"](errorResponse.message ?? 'Invalid file upload');
                            }
                        }
                    },
                });


                $(document).on('submit', '.general-setting-form', function(e) {
                    e.preventDefault();
                    $('#preloader').show();
                    let formData = $(this).serialize();
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('general-setting.store') }}",
                        data: formData,
                        success: data => {
                            $('#preloader').hide();
                            console.log(data);
                            if (data.status == 'success') {
                                toastr.success(data.message);
                            }
                            if (data.errors) {
                                toastr.error(data.errors);
                            }
                        },
                        error: err => {
                            $('#preloader').hide();
                            console.log(err);
                            if (err.status == 422) {
                                $.each(err.responseJSON.errors, function(i, error) {
                                    var el = $(document).find(
                                        '.general-setting-form [name="' + i + '"]');
                                    el.nextAll('span.text-danger').empty().text(error[0]);
                                });
                            }
                        }
                    })
                })

            })
        </script>
    @endpush
</x-admin-master>
