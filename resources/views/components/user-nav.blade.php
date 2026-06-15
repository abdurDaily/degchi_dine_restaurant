@if(auth()->user()->hasRole('Super Admin') || auth()->user()->can('users-show'))
<li class="nav-item">
    <a class="nav-link menu-link" href="#userNav" data-bs-toggle="collapse" role="button" aria-expanded="false"
        aria-controls="userNav">
        <i class="ri-user-line"></i> <span data-key="t-icons">User</span>
    </a>
    <div class="collapse menu-dropdown"
        id="userNav">
        <ul class="nav nav-sm flex-column">
            @can('users.create')
            <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link"><span data-key="t-remix">Users</span></a>
            </li>
            @endcan
            @hasrole('Super Admin')
                <li class="nav-item">
                    <a href="#roles&Permissions" class="nav-link" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="roles&Permissions" data-key="t-level-1.2">
                        Roles & Permissions
                    </a>
                    <div class="collapse menu-dropdown"
                        id="roles&Permissions">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('roles.index') }}"
                                    class="nav-link"
                                    data-key="t-roles">Roles</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('permissions.index') }}"
                                    class="nav-link"
                                    data-key="t-permissions">Permissions</a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endhasrole
        </ul>
    </div>
</li>
@endif
