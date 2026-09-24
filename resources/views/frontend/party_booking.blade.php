@extends('frontend.layout')

@section('meta_title', 'Party Booking')
@section('meta_description', 'Reserve Degchi Dine for birthdays, anniversaries, corporate events and family get-togethers. Spacious venues, customized platters and premium hospitality.')

@section('frontend_content')
<x-frontend.page-header
    title="Reserve Your Party"
    eyebrow="Party Booking"
    subtitle="Experience the perfect blend of traditional flavors and premium hospitality for your next big event."
    :backLink="route('frontend.home')"
    backText="Back to Home"
>
    <x-slot:eyebrowIcon>
        <iconify-icon icon="solar:birthday-cake-bold"></iconify-icon>
    </x-slot:eyebrowIcon>
</x-frontend.page-header>

<section class="party-booking-page" style="background: var(--brand-gredient); min-height: 60vh;">
    <div class="container px-4 px-lg-5 party-booking-main-box">
        <div class="party-booking-grid">

            {{-- Left Side: Information & Visuals --}}
            <aside class="party-booking-info">
                <div class="party-booking-info-panel">
                    <div class="party-booking-info-top">
                        <div>
                            <h2 class="party-booking-info-title">Celebrate Moments With Us</h2>
                            <p class="party-booking-info-tagline">Spacious venues · Tailored menus · Premium service</p>
                        </div>
                        <span class="party-booking-open-badge"><i class="bi bi-people-fill me-1"></i> 20+ Guests</span>
                    </div>

                    {{-- Media Highlight --}}
                    <div class="party-booking-media">
                        <video class="party-booking-video" autoplay muted loop playsinline preload="metadata"
                            poster="https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&w=800&q=75">
                            <source src="{{ asset('assets/frontend/video/PartyHallDegciDine.mp4') }}" type="video/mp4" />
                        </video>
                        <div class="party-booking-media-overlay">
                            <i class="bi bi-camera-reels-fill"></i>
                            <span>Authentic Dining Experience</span>
                        </div>
                    </div>

                    {{-- Features --}}
                    <div class="party-booking-features">
                        <article class="party-booking-feature">
                            <div class="party-booking-feature-icon"><i class="bi bi-people-fill"></i></div>
                            <div class="party-booking-feature-body">
                                <h3>Spacious Arrangements</h3>
                                <p>Comfortable seating for large gatherings and families.</p>
                            </div>
                        </article>

                        <article class="party-booking-feature">
                            <div class="party-booking-feature-icon"><i class="bi bi-egg-fried"></i></div>
                            <div class="party-booking-feature-body">
                                <h3>Customized Menu</h3>
                                <p>Curate your party menu with our signature dishes.</p>
                            </div>
                        </article>

                        <article class="party-booking-feature">
                            <div class="party-booking-feature-icon party-booking-feature-icon-gold"><i class="bi bi-star-fill"></i></div>
                            <div class="party-booking-feature-body">
                                <h3>Premium Hospitality</h3>
                                <p>Dedicated service to ensure a flawless experience.</p>
                            </div>
                        </article>
                    </div>

                    <div class="party-booking-actions">
                        <a href="{{ route('frontend.completeMenu') }}" class="btn-dine btn-dine-primary btn-dine-sm">
                            <i class="bi bi-bag-check-fill"></i>
                            Browse Full Menu
                        </a>
                        <a href="{{ route('frontend.contact') }}" class="btn-dine btn-dine-ghost btn-dine-sm">
                            <i class="bi bi-headset"></i>
                            Talk to Us
                        </a>
                    </div>
                </div>
            </aside>

            {{-- Right Side: Booking Form --}}
            <div class="party-booking-forms">
                <div class="party-booking-form-sticky">
                    <div class="cp-form-card party-booking-card">
                        <div class="cp-form-card-head">
                            <span class="cp-form-step">Booking Request</span>
                            <h3>Reserve Your Party</h3>
                            <p>Fill out the details below and our team will confirm your event within 24 hours.</p>
                        </div>

                        @if(session('success'))
                            <div class="party-booking-alert" role="alert">
                                <i class="bi bi-check-circle-fill"></i>
                                <div>
                                    <strong>Request Received!</strong>
                                    <span>{{ session('success') }}</span>
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('frontend.partyBooking.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="cp-field">
                                        <label class="cp-label" for="name">Full Name</label>
                                        <input type="text" id="name" name="name" class="cp-input" placeholder="John Doe" required value="{{ old('name') }}">
                                        @error('name')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="cp-field">
                                        <label class="cp-label" for="phone">Phone Number</label>
                                        <input type="text" id="phone" name="phone" class="cp-input" placeholder="01XXX-XXXXXX" required value="{{ old('phone') }}">
                                        @error('phone')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="cp-field">
                                        <label class="cp-label" for="total_members">Total Guests</label>
                                        <input type="number" id="total_members" name="total_members" class="cp-input" placeholder="E.g. 20" min="1" required value="{{ old('total_members') }}">
                                        @error('total_members')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="cp-field">
                                        <label class="cp-label" for="booking_date">Event Date</label>
                                        <input type="date" id="booking_date" name="booking_date" class="cp-input" required value="{{ old('booking_date') }}">
                                        @error('booking_date')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="cp-field">
                                <label class="cp-label" for="branch_id">Select Branch</label>
                                <select id="branch_id" name="branch_id" class="cp-input party-booking-select" required>
                                    <option value="" disabled selected>Where would you like to host?</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }} ({{ $branch->location }})</option>
                                    @endforeach
                                </select>
                                @error('branch_id')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                            </div>

                            <button type="submit" class="btn-dine btn-dine-primary btn-dine-100">
                                <i class="bi bi-send-fill me-2"></i> Send Request
                            </button>
                        </form>

                        {{-- Booking Intel --}}
                        <div class="party-booking-intel">
                            <div class="party-booking-intel-icon"><i class="bi bi-clipboard-check-fill"></i></div>
                            <div>
                                <h4>Party Booking Options</h4>
                                <p><strong>Gatherings &amp; Events:</strong> Ideal for Corporate Events, Birthdays, Anniversaries, and Family Get-togethers. We can comfortably host medium to large groups across our premium branches.</p>
                                <p><strong>Customized Platters:</strong> Enjoy our famous traditional Mezban, slow-cooked Kacchi Biryani, and authentic Bangla food served in dedicated party platters to share with your guests.</p>
                                <p class="mb-0"><strong>Notice:</strong> We recommend placing your booking request at least 48 hours in advance to ensure the best seating arrangements and menu availability.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection