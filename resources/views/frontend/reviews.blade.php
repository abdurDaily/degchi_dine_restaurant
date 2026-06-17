@extends('frontend.layout')

@section('meta_title', 'Guest Reviews')
@section('meta_description', 'Read what guests say about Degchi Dine. Real reviews from our members and diners in Chittagong.')

@section('frontend_content')
<main class="main-content">
    <section class="section-block reviews-section" id="testimonials">
        <div class="container px-4 px-lg-5">
            <div class="mb-4 text-center reveal">
                <h2 class="section-title">What Our Guests Say</h2>
                <div class="title-divider mx-auto"></div>
            </div>

            <div class="reviews-shell">
                <div class="reviews-toolbar">
                    <div>
                        <p class="reviews-kicker">Review layout</p>
                        <h3 class="reviews-toolbar-title">Choose how you want to browse</h3>
                    </div>
                    <div class="reviews-view-switch" role="tablist" aria-label="Switch review layout">
                        <button type="button" class="review-view-btn is-active" data-view="single" aria-pressed="true" title="Single row">
                            <i class="bi bi-list"></i>
                        </button>
                        <button type="button" class="review-view-btn" data-view="double" aria-pressed="false" title="Double column">
                            <i class="bi bi-grid"></i>
                        </button>
                    </div>
                </div>

                <p class="reviews-hint">Swipe horizontally on mobile in Single row mode, or use a cleaner two-column view on larger screens.</p>

                <div class="reviews-gallery reviews-gallery--double" id="reviewsGallery">
                    @forelse($reviews as $review)
                    <article class="review-card">
                        <div class="review-quote-icon">
                            <i class="bi bi-quote"></i>
                        </div>
                        <div class="review-stars mb-3">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $review->rating)
                                    <i class="bi bi-star-fill"></i>
                                @else
                                    <i class="bi bi-star"></i>
                                @endif
                            @endfor
                        </div>
                        <p class="review-text">"{{ $review->comment }}"</p>
                        <div class="review-author">
                            @if($review->image)
                                <img src="{{ asset('storage/' . $review->image) }}" class="review-avatar" alt="{{ $review->name }}" />
                            @else
                                @php
                                    $gravatarId = $review->email ? urlencode($review->email) : urlencode($review->name);
                                @endphp
                                <img src="https://i.pravatar.cc/150?u={{ $gravatarId }}" class="review-avatar" alt="{{ $review->name }}" />
                            @endif
                            <div class="author-info">
                                <strong class="d-block">{{ $review->name }}</strong>
                                <span class="text-muted small">{{ $review->title ?? 'Valued Member' }}</span>
                            </div>
                        </div>
                    </article>
                    @empty
                    <div class="col-12">
                        <p class="text-center text-muted py-5">No reviews yet. Be the first to share your experience!</p>
                    </div>
                    @endforelse
                </div>
            </div>

            @if($reviews->hasPages())
            <div class="d-flex justify-content-center mt-5">
                {{ $reviews->links() }}
            </div>
            @endif
        </div>
    </section>
</main>

