@php
    $replyCount = ($comment->relationLoaded('replies') ? $comment->replies->count() : 0);
@endphp
<div class="comment-wrapper" id="comment-{{ $comment->id }}">
    <div class="d-flex gap-3">
        <div class="comment-avatar">
            {{ $comment->member ? substr($comment->member->name, 0, 2) : '??' }}
        </div>
        <div class="comment-content">
            <div class="comment-meta-row">
                <span class="comment-author">
                    {{ $comment->member ? $comment->member->name : 'Unknown' }}
                </span>
                <span class="comment-time">{{ $comment->created_at->diffForHumans() }}</span>
            </div>

            <div class="comment-text">{{ $comment->comment }}</div>

            <div class="comment-actions">
                @auth('member')
                    <button class="comment-action-btn reaction-btn"
                            data-comment-id="{{ $comment->id }}"
                            data-reaction="like"
                            type="button"
                            aria-label="Like">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h9.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/>
                            <path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/>
                        </svg>
                        <span class="reaction-count likes-count">{{ $comment->likes_count }}</span>
                    </button>
                    <button class="comment-action-btn reaction-btn"
                            data-comment-id="{{ $comment->id }}"
                            data-reaction="dislike"
                            type="button"
                            aria-label="Dislike">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H7.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3H10z"/>
                            <path d="M17 2h2.67A2.31 2.31 0 0 1 22 4v7a2.31 2.31 0 0 1-2.33 2H17"/>
                        </svg>
                        <span class="reaction-count dislikes-count">{{ $comment->dislikes_count }}</span>
                    </button>
                    <button class="reply-btn"
                            data-comment-id="{{ $comment->id }}"
                            type="button">
                        Reply
                    </button>
                @else
                    <span class="comment-action-btn">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h9.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/>
                            <path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/>
                        </svg>
                        <span class="reaction-count">{{ $comment->likes_count }}</span>
                    </span>
                    <span class="comment-action-btn">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H7.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3H10z"/>
                            <path d="M17 2h2.67A2.31 2.31 0 0 1 22 4v7a2.31 2.31 0 0 1-2.33 2H17"/>
                        </svg>
                        <span class="reaction-count">{{ $comment->dislikes_count }}</span>
                    </span>
                @endauth

                @if($replyCount > 0)
                    <span class="comment-action-btn" style="cursor: default; pointer-events: none;">
                        Reply ({{ $replyCount }})
                    </span>
                @endif
            </div>

            @auth('member')
                <div class="reply-form">
                    <div class="comment-composer">
                        <form class="reply-form-submit" data-post-slug="{{ $postSlug ?? $comment->post->slug ?? '' }}">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                            <textarea name="comment" class="form-control" rows="2"
                                      placeholder="Write a reply..."></textarea>
                            <div class="comment-composer-footer">
                                <button type="button" class="btn btn-secondary btn-sm cancel-reply-btn">Cancel</button>
                                <button type="submit" class="btn btn-post btn-sm">Reply</button>
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
