@canAny(['category-list', 'menu-list'])
    <li class="nav-item">
        <a class="nav-link menu-link" href="#menuNav" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="menuNav">
            <i class="ri-restaurant-line"></i> <span data-key="t-menus">Menu Management</span>
        </a>
        <div class="collapse menu-dropdown" id="menuNav">
            <ul class="nav nav-sm flex-column">
                
                @can('category-list')
                <li class="nav-item">
                    <a href="{{ route('admin.category.index') }}" class="nav-link" data-key="t-category-list">
                        Categories
                    </a>
                </li>
                @endcan

                @can('menu-list')
                <li class="nav-item">
                    <a href="{{ route('admin.menu.index') }}" class="nav-link" data-key="t-menu-list">
                        Menu Items
                    </a>
                </li>
                @endcan

            </ul>
        </div>
    </li>
@endcanAny