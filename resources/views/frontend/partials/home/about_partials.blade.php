@php
    $aboutKicker = optional($aboutSettings->get('about_kicker'))->value ?? 'Our Heritage';
    $aboutTitle = optional($aboutSettings->get('about_title'))->value ?? 'The Story of Degchi Dine';
    $aboutLead =
        optional($aboutSettings->get('about_lead'))->value ??
        'Bringing the authentic, slow-cooked royal.';
    $aboutParagraph =
        optional($aboutSettings->get('about_paragraph'))->value ??
        'At Degchi Dine.';
    $aboutFeature1Icon = optional($aboutSettings->get('about_feature_1_icon'))->value ?? 'bi bi-fire';
    $aboutFeature1Text = optional($aboutSettings->get('about_feature_1_text'))->value ?? 'Authentic Dum Style';
    $aboutFeature2Icon = optional($aboutSettings->get('about_feature_2_icon'))->value ?? 'bi bi-patch-check-fill';
    $aboutFeature2Text = optional($aboutSettings->get('about_feature_2_text'))->value ?? 'Premium Ingredients';
    $aboutExpNumber = optional($aboutSettings->get('about_exp_number'))->value ?? '10+';
    $aboutExpText = optional($aboutSettings->get('about_exp_text'))->value ?? 'Years Of Culinary Craft';
    $aboutCtaUrl = optional($aboutSettings->get('about_cta_url'))->value ?? route('frontend.completeMenu');
    $aboutImage = optional($aboutSettings->get('about_image'))->value
        ? asset('uploads/about/' . optional($aboutSettings->get('about_image'))->value)
        : asset('assets/frontend/images/about/about.jpg');
    $ctaText = $ctaText ?? 'Contact Us';
    $aboutPage = $aboutPage ?? false;
@endphp

<!-- ABOUT -->
<section class="section-block py-5 about-section {{ $aboutPage ? 'about-page' : '' }}" id="about">
    <div class="container px-4 px-lg-5">
        <div class="row align-items-center g-4 g-lg-5">
            <div class="col-12 col-lg-6 reveal">
                <div class="about-content-block">
                    <x-frontend.section-heading :title="$aboutTitle" :kicker="$aboutKicker" 
                        class="mb-3" />
                    <p class="about-lead mb-4">
                        {{ $aboutLead }}
                    </p>
                    <div class="about-paragraph mb-4 ">
                        {!! $aboutParagraph !!}
                    </div>

                    <div class="about-features-grid mb-4">
                        <div class="about-feature-item">
                            <div class="feature-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2c0 4-4 6-4 10a4 4 0 0 0 8 0c0-4-4-6-4-10z"/></svg>
                            </div>
                            <span class="feature-text text-uppercase">{{ $aboutFeature1Text }}</span>
                        </div>
                        <div class="about-feature-item">
                            <div class="feature-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                            </div>
                            <span class="feature-text text-uppercase">{{ $aboutFeature2Text }}</span>
                        </div>
                    </div>

                    <div class="about-cta-wrap">
                        <a href="{{ $aboutCtaUrl }}" class="btn-dine btn-dine-primary">
                            <span>{{ $ctaText }} <i class="bi bi-arrow-right ms-2"></i></span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6 reveal">
                <div class="about-media-frame position-relative">
                    <div class="about-shape-backdrop"></div>

                    <div class="about-img-container">
                        <img src="{{ $aboutImage }}" alt="Degchi Dine - About Our Restaurant" class="about-main-img"
                            onerror="this.src='{{ asset('assets/frontend/images/about.png') }}'" />
                        <div class="about-img-overlay"></div>
                    </div>

                    <div class="about-experience-badge text-center">
                        <span class="exp-number">{{ $aboutExpNumber }}</span>
                        <span class="exp-text text-uppercase">{{ $aboutExpText }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
