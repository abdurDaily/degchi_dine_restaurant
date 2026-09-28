<!-- VISIT US -->
<section class="section-block visit-us-section" id="location">
    <div class="container px-4 px-lg-5">

        @php

            $contactName = optional($contactSettings->get('contact_restaurant_name'))->value ?? 'Degchi Dine';
            $contactAddress =
                optional($contactSettings->get('contact_address'))->value ??
                'Boropool Circle, Kaptan Villa, Halishahar, Chittagong.';
            $contactHours = optional($contactSettings->get('contact_hours'))->value ?? 'Mon - Sun: 11:00 AM - 11:00 PM';
            $contactPhone = optional($contactSettings->get('contact_phone'))->value ?? '+880 1234 567 890';
            $contactMapLink = optional($contactSettings->get('contact_map_link'))->value ?: 'https://maps.google.com';
            $contactMapEmbed =
                optional($contactSettings->get('contact_map_embed'))->value ?:
                'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3690.669527376662!2d91.7766299!3d22.3283281!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjLCsDE5JzQyLjAiTiA5McKwNDYnMzUuOSJF!5e0!3m2!1sen!2sbd!4v1620000000000!5m2!1sen!2sbd';
            $contactPhoneDigits = preg_replace('/\D+/', '', $contactPhone);
        @endphp

        <x-frontend.section-heading title="আমাদের ঠিকানা" subtitle="আমরা আপনাকে স্বাগত জানানোর অপেক্ষায় আছি।" />

        <div class="visit-us-grid reveal">
            <div class="visit-us-panel">
                <div class="visit-us-panel-top">
                    <div>
                        <h3 class="visit-us-name">{{ $contactName }}</h3>
                        <p class="visit-us-tagline">স্বতন্ত্র স্বাদ · আন্তরিক আতিথেয়তা</p>
                    </div>
                    <span class="visit-us-badge"><i class="bi bi-clock me-1"></i> প্রতিদিন খোলা</span>
                </div>

                <div class="visit-us-cards">
                    <article class="visit-us-card">
                        <div class="visit-us-card-icon"><i class="fa-solid fa-location-dot"></i></div>
                        <div class="visit-us-card-content">
                            <h4>ঠিকানা</h4>
                            <p>{!! nl2br(e($contactAddress)) !!}</p>
                        </div>
                    </article>

                    <article class="visit-us-card">
                        <div class="visit-us-card-icon"><i class="fa-regular fa-clock"></i></div>
                        <div class="visit-us-card-content">
                            <h4>খোলার সময়</h4>
                            <p>{{ $contactHours }}</p>
                        </div>
                    </article>

                    <article class="visit-us-card visit-us-card-accent">
                        <div class="visit-us-card-icon visit-us-card-icon-gold"><i class="fa-solid fa-phone"></i>
                        </div>
                        <div class="visit-us-card-content">
                            <h4>রিজার্ভেশন</h4>
                            <p><a href="tel:{{ $contactPhoneDigits }}">{{ $contactPhone }}</a></p>
                            <span class="visit-us-card-note">টেবিল নিশ্চিত করতে আগে থেকেই কল করুন</span>
                        </div>
                    </article>
                </div>

                <div class="visit-us-actions">
                    <a href="{{ $contactMapLink }}" target="_blank" rel="noopener noreferrer"
                        class="btn-dine btn-dine-ghost btn-dine-sm" style="font-size: 12px;">
                        <i class="fa-solid fa-diamond-turn-right"></i>
                        পথনির্দেশ দেখুন
                    </a>
                    <a href="tel:{{ $contactPhoneDigits }}" class="btn-dine btn-dine-primary btn-dine-sm"
                        style="font-size: 12px;">
                        <i class="fa-solid fa-phone"></i>
                        এখনই কল করুন
                    </a>
                </div>

                <div class="visit-us-socials">
                    <span>আমাদের অনুসরণ করুন</span>
                    <x-frontend.social-links :settings="$contactSettings" />
                </div>
            </div>

            <div class="visit-us-map">
                <div class="visit-us-map-label">
                    <i class="bi bi-pin-map-fill"></i>
                    <span>হালিশহর, চট্টগ্রাম</span>
                </div>
                <iframe src="{{ $contactMapEmbed }}" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade" title="{{ $contactName }} location map"></iframe>
            </div>
        </div>
    </div>
</section>
