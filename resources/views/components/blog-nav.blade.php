<li class="nav-item">
    <a class="nav-link menu-link" href="#blogNav" data-bs-toggle="collapse" role="button"
        aria-expanded="{{ request()->routeIs('admin.blogCategories.*') || request()->routeIs('admin.posts.*') || request()->routeIs('admin.comments.*') ? 'true' : 'false' }}"
        aria-controls="blogNav">
        <i class="ri-article-line"></i> <span data-key="t-blog">Blog</span>
    </a>
    <div class="collapse menu-dropdown {{ request()->routeIs('admin.blogCategories.*') || request()->routeIs('admin.posts.*') || request()->routeIs('admin.comments.*') ? 'show' : '' }}"
        id="blogNav">
        <ul class="nav nav-sm flex-column">
            <li class="nav-item">
                <a href="{{ route('admin.blogCategories.index') }}"
                    class="nav-link {{ request()->routeIs('admin.blogCategories.*') ? 'active' : '' }}">
                    <span data-key="t-blog-categories">Blog Categories</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.posts.index') }}"
                    class="nav-link {{ request()->routeIs('admin.posts.*') || request()->routeIs('admin.comments.*') ? 'active' : '' }}">
                    <span data-key="t-blog-posts">Blog Posts</span>
                </a>
            </li>
        </ul>
    </div>
</li>
