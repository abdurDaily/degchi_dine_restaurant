<!-- SIGNATURE PLATTERS -->
<section class="section-block platter-section" id="platters">
    <div class="container px-4 px-lg-5">
        <x-frontend.section-heading title="Our Signature Platters" subtitle="Carefully crafted selections perfect for sharing"
            icon="fa-solid fa-concierge-bell" class="platter-section-header" />

        <div class="platter-card-wrapper reveal">
            <div class="platter-card">
                <div class="bg-blob blob-1"></div>
                <div class="bg-blob blob-2"></div>

                <div class="platter-nav-column">
                    <div class="slider-nav ">
                        @forelse($signaturePlatters as $platter)
                            @php
                                $thumbnailImage = $platter->thumbnail_image
                                    ? (strpos($platter->thumbnail_image, 'http') === 0
                                        ? $platter->thumbnail_image
                                        : asset('uploads/platters/' . $platter->thumbnail_image))
                                    : 'https://images.unsplash.com/photo-1585937421612-70a008356fbe?w=500&q=80';
                            @endphp
                            <div class="nav-item">
                                <div class="nav-img-wrapper">
                                    <x-responsive-image src="{{ $platter->thumbnail_image ? (strpos($platter->thumbnail_image, 'http') === 0 ? $platter->thumbnail_image : 'uploads/platters/' . $platter->thumbnail_image) : 'https://images.unsplash.com/photo-1585937421612-70a008356fbe?w=500&q=80' }}" alt="{{ $platter->title }}"
                                        width="500" height="500"
                                        onerror="this.src='https://images.unsplash.com/photo-1585937421612-70a008356fbe?w=500&q=80'" />
                                    <div class="sticker-badge">
                                        <div class="sticker-inner">
                                            {{ \Illuminate\Support\Str::limit($platter->title, 15) }}</div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="nav-item">
                                <div class="nav-img-wrapper">
                                    <img src="https://images.unsplash.com/photo-1585937421612-70a008356fbe?w=500&q=80"
                                        alt="Signature Platter" />
                                    <div class="sticker-badge">
                                        <div class="sticker-inner">No Data</div>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="platter-content-column">
                    <div class="slider-for">
                        @forelse($signaturePlatters as $platter)
                            @php
                                $features = collect($platter->features ?? [])
                                    ->filter()
                                    ->values();
                                $menuCardImage = $platter->menu_card_image
                                    ? (strpos($platter->menu_card_image, 'http') === 0
                                        ? $platter->menu_card_image
                                        : asset('uploads/platters/' . $platter->menu_card_image))
                                    : 'https://images.unsplash.com/photo-1585937421612-70a008356fbe?w=500&q=80';
                            @endphp
                            <div class="content-item  pe-lg-5">
                                <div class="subtitle-wrapper ">
                                    <h4 class="platter-subtitle">{{ $platter->subtitle ?: 'Signature Collection' }}
                                    </h4>
                                    <span class="subtitle-line"></span>
                                </div>
                                <h2 class="platter-title">
                                    {!! nl2br(e($platter->title)) !!}
                                </h2>
                                <p class="platter-desc">
                                    {{ $platter->description ?: 'Discover our curated signature platters, designed to delight and share.' }}
                                </p>
                                @if ($features->isNotEmpty())
                                    <ul class="platter-features">
                                        @foreach ($features->take(2) as $feature)
                                            <li>
                                                @if (is_array($feature))
                                                    <i class="fa-solid {{ $feature['icon'] ?? 'fa-check' }}"></i>
                                                    <strong>{{ $feature['label'] ?? '' }}:</strong>
                                                    {{ $feature['text'] ?? '' }}
                                                @else
                                                    <i class="fa-solid fa-leaf text-success"></i>
                                                    {!! nl2br(e($feature)) !!}
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                                <div class="text-center text-lg-start platter-cta-wrap">
                                    <button class="btn-dine btn-dine-primary trigger-menu-popup"
                                        data-menu-image="{{ $menuCardImage }}"
                                        data-platter-title="{{ $platter->title }}">
                                        <i class="bi bi-eye me-2"></i>View Menu Card
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="content-item">
                                <div class="subtitle-wrapper">
                                    <h4 class="platter-subtitle">No Data</h4>
                                    <span class="subtitle-line"></span>
                                </div>
                                <h2 class="platter-title">No Signature Platters</h2>
                                <p class="platter-desc">
                                    Signature platters are coming soon!
                                </p>
                            </div>
                        @endforelse
                    </div>

                    <div class="custom-slider-arrows">
                        <button class="custom-prev" aria-label="Previous platter">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <button class="custom-next" aria-label="Next platter">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
