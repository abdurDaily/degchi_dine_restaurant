@extends('frontend.layout')

@section('meta_title', 'Apply for Membership')
@section('meta_description',
    'Apply for a Degchi Dine membership card and unlock exclusive dining benefits, rewards and
    priority service.')

    @push('front_css')
        <link rel="stylesheet"
            href="{{ asset('assets/frontend/css/member-auth.css') }}?v={{ filemtime(public_path('assets/frontend/css/member-auth.css')) }}"
            media="print" onload="this.media='all'" />
    @endpush

@section('frontend_content')
    <x-frontend.page-header title="আপনার মেম্বারশিপের জন্য আবেদন করুন" eyebrow="বিশেষ সদস্য সুবিধা"
        subtitle="আমাদের বিশেষ সদস্যদের সঙ্গে যুক্ত হয়ে উপভোগ করুন আরও বিশেষ ডাইনিং অভিজ্ঞতা।" :backLink="route('frontend.cards')"
        backText="মেম্বারশিপ কার্ডে ফিরে যান">
        <x-slot:eyebrowIcon>
            <iconify-icon icon="solar:crown-star-bold"></iconify-icon>
        </x-slot:eyebrowIcon>
    </x-frontend.page-header>

    <section class="contact-page member-apply" style="background: var(--brand-gredient); min-height: 60vh;">
        <div class="container px-4 px-lg-5 contact-page-main-box">
            <div class="contact-page-grid">
                {{-- left side --}}
                <aside class="contact-page-info order-2 order-lg-1">
                    <div class="contact-page-info-panel">
                        <div class="contact-page-info-top">
                            <div>
                                <h2 class="contact-page-info-title">ডেগচি ডাইন</h2>
                                <p class="contact-page-info-tagline">আন্তরিক আতিথেয়তা · খাঁটি স্বাদ</p>
                            </div>
                            <span class="contact-page-open-badge"><i class="bi bi-star-fill me-1"></i> প্রিমিয়াম</span>
                        </div>

                        <div class="dd-apply-stage-wrap">
                            <div class="dd-apply-card-stage">
                                <div class="dd-apply-glow"></div>
                                <img src="{{ asset('assets/frontend/images/membership.svg') }}" alt="ডেগচি প্রিমিয়াম কার্ড"
                                    class="dd-apply-card-img" />
                            </div>
                        </div>

                        <div style="margin-bottom: 1.25rem;">
                            <h3 style="font-size: 0.95rem; font-weight: 700; color: #fff; margin-bottom: 1rem;">মেম্বারশিপের
                                সুবিধা</h3>

                            <div class="dd-perk-row">
                                <div class="dd-perk-icon"><iconify-icon icon="solar:verified-check-linear"></iconify-icon>
                                </div>
                                <div class="dd-perk-text">
                                    <strong>অগ্রাধিকার ভিত্তিতে রিজার্ভেশন</strong>
                                    <span>২৪/৭ বিশেষ বুকিং সুবিধায় অপেক্ষার তালিকা এড়িয়ে চলুন।</span>
                                </div>
                            </div>

                            <div class="dd-perk-row">
                                <div class="dd-perk-icon"><iconify-icon icon="solar:wad-of-money-linear"></iconify-icon>
                                </div>
                                <div class="dd-perk-text">
                                    <strong>বিশেষ মূল্য সুবিধা</strong>
                                    <span>আপনার ডাইনিং বিল থেকে প্রযোজ্য ছাড় স্বয়ংক্রিয়ভাবে সমন্বয় করা হবে।</span>
                                </div>
                            </div>

                            <div class="dd-perk-row">
                                <div class="dd-perk-icon"><iconify-icon icon="solar:gift-linear"></iconify-icon></div>
                                <div class="dd-perk-text">
                                    <strong>বিশেষ উপহার ও সারপ্রাইজ</strong>
                                    <span>আপনার বিশেষ দিনগুলোতে উপভোগ করুন শেফের পক্ষ থেকে বিশেষ আপ্যায়ন।</span>
                                </div>
                            </div>
                        </div>

                        <div class="contact-page-actions">
                            <a href="{{ route('frontend.contact') }}" class="btn-dine btn-dine-ghost btn-dine-sm">
                                <iconify-icon icon="solar:chat-round-dots-bold"></iconify-icon>
                                যোগাযোগ করুন
                            </a>
                        </div>
                    </div>
                </aside>

                {{-- right side --}}
                <div class="contact-page-forms order-1 order-lg-2">
                    <div class="contact-form-sticky">
                        <div class="cp-form-card cp-form-card-verify">
                            <div class="cp-form-card-head">
                                <span class="cp-form-step">আবেদন ফর্ম</span>
                                <h3>মেম্বারশিপের জন্য আবেদন করুন</h3>
                                <p>নিচে আপনার তথ্যগুলো পূরণ করুন। সাধারণত এক কর্মদিবসের মধ্যে আবেদন যাচাই ও অনুমোদন করা হয়।
                                </p>
                            </div>

                            <form id="privilegeCardForm" method="POST" action="{{ route('frontend.members.register') }}"
                                enctype="multipart/form-data">
                                @csrf

                                {{-- Account Details --}}
                                <div style="margin-bottom: 1.35rem;">
                                    <h3 class="cp-section-title">
                                        <iconify-icon icon="solar:user-circle-linear"></iconify-icon>
                                        অ্যাকাউন্টের তথ্য
                                    </h3>

                                    <div class="cp-field mb-3">
                                        <label for="dd_name" class="cp-label">পূর্ণ নাম</label>
                                        <input type="text" name="name" id="dd_name" class="cp-input"
                                            placeholder="যেমন: রহিম উদ্দিন" value="{{ old('name') }}" required
                                            autocomplete="name">
                                    </div>

                                    <div class="cp-grid-2">
                                        <div class="cp-field">
                                            <label for="dd_phone" class="cp-label">ফোন নম্বর</label>
                                            <input type="tel" name="phone" id="dd_phone" class="cp-input"
                                                placeholder="যেমন: ০১৭১২৩৪৫৬৭৮" value="{{ old('phone') }}" required
                                                autocomplete="tel">
                                        </div>
                                        <div class="cp-field">
                                            <label for="dd_email" class="cp-label">ইমেইল ঠিকানা</label>
                                            <input type="email" name="email" id="dd_email" class="cp-input"
                                                placeholder="যেমন: you@email.com" value="{{ old('email') }}" required
                                                autocomplete="email">
                                        </div>
                                    </div>

                                    <div id="dd_phone_feedback" class="dd-phone-feedback d-none" role="status"></div>

                                    <p class="cp-field-hint">
                                        পাসওয়ার্ড পুনরুদ্ধারের জন্য ইমেইল প্রয়োজন। আপনি
                                        <a href="{{ route('frontend.member.login') }}">মেম্বার লগইন</a>
                                        থেকে ইমেইল দিয়েও লগইন করতে পারবেন।
                                    </p>

                                    <div class="cp-grid-2">
                                        <div class="cp-field">
                                            <label for="dd_password" class="cp-label">পাসওয়ার্ড তৈরি করুন</label>
                                            <input type="password" name="password" id="dd_password" class="cp-input"
                                                placeholder="কমপক্ষে ৮ অক্ষর" required minlength="8"
                                                autocomplete="new-password">
                                        </div>
                                        <div class="cp-field">
                                            <label for="dd_password_confirm" class="cp-label">পাসওয়ার্ড নিশ্চিত
                                                করুন</label>
                                            <input type="password" name="password_confirmation" id="dd_password_confirm"
                                                class="cp-input" placeholder="আবার পাসওয়ার্ড দিন" required minlength="8"
                                                autocomplete="new-password">
                                        </div>
                                    </div>

                                    <p class="cp-field-hint">
                                        কমপক্ষে ৮ অক্ষর হতে হবে। পরবর্তীতে আপনার
                                        <strong style="color: #fff;">ফোন নম্বর</strong>,
                                        <strong style="color: #fff;">ইমেইল</strong> অথবা
                                        <strong style="color: #fff;">কার্ড নম্বর</strong> এবং এই পাসওয়ার্ড দিয়ে লগইন করতে
                                        পারবেন।
                                    </p>
                                </div>

                                {{-- Personal Details --}}
                                <div style="margin-bottom: 1.35rem;">
                                    <h3 class="cp-section-title">
                                        <iconify-icon icon="solar:calendar-linear"></iconify-icon>
                                        ব্যক্তিগত তথ্য
                                    </h3>

                                    <div class="cp-grid-2">
                                        <div class="cp-field">
                                            <label for="dd_dob" class="cp-label">জন্মতারিখ</label>
                                            <input type="date" name="dob" id="dd_dob" class="cp-input"
                                                value="{{ old('dob') }}" required>
                                        </div>
                                        <div class="cp-field">
                                            <label for="dd_marriage" class="cp-label">বিবাহের তারিখ (ঐচ্ছিক)</label>
                                            <input type="date" name="marriage_date" id="dd_marriage" class="cp-input"
                                                value="{{ old('marriage_date') }}">
                                        </div>
                                    </div>

                                    <div class="cp-field mt-3">
                                        <label for="dd_address" class="cp-label">ঠিকানা</label>
                                        <textarea name="address" id="dd_address" class="cp-input cp-textarea" rows="2"
                                            placeholder="আপনার সম্পূর্ণ ঠিকানা" required>{{ old('address') }}</textarea>
                                    </div>
                                </div>

                                {{-- Profile Photo --}}
                                <div style="margin-bottom: 1.35rem;">
                                    <h3 class="cp-section-title">
                                        <iconify-icon icon="solar:camera-linear"></iconify-icon>
                                        প্রোফাইল ছবি
                                    </h3>

                                    <div class="cp-dropzone" id="profile_image_dropzone">
                                        <input type="file" name="profile_image" id="dd_profile_image"
                                            class="cp-dropzone-input" accept="image/webp,image/png,image/jpeg">
                                        <iconify-icon icon="solar:gallery-add-linear"
                                            class="cp-dropzone-icon"></iconify-icon>
                                        <p class="cp-dropzone-title">এখানে ছবি রাখুন অথবা <span>ব্রাউজ করুন</span></p>
                                        <p class="cp-dropzone-sub">সমর্থিত ফরম্যাট: JPG, PNG, WebP (ঐচ্ছিক)</p>
                                        <p class="cp-dropzone-name d-none" id="profile_image_file_name"></p>
                                    </div>

                                    <div class="d-none cp-preview" id="profile_image_preview_wrap">
                                        <img id="profile_image_preview" src="" alt="প্রোফাইল ছবির প্রিভিউ" />
                                        <div class="cp-preview-caption">আপনার প্রোফাইল ছবির প্রিভিউ</div>
                                    </div>
                                </div>

                                {{-- Student Status --}}
                                <div style="margin-bottom: 1.35rem;">
                                    <h3 class="cp-section-title">
                                        <iconify-icon icon="solar:square-academic-cap-linear"></iconify-icon>
                                        শিক্ষার্থী তথ্য
                                    </h3>

                                    <label class="cp-student-toggle" for="dd_is_student">
                                        <input type="checkbox" name="is_student" id="dd_is_student" value="1"
                                            class="form-check-input" @checked(old('is_student'))>
                                        <span>আমি একজন শিক্ষার্থী</span>
                                    </label>

                                    <div id="student_extra_fields" class="{{ old('is_student') ? '' : 'd-none' }}">
                                        <div id="student_discount_info" class="cp-student-info">
                                            <div class="cp-student-info-inner">
                                                <iconify-icon icon="solar:graduation-cap-bold"></iconify-icon>
                                                <div>
                                                    <strong>শিক্ষার্থী সুবিধা — প্রথম অর্ডারে ৩৫% ছাড়!</strong>
                                                    <p>
                                                        শিক্ষার্থীরা প্রথম অর্ডারে <strong>৩৫%</strong> ছাড় পাবেন
                                                        (অন্যান্য সদস্যদের জন্য <strong>৩০%</strong>)।
                                                        যাচাইয়ের জন্য আপনার স্টুডেন্ট আইডি আপলোড করুন।
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="cp-dropzone" id="student_card_dropzone">
                                            <input type="file" name="student_card" id="dd_student_card"
                                                class="cp-dropzone-input"
                                                accept="image/png,image/jpeg,image/jpg,application/pdf">
                                            <iconify-icon icon="solar:document-add-linear"
                                                class="cp-dropzone-icon"></iconify-icon>
                                            <p class="cp-dropzone-title">এখানে স্টুডেন্ট আইডি রাখুন অথবা <span>ব্রাউজ
                                                    করুন</span></p>
                                            <p class="cp-dropzone-sub">সমর্থিত ফরম্যাট: JPG, PNG, PDF</p>
                                            <p class="cp-dropzone-name d-none" id="student_card_file_name"></p>
                                        </div>

                                        <div class="d-none mb-3" id="student_card_preview_wrap">
                                            <div id="student_card_img_preview" class="d-none cp-preview">
                                                <img id="student_card_preview" src=""
                                                    alt="স্টুডেন্ট কার্ডের প্রিভিউ" />
                                            </div>
                                            <div id="student_card_pdf_indicator" class="d-none"
                                                style="text-align:center; padding:18px; border:1.5px dashed var(--secondary-30); border-radius:14px; background:var(--secondary-10);">
                                                <iconify-icon icon="solar:document-bold"
                                                    style="font-size:2.5rem; color:var(--brand-secondary);"></iconify-icon>
                                                <div style="margin-top:6px; font-size:.85rem; font-weight:600; color:#fff;"
                                                    id="student_card_pdf_name"></div>
                                                <div
                                                    style="font-size:.75rem; color:var(--text-on-dark-muted); margin-top:2px;">
                                                    PDF আপলোডের জন্য প্রস্তুত</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Terms --}}
                                <div class="dd-terms-section" id="ddTermsSection">
                                    <div class="dd-terms-required-note">
                                        <iconify-icon icon="solar:info-circle-linear"></iconify-icon>
                                        <span>আবশ্যক — আবেদন জমা দেওয়ার আগে সম্মতি জানাতে নিচের বক্সে টিক দিন</span>
                                    </div>

                                    <label class="dd-terms-wrapper" id="ddTermsLabel" for="dd_terms">
                                        <input type="checkbox" id="dd_terms" name="terms" value="1"
                                            class="dd-hidden-check">
                                        <div class="dd-visible-check" aria-hidden="true">
                                            <iconify-icon icon="solar:check-read-linear"></iconify-icon>
                                        </div>
                                        <span class="dd-terms-text">
                                            <strong>আমি {{ config('app.name') }} রিওয়ার্ডস প্রোগ্রামের শর্তাবলিতে
                                                সম্মত।</strong>
                                            এই আবেদনে দেওয়া সব তথ্য সঠিক ও সম্পূর্ণ বলে আমি নিশ্চিত করছি।
                                        </span>
                                    </label>

                                    <div class="dd-terms-error d-none" id="ddTermsError">
                                        <iconify-icon icon="solar:danger-circle-linear"></iconify-icon>
                                        শর্তাবলিতে সম্মতি জানাতে উপরের বক্সে টিক দিন।
                                    </div>
                                </div>

                                <button type="submit" id="privilegeSubmitBtn"
                                    class="btn-dine btn-dine-primary btn-dine-sm w-100">
                                    <span class="dd-submit-text">আবেদন জমা দিন</span>
                                    <iconify-icon icon="solar:arrow-right-linear" class="dd-submit-icon"></iconify-icon>
                                </button>
                            </form>

                            <p class="cp-form-footnote text-center mt-4">
                                ইতোমধ্যে মেম্বার হয়েছেন?
                                <a href="{{ route('frontend.member.login') }}">
                                    মেম্বার লগইনে প্রবেশ করুন <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div id="applyProcessingOverlay" class="dd-apply-processing d-none" aria-live="polite" aria-busy="true">
        <div class="dd-apply-processing-inner">
            <div class="dd-apply-spinner" role="presentation"></div>
            <strong>আপনার আবেদন প্রক্রিয়াধীন</strong>
            <p>অনুগ্রহ করে অপেক্ষা করুন — আমরা আপনার মেম্বারশিপ তৈরি করে নিশ্চিতকরণ বার্তা পাঠাচ্ছি। এতে কয়েক সেকেন্ড সময়
                লাগতে পারে।</p>
        </div>
    </div>

    <div id="privilegeThanks" class="d-none mt-3 alert alert-success"></div>
