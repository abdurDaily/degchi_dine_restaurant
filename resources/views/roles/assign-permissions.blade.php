<x-admin-master>
    @section('name')
        Assign Permissions
    @endsection
    @section('content')
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">Assign Permissions</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{route('roles.index')}}">Roles</a></li>
                                <li class="breadcrumb-item active">Assign Permissions</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <form id="updatePermissions" action="javascript:void(0)" method="post">
                    @csrf
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h3 class="mb-0">
                                {{ str($role->name)->headline() }}
                            </h3>
                            <div class="btn-group align-items-center ">
                                <label for="selectAllCheckBox" class="mb-0 me-3"><input id="selectAllCheckBox" type="checkbox">
                                    Check All Permissions</label>
                            </div>
                        </div>
                    </div>
                    @foreach ($permissions as $group=>$permission)
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">{{ str($group)->headline() }} Permissions</h4>

                            </div><!-- end card header -->
                            <div class="card-body px-0">

                                <table class="table table-bordered table-nowrap align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width: 10%; text-align:center;">
                                                <label for="{{ $group }}_group" class="mb-0 d-block h-100"><input
                                                        id="{{ $group }}_group" class="grpCheckbox" type="checkbox"
                                                        value="{{ $group }}"></label>
                                            </th>
                                            <th scope="col" style="width: 30%;">Permission</th>
                                            <th scope="col" style="width: 60%;">Permission Detail</th>
                                        </tr>

                                    </thead>

                                    <tbody>

                                        @foreach ($permission as $key=>$item)
                                        {{-- @dd($item); --}}
                                        <tr>
                                            <td class="text-center">
                                                <label for="{{ $item->name }}" class="mb-0 d-block h-100">
                                                    <input {{$role->hasPermissionTo($item->name)? 'checked' :''}}
                                                    value="{{ $item->id }}"
                                                    class="itemCheckBox" id="{{ $item->name }}" type="checkbox" name="permissions[]"
                                                    data-grp="{{ $item->group }}">
                                                </label>
                                            </td>
                                            <td>
                                                <label for="{{ $item->name }}" class="mb-0 d-block h-100">{{ $item->name }}</label>
                                            </td>
                                            <td>{{ $item->details ?? 'N/A' }}</td>

                                        </tr>
                                        @endforeach
                                    </tbody><!-- end tbody -->
                                </table>
                            </div>
                        </div>
                    @endforeach
                    <button class="btn btn-primary btn-lg">Update Permissions</button>
                </form>
            </div>
        </div>
    @endsection
    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#selectAllCheckBox').on('click', function() {
                    $('.itemCheckBox').prop('checked', $(this).prop('checked'));
                })

                $('#updatePermissions').on('submit', function(e) {
                    e.preventDefault();
                    $('#preloader').show();
                    $.ajax({
                        url: "{{ route('roles.assignPermissions', $role->id) }}",
                        type: "POST",
                        data: $(this).serialize(),
                        success: function(response) {
                            $('#preloader').hide();
                            if (response.status == 'success') {
                                toastr.success(response.message);
                                window.location.href = "{{ route('roles.index') }}";
                            }
                        },
                        error: err =>{
                            $('#preloader').hide();
                            console.log(err);
                            toastr.error(err.responseJSON.message);
                        }
                    })
                })

                $('.grpCheckbox').on('click', function() {
                    if ($(this).is(':checked')) {
                        $(this).closest('table').find('.itemCheckBox').prop('checked', true);
                    } else {
                        $(this).closest('table').find('.itemCheckBox').prop('checked', false);
                    }
                })
            })
        </script>
    @endpush
</x-admin-master>
