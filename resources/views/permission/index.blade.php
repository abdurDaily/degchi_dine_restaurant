<x-admin-master>
    @section('content')
    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
        <h4 class="mb-sm-0">All Permission</h4>

        <div class="page-title-right">
            <ol class="m-0 breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboards</a></li>
                <li class="breadcrumb-item active">Permissions</li>
            </ol>
        </div>

    </div>
    <div class="container-fluid">

        @foreach ($permissions as $group=>$permission)
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="mb-0 card-title flex-grow-1">{{ str($group)->headline() }} Permissions</h4>

            </div><!-- end card header -->
            <div class="px-0 card-body">

                <table class="table mb-0 align-middle table-bordered table-nowrap">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 10%; text-align:center;">#</th>
                            <th scope="col" style="width: 30%;">Permission</th>
                            <th scope="col" style="width: 60%;">Permission Detail</th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach ($permission as $key=>$item)

                        <tr>
                            <td class="text-center">{{ ++$key }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->detail ?? 'N/A' }}</td>

                        </tr>
                        @endforeach
                    </tbody><!-- end tbody -->
                </table>


            </div>
        </div>
        @endforeach

    </div>
    @endsection
</x-admin-master>
