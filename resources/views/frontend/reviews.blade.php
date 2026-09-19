@extends('frontend.layout')

@section('meta_title', 'Guest Reviews')
@section('meta_description', 'Read what guests say about Degchi Dine. Real reviews from our members and diners in Chittagong.')
@section('meta_robots', 'index, follow')

@section('frontend_content')

<x-frontend.page-header
    title="Guest Reviews"
    eyebrow="Our Testimonials"
    subtitle="Read what our valued guests have to say about their Degchi Dine experience."
/>

<section class="blog-content-section">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-9">
                <div class="blog-index-header">
                    <h2 class="h4 mb-0">What Our Guests Say</h2>
                    <span class="post-count">{{ $reviews->total() }} {{ Str::plural('review', $reviews->total()) }}</span>
                </div>

                <div class="reviews-slider">
                    @forelse($reviews as $review)
                        <div class="review-slide-item">
                            <div class="review-card">
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
                                <p class="review-text">
                                    "{{ $review->comment }}"
                                </p>
                                <hr class="review-divider" />
                                <div class="review-author">
                                    @php
                                        $avatarUrl = $review->image
                                            ? asset('storage/' . $review->image)
                                            : asset('assets/images/defult_image/degchi_dine_avater.jpg');
                                    @endphp
                                    <img src="{{ $avatarUrl }}" class="review-avatar"
                                        alt="{{ $review->name }} - Degchi Dine Guest Review"
                                        onerror="this.src='{{ asset('assets/images/defult_image/degchi_dine_avater.jpg') }}'" />
                                    <div class="author-info">
                                        <strong class="d-block">{{ $review->name }}</strong>
                                        <span class="text-muted small">{{ $review->title ?? 'Guest' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="review-slide-item">
                            <div class="review-card">
                                <div class="review-quote-icon">
                                    <i class="bi bi-quote"></i>
                                </div>
                                <div class="review-stars mb-3">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                </div>
                                <p class="review-text">
                                    "No reviews yet. Be the first to share your experience with us!"
                                </p>
                                <hr class="review-divider" />
                                <div class="review-author">
                                    <img src="{{ asset('assets/images/defult_image/degchi_dine_avater.jpg') }}" class="review-avatar" alt="Degchi Dine" />
                                    <div class="author-info">
                                        <strong class="d-block">Degchi Dine</strong>
                                        <span class="text-muted small">Coming Soon</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

                @if($reviews->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $reviews->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
