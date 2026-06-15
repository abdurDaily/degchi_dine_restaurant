@extends('frontend.layout')
@push('front_css')
<style>
    .dd-input-field[type="file"] {
        padding-top: 24px;
        padding-bottom: 8px;
        line-height: 1.2;
    }
    .dd-input-field[type="file"] ~ .dd-floating-label {
        transform: translateY(-10px) !important;
        font-size: 0.75rem !important;
        color: var(--dd-gold) !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        letter-spacing: 1px !important;
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
                        <!-- Reference your actual card image here -->
                        <img src="./images/membership.svg" alt="Degchi Premium Card" class="dd-apply-card-img" />
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
                    <p>Please provide your details below. Approvals are typically processed within one business day.</p>
                </div>

                <form id="privilegeCardForm" class="dd-apply-form-element" method="POST" action="{{ route('frontend.members.register') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="dd-input-group">
                        <input type="text" name="name" id="dd_name" class="dd-input-field" placeholder=" " required>
                        <label for="dd_name" class="dd-floating-label">Full Name</label>
                    </div>

                    <div class="dd-input-group">
                        <input type="tel" name="phone" id="dd_phone" class="dd-input-field" placeholder=" " required>
                        <label for="dd_phone" class="dd-floating-label">Phone Number</label>
                    </div>

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
                    <p class="text-muted mb-3" style="font-size: 0.78rem; margin-top: -8px;">Use at least 8 characters. You'll sign in later at <strong>Member Login</strong> using your phone (or card number) and this password.</p>

                    <div class="dd-input-grid">
                        <div class="dd-input-group">
                            <input type="date" name="dob" id="dd_dob" class="dd-input-field" placeholder=" " required>
                            <label for="dd_dob" class="dd-floating-label">Date of Birth</label>
                        </div>
                        <div class="dd-input-group">
                            <input type="date" name="marriage_date" id="dd_marriage" class="dd-input-field" placeholder=" ">
                            <label for="dd_marriage" class="dd-floating-label">Marriage Date (optional)</label>
                        </div>
                    </div>

                    <div class="dd-input-group">
                        <textarea name="address" id="dd_address" class="dd-input-field" rows="2" placeholder=" " required></textarea>
                        <label for="dd_address" class="dd-floating-label">Address</label>
                    </div>

                    <!-- Profile Image Upload -->
                    <div class="dd-input-group">
                        <input type="file" name="profile_image" id="dd_profile_image" class="dd-input-field" accept="image/webp,image/png,image/jpeg">
                        <label for="dd_profile_image" class="dd-floating-label">Upload Profile Image (WebP, PNG, JPG) (optional)</label>
                    </div>

                    <!-- Profile Image Preview -->
                    <div class="d-none mb-3" id="profile_image_preview_wrap" style="text-align:center;">
                        <img id="profile_image_preview" src="" alt="Profile Image Preview" style="max-height:180px;border-radius:10px;border:2px solid rgba(40,167,69,0.3);box-shadow:0 2px 12px rgba(0,0,0,0.08);" />
                        <div style="margin-top:6px;font-size:0.78rem;color:#888;">Preview of your profile image</div>
                    </div>
                    <div class="dd-input-grid align-items-center">
                        <div class="dd-input-group">
                            <label class="form-check form-check-inline" style="cursor: pointer;">
                                <input type="checkbox" name="is_student" id="dd_is_student" value="1" class="form-check-input">
                                <span class="form-check-label text-dark fw-semibold ms-1">I am a student</span>
                            </label>
                        </div>
                    </div>

                    {{-- Student discount info callout --}}
                    <div id="student_discount_info" style="background: linear-gradient(135deg, rgba(40,167,69,0.08), rgba(40,167,69,0.02)); border: 1px solid rgba(40,167,69,0.2); border-radius: 12px; padding: 14px 18px; margin-bottom: 16px;">
                        <div style="display:flex;align-items:flex-start;gap:10px;">
                            <iconify-icon icon="solar:graduation-cap-bold" style="font-size:22px;color:#28a745;margin-top:2px;"></iconify-icon>
                            <div>
                                <strong style="color:#28a745;font-size:0.92rem;">Student Benefit — 35% First Order Discount!</strong>
                                <p style="margin:4px 0 0;font-size:0.82rem;color:#555;line-height:1.5;">Upload your valid student ID card to verify your student status. Students receive a <strong>35% discount</strong> on their first order, compared to <strong>30%</strong> for non-student members. The image is required for student verification.</p>
                            </div>
                        </div>
                    </div>

                    <div class="dd-input-group d-none" id="student_card_group">
                        <input type="file" name="student_card" id="dd_student_card" class="dd-input-field" accept="image/png,image/jpeg,image/jpg,application/pdf">
                        <label for="dd_student_card" class="dd-floating-label">Upload your valid student ID (JPG, PNG, PDF)*</label>
                    </div>

                    {{-- Student card preview (image or PDF indicator) --}}
                    <div class="d-none mb-3" id="student_card_preview_wrap">
                        <div id="student_card_img_preview" class="d-none" style="text-align:center;">
                            <img id="student_card_preview" src="" alt="Student Card Preview"
                                 style="max-height:180px; border-radius:10px; border:2px solid rgba(40,167,69,.3); box-shadow:0 2px 12px rgba(0,0,0,.08);" />
                        </div>
                        <div id="student_card_pdf_indicator" class="d-none" style="text-align:center; padding:18px; border:2px dashed rgba(40,167,69,.4); border-radius:10px; background:rgba(40,167,69,.04);">
                            <iconify-icon icon="solar:document-bold" style="font-size:2.5rem; color:#28a745;"></iconify-icon>
                            <div style="margin-top:6px; font-size:.85rem; font-weight:600; color:#28a745;" id="student_card_pdf_name"></div>
                            <div style="font-size:.75rem; color:#888; margin-top:2px;">PDF document ready to upload</div>
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
                                <strong>I agree to the Degchi Dine rewards program terms.</strong>
                                I confirm that all details provided in this application are accurate and complete.
                            </span>
                        </label>
                        <div class="dd-terms-error d-none" id="ddTermsError">
                            <iconify-icon icon="solar:danger-circle-linear"></iconify-icon>
                            Please check the box above to confirm you agree to the terms.
                        </div>
                    </div>

                    <button type="submit" id="privilegeSubmitBtn" class="dd-submit-btn">
                        <span>Submit Application</span>
                        <iconify-icon icon="solar:arrow-right-linear" class="dd-btn-icon"></iconify-icon>
                    </button>
                </form>

                <div id="privilegeThanks" class="d-none mt-3 alert alert-success"></div>

            </div>

        </div>
    </div>
</section>
@endsection

@push('front_js')
<script>
    $(function(){
        // Toggle student card upload input
        $('#dd_is_student').on('change', function() {
            if($(this).is(':checked')) {
                $('#student_card_group').removeClass('d-none');
                $('#dd_student_card').prop('required', true);
            } else {
                $('#student_card_group').addClass('d-none');
                $('#dd_student_card').prop('required', false).val('');
                resetStudentCardPreview();
            }
        });

        // Profile image preview
        $('#dd_profile_image').on('change', function(){
            var file = this.files[0];
            if(file){
                var reader = new FileReader();
                reader.onload = function(e){
                    $('#profile_image_preview').attr('src', e.target.result);
                    $('#profile_image_preview_wrap').removeClass('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                $('#profile_image_preview_wrap').addClass('d-none');
                $('#profile_image_preview').attr('src', '');
            }
        });

        // Student card preview — supports image + PDF
        $('#dd_student_card').on('change', function(){
            var file = this.files[0];
            if(!file){ resetStudentCardPreview(); return; }

            $('#student_card_preview_wrap').removeClass('d-none');

            if(file.type === 'application/pdf'){
                // PDF: show file name indicator
                $('#student_card_img_preview').addClass('d-none');
                $('#student_card_pdf_name').text(file.name);
                $('#student_card_pdf_indicator').removeClass('d-none');
            } else {
                // Image: show preview
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

            submitBtn.prop('disabled', true).addClass('is-loading');

            var formData = new FormData(this);

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(res){
                    submitBtn.prop('disabled', false).removeClass('is-loading');
                    if(res.success){
                        // Reset form
                        form[0].reset();
                        $('#student_card_group').addClass('d-none');
                        $('#dd_student_card').prop('required', false);
                        $('#profile_image_preview_wrap').addClass('d-none');
                        $('#profile_image_preview').attr('src', '');
                        resetStudentCardPreview();

                        // Show eye-catching success popup
                        showSuccessPopup(res.message, res.card, res.redirect_url);
                    }
                },
                error: function(xhr){
                    submitBtn.prop('disabled', false).removeClass('is-loading');
                    var msg = xhr.responseJSON?.errors
                        ? Object.values(xhr.responseJSON.errors)[0][0]
                        : (xhr.responseJSON?.message || 'Unable to register. Please try again.');
                    showErrorPopup(msg);
                }
            });
        });

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
                        Go to <strong>Member Login</strong> in the top menu (or <strong>/member/login</strong>) and use your <strong>phone number</strong> or <strong>card number</strong> with the <strong>password</strong> you just created.
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
</style>
@endpush