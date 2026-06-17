<x-admin-master>
    @section('title')
        SSLCommerz Setting
    @endsection
    @section('content')
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">SSLCommerz Setting</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active">SSLCommerz Setting</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Payment Gateway Credentials</h3>
                    <p class="text-muted mb-0 mt-2">Configure SSLCommerz store credentials used for online checkout payments.</p>
                </div>
                <div class="card-body">
                    <form class="sslcommerz-setting-form row" action="javascript:void(0)" method="post">
                        @csrf
                        <div class="col-md-12">
                            <div class="mb-3 row">
                                <label for="sslStoreId" class="col-sm-3 col-form-label">Store ID</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="sslStoreId" name="store_id" value="{{ $storeId }}" placeholder="Your SSLCommerz Store ID" required>
                                    <span class="text-danger"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3 row">
                                <label for="sslStorePassword" class="col-sm-3 col-form-label">Store Password</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="sslStorePassword" name="store_password" value="{{ $storePassword }}" placeholder="Your SSLCommerz Store Password" required>
                                    <span class="text-danger"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label">Environment</label>
                                <div class="col-sm-9">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" role="switch" id="sslSandbox" name="sandbox" value="1" {{ $sandbox ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sslSandbox">Sandbox mode (disable for live payments)</label>
                                    </div>
                                    <small class="text-muted d-block mt-2">Sandbox uses test credentials. Turn off when using production SSLCommerz keys.</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <button type="submit" class="btn btn-primary">Save SSLCommerz Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            $(document).ready(function () {
                $(document).on('submit', '.sslcommerz-setting-form', function (e) {
                    e.preventDefault();
                    $('#preloader').show();
                    $('input.is-invalid').removeClass('is-invalid');
                    $('span.text-danger').empty();

                    let formData = $(this).serialize();
                    if (!$('#sslSandbox').is(':checked')) {
                        formData += '&sandbox=0';
                    }

                    $.ajax({
                        type: 'POST',
                        url: "{{ route('sslcommerz-setting.store') }}",
                        data: formData,
                        success: function (data) {
                            $('#preloader').hide();
                            if (data.status === 'success') {
                                toastr.success(data.message);
                            }
                        },
                        error: function (err) {
                            $('#preloader').hide();
                            if (err.status === 422) {
                                $.each(err.responseJSON.errors, function (i, error) {
                                    var el = $('.sslcommerz-setting-form [name="' + i + '"]');
                                    el.nextAll('span.text-danger').empty().text(error[0]);
                                    el.addClass('is-invalid');
                                });
                            } else {
                                toastr.error('Could not save SSLCommerz settings.');
                            }
                        }
                    });
                });
            });
        </script>
    @endpush
</x-admin-master>
