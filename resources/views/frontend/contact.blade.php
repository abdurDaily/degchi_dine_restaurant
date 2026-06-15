@extends('frontend.layout')

@section('frontend_content')
<style>
    /* Sticky Contact Form - using native CSS sticky */
    .premium-form-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @media (min-width: 992px) {
        .contact-form-sticky {
            position: sticky;
            top: 100px;
            z-index: 50;
        }
    }
</style>
<main class="main-content">
    <section class="contact-section py-5">
        <div class="container px-4 px-lg-5">
            <div class="row align-items-start g-5">
                <div class="col-lg-5 d-flex flex-column justify-content-between">
                    <div class="contact-info-wrapper">
                        <span class="text-uppercase tracking-wider small fw-bold" style="color: #f27a21; letter-spacing: 0.15em;">Connect With Us</span>
                        <h2 class="mb-3 fw-black contact-title">Get In Touch</h2>
                        <div class="title-divider mb-5"></div>
                        
                        <div class="contact-card-list">
                            <div class="premium-contact-item mb-4">
                                <div class="contact-icon-box">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-uppercase small text-muted">Address</h6>
                                    <p class="mb-0 info-text">
                                        Boropool Circle, Kaptan Villa,<br>
                                        Halishahar, Chittagong, Bangladesh
                                    </p>
                                </div>
                            </div>

                            <div class="premium-contact-item mb-4">
                                <div class="contact-icon-box">
                                    <i class="bi bi-telephone"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-uppercase small text-muted">Phone</h6>
                                    <p class="mb-0 info-text">
                                        <a href="tel:01898795400" class="contact-link">01898-795400</a>
                                    </p>
                                </div>
                            </div>

                            <div class="premium-contact-item mb-4">
                                <div class="contact-icon-box">
                                    <i class="bi bi-envelope"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-uppercase small text-muted">Email</h6>
                                    <p class="mb-0 info-text">
                                        <a href="mailto:degchidine@gmail.com" class="contact-link">degchidine@gmail.com</a>
                                    </p>
                                </div>
                            </div>

                            <div class="premium-contact-item">
                                <div class="contact-icon-box">
                                    <i class="bi bi-clock"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-uppercase small text-muted">Business Hours</h6>
                                    <p class="mb-0 info-text">
                                        Monday - Sunday<br>
                                        <span class="highlight-time">5:00 PM - 11:30 PM</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="contact-social mt-5">
                        <h6 class="fw-bold mb-3 text-uppercase small text-muted">Follow Our Journey</h6>
                        <div class="d-flex gap-3">
                            <a href="https://www.facebook.com/DegchiDine" target="_blank" class="social-link">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="#" target="_blank" class="social-link">
                                <i class="bi bi-instagram"></i>
                            </a>
                            <a href="#" target="_blank" class="social-link">
                                <i class="bi bi-twitter-x"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="contact-form-sticky">
                    <div id="memberCheckCard" class="premium-form-card p-5 rounded-4 position-relative overflow-hidden">
                        <div class="card-glow-bg"></div>
                        <div class="position-rlative z-1">
                            <!-- <div class="d-flex align-items-center gap-2 mb-3">
                                <div class="badge-vip">VIP ACCESS</div>
                            </div> -->
                            <h3 class="mb-2 fw-bold premium-card-title text-dark">
                                Share Your Experience
                            </h3>
                            <p class="premium-subtitle mb-4">Exclusive for our valued members. Please verify your membership card to post your review.</p>
                            
                            <form id="memberVerificationForm" class="member-check-form">
                                @csrf
                                
                                <div class="mb-4 focused-field-container">
                                    <!-- <label for="cardNumberCheck" class="form-label premium-label text-white">
                                        <i class="bi bi-credit-card-2-front label-icon-gold"></i>
                                        Membership Card Number <span class="accent-dot">*</span>
                                    </label> -->
                                    <div class="input-glow-wrapper">
                                        <input type="text" class="form-control premium-input highlighted-input" id="cardNumberCheck" name="card_number" required placeholder="Ex: DD-XXXX-XXXX">
                                    </div>
                                    <!-- <small class="text-white-50 d-block ">
                                        <i class="bi bi-info-circle-fill me-1 text-gold"></i>
                                        Your unique ID found on the face of your physical card.
                                    </small> -->
                                </div>

                                <div class="mb-4">
                                    <button type="submit" class="btn btn-premium-action w-100 fw-bold py-3" id="verifyBtn">
                                        <span id="verifyBtnText">Verify Membership Identity</span>
                                        <span id="verifyBtnSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                                    </button>
                                </div>

                                <div class="text-center">
                                    <p class="small mb-0 text-dark">
                                        Not a member yet? 
                                        <a href="{{ route('frontend.card.apply') }}" class="register-gold-link">
                                            Apply for Membership <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div id="reviewFormCard" class="premium-form-card premium-form-card--light p-5 rounded-4 d-none">
                        <div class="alert alert-premium-success mb-4 d-flex align-items-center" role="alert">
                            <i class="bi bi-shield-check me-3 success-icon"></i>
                            <div>
                                <strong class="d-block">Access Granted</strong> 
                                <span class="small">Membership verified successfully. We look forward to your valuable insights.</span>
                            </div>
                        </div>

                        <form id="contactReviewForm" class="review-form">
                            @csrf
                            <input type="hidden" name="member_card_number" id="hiddenCardNumber">
                            
                            <div class="mb-4">
                                <label class="form-label premium-label-dark">
                                    <i class="bi bi-star-fill label-icon-orange"></i>
                                    Your Rating *
                                </label>
                                <div class="rating-selector d-flex gap-3 mt-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                    <label class="rating-option">
                                        <input type="radio" name="rating" value="{{ $i }}" required>
                                        <i class="bi bi-star"></i>
                                    </label>
                                    @endfor
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="title" class="form-label premium-label-dark">
                                    <i class="bi bi-bookmark-star label-icon-orange"></i>
                                    Review Title (Optional)
                                </label>
                                <input type="text" class="form-control premium-input-light" id="title" name="title" placeholder="e.g., An Absolute Culinary Delight">
                            </div>

                            <div class="mb-4">
                                <label for="comment" class="form-label premium-label-dark">
                                    <i class="bi bi-chat-square-quote label-icon-orange"></i>
                                    Your Review *
                                </label>
                                <textarea class="form-control premium-input-light" id="comment" name="comment" rows="5" required placeholder="Share details of your experience with our dishes and ambiance..."></textarea>
                                <small class="text-muted d-block mt-2">Minimum 10 characters required.</small>
                            </div>

                            <div class="d-flex gap-3 pt-2">
                                <button type="submit" class="btn btn-premium-orange flex-grow-1 fw-bold py-3" id="submitReviewBtn">
                                    <span id="submitBtnText">
                                        <i class="bi bi-send me-2"></i>Publish Review
                                    </span>
                                    <span id="submitBtnSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                                </button>
                                <button type="button" class="btn btn-premium-light-outline fw-bold py-3 px-4" id="backBtn" onclick="goBackToVerification()">
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
</main>

