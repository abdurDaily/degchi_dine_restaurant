@extends('frontend.layout')

@section('meta_title', 'Party Booking')

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
                        <video class="hero-video" autoplay muted loop playsinline preload="metadata"
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
