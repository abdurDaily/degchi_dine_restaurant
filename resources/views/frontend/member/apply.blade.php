@extends('frontend.layout')

@section('meta_title', 'Apply for Membership')
@section('meta_description', 'Apply for a Degchi Dine membership card and unlock exclusive dining benefits, rewards and priority service.')

@push('front_css')
<style>
    .dd-apply-right-form .dd-apply-form-header {
        margin-bottom: 8px;
    }
    .dd-apply-right-form .dd-apply-form-header h2 {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 6px;
    }
    .dd-apply-right-form .dd-apply-form-header p {
        margin-bottom: 1.5rem;
        font-size: 0.92rem;
    }
    .dd-apply-section {
        margin-bottom: 1.35rem;
        padding-bottom: 0.35rem;
        border-bottom: 1px solid rgba(17, 107, 131, 0.08);
    }
    .dd-apply-section:last-of-type {
        border-bottom: none;
        margin-bottom: 0.5rem;
    }
    .dd-apply-section-title {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #116b83;
        margin: 0 0 0.9rem;
    }
    .dd-apply-section-title iconify-icon {
        font-size: 1rem;
        color: var(--dd-gold, #e7ae07);
    }
    .dd-apply-field-hint {
        font-size: 0.78rem;
        color: var(--dd-text-muted, #6c757d);
        margin: -8px 0 1rem;
        line-height: 1.45;
    }
    .dd-apply-field-hint a {
        color: #116b83;
        font-weight: 600;
        text-decoration: none;
    }
    .dd-apply-field-hint a:hover {
        text-decoration: underline;
    }
    .dd-apply-dropzone {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        width: 100%;
        min-height: 140px;
        padding: 1.25rem 1rem;
        margin-bottom: 1rem;
        background: #fff;
        border: 1.5px dashed rgba(17, 107, 131, 0.35);
        border-radius: 14px;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
        box-sizing: border-box;
    }
    .dd-apply-dropzone:hover,
    .dd-apply-dropzone.is-dragover {
        border-color: #116b83;
        background: #f4fafc;
        box-shadow: 0 0 0 4px rgba(17, 107, 131, 0.08);
    }
    .dd-apply-dropzone.has-file {
        border-style: solid;
        border-color: rgba(17, 107, 131, 0.35);
    }
    .dd-apply-dropzone-input {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 2;
    }
    .dd-apply-dropzone-icon {
        font-size: 2rem;
        color: #116b83;
        line-height: 1;
        pointer-events: none;
    }
    .dd-apply-dropzone-title {
        margin: 0;
        font-size: 0.92rem;
        font-weight: 600;
        color: #1a1a1a;
        pointer-events: none;
    }
    .dd-apply-dropzone-title span {
        color: #116b83;
    }
    .dd-apply-dropzone-sub {
        margin: 0;
        font-size: 0.78rem;
        color: #888;
        pointer-events: none;
    }
    .dd-apply-dropzone-name {
        margin: 0.15rem 0 0;
        font-size: 0.8rem;
        font-weight: 600;
        color: #116b83;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        pointer-events: none;
    }
    .dd-apply-student-toggle {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        width: 100%;
        padding: 0.75rem 0.9rem;
        background: var(--dd-input-bg, #eef7fa);
        border: 1px solid rgba(17, 107, 131, 0.2);
        border-radius: 12px;
        margin-bottom: 0.85rem;
        cursor: pointer;
        box-sizing: border-box;
    }
    .dd-apply-student-toggle .form-check-input {
        width: 1.1rem;
        height: 1.1rem;
        margin: 0;
        flex-shrink: 0;
        border-color: rgba(17, 107, 131, 0.35);
    }
    .dd-apply-student-toggle .form-check-input:checked {
        background-color: #116b83;
        border-color: #116b83;
    }
    .dd-apply-student-toggle span {
        font-size: 0.92rem;
        font-weight: 600;
        color: var(--dd-text-main, #1a1a1a);
    }
    .dd-apply-student-info {
        background: linear-gradient(135deg, rgba(40,167,69,0.08), rgba(40,167,69,0.02));
        border: 1px solid rgba(40,167,69,0.2);
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 14px;
    }
    .dd-apply-student-info-inner {
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }
    .dd-apply-student-info-inner iconify-icon {
        font-size: 22px;
        color: #28a745;
        margin-top: 2px;
        flex-shrink: 0;
    }
    .dd-apply-student-info strong {
        color: #28a745;
        font-size: 0.92rem;
    }
    .dd-apply-student-info p {
        margin: 4px 0 0;
        font-size: 0.82rem;
        color: #555;
        line-height: 1.5;
    }
    .dd-apply-preview {
        text-align: center;
        margin-bottom: 1rem;
    }
    .dd-apply-preview img {
        max-height: 160px;
        border-radius: 12px;
        border: 1px solid rgba(17, 107, 131, 0.15);
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
    .dd-apply-preview-caption {
        margin-top: 6px;
        font-size: 0.78rem;
        color: #888;
    }
    .dd-apply-form-footer {
        text-align: center;
        margin-top: 1.25rem;
        padding-top: 1.1rem;
        border-top: 1px solid rgba(17, 107, 131, 0.1);
    }
    .dd-apply-form-footer p {
        font-size: 0.88rem;
        color: var(--dd-text-muted, #6c757d);
        margin-bottom: 0.65rem;
    }
    .dd-apply-login-link {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.5rem 1.1rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: #116b83;
        border: 1.5px solid rgba(17, 107, 131, 0.35);
        border-radius: 999px;
        text-decoration: none;
        transition: all 0.25s ease;
    }
    .dd-apply-login-link:hover {
        background: #116b83;
        color: #fff;
        border-color: #116b83;
    }
    @media (max-width: 575px) {
        .dd-apply-right-form .dd-apply-form-header h2 {
            font-size: 1.45rem;
        }
    }
</style>
@endpush
@section('frontend_content')

<section class="dd-apply-wrapper">

    <!-- The Dark Atmospheric Hero Banner -->
    <div class="dd-apply-hero-banner">
        <div class="container px-4 px-lg-5 text-center position-relative">

            <a href="{{ route('frontend.cards') }}" class="dd-apply-back-btn">
                <iconify-icon icon="solar:alt-arrow-left-linear"></iconify-icon>
                <span>Return to Portfolio</span>
            </a>

            <span class="dd-apply-badge">Exclusive Access</span>
            <h1 class="dd-apply-headline">Request Your Privileges</h1>
            <p class="dd-apply-subhead">Join our inner circle to unlock a world of bespoke culinary experiences.</p>

        </div>
    </div>

    <!-- The Main Overlapping Content Box -->
    <div class="container px-4 px-lg-5">
        <div class="dd-apply-main-box">

            <!-- LEFT SIDE: Card Showcase & Perks -->
            <div class="dd-apply-left-showcase">

                <div class="dd-apply-stage-wrap" style="position: relative;">
                    <div class="dd-apply-card-stage">
                        <div class="dd-apply-glow"></div>
                        <img src="{{ asset('assets/frontend/images/membership.svg') }}" alt="Degchi Premium Card" class="dd-apply-card-img" />
                    </div>
                </div>

                <div class="dd-apply-perks-list">
                    <h3 class="dd-apply-perks-title">Membership Perks</h3>

                    <div class="dd-perk-row">
                        <div class="dd-perk-icon">
                            <iconify-icon icon="solar:verified-check-linear"></iconify-icon>
                        </div>
                        <div class="dd-perk-text">
                            <strong>Priority Reservations</strong>
                            <span>Skip the waitlist with 24/7 dedicated booking.</span>
                        </div>
                    </div>

                    <div class="dd-perk-row">
                        <div class="dd-perk-icon">
                            <iconify-icon icon="solar:wad-of-money-linear"></iconify-icon>
                        </div>
                        <div class="dd-perk-text">
                            <strong>Preferred Pricing</strong>
                            <span>Automatic deductions applied to your dining checks.</span>
                        </div>
                    </div>

                    <div class="dd-perk-row">
                        <div class="dd-perk-icon">
                            <iconify-icon icon="solar:gift-linear"></iconify-icon>
                        </div>
                        <div class="dd-perk-text">
                            <strong>Curated Surprises</strong>
                            <span>Complimentary chef treats on your special dates.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDE: The Application Form -->
            <div class="dd-apply-right-form">

                <div class="dd-apply-form-header">
                    <h2>Application Form</h2>
                    <p>Fill in your details below. Approvals are usually processed within one business day.</p>
                </div>

                <form id="privilegeCardForm" class="dd-apply-form-element" method="POST" action="{{ route('frontend.members.register') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="dd-apply-section">
                        <h3 class="dd-apply-section-title">
                            <iconify-icon icon="solar:user-circle-linear"></iconify-icon>
                            Account details
                        </h3>

                        <div class="dd-input-group">
                            <input type="text" name="name" id="dd_name" class="dd-input-field" placeholder=" " value="{{ old('name') }}" required autocomplete="name">
                            <label for="dd_name" class="dd-floating-label">Full Name</label>
                        </div>

                        <div class="dd-input-grid">
                            <div class="dd-input-group">
                                <input type="tel" name="phone" id="dd_phone" class="dd-input-field" placeholder=" " value="{{ old('phone') }}" required autocomplete="tel">
                                <label for="dd_phone" class="dd-floating-label">Phone Number</label>
                            </div>
                            <div class="dd-input-group">
                                <input type="email" name="email" id="dd_email" class="dd-input-field" placeholder=" " value="{{ old('email') }}" required autocomplete="email">
                                <label for="dd_email" class="dd-floating-label">Email Address</label>
                            </div>
                        </div>
                        <div id="dd_phone_feedback" class="dd-phone-feedback d-none" role="status"></div>
                        <p class="dd-apply-field-hint">Email is required for password recovery. You can also sign in with email at <a href="{{ route('frontend.member.login') }}">Member Login</a>.</p>

                        <div class="dd-input-grid">
                            <div class="dd-input-group">
                                <input type="password" name="password" id="dd_password" class="dd-input-field" placeholder=" " required minlength="8" autocomplete="new-password">
                                <label for="dd_password" class="dd-floating-label">Create Password</label>
                            </div>
                            <div class="dd-input-group">
                                <input type="password" name="password_confirmation" id="dd_password_confirm" class="dd-input-field" placeholder=" " required minlength="8" autocomplete="new-password">
                                <label for="dd_password_confirm" class="dd-floating-label">Confirm Password</label>
                            </div>
                        </div>
                        <p class="dd-apply-field-hint">At least 8 characters. Sign in later with your <strong>phone</strong>, <strong>email</strong>, or <strong>card number</strong> plus this password.</p>
                    </div>

                    <div class="dd-apply-section">
                        <h3 class="dd-apply-section-title">
                            <iconify-icon icon="solar:calendar-linear"></iconify-icon>
                            Personal details
                        </h3>

                        <div class="dd-input-grid">
                            <div class="dd-input-group">
                                <input type="date" name="dob" id="dd_dob" class="dd-input-field" placeholder=" " value="{{ old('dob') }}" required>
                                <label for="dd_dob" class="dd-floating-label">Date of Birth</label>
                            </div>
                            <div class="dd-input-group">
                                <input type="date" name="marriage_date" id="dd_marriage" class="dd-input-field" placeholder=" " value="{{ old('marriage_date') }}">
                                <label for="dd_marriage" class="dd-floating-label">Marriage Date (optional)</label>
                            </div>
                        </div>

                        <div class="dd-input-group">
                            <textarea name="address" id="dd_address" class="dd-input-field" rows="2" placeholder=" " required>{{ old('address') }}</textarea>
                            <label for="dd_address" class="dd-floating-label">Address</label>
                        </div>
                    </div>

                    <div class="dd-apply-section">
                        <h3 class="dd-apply-section-title">
                            <iconify-icon icon="solar:camera-linear"></iconify-icon>
                            Profile photo
                        </h3>

                        <div class="dd-apply-dropzone" id="profile_image_dropzone">
                            <input type="file" name="profile_image" id="dd_profile_image" class="dd-apply-dropzone-input" accept="image/webp,image/png,image/jpeg">
                            <iconify-icon icon="solar:gallery-add-linear" class="dd-apply-dropzone-icon"></iconify-icon>
                            <p class="dd-apply-dropzone-title">Drop your image here, or <span>browse</span></p>
                            <p class="dd-apply-dropzone-sub">Supports: JPG, PNG, WebP (optional)</p>
                            <p class="dd-apply-dropzone-name d-none" id="profile_image_file_name"></p>
                        </div>

                        <div class="d-none dd-apply-preview" id="profile_image_preview_wrap">
                            <img id="profile_image_preview" src="" alt="Profile Image Preview" />
                            <div class="dd-apply-preview-caption">Preview of your profile image</div>
                        </div>
                    </div>

                    <div class="dd-apply-section">
                        <h3 class="dd-apply-section-title">
                            <iconify-icon icon="solar:square-academic-cap-linear"></iconify-icon>
                            Student status
                        </h3>

                        <label class="dd-apply-student-toggle" for="dd_is_student">
                            <input type="checkbox" name="is_student" id="dd_is_student" value="1" class="form-check-input" @checked(old('is_student'))>
                            <span>I am a student</span>
                        </label>

                        <div id="student_extra_fields" class="{{ old('is_student') ? '' : 'd-none' }}">
                            <div id="student_discount_info" class="dd-apply-student-info">
                                <div class="dd-apply-student-info-inner">
                                    <iconify-icon icon="solar:graduation-cap-bold"></iconify-icon>
                                    <div>
                                        <strong>Student Benefit — 35% First Order Discount!</strong>
                                        <p>Upload a valid student ID to verify. Students get <strong>35%</strong> on the first order (vs <strong>30%</strong> for other members).</p>
                                    </div>
                                </div>
                            </div>

                            <div class="dd-apply-dropzone" id="student_card_dropzone">
                                <input type="file" name="student_card" id="dd_student_card" class="dd-apply-dropzone-input" accept="image/png,image/jpeg,image/jpg,application/pdf">
                                <iconify-icon icon="solar:document-add-linear" class="dd-apply-dropzone-icon"></iconify-icon>
                                <p class="dd-apply-dropzone-title">Drop student ID here, or <span>browse</span></p>
                                <p class="dd-apply-dropzone-sub">Supports: JPG, PNG, PDF *</p>
                                <p class="dd-apply-dropzone-name d-none" id="student_card_file_name"></p>
                            </div>

                            <div class="d-none mb-3" id="student_card_preview_wrap">
                                <div id="student_card_img_preview" class="d-none dd-apply-preview">
                                    <img id="student_card_preview" src="" alt="Student Card Preview" />
                                </div>
                                <div id="student_card_pdf_indicator" class="d-none" style="text-align:center; padding:18px; border:1.5px dashed rgba(40,167,69,.4); border-radius:14px; background:rgba(40,167,69,.04);">
                                    <iconify-icon icon="solar:document-bold" style="font-size:2.5rem; color:#28a745;"></iconify-icon>
                                    <div style="margin-top:6px; font-size:.85rem; font-weight:600; color:#28a745;" id="student_card_pdf_name"></div>
                                    <div style="font-size:.75rem; color:#888; margin-top:2px;">PDF document ready to upload</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dd-terms-section" id="ddTermsSection">
                        <div class="dd-terms-required-note">
                            <iconify-icon icon="solar:info-circle-linear"></iconify-icon>
                            <span>Required — tap the box below to agree before submitting</span>
                        </div>
                        <label class="dd-terms-wrapper" id="ddTermsLabel" for="dd_terms">
                            <input type="checkbox" id="dd_terms" name="terms" value="1" class="dd-hidden-check">
                            <div class="dd-visible-check" aria-hidden="true">
                                <iconify-icon icon="solar:check-read-linear"></iconify-icon>
                            </div>
                            <span class="dd-terms-text">
                                <strong>I agree to the {{ config('app.name') }} rewards program terms.</strong>
                                I confirm that all details provided in this application are accurate and complete.
                            </span>
                        </label>
                        <div class="dd-terms-error d-none" id="ddTermsError">
                            <iconify-icon icon="solar:danger-circle-linear"></iconify-icon>
                            Please check the box above to confirm you agree to the terms.
                        </div>
                    </div>

                    <button type="submit" id="privilegeSubmitBtn" class="dd-submit-btn">
                        <span class="dd-submit-text">Submit Application</span>
                        <iconify-icon icon="solar:arrow-right-linear" class="dd-btn-icon dd-submit-icon"></iconify-icon>
                    </button>
                </form>

                <div class="dd-apply-form-footer">
                    <p>Already a member?</p>
                    <a href="{{ route('frontend.member.login') }}" class="dd-apply-login-link">
                        <iconify-icon icon="solar:login-2-linear"></iconify-icon>
                        Sign in to Member Login
                    </a>
                </div>

                <div id="applyProcessingOverlay" class="dd-apply-processing d-none" aria-live="polite" aria-busy="true">
                    <div class="dd-apply-processing-inner">
                        <div class="dd-apply-spinner" role="presentation"></div>
                        <strong>Processing your application</strong>
                        <p>Please wait — we're creating your membership and sending confirmation. This may take a few seconds.</p>
                    </div>
                </div>

                <div id="privilegeThanks" class="d-none mt-3 alert alert-success"></div>

            </div>

        </div>
    </div>
</section>
@endsection

@push('front_js')
<script>
    $(function(){
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

            setPhoneFeedback('checking', '<iconify-icon icon="svg-spinners:ring-resize"></iconify-icon> Checking phone number…');

            $.get('{{ route('frontend.members.check-phone') }}', { phone: phone })
                .done(function(res) {
                    phoneAvailable = !!res.available;
                    if (res.available) {
                        setPhoneFeedback('valid', '<iconify-icon icon="solar:check-circle-linear"></iconify-icon> ' + res.message);
                    } else {
                        setPhoneFeedback('invalid', '<iconify-icon icon="solar:close-circle-linear"></iconify-icon> ' + res.message + ' <a href="{{ route('frontend.member.login') }}">Sign in</a>');
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
                btn.find('.dd-submit-text').text('Submitting…');
                overlay.removeClass('d-none');
                $('body').addClass('dd-apply-busy');
            } else {
                btn.prop('disabled', false).removeClass('is-loading');
                btn.find('.dd-submit-text').text('Submit Application');
                overlay.addClass('d-none');
                $('body').removeClass('dd-apply-busy');
            }
        }

        // Toggle student fields
        $('#dd_is_student').on('change', function() {
            if($(this).is(':checked')) {
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

        // Profile image preview
        $('#dd_profile_image').on('change', function(){
            var file = this.files[0];
            if(file){
                $('#profile_image_dropzone').addClass('has-file');
                $('#profile_image_file_name').removeClass('d-none').text(file.name);
                var reader = new FileReader();
                reader.onload = function(e){
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

        // Student card preview — supports image + PDF
        $('#dd_student_card').on('change', function(){
            var file = this.files[0];
            if(!file){
                $('#student_card_dropzone').removeClass('has-file');
                $('#student_card_file_name').addClass('d-none').text('');
                resetStudentCardPreview();
                return;
            }

            $('#student_card_dropzone').addClass('has-file');
            $('#student_card_file_name').removeClass('d-none').text(file.name);
            $('#student_card_preview_wrap').removeClass('d-none');

            if(file.type === 'application/pdf'){
                $('#student_card_img_preview').addClass('d-none');
                $('#student_card_pdf_name').text(file.name);
                $('#student_card_pdf_indicator').removeClass('d-none');
            } else {
                $('#student_card_pdf_indicator').addClass('d-none');
                var reader = new FileReader();
                reader.onload = function(e){
                    $('#student_card_preview').attr('src', e.target.result);
                    $('#student_card_img_preview').removeClass('d-none');
                };
                reader.readAsDataURL(file);
            }
        });

        function resetStudentCardPreview(){
            $('#student_card_preview_wrap').addClass('d-none');
            $('#student_card_img_preview').addClass('d-none');
            $('#student_card_pdf_indicator').addClass('d-none');
            $('#student_card_preview').attr('src', '');
            $('#student_card_pdf_name').text('');
        }

        $('#dd_terms').on('change', function(){
            $('#ddTermsSection').removeClass('is-error');
            $('#ddTermsError').addClass('d-none');
        });

        $('#privilegeCardForm').on('submit', function(e){
            e.preventDefault();
            var form = $(this);
            var submitBtn = $('#privilegeSubmitBtn');

            if (!$('#dd_terms').is(':checked')) {
                $('#ddTermsSection').addClass('is-error');
                $('#ddTermsError').removeClass('d-none');
                $('html, body').animate({ scrollTop: $('#ddTermsSection').offset().top - 120 }, 300);
                return;
            }

            if ($('#dd_password').val() !== $('#dd_password_confirm').val()) {
                showErrorPopup('Password and confirmation do not match.');
                return;
            }

            var phone = $('#dd_phone').val().trim();
            if (phone.length < 10) {
                showErrorPopup('Please enter a valid phone number.');
                return;
            }

            var email = $('#dd_email').val().trim();
            if (!email || email.indexOf('@') === -1) {
                showErrorPopup('Please enter a valid email address. Email is required for password recovery.');
                return;
            }

            setProcessing(true);

            $.get('{{ route('frontend.members.check-phone') }}', { phone: phone })
                .done(function(res) {
                    if (!res.available) {
                        setProcessing(false);
                        phoneAvailable = false;
                        setPhoneFeedback('invalid', '<iconify-icon icon="solar:close-circle-linear"></iconify-icon> ' + res.message + ' <a href="{{ route('frontend.member.login') }}">Sign in</a>');
                        showErrorPopup(res.message);
                        return;
                    }
                    phoneAvailable = true;
                    submitApplication(form);
                })
                .fail(function() {
                    setProcessing(false);
                    showErrorPopup('Could not verify phone number. Please try again.');
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
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(res){
                    setProcessing(false);
                    if(res.success){
                        form[0].reset();
                        phoneAvailable = false;
                        $('#dd_phone_feedback').addClass('d-none');
                        $('#student_extra_fields').addClass('d-none');
                        $('#dd_student_card').prop('required', false);
                        $('#profile_image_dropzone, #student_card_dropzone').removeClass('has-file');
                        $('#profile_image_file_name, #student_card_file_name').addClass('d-none').text('');
                        $('#profile_image_preview_wrap').addClass('d-none');
                        $('#profile_image_preview').attr('src', '');
                        resetStudentCardPreview();
                        showSuccessPopup(res.message, res.card, res.redirect_url);
                    }
                },
                error: function(xhr){
                    setProcessing(false);
                    var msg = xhr.responseJSON?.errors
                        ? Object.values(xhr.responseJSON.errors)[0][0]
                        : (xhr.responseJSON?.message || 'Unable to register. Please try again.');
                    if (xhr.responseJSON?.errors?.phone) {
                        phoneAvailable = false;
                        setPhoneFeedback('invalid', '<iconify-icon icon="solar:close-circle-linear"></iconify-icon> ' + msg + ' <a href="{{ route('frontend.member.login') }}">Sign in</a>');
                    }
                    showErrorPopup(msg);
                }
            });
        }

        function showSuccessPopup(message, cardNumber, dashboardUrl){
            var overlay = $('<div>').css({
                position:'fixed', inset:0, background:'rgba(0,0,0,.6)',
                zIndex:99999, display:'flex', alignItems:'center', justifyContent:'center'
            });
            var box = $('<div>').css({
                background:'#fff', borderRadius:'20px', padding:'40px 36px',
                maxWidth:'440px', width:'90%', textAlign:'center',
                boxShadow:'0 24px 60px rgba(0,0,0,.35)',
                animation:'successPopIn .4s cubic-bezier(.34,1.56,.64,1)'
            });
            box.html(`
                <div style="font-size:3.5rem;margin-bottom:12px;">🎉</div>
                <h3 style="color:#1a1a1a;font-weight:800;margin-bottom:8px;">Application Submitted!</h3>
                <p style="color:#555;font-size:.93rem;line-height:1.6;margin-bottom:${cardNumber ? '16px' : '24px'};">${message}</p>
                ${cardNumber ? `
                <div style="background:#f0faf4;border:1.5px solid #28a745;border-radius:12px;padding:14px 20px;margin-bottom:16px;">
                    <div style="font-size:.75rem;color:#888;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Your Card Number</div>
                    <div style="font-size:1.3rem;font-weight:800;color:#28a745;letter-spacing:.08em;">${cardNumber}</div>
                    <div style="font-size:.75rem;color:#888;margin-top:4px;">Save this — use it at checkout for discounts</div>
                </div>` : ''}
                <div style="background:#f8f5ef;border:1px solid #e6dfd7;border-radius:12px;padding:14px 18px;margin-bottom:18px;text-align:left;">
                    <div style="font-size:.75rem;color:#888;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">Sign in again later</div>
                    <p style="font-size:.85rem;color:#555;line-height:1.55;margin:0;">
                        Go to <strong>Member Login</strong> in the top menu (or <strong>/member/login</strong>) and use your <strong>phone</strong>, <strong>email</strong>, or <strong>card number</strong> with the <strong>password</strong> you just created.
                    </p>
                </div>
                ${dashboardUrl ? `
                <a href="${dashboardUrl}" style="display:block;background:#28a745;color:#fff;border:none;padding:13px 32px;border-radius:12px;font-size:.95rem;font-weight:700;cursor:pointer;width:100%;text-decoration:none;margin-bottom:10px;">
                    Go to My Dashboard
                </a>` : ''}
                <button id="successPopupClose" style="background:#f3f4f6;color:#333;border:none;padding:13px 32px;border-radius:12px;font-size:.95rem;font-weight:700;cursor:pointer;width:100%;">
                    Close
                </button>
            `);
            overlay.append(box);
            $('body').append(overlay);
            overlay.find('#successPopupClose').on('click', function(){ overlay.remove(); });
            if (dashboardUrl) {
                overlay.find('a[href="' + dashboardUrl + '"]').on('click', function(){ overlay.remove(); });
            }
            overlay.on('click', function(e){ if(e.target===this) overlay.remove(); });
        }

        function showErrorPopup(message){
            var overlay = $('<div>').css({
                position:'fixed', inset:0, background:'rgba(0,0,0,.5)',
                zIndex:99999, display:'flex', alignItems:'center', justifyContent:'center'
            });
            var box = $('<div>').css({
                background:'#fff', borderRadius:'20px', padding:'36px',
                maxWidth:'400px', width:'90%', textAlign:'center',
                boxShadow:'0 20px 50px rgba(0,0,0,.3)'
            });
            box.html(`
                <div style="font-size:3rem;margin-bottom:10px;">⚠️</div>
                <h4 style="color:#c0392b;font-weight:700;margin-bottom:8px;">Oops!</h4>
                <p style="color:#555;font-size:.9rem;line-height:1.55;margin-bottom:20px;">${message}</p>
                <button id="errPopupClose" style="background:#e74c3c;color:#fff;border:none;padding:11px 28px;border-radius:10px;font-size:.9rem;font-weight:700;cursor:pointer;">
                    Try Again
                </button>
            `);
            overlay.append(box);
            $('body').append(overlay);
            overlay.find('#errPopupClose').on('click', function(){ overlay.remove(); });
            overlay.on('click', function(e){ if(e.target===this) overlay.remove(); });
        }
    });
</script>
<style>
@keyframes successPopIn {
    from { transform: scale(.7); opacity: 0; }
    to   { transform: scale(1); opacity: 1; }
}
.dd-terms-section {
    border: 2px solid var(--dd-border);
    border-radius: 14px;
    padding: 16px 18px;
    margin-top: 10px;
    margin-bottom: 28px;
    background: rgba(212, 175, 55, 0.04);
    transition: border-color 0.25s ease, box-shadow 0.25s ease;
}
.dd-terms-section.is-error {
    border-color: #e74c3c;
    box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.12);
    animation: termsShake 0.45s ease;
}
.dd-terms-required-note {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--dd-gold);
    margin-bottom: 12px;
}
.dd-terms-section .dd-terms-wrapper {
    margin-top: 0;
    margin-bottom: 0;
}
.dd-terms-error {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 12px;
    font-size: 0.82rem;
    color: #e74c3c;
    font-weight: 600;
}
@keyframes termsShake {
    0%, 100% { transform: translateX(0); }
    20%, 60% { transform: translateX(-6px); }
    40%, 80% { transform: translateX(6px); }
}
.dd-phone-feedback {
    font-size: 0.82rem;
    margin-top: -4px;
    margin-bottom: 14px;
    display: flex;
    align-items: flex-start;
    gap: 6px;
    line-height: 1.45;
}
.dd-phone-feedback.is-valid { color: #116b83; }
.dd-phone-feedback.is-invalid { color: #c0392b; }
.dd-phone-feedback.is-checking { color: var(--dd-text-muted); }
.dd-phone-feedback a { font-weight: 600; margin-left: 4px; }
.dd-submit-btn.is-loading {
    opacity: 0.85;
    pointer-events: none;
}
.dd-submit-btn.is-loading .dd-submit-icon {
    animation: ddSpin 0.8s linear infinite;
}
@keyframes ddSpin {
    to { transform: rotate(360deg); }
}
.dd-apply-processing {
    position: fixed;
    inset: 0;
    z-index: 99998;
    background: rgba(8, 56, 68, 0.55);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.25rem;
}
.dd-apply-processing-inner {
    background: #fff;
    border-radius: 18px;
    padding: 32px 28px;
    max-width: 380px;
    width: 100%;
    text-align: center;
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.2);
}
.dd-apply-processing-inner strong {
    display: block;
    color: #116b83;
    font-size: 1.05rem;
    margin-bottom: 8px;
}
.dd-apply-processing-inner p {
    margin: 0;
    font-size: 0.88rem;
    color: #5a7a85;
    line-height: 1.55;
}
.dd-apply-spinner {
    width: 44px;
    height: 44px;
    margin: 0 auto 16px;
    border: 3px solid rgba(17, 107, 131, 0.15);
    border-top-color: #116b83;
    border-radius: 50%;
    animation: ddSpin 0.75s linear infinite;
}
body.dd-apply-busy { overflow: hidden; }
</style>
@endpush