
@extends('frontend.layout')

@section('meta_title',  $post->title)
@section('meta_robots', 'index, follow')

@push('front_css')
<style>
    :root {
        --brand: #116b83;
        --brand-dark: #0a4554;
        --brand-teal: #116b83;
        --brand-teal-dark: #083844;
        --brand-gold: #e7ae07;
        --brand-gold-hover: #c99606;
        --brand-gold-light: rgba(231, 174, 7, 0.15);
        --brand-teal-light: rgba(17, 107, 131, 0.12);
        --brand-red: #0d5566;
    }

    .blog-detail-header {
        background: linear-gradient(135deg, var(--brand), var(--brand-dark));
        color: #fff;
        padding: 3rem 0;
        margin-bottom: 2rem;
    }

    .blog-detail-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .blog-detail-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        font-size: 0.95rem;
        opacity: 0.9;
    }

    .blog-detail-content {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #2d2d2d;
    }

    .blog-detail-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 1.5rem 0;
    }

    /* Comments */
    .comment-wrapper {
        background: #fff;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .comment-wrapper:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }

    .comment-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--brand-teal-light);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--brand);
        font-weight: 600;
        flex-shrink: 0;
    }

    .comment-content {
        flex: 1;
    }

    .comment-author {
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 0.25rem;
    }

    .comment-text {
        color: #4a4a4a;
        line-height: 1.6;
        margin-bottom: 0.5rem;
    }

    .comment-time {
        font-size: 0.8rem;
        color: #6c757d;
    }

    .comment-actions {
        display: flex;
        gap: 1rem;
        margin-top: 0.5rem;
    }

    .comment-action-btn {
        background: none;
        border: none;
        color: #6c757d;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.3s ease;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .comment-action-btn:hover {
        background: var(--brand-teal-light);
        color: var(--brand);
    }

    .comment-action-btn.liked {
        color: var(--brand);
    }

    .comment-action-btn.disliked {
        color: var(--brand-red);
    }

    .reply-form {
        margin-top: 1rem;
        padding-left: 2rem;
        display: none;
    }

    .reply-form.active {
        display: block;
    }

    .reply-btn {
        color: var(--brand);
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 500;
        background: none;
        border: none;
        padding: 0;
    }

    .reply-btn:hover {
            text-decoration: underline;
        }

    .nested-comment {
        margin-left: 2.5rem;
        padding-left: 1.5rem;
        border-left: 2px solid var(--brand-teal-light);
    }

    .comment-form {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 2rem;
    }

    .comment-form textarea {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        padding: 0.75rem;
        resize: vertical;
        min-height: 100px;
        transition: all 0.3s ease;
        width: 100%;
    }

    .comment-form textarea:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px var(--brand-teal-light);
        outline: none;
    }

    .btn-primary {
        background: var(--brand);
        border: none;
        padding: 0.625rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: var(--brand-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(17, 107, 131, 0.3);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .btn-secondary {
        background: #e9ecef;
        border: none;
        color: #495057;
        padding: 0.625rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background: #dee2e6;
    }

    .login-to-comment {
        background: var(--brand-teal-light);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        color: var(--brand);
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .blog-detail-title {
            font-size: 1.8rem;
        }
        
        .nested-comment {
            margin-left: 1rem;
            padding-left: 0.75rem;
        }
        
        .reply-form {
            padding-left: 1rem;
        }
    }
</style>
@endpush

@section('content')
<!-- Header -->
<div class="blog-detail-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <h1 class="blog-detail-title">{{ $post->title }}</h1>
                <div class="blog-detail-meta">
                    <span>
                        <i class="ri-user-line"></i> 
                        {{ $post->author?->name ?? 'Unknown' }}
                    </span>
                    <span>
                        <i class="ri-calendar-line"></i> 
                        {{ $post->created_at->format('M d, Y') }}
                    </span>
                    <span>
                        <i class="ri-eye-line"></i> 
                        {{ number_format($post->view_count) }} views
                    </span>
                    @if($post->blogCategory)
                        <span>
                            <i class="ri-price-tag-3-line"></i> 
                            {{ $post->blogCategory->name }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- Featured Image -->
            @if($post->image_url)
                <div class="mb-4">
                    <img src="{{ $post->image_url }}" 
                         alt="{{ $post->title }}" 
                         class="img-fluid rounded" 
                         style="width: 100%; max-height: 500px; object-fit: cover;">
                </div>
            @endif

            <!-- Content -->
            <div class="blog-detail-content">
                {!! $post->content !!}
            </div>

            <!-- Related Posts -->
            @if($relatedPosts->count() > 0)
                <div class="mt-5 pt-4 border-top">
                    <h4 class="mb-3">Related Posts</h4>
                    <div class="row g-3">
                        @foreach($relatedPosts as $related)
                            <div class="col-md-4">
                                <div class="blog-card">
                                    <div class="blog-card-image-wrapper">
                                        @if($related->image_url)
                                            <img src="{{ $related->image_url }}" 
                                                 alt="{{ $related->title }}" 
                                                 class="blog-card-image" 
                                                 style="height: 150px;">
                                        @endif
                                    </div>
                                    <div class="blog-card-body" style="padding: 1rem;">
                                        <a href="{{ route('frontend.blog.show', $related->slug) }}" 
                                           class="blog-card-title" 
                                           style="font-size: 0.95rem;">
                                            {{ $related->title }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Comments Section -->
            <div class="mt-5 pt-4 border-top">
                <h4 class="mb-4">
                    Comments ({{ $post->allComments->where('is_active', true)->count() }})
                </h4>

                @if(!$post->comments_enabled)
                    <div class="alert alert-warning">
                        <i class="ri-chat-off-line me-2"></i>
                        Comments are disabled for this post.
                    </div>
                @else
                    <!-- Comment Form -->
                    @auth('member')
                        <div class="comment-form">
                            <h5 class="mb-3">Leave a Comment</h5>
                            <form id="commentForm" data-post-slug="{{ $post->slug }}">
                                @csrf
                                <div class="mb-3">
                                    <textarea name="comment" class="form-control" rows="4" 
                                              placeholder="Write your comment..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-send-plane-line me-2"></i>Post Comment
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="login-to-comment">
                            <i class="ri-login-box-line fs-4 d-block mb-2"></i>
                            Please <a href="{{ route('frontend.member.login') }}" class="text-decoration-none fw-bold" 
                                       style="color: var(--brand);">login as member</a> to comment
                        </div>
                    @endauth

                    <!-- Comments List -->
                    <div id="commentsContainer" class="mt-4">
                        @foreach($post->comments as $comment)
                            @include('frontend.blog.partials.comment', ['comment' => $comment, 'postSlug' => $post->slug])
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('front_js')
<script>
    $(document).ready(function() {
        const commentUrl = "{{ url('/blog') }}/:slug/comments";
        const reactUrl = "{{ route('frontend.blog.react', ':comment') }}";
        const memberLoginUrl = "{{ route('frontend.member.login') }}";
        const isMember = @json(auth('member')->check());

        function postComment(slug, commentText, parentId, onSuccess) {
            $.ajax({
                url: commentUrl.replace(':slug', slug),
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    comment: commentText,
                    parent_id: parentId || null
                },
                success: function(response) {
                    if (response.success) {
                        onSuccess(response);
                        toastr.success(parentId ? 'Reply posted successfully!' : 'Comment posted successfully!');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 401) {
                        toastr.error('Please login as member to comment');
                        window.location.href = memberLoginUrl;
                    } else if (xhr.status === 403) {
                        toastr.error('Comments are disabled for this post');
                    } else if (xhr.status === 422) {
                        toastr.error((xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.comment && xhr.responseJSON.errors.comment[0]) || 'Invalid comment');
                    } else {
                        toastr.error('Error posting comment');
                    }
                }
            });
        }

        $('#commentForm').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            postComment(form.data('post-slug'), form.find('textarea[name="comment"]').val(), null, function(response) {
                $('#commentsContainer').prepend(response.comment);
                form.find('textarea[name="comment"]').val('');
            });
        });

        $(document).on('click', '.reply-btn', function() {
            const parentComment = $(this).closest('.comment-wrapper');
            const replyForm = parentComment.find('> .d-flex > .comment-content > .reply-form').first();
            $('.reply-form').not(replyForm).removeClass('active');
            replyForm.toggleClass('active');
            if (replyForm.hasClass('active')) {
                replyForm.find('textarea[name="comment"]').focus();
            }
        });

        $(document).on('click', '.cancel-reply-btn', function() {
            $(this).closest('.reply-form').removeClass('active');
        });

        $(document).on('submit', '.reply-form-submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const parentId = form.find('input[name="parent_id"]').val();
            postComment(form.data('post-slug'), form.find('textarea[name="comment"]').val(), parentId, function(response) {
                $('#replies-' + parentId).append(response.comment);
                form.find('textarea[name="comment"]').val('');
                form.closest('.reply-form').removeClass('active');
            });
        });

        $(document).on('click', '.reaction-btn', function() {
            if (!isMember) {
                toastr.error('Please login as member to react');
                window.location.href = memberLoginUrl;
                return;
            }

            const button = $(this);
            const commentId = button.data('comment-id');
            const reaction = button.data('reaction');
            const actions = button.closest('.comment-actions');

            $.ajax({
                url: reactUrl.replace(':comment', commentId),
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', reaction: reaction },
                success: function(response) {
                    if (!response.success) return;
                    actions.find('.likes-count').text(response.likes);
                    actions.find('.dislikes-count').text(response.dislikes);
                    actions.find('[data-reaction="like"]').removeClass('liked');
                    actions.find('[data-reaction="dislike"]').removeClass('disliked');
                    if (!response.removed) {
                        button.addClass(reaction === 'like' ? 'liked' : 'disliked');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 401) {
                        toastr.error('Please login as member to react');
                        window.location.href = memberLoginUrl;
                    } else {
                        toastr.error('Error processing reaction');
                    }
                }
            });
        });
    });
</script>
@endpush