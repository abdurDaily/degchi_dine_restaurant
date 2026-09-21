<!-- REVIEWS -->
<section class="section-block reviews-section" id="testimonials">
    <div class="container px-4 px-lg-5">
        <x-frontend.section-heading title="আমাদের অতিথিদের মন্তব্য" />

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
                                    ? 'storage/' . $review->image
                                    : 'assets/images/defult_image/degchi_dine_avater.jpg';
                            @endphp
                            <img src="{{ asset($avatarUrl) }}" class="review-avatar"
                                alt="{{ $review->name }} - Degchi Dine Guest Review"
                                width="48" height="48"
                                loading="lazy"
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
                            <img src="{{ asset('assets/images/defult_image/degchi_dine_avater.jpg') }}" class="review-avatar" alt="Degchi Dine" width="48" height="48" loading="lazy" />
                            <div class="author-info">
                                <strong class="d-block">Degchi Dine</strong>
                                <span class="text-muted small">Coming Soon</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>
