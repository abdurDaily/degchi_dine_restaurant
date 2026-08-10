<x-admin-master>
    @section('title')
        Permissions
    @endsection
    @section('content')
        <div class="container-fluid py-4 admin-crud-page permissions-page">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">Permissions</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active">Permissions</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info border-0 mb-4">
                Permissions are grouped like the admin sidebar
                (<strong>Main</strong>, <strong>Catalog</strong>, <strong>Content</strong>, <strong>System</strong>).
                Open each section dropdown to see View / Create / Edit / Delete actions.
                If a section is missing, run
                <code>php artisan db:seed --class=RestaurantPermissionSeeder</code>
                on the server.
            </div>

            @if (empty($sections))
                <div class="alert alert-warning">
                    No permissions found in the database. Seed them with:
                    <code>php artisan db:seed --class=RestaurantPermissionSeeder</code>
                </div>
            @else
                @include('permission.partials.grouped-list', [
                    'sections' => $sections,
                    'selectable' => false,
                    'role' => null,
                ])
            @endif
        </div>
    @endsection

    @push('styles')
        @include('permission.partials.styles')
    @endpush
</x-admin-master>
