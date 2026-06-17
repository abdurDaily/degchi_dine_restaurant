@extends('frontend.layout')

@section('meta_title', 'Contact Us')
@section('meta_description', 'Contact Degchi Dine for reservations, membership reviews, directions and opening hours in Halishahar, Chittagong.')

@section('frontend_content')
@php
    $contactName = optional($contactSettings->get('contact_restaurant_name'))->value ?? 'Degchi Dine';
    $contactAddress = optional($contactSettings->get('contact_address'))->value ?? "Boropool Circle, Kaptan Villa,\nHalishahar, Chittagong, Bangladesh";
    $contactHours = optional($contactSettings->get('contact_hours'))->value ?? 'Mon - Sun: 5:00 PM - 11:30 PM';
    $contactPhone = optional($contactSettings->get('contact_phone'))->value ?? '01898-795400';
    $contactEmail = optional($contactSettings->get('contact_email'))->value ?? 'degchidine@gmail.com';
    $contactMapLink = optional($contactSettings->get('contact_map_link'))->value ?: 'https://maps.google.com';
    $contactMapEmbed = optional($contactSettings->get('contact_map_embed'))->value ?: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3690.669527376662!2d91.7766299!3d22.3283281!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjLCsDE5JzQyLjAiTiA5McKwNDYnMzUuOSJF!5e0!3m2!1sen!2sbd!4v1620000000000!5m2!1sen!2sbd';
    $contactFacebookUrl = optional($contactSettings->get('contact_facebook_url'))->value ?? 'https://www.facebook.com/DegchiDine';
    $contactInstagramUrl = optional($contactSettings->get('contact_instagram_url'))->value ?? '#';
    $contactPhoneDigits = preg_replace('/\D+/', '', $contactPhone);
@endphp

