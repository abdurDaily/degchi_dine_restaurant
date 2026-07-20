
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

    /* Hero — centered heritage style */
    .blog-detail-header {
        background:
            radial-gradient(ellipse 70% 60% at 50% 45%, rgba(17, 107, 131, 0.45) 0%, transparent 70%),
            linear-gradient(180deg, #0d5a6e 0%, #083844 55%, #062a33 100%);
        color: #fff;
        padding: 4.5rem 1.5rem 4rem;
        margin-bottom: 2.5rem;
        text-align: center;
    }

    .blog-detail-header-inner {
        max-width: 760px;
        margin: 0 auto;
    }

    .blog-detail-eyebrow {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.92);
        margin-bottom: 1.15rem;
    }

    .blog-detail-eyebrow svg {
        width: 14px;
        height: 14px;
        fill: currentColor;
        flex-shrink: 0;
    }

    .blog-detail-title {
        font-size: clamp(1.85rem, 4.5vw, 2.75rem);
        font-weight: 700;
        line-height: 1.2;
        margin: 0 0 1rem;
        color: #fff;
        letter-spacing: -0.02em;
    }

    .blog-detail-lead {
        font-size: 1.05rem;
        line-height: 1.7;
        color: rgba(255, 255, 255, 0.82);
        max-width: 560px;
        margin: 0 auto 1.5rem;
        font-weight: 400;
    }

    .blog-detail-meta {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.75rem 1.5rem;
        font-size: 0.88rem;
        color: rgba(255, 255, 255, 0.72);
    }

    .blog-detail-meta span {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
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

    /* Featured image / placeholder */
    .blog-featured-image {
        width: 100%;
        max-height: 500px;
        object-fit: cover;
        border-radius: 16px;
        display: block;
    }

    .blog-image-placeholder {
        width: 100%;
        min-height: 320px;
        max-height: 500px;
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        background:
            linear-gradient(135deg, rgba(17, 107, 131, 0.08) 0%, rgba(231, 174, 7, 0.12) 100%),
            linear-gradient(135deg, #e8f4f7 0%, #cfe4eb 100%);
        border: 1px dashed rgba(17, 107, 131, 0.25);
        color: var(--brand);
        overflow: hidden;
        position: relative;
    }

    .blog-image-placeholder::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 20% 30%, rgba(231, 174, 7, 0.18) 0%, transparent 45%),
            radial-gradient(circle at 80% 70%, rgba(17, 107, 131, 0.15) 0%, transparent 40%);
        pointer-events: none;
    }

    .blog-image-placeholder-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.75);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 16px rgba(17, 107, 131, 0.12);
        position: relative;
        z-index: 1;
    }

    .blog-image-placeholder-icon svg {
        width: 36px;
        height: 36px;
        stroke: var(--brand);
        fill: none;
        stroke-width: 1.5;
    }

    .blog-image-placeholder-label {
        font-size: 0.95rem;
        font-weight: 500;
        opacity: 0.85;
        position: relative;
        z-index: 1;
    }

    /* Comments panel — reference card UI */
    .comments-panel {
        margin-top: 2.5rem;
        padding: 1.5rem;
        border-radius: 20px;
        background: #fff;
        border: 1px solid rgba(17, 107, 131, 0.08);
        box-shadow: 0 12px 40px rgba(17, 107, 131, 0.08);
    }

    .comments-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
    }

    .comments-section-title {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        font-weight: 700;
        color: #1a1a1a;
        font-size: 1.25rem;
        margin: 0;
    }

    .comments-section-title .count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 1.85rem;
        height: 1.6rem;
        padding: 0 0.55rem;
        border-radius: 999px;
        background: var(--brand);
        color: #fff;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .comment-composer {
        background: #f8fafb;
        border: 1px solid #e6ecef;
        border-radius: 16px;
        padding: 1rem 1.1rem 0.9rem;
        margin-bottom: 1.5rem;
    }

    .comment-composer textarea {
        border: none;
        background: transparent;
        box-shadow: none;
        padding: 0.35rem 0.15rem 0.75rem;
        min-height: 72px;
        resize: vertical;
        width: 100%;
        font-size: 0.95rem;
        color: #2d2d2d;
    }

    .comment-composer textarea:focus {
        outline: none;
        box-shadow: none;
        border: none;
    }

    .comment-composer-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.75rem;
        padding-top: 0.35rem;
        border-top: 1px solid #e8eef1;
    }

    .comment-composer .btn-post {
        background: var(--brand);
        color: #fff;
        border: none;
        border-radius: 999px;
        padding: 0.5rem 1.35rem;
        font-weight: 600;
        font-size: 0.9rem;
        transition: background 0.2s ease, transform 0.15s ease;
    }

    .comment-composer .btn-post:hover {
        background: var(--brand-dark);
        color: #fff;
        transform: translateY(-1px);
    }

    .login-to-comment {
        background: linear-gradient(135deg, rgba(17, 107, 131, 0.08), rgba(231, 174, 7, 0.1));
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        color: var(--brand);
        font-weight: 500;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(17, 107, 131, 0.12);
    }

    .comment-wrapper {
        background: transparent;
        border-radius: 0;
        padding: 1.15rem 0;
        border: none;
        border-bottom: 1px solid #eef2f4;
        box-shadow: none;
        margin-bottom: 0;
    }

    .comment-wrapper:last-child {
        border-bottom: none;
    }

    .comment-wrapper:hover {
        box-shadow: none;
        border-color: #eef2f4;
    }

    .comment-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #d4e8ee, #c5dde5);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--brand);
        font-weight: 700;
        font-size: 0.8rem;
        letter-spacing: 0.02em;
        flex-shrink: 0;
        text-transform: uppercase;
    }

    .comment-content {
        flex: 1;
        min-width: 0;
    }

    .comment-meta-row {
        display: flex;
        align-items: baseline;
        flex-wrap: wrap;
        gap: 0.35rem 0.65rem;
        margin-bottom: 0.35rem;
    }

    .comment-author {
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
        font-size: 0.92rem;
    }

    .comment-time {
        font-size: 0.78rem;
        color: #9aa3ab;
        margin: 0;
    }

    .comment-text {
        color: #3a4148;
        line-height: 1.6;
        margin: 0 0 0.65rem;
        white-space: pre-wrap;
        word-break: break-word;
        font-size: 0.94rem;
    }

    .comment-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.15rem 0.85rem;
        margin-top: 0;
        padding-top: 0;
        border-top: none;
    }

    .comment-action-btn {
        background: none;
        border: none;
        color: #7a848e;
        font-size: 0.82rem;
        cursor: pointer;
        transition: color 0.2s ease;
        padding: 0.2rem 0.15rem;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        line-height: 1;
    }

    .comment-action-btn svg {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
        stroke: currentColor;
        fill: none;
        stroke-width: 1.75;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .comment-action-btn .reaction-count {
        font-weight: 600;
        font-variant-numeric: tabular-nums;
        min-width: 0.75rem;
        color: inherit;
    }

    .comment-action-btn:hover {
        background: none;
        color: var(--brand);
    }

    .comment-action-btn.liked {
        color: var(--brand);
        background: none;
        border: none;
    }

    .comment-action-btn.liked svg {
        fill: currentColor;
        stroke: currentColor;
    }

    .comment-action-btn.disliked {
        color: #b42318;
        background: none;
        border: none;
    }

    .comment-action-btn.disliked svg {
        fill: currentColor;
        stroke: currentColor;
    }

    .reply-btn {
        color: var(--brand);
        cursor: pointer;
        font-size: 0.82rem;
        font-weight: 600;
        background: none;
        border: none;
        padding: 0.2rem 0.15rem;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        white-space: nowrap;
    }

    .reply-btn:hover {
        background: none;
        text-decoration: underline;
        color: var(--brand-dark);
    }

    .reply-form {
        margin-top: 0.85rem;
        display: none;
    }

    .reply-form.active {
        display: block;
    }

    .reply-form .comment-form,
    .reply-form .comment-composer {
        margin-top: 0;
        margin-bottom: 0;
    }

    .nested-comment {
        margin-left: 1.15rem;
        margin-top: 0.25rem;
        padding-left: 1.15rem;
        border-left: 2px solid #e6ecef;
        position: relative;
    }

    .nested-comment > .comment-wrapper {
        background: transparent;
    }

    .comment-form {
        background: #f8fafb;
        border-radius: 16px;
        padding: 1rem 1.1rem;
        margin-top: 0;
        border: 1px solid #e6ecef;
    }

    .comment-form textarea {
        border-radius: 10px;
        border: 1px solid #dce4e8;
        padding: 0.75rem 0.9rem;
        resize: vertical;
        min-height: 80px;
        transition: all 0.25s ease;
        width: 100%;
        background: #fff;
    }

    .comment-form textarea:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px var(--brand-teal-light);
        outline: none;
    }

    .btn-primary {
        background: var(--brand);
        border: none;
        padding: 0.5rem 1.25rem;
        border-radius: 999px;
        font-weight: 600;
        transition: all 0.25s ease;
    }

    .btn-primary:hover {
        background: var(--brand-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(17, 107, 131, 0.3);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .btn-secondary {
        background: #e9ecef;
        border: none;
        color: #495057;
        padding: 0.5rem 1.15rem;
        border-radius: 999px;
        font-weight: 500;
        transition: all 0.25s ease;
    }

    .btn-secondary:hover {
        background: #dee2e6;
    }

    @media (max-width: 768px) {
        .blog-detail-header {
            padding: 3.25rem 1rem 2.75rem;
        }

        .blog-detail-lead {
            font-size: 0.95rem;
        }

        .blog-image-placeholder {
            min-height: 220px;
        }

        .comments-panel {
            padding: 1.1rem;
            border-radius: 16px;
        }

        .nested-comment {
            margin-left: 0.35rem;
            padding-left: 0.75rem;
        }
    }
</style>
@endpush

@section('frontend_content')
@php
    $heroLead = \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($post->content))), 140);
