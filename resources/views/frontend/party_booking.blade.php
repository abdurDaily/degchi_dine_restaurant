@extends('frontend.layout')

@section('meta_title', 'Party Booking')
@section('meta_description', 'Reserve Degchi Dine for birthdays, anniversaries, corporate events and family
    get-togethers. Spacious venues, customized platters and premium hospitality.')

@section('frontend_content')
    <x-frontend.page-header title="পার্টি আয়োজনের জন্য বুকিং করুন" eyebrow="পার্টি বুকিং"
        subtitle="আপনার বিশেষ আয়োজনের জন্য উপভোগ করুন ঐতিহ্যবাহী স্বাদ ও প্রিমিয়াম আতিথেয়তার চমৎকার সমন্বয়।"
        :backLink="route('frontend.home')" backText="হোমে ফিরে যান">
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
                                <h2 class="party-booking-info-title">আমাদের সঙ্গে উদযাপন করুন</h2>
                                <p class="party-booking-info-tagline">প্রশস্ত আয়োজন · পছন্দমতো মেনু · প্রিমিয়াম সেবা</p>
                            </div>
                            <span class="party-booking-open-badge"><i class="bi bi-people-fill me-1"></i> ২০+ অতিথি</span>
                        </div>

                        {{-- Media Highlight --}}
                        <div class="party-booking-media">
                            <video class="party-booking-video" autoplay muted loop playsinline preload="metadata"
                                poster="https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&w=800&q=75">
                                <source src="{{ asset('assets/frontend/video/PartyHallDegciDine.mp4') }}"
                                    type="video/mp4" />
                            </video>
                            <div class="party-booking-media-overlay">
                                <i class="bi bi-camera-reels-fill"></i>
                                <span>অথেন্টিক ডাইনিং অভিজ্ঞতা</span>
                            </div>
                        </div>

                        {{-- Features --}}
                        <div class="party-booking-features">
                            <article class="party-booking-feature">
                                <div class="party-booking-feature-icon"><i class="bi bi-people-fill"></i></div>
                                <div class="party-booking-feature-body">
                                    <h3>প্রশস্ত আয়োজন</h3>
                                    <p>বড় আয়োজন ও পরিবারের জন্য আরামদায়ক বসার ব্যবস্থা।</p>
                                </div>
                            </article>

                            <article class="party-booking-feature">
                                <div class="party-booking-feature-icon"><i class="bi bi-egg-fried"></i></div>
                                <div class="party-booking-feature-body">
                                    <h3>পছন্দমতো মেনু</h3>
                                    <p>আমাদের বিশেষ খাবার দিয়ে আপনার পার্টির মেনু সাজিয়ে নিন।</p>
                                </div>
                            </article>

                            <article class="party-booking-feature">
                                <div class="party-booking-feature-icon party-booking-feature-icon-gold"><i
                                        class="bi bi-star-fill"></i></div>
                                <div class="party-booking-feature-body">
                                    <h3>প্রিমিয়াম আতিথেয়তা</h3>
                                    <p>আপনার আয়োজনকে নির্বিঘ্ন ও স্মরণীয় করতে বিশেষ সেবা।</p>
                                </div>
                            </article>
                        </div>

                        <div class="party-booking-actions">
                            <a href="{{ route('frontend.completeMenu') }}" class="btn-dine btn-dine-primary btn-dine-sm">
                                <i class="bi bi-bag-check-fill"></i>
                                সম্পূর্ণ মেনু দেখুন
                            </a>
                            <a href="{{ route('frontend.contact') }}" class="btn-dine btn-dine-ghost btn-dine-sm">
                                <i class="bi bi-headset"></i>
                                আমাদের সঙ্গে কথা বলুন
                            </a>
                        </div>
                    </div>
                </aside>

                {{-- Right Side: Booking Form --}}
                <div class="party-booking-forms">
                    <div class="party-booking-form-sticky">
                        <div class="cp-form-card party-booking-card">
                            <div class="cp-form-card-head">
                                <span class="cp-form-step">বুকিং অনুরোধ</span>
                                <h3>পার্টি আয়োজনের জন্য বুকিং করুন</h3>
                                <p>নিচের তথ্যগুলো পূরণ করুন। আমাদের টিম ২৪ ঘণ্টার মধ্যে আপনার আয়োজনের বুকিং নিশ্চিত করবে।
                                </p>
                            </div>

                            @if (session('success'))
                                <div class="party-booking-alert" role="alert">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <div>
                                        <strong>অনুরোধ গ্রহণ করা হয়েছে!</strong>
                                        <span>{{ session('success') }}</span>
                                    </div>
                                </div>
                            @endif

                            <form action="{{ route('frontend.partyBooking.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="cp-field">
                                            <label class="cp-label" for="name">পূর্ণ নাম</label>
                                            <input type="text" id="name" name="name" class="cp-input"
                                                placeholder="যেমন: রহিম উদ্দিন" required value="{{ old('name') }}">
                                            @error('name')
                                                <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="cp-field">
                                            <label class="cp-label" for="phone">ফোন নম্বর</label>
                                            <input type="text" id="phone" name="phone" class="cp-input"
                                                placeholder="০১XXX-XXXXXX" required value="{{ old('phone') }}">
                                            @error('phone')
                                                <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="cp-field">
                                            <label class="cp-label" for="total_members">মোট অতিথি</label>
                                            <input type="number" id="total_members" name="total_members" class="cp-input"
                                                placeholder="যেমন: ২০" min="1" required
                                                value="{{ old('total_members') }}">
                                            @error('total_members')
                                                <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="cp-field">
                                            <label class="cp-label" for="booking_date">অনুষ্ঠানের তারিখ</label>
                                            <input type="date" id="booking_date" name="booking_date" class="cp-input"
                                                required value="{{ old('booking_date') }}">
                                            @error('booking_date')
                                                <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="cp-field">
                                    <label class="cp-label" for="branch_id">শাখা নির্বাচন করুন</label>
                                    <select id="branch_id" name="branch_id" class="cp-input party-booking-select"
                                        required>
                                        <option value="" disabled selected>কোথায় আয়োজন করতে চান?</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }} ({{ $branch->location }})</option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                    @enderror
                                </div>

                                <button type="submit" class="btn-dine btn-dine-primary btn-dine-100">
                                    <i class="bi bi-send-fill me-2"></i> অনুরোধ পাঠান
                                </button>
                            </form>

                            {{-- Booking Intel --}}
                            <div class="party-booking-intel">
                                <div class="party-booking-intel-icon"><i class="bi bi-clipboard-check-fill"></i></div>
                                <div>
                                    <h4>পার্টি বুকিংয়ের সুবিধা</h4>
                                    <p><strong>আয়োজন ও অনুষ্ঠান:</strong> কর্পোরেট অনুষ্ঠান, জন্মদিন, বিবাহবার্ষিকী এবং
                                        পারিবারিক আয়োজনের জন্য উপযুক্ত। আমাদের প্রিমিয়াম শাখাগুলোতে মাঝারি থেকে বড় আয়োজনের
                                        জন্য আরামদায়ক ব্যবস্থা রয়েছে।</p>
                                    <p><strong>পছন্দমতো প্ল্যাটার:</strong> আমাদের জনপ্রিয় ঐতিহ্যবাহী মেজবান, ধীরে রান্না
                                        করা কাচ্চি বিরিয়ানি এবং খাঁটি বাংলা খাবার উপভোগ করুন বিশেষ পার্টি প্ল্যাটারে, যা
                                        আপনার অতিথিদের সঙ্গে ভাগ করে উপভোগ করতে পারবেন।</p>
                                    <p class="mb-0"><strong>বিশেষ দ্রষ্টব্য:</strong> সর্বোত্তম বসার ব্যবস্থা ও মেনুর
                                        প্রাপ্যতা নিশ্চিত করতে আমরা অন্তত ৪৮ ঘণ্টা আগে বুকিংয়ের অনুরোধ করার পরামর্শ দিই।</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
