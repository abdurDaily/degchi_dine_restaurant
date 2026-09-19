@extends('frontend.layout')

@section('meta_title', 'Blog')
@section('meta_robots', 'index, follow')
  
@section('frontend_content')

<x-frontend.page-header
    title="Our Blog"
    eyebrow="Our Stories"
    subtitle="Stories, updates, and insights from Degchi Dine. Browse all published posts below."
/>

<section class="blog-content-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-9">
                <div class="blog-index-header">
                    <h2 class="h4 mb-0">Published Posts</h2>
                    <span class="post-count">{{ $posts->total() }} {{ Str::plural('post', $posts->total()) }}</span>
                </div>

                <div class="row g-4">
                    @forelse($posts as $post)
                        <div class="col-md-6 col-xl-4">
                            <article class="blog-card h-100">
                                <div class="blog-card-image-wrapper">
                                    @if($post->image_url)
                                        <img src="{{ $post->image_url }}"
                                             alt="{{ $post->title }}"
                                             class="blog-card-image"
                                             loading="lazy">
                                    @else
                                        <div class="blog-card-placeholder" aria-hidden="true">
                                            <i class="ri-article-line"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="blog-card-body">
                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                        @if($post->blogCategory)
                                            <span class="category-badge">{{ $post->blogCategory->name }}</span>
                                        @else
                                            <span></span>
                                        @endif
                                        <small class="text-nowrap" style="color: var(--text-on-dark-muted);">
                                            <i class="ri-eye-line"></i> {{ number_format($post->view_count) }}
                                        </small>
                                    </div>

                                    <a href="{{ route('frontend.blog.show', $post->slug) }}" class="blog-card-title">
                                        {{ $post->title }}
                                    </a>

                                    <!-- <p class="blog-card-excerpt">{{ $post->excerpt }}</p> -->

                                    <div class="blog-card-meta">
                                        <span><i class="ri-user-line"></i> {{ $post->author?->name ?? 'Admin' }}</span>
                                        <span><i class="ri-calendar-line"></i> {{ $post->created_at->format('M d, Y') }}</span>
                                    </div>

                                    <a href="{{ route('frontend.blog.show', $post->slug) }}" class="blog-card-btn">
                                        Read More <i class="ri-arrow-right-line ms-1"></i>
                                    </a>
                                </div>
                            </article>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="blog-empty-state">
                                <i class="ri-article-line fs-1 mb-3 d-block" style="color: var(--brand-gold);"></i>
                                <h3 class="h5" style="color: var(--text-on-dark);">No published posts yet</h3>
                                <p class="mb-0" style="color: var(--text-on-dark-muted);">Check back soon for new articles.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                @if($posts->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $posts->links() }}
                    </div>
                @endif
            </div>

            <div class="col-lg-3">
                <aside class="blog-sidebar-card">
                    <h5 class="blog-sidebar-title">Categories</h5>
                    <div class="d-flex flex-wrap gap-2">
                        @forelse($categories as $category)
                            <a href="{{ route('frontend.blog.index', ['category' => $category->slug]) }}" class="category-badge text-decoration-none">
                                {{ $category->name }}
                            </a>
                        @empty
                            <span style="color: var(--text-on-dark-faint);" class="small">No categories yet.</span>
                        @endforelse
                    </div>
                </aside>
            </div>
        </div>
    </div>
</section>
@endsection