@endphp
<!-- Header -->
<div class="blog-detail-header">
    <div class="container">
        <div class="blog-detail-header-inner py-5">
            <div class="blog-detail-eyebrow">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
                {{ $post->blogCategory?->name ?? 'Our Stories' }}
            </div>
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
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- Featured Image -->
            <div class="mb-4">
                @if($post->image_url)
                    <img src="{{ $post->image_url }}"
                         alt="{{ $post->title }}"
                         class="blog-featured-image">
                @else
                    <div class="blog-image-placeholder" aria-hidden="true">
                        <div class="blog-image-placeholder-icon">
                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <rect x="3" y="5" width="18" height="14" rx="2"/>
                                <circle cx="8.5" cy="10" r="1.5"/>
                                <path d="M21 16l-5.5-5.5a1.5 1.5 0 0 0-2.12 0L6 18"/>
                            </svg>
                        </div>
                        <span class="blog-image-placeholder-label">No featured image</span>
                    </div>
                @endif
            </div>

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
            <div class="comments-panel">
                <div class="comments-header">
                    <h4 class="comments-section-title">
                        Comments
                        <span class="count-badge">{{ $post->allComments->where('is_active', true)->count() }}</span>
                    </h4>
                </div>

                @if(!$post->comments_enabled)
                    <div class="alert alert-warning mb-0">
                        <i class="ri-chat-off-line me-2"></i>
                        Comments are disabled for this post.
                    </div>
                @else
                    @auth('member')
                        <div class="comment-composer">
                            <form id="commentForm" data-post-slug="{{ $post->slug }}">
                                @csrf
                                <textarea name="comment" class="form-control" rows="3"
                                          placeholder="Share your mind..." required></textarea>
                                <div class="comment-composer-footer">
                                    <button type="submit" class="btn btn-post">Post</button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="login-to-comment">
                            <i class="ri-login-box-line fs-4 d-block mb-2"></i>
                            Please <a href="{{ route('frontend.member.login') }}" class="text-decoration-none fw-bold"
                                       style="color: var(--brand);">login as member</a> to comment
                        </div>
                    @endauth

                    <div id="commentsContainer">
                        @forelse($post->comments as $comment)
                            @include('frontend.blog.partials.comment', ['comment' => $comment, 'postSlug' => $post->slug])
                        @empty
                            <p class="comments-empty text-muted text-center py-3 mb-0" style="font-size: 0.95rem;">No comments yet. Be the first to share your thoughts.</p>
                        @endforelse
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
        const canOrderAndComment = @json(auth('member')->check() ? auth('member')->user()->canOrderAndComment() : false);
        const accountRestrictedMessage = @json(\App\Models\Member::ACCOUNT_RESTRICTED_MESSAGE);

        function postComment(slug, commentText, parentId, onSuccess) {
            if (isMember && !canOrderAndComment) {
                alert(accountRestrictedMessage);
                return;
            }

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
                        const msg = (xhr.responseJSON && xhr.responseJSON.error) || 'Comments are disabled for this post';
                        if (xhr.responseJSON && xhr.responseJSON.account_restricted) {
                            alert(msg);
                        } else {
                            toastr.error(msg);
                        }
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
                $('#commentsContainer .comments-empty').remove();
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

            if (!canOrderAndComment) {
                alert(accountRestrictedMessage);
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
                    } else if (xhr.status === 403 && xhr.responseJSON && xhr.responseJSON.account_restricted) {
                        alert(xhr.responseJSON.error || accountRestrictedMessage);
                    } else {
                        toastr.error('Error processing reaction');
                    }
                }
            });
        });
    });
</script>
@endpush
