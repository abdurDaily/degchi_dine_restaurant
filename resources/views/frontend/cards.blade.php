@extends('frontend.layout')

@section('meta_title', 'Membership Cards')
@section('meta_description', 'Explore Degchi Dine Golden Card and Membership Card benefits. Apply for exclusive dining
    privileges and rewards.')

@section('frontend_content')

    <main class="luxury-portal-container">
        <section class="premium-identity-showcase py-5 mt-5">
            <div class="container px-4 px-lg-5">

                <!-- Title Panel -->
                <div class="text-center my-5 reveal visible">
                    <span class="luxury-meta-label mb-2">ডেগচি রিওয়ার্ডস</span>
                    <h2 class="luxury-title">আপনার ডাইনিং অভিজ্ঞতাকে আরও বিশেষ করে তুলুন</h2>
                    <div class="luxury-accent-line mx-auto"></div>
                </div>

                <!-- Symmetrical Two-Column Card Grid -->
                <div class="row g-5 align-items-stretch justify-content-center">

                    <!-- Card Option 2: Membership Pass -->
                    <div class="col-12 col-md-6 col-lg-5">
                        <a href="{{ route('frontend.card.apply') }}" class="luxury-card-anchor"
                            aria-label="মেম্বারশিপ কার্ড দেখুন">
                            <div class="luxury-interactive-card h-100">

                                <!-- Clean Image Wrapper -->
                                <div class="card-image-wrapper">
                                    <img src="{{ asset('assets/frontend/images/membership.svg') }}"
                                        alt="ডেগচি মেম্বারশিপ কার্ড" class="card-img-fit" />
                                </div>

                                <!-- Text Details -->
                                <div class="card-body-details p-4 p-lg-5">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h3 class="card-main-title">মেম্বারশিপ কার্ড</h3>
                                        <div class="luxury-action-arrow">
                                            <iconify-icon icon="solar:alt-arrow-right-linear"></iconify-icon>
                                        </div>
                                    </div>
                                    <p class="card-summary-text">
                                        বিশেষ সদস্য সুবিধা, অগ্রাধিকার ভিত্তিতে টেবিল রিজার্ভেশন এবং
                                        বিভিন্ন বিশেষ রিওয়ার্ড ও সুবিধা উপভোগ করুন।
                                    </p>
                                </div>

                            </div>
                        </a>
                    </div>

                    <!-- Card Option 1: Golden Card (Privilege Pass) -->
                    <div class="col-12 col-md-6 col-lg-5">
                        <a href="#" class="luxury-card-anchor" data-bs-toggle="modal"
                            data-bs-target="#goldenCardModal" aria-label="গোল্ডেন কার্ডের জন্য আবেদন করুন">
                            <div class="luxury-interactive-card h-100">

                                <!-- Clean Image Wrapper -->
                                <div class="card-image-wrapper">
                                    <img src="{{ asset('assets/frontend/images/privilege_card.svg') }}"
                                        alt="ডেগচি গোল্ডেন কার্ড" class="card-img-fit" />
                                </div>

                                <!-- Text Details -->
                                <div class="card-body-details p-4 p-lg-5">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h3 class="card-main-title">গোল্ডেন কার্ড</h3>
                                        <div class="luxury-action-arrow">
                                            <iconify-icon icon="solar:alt-arrow-right-linear"></iconify-icon>
                                        </div>
                                    </div>
                                    <p class="card-summary-text">
                                        অগ্রাধিকার ভিত্তিতে টেবিল রিজার্ভেশন, বিশেষ রিওয়ার্ড এবং
                                        ডেগচি ডাইনে প্রতিবার ডাইনিংয়ে উপভোগ করুন অতিরিক্ত বিশেষ সুবিধা।
                                    </p>
                                </div>

                            </div>
                        </a>
                    </div>

                </div>
            </div>
        </section>
    </main>

    <!-- Golden Card Terms & Upgrade Modal -->
    <div class="modal fade golden-card-modal" id="goldenCardModal" tabindex="-1" aria-labelledby="goldenCardModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-dark"
                style="border-radius: 1.5rem; border: 1px solid rgba(223, 166, 83, 0.2); background: #ffffff; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title fw-bold text-uppercase" id="goldenCardModalLabel"
                        style="letter-spacing: 1px; color: #1f1412; font-size: 1.25rem;">
                        গোল্ডেন কার্ড আপগ্রেড
                    </h4>
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="বন্ধ করুন">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body" style="padding: 1.5rem;">
                    <div class="p-3 mb-4 rounded-3"
                        style="background: #faf8f5; border: 1px dashed rgba(223, 166, 83, 0.3);">
                        <h5 class="fw-bold mb-2 text-dark" style="font-size: 0.95rem;"><i
                                class="bi bi-info-circle-fill text-warning me-2"></i>যোগ্যতা ও শর্তাবলি</h5>
                        <ul class="mb-0 text-muted ps-3" style="font-size: 0.85rem; line-height: 1.6;">
                            <li>আপনার একটি সক্রিয় সাধারণ মেম্বারশিপ কার্ড থাকতে হবে।</li>
                            <li>আপনার মোট ক্রয়ের পরিমাণ <strong>৳২,০০০.০০</strong> বা তার বেশি হতে হবে।</li>
                            <li>আপগ্রেডের তারিখ থেকে গোল্ডেন কার্ডের মেয়াদ <strong>৫ বছর</strong>।</li>
                            <li>গোল্ডেন কার্ডধারীরা সব খাবারের ওপর <strong>ফ্ল্যাট ১০% ছাড়</strong> পাবেন।</li>
                        </ul>
                    </div>

                    <form id="goldenCardApplyForm" action="{{ route('frontend.golden.card.apply') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="modal_card_number" class="form-label fw-bold text-dark mb-2"
                                style="font-size: 0.85rem;">আপনার মেম্বারশিপ কার্ড নম্বর</label>
                            <input type="text" class="form-control py-3" name="unique_card_number" id="modal_card_number"
                                placeholder="যেমন: MEM0001_1234" required
                                style="border-radius: 0.75rem; border: 1px solid #e6dfd7; background: #faf8f5; font-size: 0.95rem; padding-left: 1rem;">
                        </div>

                        <div id="goldenFeedback" class="alert d-none py-2 mt-3"
                            style="font-size: 0.85rem; border-radius: 0.75rem;"></div>

                        <div class="d-grid mt-4">
                            <button type="submit" id="goldenUpgradeBtn" class="btn py-3 fw-bold text-white"
                                style="background: #1f1412; border-radius: 0.75rem; transition: all 0.3s ease; border: none; font-size: 0.95rem;">
                                কার্ড যাচাই ও আপগ্রেড করুন
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('front_js')
        <script>
            $(function() {
                $('#goldenCardApplyForm').on('submit', function(e) {
                    e.preventDefault();
                    var form = $(this);
                    var submitBtn = $('#goldenUpgradeBtn');
                    var feedback = $('#goldenFeedback');

                    submitBtn.prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm me-2"></span>প্রক্রিয়াধীন...');
                    feedback.addClass('d-none').removeClass('alert-success alert-danger');

                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: form.serialize(),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            submitBtn.prop('disabled', false).text('কার্ড যাচাই ও আপগ্রেড করুন');
                            if (res.success) {
                                feedback.removeClass('d-none').addClass('alert-success').text(res
                                    .message);
                                form[0].reset();
                                if (typeof toastr !== 'undefined') {
                                    toastr.success(res.message);
                                }
                            } else {
                                feedback.removeClass('d-none').addClass('alert-danger').text(res
                                    .message);
                            }
                        },
                        error: function(xhr) {
                            submitBtn.prop('disabled', false).text('কার্ড যাচাই ও আপগ্রেড করুন');
                            var msg = xhr.responseJSON?.message ||
                                'যাচাই ব্যর্থ হয়েছে। অনুগ্রহ করে আপনার কার্ড নম্বরটি সঠিকভাবে দিন।';
                            feedback.removeClass('d-none').addClass('alert-danger').text(msg);
                            if (typeof toastr !== 'undefined') {
                                toastr.error(msg);
                            }
                        }
                    });
                });
            });
        </script>
    @endpush

@endsection
