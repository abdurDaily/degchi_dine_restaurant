
@extends('frontend.layout')

@section('meta_title',  $post->title)
@section('meta_robots', 'index, follow')

@section('frontend_content')
@php
    $heroLead = \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($post->content))), 140);
@endphp

<x-frontend.page-header
    :title="$post->title"
    :eyebrow="$post->blogCategory?->name ?? 'Our Stories'"
    variant="detail"
>
    <x-slot:meta>
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
    </x-slot:meta>
</x-frontend.page-header>

<section class="blog-content-section">
    <div class="container pb-5">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <!-- Featured Image -->
                <div class="mb-4">
                    @if($post->image_url)
                        <div class="blog-card-image-wrapper" style="border-radius: 16px;">
                            <img src="{{ $post->image_url }}"
                                 alt="{{ $post->title }}"
                                 class="blog-card-image">
                        </div>
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
                    <div class="mt-5 pt-4">
                        <h4 class="mb-3" style="color: var(--text-on-dark);">Related Posts</h4>
                        <div class="row g-4 related-posts">
                            @foreach($relatedPosts as $related)
                                <div class="col-md-4">
                                    <article class="blog-card h-100">
                                        <div class="blog-card-image-wrapper">
                                            @if($related->image_url)
                                                <img src="{{ $related->image_url }}"
                                                     alt="{{ $related->title }}"
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
                                                @if($related->blogCategory)
                                                    <span class="category-badge">{{ $related->blogCategory->name }}</span>
                                                @else
                                                    <span></span>
                                                @endif
                                                <small class="text-nowrap" style="color: var(--text-on-dark-muted);">
                                                    <i class="ri-eye-line"></i> {{ number_format($related->view_count) }}
                                                </small>
                                            </div>

                                            <a href="{{ route('frontend.blog.show', $related->slug) }}" class="blog-card-title">
                                                {{ $related->title }}
                                            </a>

                                            <div class="blog-card-meta">
                                                <span><i class="ri-user-line"></i> {{ $related->author?->name ?? 'Admin' }}</span>
                                                <span><i class="ri-calendar-line"></i> {{ $related->created_at->format('M d, Y') }}</span>
                                            </div>

                                            <a href="{{ route('frontend.blog.show', $related->slug) }}" class="blog-card-btn">
                                                Read More <i class="ri-arrow-right-line ms-1"></i>
                                            </a>
                                        </div>
                                    </article>
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
                    <div class="mb-0 p-3" style="background: rgba(231, 174, 7, 0.15); border: 1px solid rgba(231, 174, 7, 0.3); border-radius: 12px; color: var(--brand-gold);">
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
                            Please <a href="{{ route('frontend.member.login') }}" class="text-decoration-none fw-bold">login as member</a> to comment
                        </div>
                    @endauth

                    <div id="commentsContainer">
                        @forelse($post->comments as $comment)
                            @include('frontend.blog.partials.comment', ['comment' => $comment, 'postSlug' => $post->slug])
                        @empty
                            <p class="comments-empty text-center py-3 mb-0 small" style="color: var(--text-on-dark-muted);">No comments yet. Be the first to share your thoughts.</p>
                        @endforelse
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

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