@endsection

@push('front_js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var phoneCheckTimer = null;
            var phoneAvailable = false;

            function setPhoneFeedback(type, message) {
                var el = $('#dd_phone_feedback');
                el.removeClass('d-none is-valid is-invalid is-checking')
                    .addClass(type === 'valid' ? 'is-valid' : (type === 'invalid' ? 'is-invalid' : 'is-checking'))
                    .html(message);
            }

            function checkPhoneAvailability() {
                var phone = $('#dd_phone').val().trim();
                if (phone.length < 10) {
                    phoneAvailable = false;
                    $('#dd_phone_feedback').addClass('d-none');
                    return;
                }

                setPhoneFeedback('checking',
                    '<iconify-icon icon="svg-spinners:ring-resize"></iconify-icon> ফোন নম্বর যাচাই করা হচ্ছে…');

                $.get('{{ route('frontend.members.check-phone') }}', {
                        phone: phone
                    })
                    .done(function(res) {
                        phoneAvailable = !!res.available;
                        if (res.available) {
                            setPhoneFeedback('valid',
                                '<iconify-icon icon="solar:check-circle-linear"></iconify-icon> ' + res
                                .message);
                        } else {
                            setPhoneFeedback('invalid',
                                '<iconify-icon icon="solar:close-circle-linear"></iconify-icon> ' + res
                                .message + ' <a href="{{ route('frontend.member.login') }}">লগইন করুন</a>');
                        }
                    })
                    .fail(function() {
                        phoneAvailable = false;
                        $('#dd_phone_feedback').addClass('d-none');
                    });
            }

            $('#dd_phone').on('input blur', function() {
                clearTimeout(phoneCheckTimer);
                phoneCheckTimer = setTimeout(checkPhoneAvailability, 450);
            });

            function setProcessing(active) {
                var btn = $('#privilegeSubmitBtn');
                var overlay = $('#applyProcessingOverlay');
                if (active) {
                    btn.prop('disabled', true).addClass('is-loading');
                    btn.find('.dd-submit-text').text('জমা দেওয়া হচ্ছে…');
                    overlay.removeClass('d-none');
                    $('body').addClass('dd-apply-busy');
                } else {
                    btn.prop('disabled', false).removeClass('is-loading');
                    btn.find('.dd-submit-text').text('আবেদন জমা দিন');
                    overlay.addClass('d-none');
                    $('body').removeClass('dd-apply-busy');
                }
            }

            $('#dd_is_student').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#student_extra_fields').removeClass('d-none');
                    $('#dd_student_card').prop('required', true);
                } else {
                    $('#student_extra_fields').addClass('d-none');
                    $('#dd_student_card').prop('required', false).val('');
                    $('#student_card_dropzone').removeClass('has-file');
                    $('#student_card_file_name').addClass('d-none').text('');
                    resetStudentCardPreview();
                }
            });

            function bindDropzone(zoneSelector, inputSelector) {
                var $zone = $(zoneSelector);
                var $input = $(inputSelector);
                $zone.on('dragenter dragover', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $zone.addClass('is-dragover');
                });
                $zone.on('dragleave drop', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $zone.removeClass('is-dragover');
                });
                $zone.on('drop', function(e) {
                    var files = e.originalEvent.dataTransfer && e.originalEvent.dataTransfer.files;
                    if (files && files.length) {
                        $input[0].files = files;
                        $input.trigger('change');
                    }
                });
            }

            bindDropzone('#profile_image_dropzone', '#dd_profile_image');
            bindDropzone('#student_card_dropzone', '#dd_student_card');

            $('#dd_profile_image').on('change', function() {
                var file = this.files[0];
                if (file) {
                    $('#profile_image_dropzone').addClass('has-file');
                    $('#profile_image_file_name').removeClass('d-none').text(file.name);
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#profile_image_preview').attr('src', e.target.result);
                        $('#profile_image_preview_wrap').removeClass('d-none');
                    };
                    reader.readAsDataURL(file);
                } else {
                    $('#profile_image_dropzone').removeClass('has-file');
                    $('#profile_image_file_name').addClass('d-none').text('');
                    $('#profile_image_preview_wrap').addClass('d-none');
                    $('#profile_image_preview').attr('src', '');
                }
            });

            $('#dd_student_card').on('change', function() {
                var file = this.files[0];
                if (!file) {
                    $('#student_card_dropzone').removeClass('has-file');
                    $('#student_card_file_name').addClass('d-none').text('');
                    resetStudentCardPreview();
                    return;
                }

                $('#student_card_dropzone').addClass('has-file');
                $('#student_card_file_name').removeClass('d-none').text(file.name);
                $('#student_card_preview_wrap').removeClass('d-none');

                if (file.type === 'application/pdf') {
                    $('#student_card_img_preview').addClass('d-none');
                    $('#student_card_pdf_name').text(file.name);
                    $('#student_card_pdf_indicator').removeClass('d-none');
                } else {
                    $('#student_card_pdf_indicator').addClass('d-none');
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#student_card_preview').attr('src', e.target.result);
                        $('#student_card_img_preview').removeClass('d-none');
                    };
                    reader.readAsDataURL(file);
                }
            });

            function resetStudentCardPreview() {
                $('#student_card_preview_wrap').addClass('d-none');
                $('#student_card_img_preview').addClass('d-none');
                $('#student_card_pdf_indicator').addClass('d-none');
                $('#student_card_preview').attr('src', '');
                $('#student_card_pdf_name').text('');
            }

            $('#dd_terms').on('change', function() {
                $('#ddTermsSection').removeClass('is-error');
                $('#ddTermsError').addClass('d-none');
            });

            $('#privilegeCardForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var submitBtn = $('#privilegeSubmitBtn');

                if (!$('#dd_terms').is(':checked')) {
                    $('#ddTermsSection').addClass('is-error');
                    $('#ddTermsError').removeClass('d-none');
                    $('html, body').animate({
                        scrollTop: $('#ddTermsSection').offset().top - 120
                    }, 300);
                    return;
                }

                if ($('#dd_password').val() !== $('#dd_password_confirm').val()) {
                    showErrorPopup('পাসওয়ার্ড এবং নিশ্চিত করা পাসওয়ার্ড মিলছে না।');
                    return;
                }

                var phone = $('#dd_phone').val().trim();
                if (phone.length < 10) {
                    showErrorPopup('অনুগ্রহ করে একটি সঠিক ফোন নম্বর দিন।');
                    return;
                }

                var email = $('#dd_email').val().trim();
                if (!email || email.indexOf('@') === -1) {
                    showErrorPopup(
                        'অনুগ্রহ করে একটি সঠিক ইমেইল ঠিকানা দিন। পাসওয়ার্ড পুনরুদ্ধারের জন্য ইমেইল প্রয়োজন।');
                    return;
                }

                setProcessing(true);

                $.get('{{ route('frontend.members.check-phone') }}', {
                        phone: phone
                    })
                    .done(function(res) {
                        if (!res.available) {
                            setProcessing(false);
                            phoneAvailable = false;
                            setPhoneFeedback('invalid',
                                '<iconify-icon icon="solar:close-circle-linear"></iconify-icon> ' +
                                res.message +
                                ' <a href="{{ route('frontend.member.login') }}">লগইন করুন</a>');
                            showErrorPopup(res.message);
                            return;
                        }
                        phoneAvailable = true;
                        submitApplication(form);
                    })
                    .fail(function() {
                        setProcessing(false);
                        showErrorPopup('ফোন নম্বর যাচাই করা সম্ভব হয়নি। অনুগ্রহ করে আবার চেষ্টা করুন।');
                    });
            });

            function submitApplication(form) {
                var formData = new FormData(form[0]);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        setProcessing(false);
                        if (res.success) {
                            form[0].reset();
                            phoneAvailable = false;
                            $('#dd_phone_feedback').addClass('d-none');
                            $('#student_extra_fields').addClass('d-none');
                            $('#dd_student_card').prop('required', false);
                            $('#student_card_dropzone').removeClass('has-file');
                            $('#student_card_file_name').addClass('d-none').text('');
                            resetStudentCardPreview();
                            $('#profile_image_dropzone').removeClass('has-file');
                            $('#profile_image_file_name').addClass('d-none').text('');
                            $('#profile_image_preview_wrap').addClass('d-none');
                            $('#profile_image_preview').attr('src', '');
                            showSuccessPopup(res.message, res.card, res.redirect_url);
                        }
                    },
                    error: function(xhr) {
                        setProcessing(false);
                        var msg = xhr.responseJSON?.errors ?
                            Object.values(xhr.responseJSON.errors)[0][0] :
                            (xhr.responseJSON?.message || 'রেজিস্ট্রেশন করা সম্ভব হয়নি। অনুগ্রহ করে আবার চেষ্টা করুন।');
                        if (xhr.responseJSON?.errors?.phone) {
                            phoneAvailable = false;
                            setPhoneFeedback('invalid',
                                '<iconify-icon icon="solar:close-circle-linear"></iconify-icon> ' +
                                msg + ' <a href="{{ route('frontend.member.login') }}">লগইন করুন</a>'
                            );
                        }
                        showErrorPopup(msg);
                    }
                });
            }

            function showSuccessPopup(message, cardNumber, dashboardUrl) {
                var overlay = document.createElement('div');
                overlay.style.cssText =
                    'position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:99999;display:flex;align-items:center;justify-content:center;';

                var cardHtml = cardNumber ?
                    '<div style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:12px;padding:14px 20px;margin-bottom:16px;"><div style="font-size:.75rem;color:rgba(255,255,255,0.6);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">আপনার কার্ড নম্বর</div><div style="font-size:1.3rem;font-weight:800;color:#ff6b35;letter-spacing:.08em;">' +
                    cardNumber +
                    '</div><div style="font-size:.75rem;color:rgba(255,255,255,0.6);margin-top:4px;">এই নম্বরটি সংরক্ষণ করুন — ছাড় পেতে চেকআউটের সময় ব্যবহার করুন</div></div>' :
                    '';

                var dashBtnHtml = dashboardUrl ? '<a href="' + dashboardUrl +
                    '" style="display:block;background:linear-gradient(135deg,#ff6b35,#e63946);color:#fff;border:none;padding:13px 32px;border-radius:999px;font-size:.95rem;font-weight:700;cursor:pointer;width:100%;text-decoration:none;margin-bottom:10px;">আমার ড্যাশবোর্ডে যান</a>' :
                    '';

                var box = document.createElement('div');
                box.style.cssText =
                    'background:#0f4a55;border-radius:20px;padding:40px 36px;max-width:440px;width:90%;text-align:center;box-shadow:0 24px 60px rgba(0,0,0,.35);border:1px solid rgba(255,255,255,0.08);animation:successPopIn .4s cubic-bezier(.34,1.56,.64,1);';
                box.innerHTML = '<div style="font-size:3.5rem;margin-bottom:12px;">\uD83C\uDF89</div>' +
                    '<h3 style="color:#fff;font-weight:800;margin-bottom:8px;">আবেদন সফলভাবে জমা হয়েছে!</h3>' +
                    '<p style="color:rgba(255,255,255,0.85);font-size:.93rem;line-height:1.6;margin-bottom:' + (
                        cardNumber ? '16px' : '24px') + ';">' + message + '</p>' +
                    cardHtml +
                    '<div style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:14px 18px;margin-bottom:18px;text-align:left;">' +
                    '<div style="font-size:.75rem;color:rgba(255,255,255,0.6);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">পরে আবার লগইন করতে</div>' +
                    '<p style="font-size:.85rem;color:rgba(255,255,255,0.85);line-height:1.55;margin:0;">উপরের মেনু থেকে <strong style="color:#fff;">মেম্বার লগইন</strong> (অথবা <strong style="color:#fff;">/member/login</strong>) পেজে গিয়ে আপনার <strong style="color:#fff;">ফোন</strong>, <strong style="color:#fff;">ইমেইল</strong> অথবা <strong style="color:#fff;">কার্ড নম্বর</strong> এবং এইমাত্র তৈরি করা <strong style="color:#fff;">পাসওয়ার্ড</strong> ব্যবহার করুন।</p></div>' +
                    dashBtnHtml +
                    '<button id="successPopupClose" style="background:rgba(255,255,255,0.08);color:#fff;border:1px solid rgba(255,255,255,0.15);padding:13px 32px;border-radius:999px;font-size:.95rem;font-weight:700;cursor:pointer;width:100%;">বন্ধ করুন</button>';

                overlay.appendChild(box);
                document.body.appendChild(overlay);

                overlay.querySelector('#successPopupClose').addEventListener('click', function() {
                    overlay.remove();
                });
                if (dashboardUrl) {
                    var dashLink = overlay.querySelector('a[href="' + dashboardUrl + '"]');
                    if (dashLink) dashLink.addEventListener('click', function() {
                        overlay.remove();
                    });
                }
                overlay.addEventListener('click', function(e) {
                    if (e.target === overlay) overlay.remove();
                });
            }

            function showErrorPopup(message) {
                var overlay = document.createElement('div');
                overlay.style.cssText =
                    'position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:99999;display:flex;align-items:center;justify-content:center;';

                var box = document.createElement('div');
                box.style.cssText =
                    'background:#0f4a55;border-radius:20px;padding:36px;max-width:400px;width:90%;text-align:center;box-shadow:0 20px 50px rgba(0,0,0,.3);border:1px solid rgba(255,255,255,0.08);';
                box.innerHTML = '<div style="font-size:3rem;margin-bottom:10px;">\u26A0\uFE0F</div>' +
                    '<h4 style="color:#ef4444;font-weight:700;margin-bottom:8px;">দুঃখিত!</h4>' +
                    '<p style="color:rgba(255,255,255,0.85);font-size:.9rem;line-height:1.55;margin-bottom:20px;">' +
                    message + '</p>' +
                    '<button id="errPopupClose" style="background:linear-gradient(135deg,#ff6b35,#e63946);color:#fff;border:none;padding:11px 28px;border-radius:999px;font-size:.9rem;font-weight:700;cursor:pointer;">আবার চেষ্টা করুন</button>';

                overlay.appendChild(box);
                document.body.appendChild(overlay);

                overlay.querySelector('#errPopupClose').addEventListener('click', function() {
                    overlay.remove();
                });
                overlay.addEventListener('click', function(e) {
                    if (e.target === overlay) overlay.remove();
                });
            }
        });
    </script>
@endpush
