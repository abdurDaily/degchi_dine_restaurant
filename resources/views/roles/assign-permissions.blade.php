<x-admin-master>
    @section('title')
        Assign Permissions
    @endsection
    @section('content')
        <div class="container-fluid py-4 admin-crud-page permissions-page">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">Assign Permissions</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
                                <li class="breadcrumb-item active">Assign Permissions</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <form id="updatePermissions" action="javascript:void(0)" method="post">
                @csrf

                <div class="card mb-4">
                    <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <h3 class="mb-1">{{ str($role->name)->headline() }}</h3>
                            <p class="text-muted mb-0">
                                Open each sidebar section dropdown and assign View / Create / Edit / Delete permissions.
                                For status updates, also enable the <strong>Edit</strong> (or Moderate) permission for that section.
                            </p>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <label for="selectAllCheckBox" class="mb-0 d-flex align-items-center gap-2">
                                <input id="selectAllCheckBox" type="checkbox" class="form-check-input m-0">
                                <span>Check All Permissions</span>
                            </label>
                            <a href="{{ route('roles.index') }}" class="btn btn-light">Back to Roles</a>
                            <button type="submit" class="btn btn-primary">Update Permissions</button>
                        </div>
                    </div>
                </div>

                @if (empty($sections))
                    <div class="alert alert-warning">
                        No permissions found. Run
                        <code>php artisan db:seed --class=RestaurantPermissionSeeder</code>
                        then refresh this page.
                    </div>
                @else
                    @include('permission.partials.grouped-list', [
                        'sections' => $sections,
                        'selectable' => true,
                        'role' => $role,
                    ])
                @endif

                <div class="sticky-update-bar">
                    <button type="submit" class="btn btn-primary btn-lg">Update Permissions</button>
                </div>
            </form>
        </div>
    @endsection

    @push('styles')
        @include('permission.partials.styles')
    @endpush

    @push('scripts')
        <script>
            $(document).ready(function () {
                function syncGroupCheckbox($card) {
                    const $items = $card.find('.itemCheckBox');
                    const total = $items.length;
                    const checked = $items.filter(':checked').length;
                    $card.find('.grpCheckbox').prop('checked', total > 0 && total === checked);
                }

                function syncSectionCheckbox(sectionKey) {
                    const $items = $('.itemCheckBox[data-section="' + sectionKey + '"]');
                    const total = $items.length;
                    const checked = $items.filter(':checked').length;
                    $('.sectionCheckbox[data-section="' + sectionKey + '"]').prop('checked', total > 0 && total === checked);
                }

                function syncSelectAll() {
                    const $items = $('.itemCheckBox');
                    const total = $items.length;
                    const checked = $items.filter(':checked').length;
                    $('#selectAllCheckBox').prop('checked', total > 0 && total === checked);
                }

                function refreshAllStates() {
                    $('.permission-group-card').each(function () {
                        syncGroupCheckbox($(this));
                    });
                    $('.sectionCheckbox').each(function () {
                        syncSectionCheckbox($(this).data('section'));
                    });
                    syncSelectAll();
                }

                refreshAllStates();

                $('#selectAllCheckBox').on('change', function () {
                    const checked = $(this).prop('checked');
                    $('.itemCheckBox, .grpCheckbox, .sectionCheckbox').prop('checked', checked);
                });

                $('.sectionCheckbox').on('change', function () {
                    const section = $(this).data('section');
                    const checked = $(this).prop('checked');
                    $('.itemCheckBox[data-section="' + section + '"]').prop('checked', checked);
                    $('.grpCheckbox[data-section="' + section + '"]').prop('checked', checked);
                    syncSelectAll();
                });

                $('.grpCheckbox').on('change', function () {
                    const checked = $(this).prop('checked');
                    const $card = $(this).closest('.permission-group-card');
                    $card.find('.itemCheckBox').prop('checked', checked);
                    syncSectionCheckbox($(this).data('section'));
                    syncSelectAll();
                });

                $(document).on('change', '.itemCheckBox', function () {
                    syncGroupCheckbox($(this).closest('.permission-group-card'));
                    syncSectionCheckbox($(this).data('section'));
                    syncSelectAll();
                });

                $('#updatePermissions').on('submit', function (e) {
                    e.preventDefault();
                    $('#preloader').show();
                    $.ajax({
                        url: "{{ route('roles.assignPermissions', $role->id) }}",
                        type: "POST",
                        data: $(this).serialize(),
                        success: function (response) {
                            $('#preloader').hide();
                            if (response.status == 'success') {
                                toastr.success(response.message);
                                window.location.href = "{{ route('roles.index') }}";
                            }
                        },
                        error: function (err) {
                            $('#preloader').hide();
                            toastr.error(err.responseJSON?.message || 'Something went wrong');
                        }
                    });
                });
            });
        </script>
    @endpush
</x-admin-master>