<section class="dd-apply-wrapper contact-page">
    <div class="dd-apply-hero-banner">
        <div class="container px-4 px-lg-5 text-center position-relative">
            <a href="{{ route('frontend.home') }}" class="dd-apply-back-btn">
                <iconify-icon icon="solar:alt-arrow-left-linear"></iconify-icon>
                <span>Back to Home</span>
            </a>

            <div class="contact-page-hero-icon">
                <iconify-icon icon="solar:chat-round-dots-bold"></iconify-icon>
            </div>

            <span class="contact-page-kicker">Connect With Us</span>
            <h1 class="dd-apply-headline mb-2">Get In Touch</h1>
            <p class="contact-page-hero-lead mx-auto">
                Reach out for reservations, feedback, or membership reviews. We would love to hear from you.
            </p>
        </div>
    </div>

    <div class="container px-4 px-lg-5 contact-page-main-box">
        <div class="contact-page-grid">
            <aside class="contact-page-info order-2 order-lg-1">
                <div class="contact-page-info-panel">
                    <div class="contact-page-info-top">
                        <div>
                            <h2 class="contact-page-info-title">{{ $contactName }}</h2>
                            <p class="contact-page-info-tagline">Warm hospitality · Authentic flavors</p>
                        </div>
                        <span class="contact-page-open-badge"><i class="bi bi-clock me-1"></i> Open Daily</span>
                    </div>

                    <div class="contact-page-cards">
                        <article class="contact-page-card">
                            <div class="contact-page-card-icon"><i class="bi bi-geo-alt-fill"></i></div>
                            <div class="contact-page-card-body">
                                <h3>Address</h3>
                                <p>{!! nl2br(e($contactAddress)) !!}</p>
                            </div>
                        </article>

                        <article class="contact-page-card">
                            <div class="contact-page-card-icon"><i class="bi bi-telephone-fill"></i></div>
                            <div class="contact-page-card-body">
                                <h3>Phone</h3>
                                <p><a href="tel:{{ $contactPhoneDigits }}">{{ $contactPhone }}</a></p>
                            </div>
                        </article>

                        <article class="contact-page-card">
                            <div class="contact-page-card-icon"><i class="bi bi-envelope-fill"></i></div>
                            <div class="contact-page-card-body">
                                <h3>Email</h3>
                                <p><a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a></p>
                            </div>
                        </article>

                        <article class="contact-page-card contact-page-card-accent">
                            <div class="contact-page-card-icon contact-page-card-icon-gold"><i class="bi bi-clock-fill"></i></div>
                            <div class="contact-page-card-body">
                                <h3>Business Hours</h3>
                                <p>{{ $contactHours }}</p>
                            </div>
                        </article>
                    </div>

                    <div class="contact-page-actions">
                        <a href="{{ $contactMapLink }}" target="_blank" rel="noopener noreferrer" class="contact-page-btn contact-page-btn-teal">
                            <i class="bi bi-signpost-split-fill"></i>
                            Get Directions
                        </a>
                        <a href="tel:{{ $contactPhoneDigits }}" class="contact-page-btn contact-page-btn-gold">
                            <i class="bi bi-telephone-fill"></i>
                            Call Now
                        </a>
                    </div>

                    <div class="contact-page-socials">
                        <span>Follow us</span>
                        <a href="{{ $contactFacebookUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="{{ $contactInstagramUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>

                <div class="contact-page-map">
                    <div class="contact-page-map-label">
                        <i class="bi bi-pin-map-fill"></i>
                        <span>Halishahar, Chittagong</span>
                    </div>
                    <iframe
                        src="{{ $contactMapEmbed }}"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="{{ $contactName }} location map"></iframe>
                </div>
            </aside>

            <div class="contact-page-forms order-1 order-lg-2">
                <div class="contact-form-sticky">
                    <div id="memberCheckCard" class="cp-form-card cp-form-card-verify">
                        <div class="cp-form-card-head">
                            <span class="cp-form-step">Step 1</span>
                            <h3>Share Your Experience</h3>
                            <p>Exclusive for our valued members. Verify your membership card to post your review.</p>
                        </div>

                        <form id="memberVerificationForm" class="member-check-form">
                            @csrf

                            <div class="cp-field cp-field-highlight">
                                <label for="cardNumberCheck" class="cp-label">Membership Card Number</label>
                                <input type="text" class="cp-input" id="cardNumberCheck" name="card_number" required placeholder="Ex: DD-XXXX-XXXX">
                            </div>

                            <button type="submit" class="cp-btn cp-btn-primary w-100" id="verifyBtn">
                                <span id="verifyBtnText">Verify Membership Identity</span>
                                <span id="verifyBtnSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                            </button>

                            <p class="cp-form-footnote text-center mb-0">
                                Not a member yet?
                                <a href="{{ route('frontend.card.apply') }}" class="cp-link-gold">
                                    Apply for Membership <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </p>
                        </form>
                    </div>

                    <div id="reviewFormCard" class="cp-form-card cp-form-card-review d-none">
                        <div class="cp-alert-success mb-4" role="alert">
                            <i class="bi bi-shield-check"></i>
                            <div>
                                <strong>Access Granted</strong>
                                <span>Membership verified successfully. We look forward to your valuable insights.</span>
                            </div>
                        </div>

                        <form id="contactReviewForm" class="review-form">
                            @csrf
                            <input type="hidden" name="member_card_number" id="hiddenCardNumber">

                            <div class="cp-field mb-4">
                                <label class="cp-label">Your Rating *</label>
                                <div class="cp-rating d-flex gap-2 mt-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                    <label class="cp-rating-option">
                                        <input type="radio" name="rating" value="{{ $i }}" required>
                                        <i class="bi bi-star-fill"></i>
                                    </label>
                                    @endfor
                                </div>
                            </div>

                            <div class="cp-field mb-4">
                                <label for="title" class="cp-label">Review Title (Optional)</label>
                                <input type="text" class="cp-input" id="title" name="title" placeholder="e.g., An Absolute Culinary Delight">
                            </div>

                            <div class="cp-field mb-4">
                                <label for="comment" class="cp-label">Your Review *</label>
                                <textarea class="cp-input cp-textarea" id="comment" name="comment" rows="5" required placeholder="Share details of your experience with our dishes and ambiance..."></textarea>
                                <small class="cp-field-hint">Minimum 10 characters required.</small>
                            </div>

                            <div class="cp-form-actions">
                                <button type="submit" class="cp-btn cp-btn-primary flex-grow-1" id="submitReviewBtn">
                                    <span id="submitBtnText">
                                        <i class="bi bi-send me-2"></i>Publish Review
                                    </span>
                                    <span id="submitBtnSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                                </button>
                                <button type="button" class="cp-btn cp-btn-outline" id="backBtn" onclick="goBackToVerification()">
                                    <i class="bi bi-chevron-left"></i> Change ID
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.getElementById('memberVerificationForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const verifyBtn = document.getElementById('verifyBtn');
        const verifyBtnText = document.getElementById('verifyBtnText');
        const verifyBtnSpinner = document.getElementById('verifyBtnSpinner');
        const cardNumber = document.getElementById('cardNumberCheck').value;

        verifyBtn.disabled = true;
        verifyBtnText.textContent = 'Verifying Credentials...';
        verifyBtnSpinner.classList.remove('d-none');

        try {
            const response = await fetch('{{ route("frontend.reviews.verify-member") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ card_number: cardNumber }),
            });

            const data = await response.json();

            if (data.success) {
                document.getElementById('hiddenCardNumber').value = cardNumber;

                document.getElementById('memberCheckCard').classList.add('d-none');
                document.getElementById('reviewFormCard').classList.remove('d-none');

                toastr.success(data.message, 'Member Verified');
                document.getElementById('reviewFormCard').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                toastr.error(data.message || 'Verification failed', 'Error');
            }
        } catch (error) {
            toastr.error('An error occurred. Please try again.', 'Error');
            console.error('Error:', error);
        } finally {
            verifyBtn.disabled = false;
            verifyBtnText.textContent = 'Verify Membership Identity';
            verifyBtnSpinner.classList.add('d-none');
        }
    });

    function goBackToVerification() {
        document.getElementById('memberCheckCard').classList.remove('d-none');
        document.getElementById('reviewFormCard').classList.add('d-none');
        document.getElementById('memberVerificationForm').reset();
        document.getElementById('contactReviewForm').reset();
        document.querySelectorAll('.cp-rating-option').forEach((opt) => opt.classList.remove('checked'));
        document.getElementById('memberCheckCard').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    document.getElementById('contactReviewForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const submitBtn = document.getElementById('submitReviewBtn');
        const submitBtnText = document.getElementById('submitBtnText');
        const submitBtnSpinner = document.getElementById('submitBtnSpinner');

        submitBtn.disabled = true;
        submitBtnText.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Publishing...';
        submitBtnSpinner.classList.remove('d-none');

        const formData = new FormData(document.getElementById('contactReviewForm'));

        try {
            const response = await fetch('{{ route("frontend.reviews.store") }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await response.json();

            if (data.success) {
                toastr.success(data.message, 'Success');
                document.getElementById('contactReviewForm').reset();
                document.getElementById('memberVerificationForm').reset();
                document.querySelectorAll('.cp-rating-option').forEach((opt) => opt.classList.remove('checked'));

                setTimeout(() => {
                    window.location.href = '{{ route("frontend.reviews.index") }}';
                }, 2000);
            } else {
                toastr.error(data.message || 'Failed to submit review', 'Error');
            }
        } catch (error) {
            console.error('Fetch error:', error);
            toastr.error('An error occurred. Please try again.', 'Error');
        } finally {
            submitBtn.disabled = false;
            submitBtnText.innerHTML = '<i class="bi bi-send me-2"></i>Publish Review';
            submitBtnSpinner.classList.add('d-none');
        }
    });

    const ratingOptions = document.querySelectorAll('.cp-rating-option');
    ratingOptions.forEach((option, index) => {
        option.addEventListener('click', () => {
            ratingOptions.forEach((opt, optIndex) => {
                if (optIndex <= index) {
                    opt.classList.add('checked');
                } else {
                    opt.classList.remove('checked');
                }
            });
        });
    });
</script>
@endsection
