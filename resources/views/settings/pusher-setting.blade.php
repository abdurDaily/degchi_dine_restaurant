<x-admin-master>
    @push('styles')
    @endpush
    @section('title')
        Pusher Setting
    @endsection
    @section('content')
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">Pusher Setting</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item active">Pusher Setting</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->
        </div>
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3>Pusher Setting</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form class="pusher-setting-form row" action="javascript:void(0)" method="post">
                            @csrf
                            <div class="col-md-12">
                                <div class="mb-3 row">
                                    <label for="pusherAppId" class="col-sm-2 col-form-label">Pusher APP ID:</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="pusherAppId" name="pusher_app_id" value="{{$settings->where('key','pusher_app_id')->first() ? $settings->where('key','pusher_app_id')->first()->value : ''}}" placeholder="Pusher APP ID">
                                        <span class="text-danger"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3 row">
                                    <label for="pusherAppKey" class="col-sm-2 col-form-label">Pusher APP KEY:</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="pusherAppKey" name="pusher_app_key" value="{{$settings->where('key','pusher_app_key')->first() ? $settings->where('key','pusher_app_key')->first()->value : ''}}" placeholder="Pusher App Key">
                                        <span class="text-danger"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3 row">
                                    <label for="pusherAppSecret" class="col-sm-2 col-form-label">Pusher APP Secret:</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="pusherAppSecret" name="pusher_app_secret" value="{{$settings->where('key','pusher_app_secret')->first() ? $settings->where('key','pusher_app_secret')->first()->value : ''}}" placeholder="Pusher App Secret">
                                        <span class="text-danger"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3 row">
                                    <label for="pusherAppCluster" class="col-sm-2 col-form-label">Pusher APP Cluster:</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="pusherAppCluster" name="pusher_app_cluster" value="{{$settings->where('key','pusher_app_cluster')->first() ? $settings->where('key','pusher_app_cluster')->first()->value : ''}}" placeholder="Pusher App Cluster">
                                        <span class="text-danger"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <button type="submit" class="btn btn-primary">Save Setting</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @push('scripts')
        <script>
            $(document).ready(function () {
                $(document).on('submit', '.pusher-setting-form', function (e) {
                    e.preventDefault();
                    $('#preloader').show();
                    $('input.is-invalid').removeClass('is-invalid');
                    $('span.text-danger').empty();
                    let formData = $(this).serialize();
                    $.ajax({
                        type: 'POST',
                        url: "{{route('pusher-setting.store')}}",
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
                            if(err.status == 422){
                                $.each(err.responseJSON.errors, function (i, error) {
                                    var el = $(document).find('.pusher-setting-form [name="'+i+'"]');
                                    el.nextAll('span.text-danger').empty().text(error[0]);
                                    el.addClass('is-invalid');
                                });
                            }
                        }
                    })
                })

            })
        </script>
    @endpush
</x-admin-master>
