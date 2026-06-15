<x-admin-master>
    @section('title')
        Roles
    @endsection
    @section('content')
        <div class="container-fluid"><!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">Roles</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item active">Roles</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h2>Role List</h2>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addRoleModal">Add Role</button>
                </div>
                <div class="card-body px-3">
                    <table id="dataRole" class="table table-bordered dataTable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Name</th>
                                <th>Permissions</th>
                                <th style="min-width: 162px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @include('roles.create')
        @include('roles.edit')
    @endsection

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#dataRole').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('roles.index') }}",
                    columns: [
                        {
                            data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false
                        },
                        {
                            data: 'name', name: 'name'
                        },
                        {
                            data: 'permissions', name: 'permissions'
                        },
                        {
                            data: 'action', name: 'action', orderable: false, searchable: false
                        }
                    ],

                    oLanguage: {
                        "sInfo": "Got a total of _TOTAL_ entries to show (_START_ to _END_)",
                        "sZeroRecords": "No Role Found",
                    },
                });

                $('#addRole').on('submit', function(e) {
                    e.preventDefault();
                    $('#preloader').show();
                    var formData = $(this).serialize();


                    $.ajax({
                        url: "{{ route('roles.store') }}",
                        type: 'POST',
                        data: formData,
                        success: res => {
                            if (res.status) {
                                $('#addRoleModal').modal('hide');
                                $('#preloader').hide();
                                $('#dataRole').DataTable().draw(true);
                                toastr.success(res.message);
                            } else {
                                $('#preloader').hide();
                                toastr.error(res.message);
                            }
                        },
                        error: err => {
                            $('#preloader').hide();
                            console.log(err);
                            if (err.status == 422) {
                                $.each(err.responseJSON.errors, function(i, error) {
                                    var el = $(document).find('[name="' + i + '"]');
                                    el.addClass('is-invalid');
                                    el.nextAll('span.text-danger').empty().text(error[0]);
                                    Command: toastr['error'](error[0]);
                                });

                            } else{
                                Command: toastr['error']('Something went wrong!');
                            }
                        }
                    });
                });

                $(document).on('click', '.edit-role', function(e) {
                    e.preventDefault();
                    let id = $(this).data('id');
                    let name = $(this).data('name');

                    $(document).find('#editRoleModal #editRoleId').val(id);
                    $(document).find('#editRoleModal #roleName').val(name);

                    $(document).find('#editRoleModal').modal('show');
                })

                $('#editRoleModal').on('hidden.bs.modal', function (e) {
                    $(document).find('#editRoleModal #editRoleId').val('');
                    $(document).find('#editRoleModal #roleName').val('');
                    $(this).find('input').nextAll('span.text-danger').empty();
                    $(this).find('input').removeClass('is-invalid');
                });

                $('#addRoleModal').on('hidden.bs.modal', function (e) {
                    $(this).find('input').val('');
                    $(this).find('input').nextAll('span.text-danger').empty();
                    $(this).find('input').removeClass('is-invalid');
                });

                $(document).find('#editRole').on('submit', function(e) {
                    e.preventDefault();
                    $('#preloader').show();
                    let id = $(this).find('#editRoleId').val();
                    let formData = $(this).serialize();
                    $.ajax({
                        url: "{{ route('roles.index') }}" + '/' + id,
                        type: 'PUT',
                        data: formData,
                        success: res => {
                            if (res.status) {
                                $('#editRoleModal').modal('hide');
                                $('#preloader').hide();
                                $('#dataRole').DataTable().draw(true);
                                toastr.success(res.message);
                            } else {
                                $('#preloader').hide();
                                toastr.error(res.message);
                            }
                        },
                        error: err => {
                            $('#preloader').hide();
                            console.log(err);
                            if (err.status == 422) {
                                $.each(err.responseJSON.errors, function(i, error) {
                                    var el = $(document).find('[name="' + i + '"]');
                                    el.addClass('is-invalid');
                                    el.nextAll('span.text-danger').empty().text(error[0]);
                                    Command: toastr['error'](error[0]);
                                });
                            } else{
                                Command: toastr['error']('Something went wrong!');
                            }
                        }
                    });
                })
            })
        </script>
    @endpush

</x-admin-master>
