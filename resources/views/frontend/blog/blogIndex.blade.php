@extends('frontend.layout')

@section('meta_title', 'Blog')
@section('meta_robots', 'index, follow')

@push('front_css')
<style>
    .blog-page-hero {
        background: linear-gradient(135deg, #116b83 0%, #0a4554 100%);
        color: #fff;
        padding: 3.5rem 0 2.5rem;
        margin-bottom: 2rem;
    }

    .blog-page-hero h1 {
        font-size: clamp(1.75rem, 4vw, 2.5rem);
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .blog-page-hero p {
        opacity: 0.9;
        margin-bottom: 0;
        max-width: 640px;
    }

    .blog-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(17, 107, 131, 0.08);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        border: 1px solid rgba(17, 107, 131, 0.08);
    }

    .blog-card:hover {
        box-shadow: 0 16px 40px rgba(17, 107, 131, 0.16);
        transform: translateY(-6px);
    }

    .blog-card-image-wrapper {
        overflow: hidden;
        position: relative;
        background: linear-gradient(135deg, #e8f4f7 0%, #d4e8ee 100%);
        min-height: 220px;
    }

    .blog-card-image {
        width: 100%;
        height: 220px;
        object-fit: cover;
        display: block;
        transition: transform 0.45s ease;
    }

    .blog-card:hover .blog-card-image {
        transform: scale(1.06);
    }

    .blog-card-placeholder {
        width: 100%;
        height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #116b83;
        font-size: 3rem;
        background: linear-gradient(135deg, #e8f4f7 0%, #cfe4eb 100%);
    }

    .blog-card-body {
        padding: 1.25rem 1.35rem 1.5rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .blog-card-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 0.75rem;
        display: -webkit-box;
        line-clamp: 2;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-decoration: none;
        line-height: 1.4;
    }

    .blog-card-title:hover {
        color: #116b83;
    }

    .blog-card-excerpt {
        color: #6c757d;
        font-size: 0.95rem;
        line-height: 1.6;
        flex: 1;
        display: -webkit-box;
        line-clamp: 3;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: 1rem;
    }

    .blog-card-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem 1rem;
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 1rem;
        padding-bottom: 0.85rem;
        border-bottom: 1px solid #eef2f4;
    }

    .blog-card-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 0.7rem 1rem;
        background: #116b83;
        color: #fff !important;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.2s ease, transform 0.2s ease;
    }

    .blog-card-btn:hover {
        background: #0a4554;
        color: #fff !important;
        transform: translateY(-1px);
    }

    .category-badge {
        display: inline-block;
        padding: 0.3rem 0.8rem;
        background: rgba(17, 107, 131, 0.12);
        color: #116b83;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .blog-sidebar-card {
        background: #fff;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 8px 24px rgba(17, 107, 131, 0.08);
        border: 1px solid rgba(17, 107, 131, 0.08);
        position: sticky;
        top: 100px;
    }

    .blog-sidebar-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid rgba(17, 107, 131, 0.12);
    }

    .blog-empty-state {
        background: #fff;
        border-radius: 16px;
        padding: 3rem 2rem;
        text-align: center;
        box-shadow: 0 8px 24px rgba(17, 107, 131, 0.08);
    }

    @media (max-width: 768px) {
        .blog-card-image,
        .blog-card-placeholder {
            height: 190px;
            min-height: 190px;
        }
    }
</style>
@endpush

@section('content')
<section class="blog-page-hero">
    <div class="container">
        <h1>Our Blog</h1>
        <p>Stories, updates, and insights from Degchi Dine. Browse all published posts below.</p>
    </div>
</section>

<div class="container pb-5">
    <div class="row g-4">
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 mb-0" style="color: #116b83;">Published Posts</h2>
                <span class="text-muted">{{ $posts->total() }} {{ Str::plural('post', $posts->total()) }}</span>
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
                                    <small class="text-muted text-nowrap">
                                        <i class="ri-eye-line"></i> {{ number_format($post->view_count) }}
                                    </small>
                                </div>

                                <a href="{{ route('frontend.blog.show', $post->slug) }}" class="blog-card-title">
                                    {{ $post->title }}
                                </a>

                                <p class="blog-card-excerpt">{{ $post->excerpt }}</p>

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
                            <i class="ri-article-line fs-1 text-muted mb-3 d-block"></i>
                            <h3 class="h5">No published posts yet</h3>
                            <p class="text-muted mb-0">Check back soon for new articles.</p>
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
                        <span class="category-badge">{{ $category->name }}</span>
                    @empty
                        <span class="text-muted small">No categories yet.</span>
                    @endforelse
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection
