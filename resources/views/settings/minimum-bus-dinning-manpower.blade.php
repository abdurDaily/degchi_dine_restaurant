<x-admin-master>
    @push('styles')
    @endpush
    @section('title', ' Off Day Minimum Bus Dinning Manpower Setting')

    @section('content')
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">{{ __('Off Day Minimum Bus Dinning Manpower Setting') }}</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item active">{{ __('Off Day Minimum Bus Dinning Manpower Setting') }}</li>
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
                    <h3 class="h5">{{ __('Off Day Minimum Bus Dinning Manpower Setting') }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form class="offday-min-manpower-form row" action="javascript:void(0)" method="post">
                            @csrf
                            <div class="col-md-12">
                                <div class="mb-3 row">
                                    <label for="offday_minimum_dinning_manpower" class="col-sm-2 col-form-label">Dinning Min. Manpower</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="offday_minimum_dinning_manpower" name="offday_minimum_dinning_manpower" value="{{$settings->where('key','offday_minimum_dinning_manpower')->first() ? $settings->where('key','offday_minimum_dinning_manpower')->first()->value : ''}}" placeholder="Bus Min. Manpower">
                                        <span class="text-danger"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3 row">
                                    <label for="offday_minimum_bus_manpower" class="col-sm-2 col-form-label">Bus Min. Manpower</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="offday_minimum_bus_manpower" name="offday_minimum_bus_manpower" value="{{$settings->where('key','offday_minimum_bus_manpower')->first() ? $settings->where('key','offday_minimum_bus_manpower')->first()->value : ''}}" placeholder="Bus Min. Manpower">
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
            $(document).on('submit', '.offday-min-manpower-form', function (e) {
                e.preventDefault();
                $('#preloader').show();
                $('input.is-invalid').removeClass('is-invalid');
                $('span.text-danger').empty();
                let formData = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: "{{route('minimum-bus-dinning-manpower-setting.store')}}",
                    data: formData,
                    success: data => {
                        $('#preloader').hide();
                        console.log(data);
                        if (data.status == 'success') {
                            toastr.success(data.message);
                            location.reload();
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
                                var el = $(document).find('.offday-min-manpower-form [name="'+i+'"]');
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