<style>
    /* Fixed the huge top gap by tightening section block padding */
    .reviews-section {
        padding: 3rem 0;
    }

    .section-title {
        font-size: 2.25rem;
        font-weight: 700;
        color: #2b0e11; /* Food brand dark maroon tint */
        margin-bottom: 0.5rem;
    }

    .title-divider {
        width: 60px;
        height: 4px;
        background: #f27a21; /* Matches navbar active accent orange */
        margin: 0.75rem auto 1.5rem auto;
    }

    .reviews-shell {
        margin-bottom: 3rem;
    }

    .reviews-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1.5rem;
    }

    .reviews-kicker {
        font-size: 0.85rem;
        color: #f27a21; /* Accent Orange */
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.25rem;
    }

    .reviews-toolbar-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
    }

    .reviews-view-switch {
        display: flex;
        gap: 0.5rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 0.25rem;
        background-color: #fafafa;
    }

    .review-view-btn {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: transparent;
        cursor: pointer;
        font-size: 1.25rem;
        color: #718096;
        transition: all 0.2s ease-in-out;
        border-radius: 0.375rem;
    }

    .review-view-btn.is-active {
        background-color: #f27a21; /* Theme Orange background */
        color: white;
    }

    .review-view-btn:hover:not(.is-disabled):not(.is-active) {
        background-color: #edf2f7;
        color: #2b0e11;
    }

    .review-view-btn.is-disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    .reviews-hint {
        font-size: 0.875rem;
        color: #718096;
        margin-bottom: 2rem;
        font-style: italic;
    }

    .reviews-gallery {
        display: grid;
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .reviews-gallery--single {
        grid-template-columns: 1fr;
    }

    .reviews-gallery--double {
        grid-template-columns: repeat(2, 1fr);
    }

    .review-card {
        background: white;
        padding: 2rem;
        border-radius: 0.75rem;
        border: 1px solid #f1f2f4;
        box-shadow: 0 4px 12px rgba(43, 14, 17, 0.03);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
    }

    .review-card:hover {
        box-shadow: 0 12px 24px rgba(242, 122, 33, 0.08);
        transform: translateY(-4px);
        border-color: rgba(242, 122, 33, 0.15);
    }

    .review-quote-icon {
        font-size: 2.5rem;
        color: #f27a21;
        opacity: 0.15;
        margin-bottom: 0.5rem;
        line-height: 1;
    }

    .review-stars {
        display: flex;
        gap: 0.25rem;
        font-size: 1rem;
    }

    .review-stars .bi-star-fill {
        color: #f27a21; /* Unified color scheme theme stars */
    }

    .review-stars .bi-star {
        color: #e2e8f0;
    }

    .review-text {
        font-size: 0.975rem;
        line-height: 1.6;
        color: #4a5568;
        margin-bottom: 1.5rem;
    }

    .review-author {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .review-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #f1f2f4;
    }

    .author-info strong {
        font-size: 0.95rem;
        color: #2b0e11;
    }

    .author-info span {
        display: block;
        font-size: 0.8rem;
        color: #718096;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .reviews-gallery--double {
            grid-template-columns: 1fr;
        }

        .section-title {
            font-size: 1.75rem;
        }

        .reviews-toolbar {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .review-card {
            padding: 1.5rem;
        }
    }
</style>

<script>
    // Review Layout Switcher Logic remains completely intact
    document.addEventListener('DOMContentLoaded', function () {
        const gallery = document.getElementById('reviewsGallery');
        const buttons = document.querySelectorAll('.review-view-btn');
        const mobileBreakpoint = 992;

        if (!gallery || buttons.length === 0) {
            return;
        }

        function syncReviewLayout() {
            const isMobile = window.innerWidth < mobileBreakpoint;
            const currentView = isMobile ? 'single' : (gallery.dataset.currentView || 'double');

            gallery.classList.remove('reviews-gallery--single', 'reviews-gallery--double');
            gallery.classList.add(`reviews-gallery--${currentView}`);
            gallery.dataset.currentView = currentView;

            buttons.forEach((button) => {
                const isActive = button.dataset.view === currentView;
                const disableDoubleOnMobile = isMobile && button.dataset.view === 'double';

                button.classList.toggle('is-active', isActive);
                button.setAttribute('aria-pressed', String(isActive));
                button.disabled = disableDoubleOnMobile;
                button.classList.toggle('is-disabled', disableDoubleOnMobile);
            });
        }

        buttons.forEach((button) => {
            button.addEventListener('click', () => {
                if (button.disabled) {
                    return;
                }
                gallery.dataset.currentView = button.dataset.view;
                syncReviewLayout();
            });
        });

        syncReviewLayout();
        window.addEventListener('resize', syncReviewLayout);
    });
</script>
@endsection