<style>
    /* Global Base */
    .contact-section {
        background: #fdfdfd;
        padding: 5rem 0;
    }

    .contact-title {
        color: #2b0e11;
        font-size: 2.75rem;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .title-divider {
        width: 50px;
        height: 5px;
        background: #f27a21;
        border-radius: 10px;
    }

    /* Premium Typography */
    .info-text {
        color: #4a5568;
        font-size: 1.05rem;
        line-height: 1.6;
        font-weight: 400;
    }

    .highlight-time {
        color: #2b0e11;
        font-weight: 600;
    }

    /* Elegant Interactive Contact Items */
    .premium-contact-item {
        display: flex;
        gap: 1.5rem;
        align-items: center;
        padding: 1rem;
        border-radius: 0.75rem;
        transition: background 0.2s;
    }
    
    .premium-contact-item:hover {
        background: #f7f7f7;
    }

    .contact-icon-box {
        flex-shrink: 0;
        width: 46px;
        height: 46px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #f27a21;
        font-size: 1.2rem;
    }

    .contact-link {
        color: inherit;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
    }

    .contact-link:hover {
        color: #f27a21;
    }

    /* Premium Social Circles */
    .contact-social .social-link {
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        color: #4a5568;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .contact-social .social-link:hover {
        background: #2b0e11 !important;
        color: #fff !important;
        border-color: #2b0e11;
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(43, 14, 17, 0.15);
    }

    /* ==========================================================================
       THE PREMIUM MEMBERSHIP CARD BLOCK (Dark, Luxe Theme)
       ========================================================================== */
    .premium-form-card {
        /* background: linear-gradient(145deg, #2b0e11 0%, #170708 100%) !important; */
        box-shadow: 0 20px 40px rgba(235, 138, 11, 0.06);
        /* border: 1px solid #3d171b; */
        color: #fff;
    }

    .card-glow-bg {
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(242, 122, 33, 0.12) 0%, transparent 60%);
        pointer-events: none;
    }

    .badge-vip {
        background: rgba(242, 122, 33, 0.15);
        border: 1px solid rgba(242, 122, 33, 0.4);
        color: #f27a21;
        padding: 0.25rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        border-radius: 4px;
        display: inline-block;
    }

    .premium-subtitle {
        color: #a0aec0;
        font-size: 0.95rem;
    }

    /* CRITICAL ASSIGNMENT: Membership Card Field Intense Highlight UI */
    .focused-field-container {
        background: rgba(252, 122, 33, 0.04);
        padding: 1.5rem;
        border-radius: 1rem;
        border: 1px dashed rgba(242, 122, 33, 0.25);
    }

    .label-icon-gold {
        color: #f27a21;
        margin-right: 0.5rem;
        font-size: 1.15rem;
    }

    .accent-dot {
        color: #f27a21;
    }

    /* Glowing Highlight wrapper configuration */
    .input-glow-wrapper {
        position: relative;
    }

    .premium-input.highlighted-input {
        /* background-color: #1f0b0d !important; */
        border: 2px solid #f27a21 !important; /* Prominent signature color border */
        /* color: #ffffff !important; */
        font-size: 1.1rem;
        letter-spacing: 0.05em;
        font-weight: 600;
        padding: 1rem 1.25rem;
        box-shadow: 0 0 15px rgba(242, 122, 33, 0.25), inset 0 1px 3px rgba(0,0,0,0.5);
        animation: subtlePulse 3s infinite alternate;
    }

    .premium-input.highlighted-input:focus {
        /* background-color: #2b0e11 !important; */
        border-color: #ff9d54 !important;
        box-shadow: 0 0 25px rgba(242, 122, 33, 0.45), inset 0 1px 2px rgba(0,0,0,0.5);
        outline: none;
    }

    @keyframes subtlePulse {
        0% { box-shadow: 0 0 12px rgba(242, 122, 33, 0.2); }
        100% { box-shadow: 0 0 22px rgba(242, 122, 33, 0.35); }
    }

    /* Action Buttons Architecture */
    .btn-premium-action {
        background: linear-gradient(90deg, #f27a21 0%, #ff923c 100%);
        border: none;
        color: #fff;
        border-radius: 0.75rem;
        font-size: 1.05rem;
        letter-spacing: 0.02em;
        box-shadow: 0 6px 20px rgba(242, 122, 33, 0.3);
        transition: all 0.3s ease;
    }

    .btn-premium-action:hover:not(:disabled) {
        background: linear-gradient(90deg, #e06914 0%, #f27a21 100%);
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(242, 122, 33, 0.45);
        color: #fff;
    }

    .register-gold-link {
        color: #f27a21;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s;
    }

    .register-gold-link:hover {
        color: #ff923c;
        text-shadow: 0 0 8px rgba(242, 122, 33, 0.3);
    }

    /* ==========================================================================
       REVIEW WRITING CARD STATE (Premium Light Theme)
       ========================================================================== */
    .premium-form-card--light {
        background: #ffffff !important;
        border: 1px solid #eef0f3;
        box-shadow: 0 20px 45px rgba(0,0,0,0.05);
        color: #2d3748;
    }

    .alert-premium-success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
        border-radius: 0.75rem;
        padding: 1.25rem;
    }

    .success-icon {
        font-size: 1.75rem;
        color: #15803d;
    }

    .premium-label-dark {
        color: #2b0e11;
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
    }

    .label-icon-orange {
        color: #f27a21;
        margin-right: 0.35rem;
    }

    .premium-input-light {
        border: 1px solid #cbd5e1;
        border-radius: 0.75rem;
        padding: 0.85rem 1.25rem;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        background-color: #f8fafc;
    }

    .premium-input-light:focus {
        background-color: #fff;
        border-color: #f27a21;
        box-shadow: 0 0 0 4px rgba(242, 122, 33, 0.1);
    }

    .btn-premium-orange {
        background: #2b0e11;
        border: 1px solid #2b0e11;
        color: #fff;
        border-radius: 0.75rem;
        transition: all 0.2s;
    }

    .btn-premium-orange:hover {
        background: #f27a21;
        border-color: #f27a21;
        color: #fff;
        box-shadow: 0 6px 18px rgba(242, 122, 33, 0.25);
    }

    .btn-premium-light-outline {
        background: transparent;
        border: 1px solid #cbd5e1;
        color: #64748b;
        border-radius: 0.75rem;
        transition: all 0.2s;
    }

    .btn-premium-light-outline:hover {
        background: #f1f5f9;
        color: #1e293b;
        border-color: #94a3b8;
    }

    /* Rating Stars interactive UI mechanics */
    .rating-selector .rating-option {
        font-size: 2rem;
        cursor: pointer;
        color: #e2e8f0;
        transition: transform 0.2s;
    }

    .rating-selector .rating-option:hover {
        transform: scale(1.2);
    }

    .rating-option input {
        display: none;
    }

    .rating-option.checked .bi-star::before,
    .rating-option:hover .bi-star::before {
        content: "\f586"; /* Custom star-fill symbol conversion */
        color: #f27a21;
    }

    /* Sticky form column on desktop */
    @media (min-width: 992px) {
        .contact-form-sticky {
            position: sticky;
            top: 100px;
            align-self: start;
        }
    }

    /* Responsive Configurations */
    @media (max-width: 991px) {
        .contact-section { padding: 3.5rem 0; }
        .premium-form-card { padding: 2rem !important; }
    }
</style>

<script>
    // System core script mechanisms remain entirely intact
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
                document.getElementById('reviewFormCard').scrollIntoView({ behavior: 'smooth' });
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
        document.querySelector('html').scrollTop = 0;
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

    const ratingOptions = document.querySelectorAll('.rating-option');
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