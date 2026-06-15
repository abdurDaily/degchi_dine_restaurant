<x-admin-master>
    @section('title', 'Users')

    @section('content')
        <div class="container-fluid">
            <x-breadcrumb />
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            @can('users-create')
                            <a href="{{ route('users.create') }}" class="btn btn-primary">Add User</a>
                            @endcan
                        </div>
                        <div class="card-body">
                            <table class="table yajra-datatable w-100 table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Image') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('User Number') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let datatable = new DataTable(".yajra-datatable", {
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('users.index') }}",
                    type: 'GET',
                },
                columns: [
                    {data:'DT_RowIndex',orderable:false,searchable:false},
                    {
                        data: 'image',
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
                        data: 'user_number',
                    },
                    {
                        data: 'status',
                        searchable: false,
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // user delete
            function deleteUser(id) {
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
                            url: "{{ route('users.destroy', ':id') }}".replace(':id', id),
                            type: 'DELETE',
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
                })
            }
        </script>
    @endpush


</x-admin-master>
