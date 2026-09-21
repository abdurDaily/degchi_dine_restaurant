<!-- REELS -->
<section class="section-block py-5 reels-section" id="video">
    <div class="container px-4 px-lg-5">
        <div class="reels-header-shell reveal mb-5 reels-section-header-block">
            <div class="reels-section-header">
                <div class="reels-header-left">
                    <x-frontend.section-heading title="Watch Us on facebook"
                        subtitle="Kitchen energy, chef moments, and guest vibes from Degchi Dine. Fresh reels every week."
                        icon="line" align="start" :showDivider="false" />
                    <div class="reels-header-cta-mobile d-md-none mt-3">
                        <a href="{{ $contactSettings['contact_facebook_url']->value ?? 'https://www.facebook.com/DegchiDine' }}"
                            class="btn-dine btn-dine-primary w-100 justify-content-center" target="_blank"
                            rel="noopener noreferrer">
                            <i class="bi bi-facebook" aria-hidden="true"></i><span>Follow on Facebook</span>
                        </a>
                    </div>
                </div>

                <div class="reels-header-right d-none d-md-flex align-items-center">
                    <a href="{{ $contactSettings['contact_facebook_url']->value ?? 'https://www.facebook.com/DegchiDine' }}"
                        class="btn-dine btn-dine-primary" target="_blank" rel="noopener noreferrer">
                        <i class="bi bi-facebook" aria-hidden="true"></i><span>Follow on Facebook</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="reels-slider-container-wrap position-relative">
            <div id="reelsSlider" class="reels-slick reveal">
                @forelse($facebookReels as $reel)
                    @php
                        $reelThumbnail = $reel->thumbnail
                            ? (strpos($reel->thumbnail, 'http') === 0
                                ? $reel->thumbnail
                                : asset('uploads/reels/' . $reel->thumbnail))
                            : asset('assets/placeholder/placeholder.png');
                        $reelUrl = $reel->facebook_url ?: 'https://www.facebook.com/DegchiDine';
                    @endphp
                    <div class="reel-slide-wrap">
                        <a class="reel-card" href="{{ $reelUrl }}" target="_blank" rel="noopener noreferrer">
                            <div class="reel-card-thumb">
                                <div class="reel-progress-indicator"></div>
                                <x-responsive-image src="{{ $reel->thumbnail ? (strpos($reel->thumbnail, 'http') === 0 ? $reel->thumbnail : 'uploads/reels/' . $reel->thumbnail) : 'assets/placeholder/placeholder.png' }}"
                                    alt="{{ $reel->title ?? 'Degchi Dine Facebook Reel' }}" width="360" height="640" loading="lazy" />
                                <div class="reel-card-overlay">
                                    @if($reel->title)
                                        <span class="reel-card-title">{{ $reel->title }}</span>
                                    @endif
                                    <span class="reel-play-icon"><i class="bi bi-play-fill"></i></span>
                                    <span class="reel-watch-label text-uppercase"><i
                                            class="bi bi-facebook me-1"></i>Watch on Facebook</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="reel-slide-wrap">
                        <div class="reel-card">
                            <div class="reel-card-thumb">
                                <div class="reel-progress-indicator"></div>
                                <img src="{{ asset('assets/placeholder/placeholder.png') }}" alt="No reels available"
                                    width="360" height="640" loading="lazy" />
                                <div class="reel-card-overlay">
                                    <span class="reel-play-icon"><i class="bi bi-play-fill"></i></span>
                                    <span class="reel-watch-label text-uppercase"><i
                                            class="bi bi-facebook me-1"></i>Watch on Facebook</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
