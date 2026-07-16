<div class="comment-wrapper" id="comment-{{ $comment->id }}">
    <div class="d-flex gap-3">
        <div class="comment-avatar">
            {{ $comment->member ? substr($comment->member->name, 0, 2) : '??' }}
        </div>
        <div class="comment-content">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="comment-author">
                        {{ $comment->member ? $comment->member->name : 'Unknown' }}
                    </div>
                    <div class="comment-text">{{ $comment->comment }}</div>
                    <div class="comment-time">{{ $comment->created_at->diffForHumans() }}</div>
                </div>
                @if(auth('member')->check())
                    <button class="reply-btn btn btn-sm btn-link text-decoration-none"
                            data-comment-id="{{ $comment->id }}">
                        <i class="ri-reply-line"></i> Reply
                    </button>
                @endif
            </div>

            <div class="comment-actions">
                @auth('member')
                    <button class="comment-action-btn reaction-btn"
                            data-comment-id="{{ $comment->id }}"
                            data-reaction="like"
                            type="button">
                        <i class="ri-thumb-up-line"></i>
                        <span class="reaction-count likes-count">{{ $comment->likes_count }}</span>
                    </button>
                    <button class="comment-action-btn reaction-btn"
                            data-comment-id="{{ $comment->id }}"
                            data-reaction="dislike"
                            type="button">
                        <i class="ri-thumb-down-line"></i>
                        <span class="reaction-count dislikes-count">{{ $comment->dislikes_count }}</span>
                    </button>
                @else
                    <span class="comment-action-btn">
                        <i class="ri-thumb-up-line"></i>
                        <span class="reaction-count">{{ $comment->likes_count }}</span>
                    </span>
                    <span class="comment-action-btn">
                        <i class="ri-thumb-down-line"></i>
                        <span class="reaction-count">{{ $comment->dislikes_count }}</span>
                    </span>
                @endauth
            </div>

            @auth('member')
                <div class="reply-form">
                    <div class="comment-form">
                        <form class="reply-form-submit" data-post-slug="{{ $postSlug ?? $comment->post->slug ?? '' }}">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                            <div class="mb-2">
                                <textarea name="comment" class="form-control" rows="3"
                                          placeholder="Write a reply..."></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="ri-send-plane-line me-1"></i>Reply
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm cancel-reply-btn">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endauth

            @if($comment->relationLoaded('replies') && $comment->replies->count() > 0)
                <div class="nested-comment" id="replies-{{ $comment->id }}">
                    @foreach($comment->replies as $reply)
                        @include('frontend.blog.partials.comment', [
                            'comment' => $reply,
                            'postSlug' => $postSlug ?? ($comment->post->slug ?? ''),
                        ])
                    @endforeach
                </div>
            @else
                <div class="nested-comment" id="replies-{{ $comment->id }}"></div>
            @endif
        </div>
    </div>
</div>
