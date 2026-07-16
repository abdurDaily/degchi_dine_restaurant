@extends('frontend.layout')

@section('meta_title', 'Party Booking')

@push('front_css')
<style>
    :root {
        /* Primary Brand Colors — Degchi Dine palette */
        --brand: #116b83;
        --brand-dark: #0a4554;
        --brand-teal: #116b83;
        --brand-teal-dark: #083844;
        --brand-gold: #e7ae07;
        --brand-gold-hover: #c99606;
        --brand-gold-light: rgba(231, 174, 7, 0.15);
        --brand-teal-light: rgba(17, 107, 131, 0.12);
        --brand-red: #0d5566;

        /* Background Colors */
        --bg-main: #f4f9fb;
        --bg-soft: #fafdfe;
        --bg-accent: #e8f4f7;
        --bg-light: #f8fafc;

        /* Text Colors */
        --text-main: #0d3d4a;
        --text-muted: #5a7a85;
        --text-light: #8aa3ad;
        --text-dark: #083844;

        /* Shadow */
        --card-shadow: 0 16px 30px rgba(17, 107, 131, 0.12);
        --shadow-sm: 0 4px 10px rgba(17, 107, 131, 0.08);
        --shadow-md: 0 8px 20px rgba(17, 107, 131, 0.12);
        --shadow-lg: 0 16px 40px rgba(17, 107, 131, 0.15);

        /* Borders */
        --border-color: #d4e8ee;
        --border-dark: #b8d4dc;
    }

    .booking-section {
        background-color: var(--bg-main);
        padding: 80px 0;
        min-height: 80vh;
        overflow: hidden;
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInLeft {
        from {
            opacity: 0;
            transform: translateX(-40px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes fadeInRight {
        from {
            opacity: 0;
            transform: translateX(40px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .fade-in-up {
        animation: fadeInUp 0.8s ease-out forwards;
    }

    .fade-in-left {
        animation: fadeInLeft 0.8s ease-out forwards;
    }

    .fade-in-right {
        animation: fadeInRight 0.8s ease-out forwards;
    }

    /* Left Side Content */
    .booking-info-content {
        padding-right: 30px;
    }

    .booking-info-title {
        font-family: "Poppins", sans-serif;
        font-weight: 700;
        font-size: 2.8rem;
        color: var(--brand);
        margin-bottom: 15px;
        line-height: 1.2;
    }

    .booking-info-subtitle {
        font-family: "Manrope", sans-serif;
        font-size: 1.1rem;
        color: var(--text-muted);
        margin-bottom: 30px;
    }

    .media-gallery {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--shadow-lg);
        margin-bottom: 40px;
        height: 450px;
        background-color: #000;
    }

    .media-gallery video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .media-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(10, 69, 84, 0.8) 0%, rgba(10, 69, 84, 0.2) 50%, rgba(10, 69, 84, 0) 100%);
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 25px;
        color: white;
        pointer-events: none;
    }

    .feature-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .feature-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 20px;
        background: #ffffff;
        padding: 15px 20px;
        border-radius: 12px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        transition: transform 0.3s ease;
        opacity: 0; /* for animation */
    }

    .feature-item:hover {
        transform: translateY(-5px);
        border-color: var(--brand-gold);
    }

    .feature-icon {
        width: 50px;
        height: 50px;
        background-color: var(--brand-teal-light);
        color: var(--brand);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .feature-text h4 {
        margin: 0 0 5px 0;
        font-family: "Poppins", sans-serif;
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-dark);
    }

    .feature-text p {
        margin: 0;
        font-size: 0.95rem;
        color: var(--text-muted);
    }

    /* Right Side Form */
    .booking-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
        border: 1px solid var(--border-color);
    }

    .booking-header {
        background: linear-gradient(135deg, var(--brand), var(--brand-dark));
        color: #ffffff;
        padding: 30px;
        text-align: center;
        position: relative;
    }

    .booking-header h3 {
        margin: 0;
        font-family: "Poppins", sans-serif;
        font-weight: 600;
        font-size: 1.8rem;
    }

    .booking-header p {
        margin-top: 5px;
        margin-bottom: 0;
        opacity: 0.9;
        font-size: 0.95rem;
    }

    .booking-body {
        padding: 35px;
    }

    .form-group {
        margin-bottom: 22px;
    }

    .form-label {
        color: var(--text-dark);
        font-weight: 600;
        margin-bottom: 8px;
        display: block;
        font-size: 0.95rem;
    }

    .form-control, .form-select {
        border: 2px solid var(--border-color);
        border-radius: 10px;
        padding: 12px 18px;
        font-size: 1rem;
        color: var(--text-main);
        background-color: var(--bg-soft);
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 0.25rem var(--brand-teal-light);
        outline: none;
        background-color: #ffffff;
    }
    
    .form-control::placeholder {
        color: #a0b8c0;
    }

    .btn-submit {
        background-color: var(--brand);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        padding: 15px 20px;
        font-size: 1.05rem;
        font-weight: 600;
        width: 100%;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: 10px;
    }

    .btn-submit:hover {
        background-color: var(--brand-dark);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .alert-success {
        background-color: #d1e7dd;
        color: #0f5132;
        border: 1px solid #badbcc;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 25px;
    }

    /* Responsive */
    @media (max-width: 991.98px) {
        .booking-info-content {
            padding-right: 0;
            margin-bottom: 50px;
        }
        
        .booking-info-title {
            font-size: 2.2rem;
        }
    }
</style>
@endpush

@section('frontend_content')
<section class="booking-section">
    <div class="container">
        <div class="row">
            
            <!-- Left Side: Information & Visuals -->
            <div class="col-lg-6 fade-in-left">
                <div class="booking-info-content">
                    <h1 class="booking-info-title">Celebrate Moments With Us</h1>
                    <p class="booking-info-subtitle">Experience the perfect blend of traditional flavors and premium hospitality for your next big event.</p>
                    
                    <!-- Media Highlight --> 
                    <div class="media-gallery">
                        {{-- <video autoplay muted loop playsinline poster="https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&w=1800&q=80">
                            <source src="{{ asset('assets/frontend/video/PartyHallDegciDine.mp4') }}" type="video/mp4" />
                        </video> --}}
                        <video class="hero-video" autoplay muted loop playsinline preload="auto"
                                poster="https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&w=1800&q=80">
                                <source src="{{ asset('assets/frontend/video/PartyHallDegciDine.mp4') }}"
                                    type="video/mp4" />
                            </video>
                        <div class="media-overlay">
                            <h4 class="mb-1 text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Authentic Dining Experience</h4>
                            <p class="mb-0 text-white-50" style="font-size: 0.9rem;">Make your parties memorable with our signature platters.</p>
                        </div>
                    </div>

                    <!-- Features -->
                    <ul class="feature-list mt-4">
                        <li class="feature-item fade-in-up" style="animation-delay: 0.2s;">
                            <div class="feature-icon"><i class="fa-solid fa-users"></i></div>
                            <div class="feature-text">
                                <h4>Spacious Arrangements</h4>
                                <p>Comfortable seating for large gatherings and families.</p>
                            </div>
                        </li>
                        <li class="feature-item fade-in-up" style="animation-delay: 0.4s;">
                            <div class="feature-icon"><i class="fa-solid fa-utensils"></i></div>
                            <div class="feature-text">
                                <h4>Customized Menu</h4>
                                <p>Curate your party menu with our signature dishes.</p>
                            </div>
                        </li>
                        <li class="feature-item fade-in-up" style="animation-delay: 0.6s;">
                            <div class="feature-icon"><i class="fa-solid fa-star"></i></div>
                            <div class="feature-text">
                                <h4>Premium Hospitality</h4>
                                <p>Dedicated service to ensure a flawless experience.</p>
                            </div>
                        </li>
                    </ul>

                </div>
            </div>
            
            <!-- Right Side: Booking Form -->
            <div class="col-lg-6 fade-in-right" style="animation-delay: 0.3s;">
                <div class="booking-card" style="position: sticky; top: 120px; z-index: 10;">
                    <div class="booking-header">
                        <h3>Reserve Your Party</h3>
                        <p>Fill out the details below to request a booking</p>
                    </div>
                    <div class="booking-body">
                        @if(session('success'))
                            <div class="alert alert-success fade-in-up">
                                <i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('frontend.partyBooking.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="form-label" for="name">Full Name</label>
                                    <input type="text" id="name" name="name" class="form-control" placeholder="John Doe" required value="{{ old('name') }}">
                                    @error('name')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="form-label" for="phone">Phone Number</label>
                                    <input type="text" id="phone" name="phone" class="form-control" placeholder="01XXX-XXXXXX" required value="{{ old('phone') }}">
                                    @error('phone')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="form-label" for="total_members">Total Guests</label>
                                    <input type="number" id="total_members" name="total_members" class="form-control" placeholder="E.g. 20" min="1" required value="{{ old('total_members') }}">
                                    @error('total_members')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="form-label" for="booking_date">Event Date</label>
                                    <input type="date" id="booking_date" name="booking_date" class="form-control" required value="{{ old('booking_date') }}">
                                    @error('booking_date')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="branch_id">Select Branch</label>
                                <select id="branch_id" name="branch_id" class="form-select" required>
                                    <option value="" disabled selected>Where would you like to host?</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }} ({{ $branch->location }})</option>
                                    @endforeach
                                </select>
                                @error('branch_id')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                            </div>

                            <div class="mt-2">
                                <button type="submit" class="btn-submit">
                                    <i class="fa-solid fa-paper-plane me-2"></i> Send Request
                                </button>
                            </div>
                        </form>

                    <!-- Booking Intel -->
                    <div class="booking-intel mt-4 p-4 fade-in-up" style="background: rgba(17, 107, 131, 0.05); border-radius: 15px; border-left: 4px solid var(--brand); animation-delay: 0.8s;">
                        <h4 style="font-family: 'Poppins', sans-serif; font-size: 1.2rem; color: var(--brand); margin-bottom: 10px;">Party Booking Options</h4>
                        <p style="font-size: 0.95rem; color: var(--text-muted); margin-bottom: 10px;">
                            <strong>Gatherings & Events:</strong> Ideal for Corporate Events, Birthdays, Anniversaries, and Family Get-togethers. We can comfortably host medium to large groups across our premium branches.
                        </p>
                        <p style="font-size: 0.95rem; color: var(--text-muted); margin-bottom: 10px;">
                            <strong>Customized Platters:</strong> Enjoy our famous traditional Mezban, slow-cooked Kacchi Biryani, and authentic Bangla food served in dedicated party platters to share with your guests.
                        </p>
                        <p style="font-size: 0.95rem; color: var(--text-muted); margin-bottom: 0;">
                            <strong>Notice:</strong> We recommend placing your booking request at least 48 hours in advance to ensure the best seating arrangements and menu availability.
                        </p>
                    </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>
@endsection
