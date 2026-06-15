<x-admin-master>
    @section('title')
        Users by Role
    @endsection

    @section('content')
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">Roles</h4>

                        <div class="page-title-right">
                            <ol class="m-0 breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
                                <li class="breadcrumb-item active">Users</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h2>User list of {{ $role->name }} role</h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#userSearchModal">Add Users</button>
                </div>
                <div class="px-3 card-body">
                    <table id="roleUsers" class="table table-bordered dataTable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>User Number</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
        {{-- @include('roles.user-remove') --}}
        @include('roles.add-users')
    @endsection

    @push('scripts')
        <script>
            let datatable = new DataTable("#roleUsers", {
                processing: true,
                serverSide: true,
                ajax: "{{ route('roles.users', $role->id) }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'user_number'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
            });

            function removeRole(userId) {
                var roleId = @json($role->id);
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('roles.user.remove') }}", // Ensure this is inside a Blade script block
                            type: 'POST',
                            data: {
                                user_id: userId,
                                role_id: roleId
                            },
                            success: function(data) {
                                Swal.fire(
                                    'Deleted!',
                                    data.message,
                                    'success'
                                )
                                datatable.draw();
                            },
                            error: function(data) {
                                console.log('Error:', data);
                                Swal.fire("Failed!", data.responseJSON.message, "warning");
                            }
                        });
                    }
                });
            }


            $(document).on('submit', '#addUsers', function(e) {
                e.preventDefault();
                $('#preloader').show();
                let formData = $(this).serialize();
                $.ajax({
                    url: "{{ route('roles.user.add', $role->id) }}",
                    type: 'POST',
                    data: formData,
                    success: res => {
                        if (res.status) {
                            $('#userSearchModal').modal('hide');
                            $('#preloader').hide();
                            $('#roleUsers').DataTable().draw(true);
                            toastr.success(res.message);
                        } else {
                            $('#preloader').hide();
                            toastr.error(res.message);
                        }
                    },
                    error: err => {
                        $('#preloader').hide();
                    }
                });
            })


        </script>
    @endpush
</x-admin-master>
