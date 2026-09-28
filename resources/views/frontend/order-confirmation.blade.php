@extends('frontend.layout')

@section('meta_title', 'Order Confirmation')
@section('meta_robots', 'noindex, nofollow')

@section('frontend_content')
    @php
        $statusClass = match ($order->status) {
            'completed' => 'oc-badge-completed',
            'confirmed' => 'oc-badge-confirmed',
            'canceled' => 'oc-badge-canceled',
            default => 'oc-badge-pending',
        };
        $items = is_array($orderItems ?? null) ? $orderItems : [];
    @endphp

    <style>
        /* Order confirmation — aligned with checkout theme */
        .order-confirm .oc-badge {
            display: inline-block;
            padding: 0.3rem 0.75rem;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: capitalize;
        }

        .order-confirm .oc-badge-pending {
            background: var(--secondary-10);
            color: var(--brand-secondary);
        }

        .order-confirm .oc-badge-confirmed {
            background: rgba(16, 185, 129, 0.15);
            color: #37d28a;
        }

        .order-confirm .oc-badge-completed {
            background: rgba(16, 185, 129, 0.15);
            color: #37d28a;
        }

        .order-confirm .oc-badge-canceled {
            background: rgba(239, 68, 68, 0.15);
            color: #ff6b6b;
        }

        .order-confirm .oc-timeline {
            position: relative;
        }

        .order-confirm .oc-timeline-step {
            position: relative;
            padding: 0 0 1.5rem 2rem;
        }

        .order-confirm .oc-timeline-step:last-child {
            padding-bottom: 0;
        }

        .order-confirm .oc-timeline-step::before {
            content: '';
            position: absolute;
            left: 6px;
            top: 18px;
            bottom: 0;
            width: 2px;
            background: var(--card-dark-border);
        }

        .order-confirm .oc-timeline-step:last-child::before {
            display: none;
        }

        .order-confirm .oc-timeline-dot {
            position: absolute;
            left: 0;
            top: 0;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 2px solid var(--card-dark-border-hover);
            background: var(--card-dark-bg);
            z-index: 1;
        }

        .order-confirm .oc-timeline-step.is-done .oc-timeline-dot {
            background: var(--brand-secondary);
            border-color: var(--brand-secondary);
            box-shadow: 0 0 0 4px var(--secondary-10);
        }

        .order-confirm .oc-timeline-step.is-done .oc-timeline-dot::after {
            content: '✓';
            position: absolute;
            inset: -3px;
            color: #fff;
            font-size: 0.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .order-confirm .oc-timeline-step.is-active .oc-timeline-dot {
            border-color: var(--brand-secondary);
            box-shadow: 0 0 0 4px var(--secondary-10), 0 0 18px var(--secondary-25);
            animation: oc-pulse 1.6s ease-in-out infinite;
        }

        @keyframes oc-pulse {

            0%,
            100% {
                box-shadow: 0 0 0 4px var(--secondary-10), 0 0 12px var(--secondary-25);
            }

            50% {
                box-shadow: 0 0 0 6px var(--secondary-10), 0 0 22px var(--secondary-30);
            }
        }

        .order-confirm .oc-timeline-step.is-canceled .oc-timeline-dot {
            background: #ff6b6b;
            border-color: #ff6b6b;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15);
        }

        .order-confirm .oc-timeline-step.is-canceled .oc-timeline-dot::after {
            content: '✕';
            position: absolute;
            inset: -3px;
            color: #fff;
            font-size: 0.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .order-confirm .oc-timeline-step strong {
            display: block;
            font-size: 0.92rem;
            font-weight: 600;
            color: var(--text-on-dark);
        }

        .order-confirm .oc-timeline-step span {
            display: block;
            font-size: 0.8rem;
            color: var(--text-on-dark-muted);
            margin-top: 3px;
        }

        .order-confirm .oc-timeline-step.is-canceled strong {
            color: #ff6b6b;
        }

        .order-confirm .oc-remarks {
            background: var(--secondary-10);
            border: 1px solid var(--card-dark-border);
            border-radius: 12px;
            padding: 0.85rem 1rem;
            margin-top: 1rem;
        }

        .order-confirm .oc-remarks small {
            display: block;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-on-dark-subtle);
            margin-bottom: 4px;
        }

        .order-confirm .oc-remarks p {
            margin: 0;
            font-size: 0.88rem;
            color: var(--text-on-dark);
        }

        .order-confirm .oc-action-btn {
            flex: 1;
            min-width: 140px;
        }

        @media (max-width: 576px) {
            .order-confirm .oc-action-btn {
                flex: 1 1 100%;
                min-width: 100%;
                font-size: 0.98rem;
                padding: 0.95rem 1rem;
            }
        }

        .order-confirm .oc-action-btn-outline {
            background: transparent;
            border: 1.5px solid var(--brand-secondary);
            color: var(--brand-secondary);
            box-shadow: none;
        }

        .order-confirm .oc-action-btn-outline:hover {
            background: var(--secondary-10);
            transform: translateY(-2px);
            color: var(--brand-secondary);
        }

        .order-confirm .oc-contact-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
        }
    </style>

    <x-frontend.page-header title="অর্ডার #{{ $order->id }}" :subtitle="!empty($needsPhoneVerification) ? 'আপনার অর্ডারের অবস্থা ও রসিদ দেখতে চেকআউটে ব্যবহৃত ফোন নম্বরটি দিন।' : ($order->status === 'canceled' ? 'এই অর্ডারটি বাতিল করা হয়েছে। নিচে বিস্তারিত দেখুন অথবা সহায়তার জন্য আমাদের সাথে যোগাযোগ করুন।' : 'হ্যালো ' . explode(' ', $order->customer_name)[0] . ' — আপনার অর্ডারের বর্তমান অবস্থা ও সম্পূর্ণ রসিদ নিচে দেখুন।')" eyebrow="অর্ডার নিশ্চিতকরণ">
        <x-slot:eyebrowIcon>
            @if (!empty($needsPhoneVerification))
                <iconify-icon icon="solar:lock-keyhole-linear"></iconify-icon>
            @elseif ($order->status === 'canceled')
                <iconify-icon icon="solar:close-circle-bold"></iconify-icon>
            @elseif ($order->status === 'completed')
                <iconify-icon icon="solar:check-circle-bold"></iconify-icon>
            @else
                <iconify-icon icon="solar:delivery-linear"></iconify-icon>
            @endif
        </x-slot:eyebrowIcon>
        <x-slot:meta>
            <span><i class="bi bi-bag-check"></i> কার্ট</span>
            <span><i class="bi bi-chevron-right"></i></span>
            <span><i class="bi bi-credit-card"></i> চেকআউট</span>
            <span><i class="bi bi-chevron-right"></i></span>
            <span style="opacity: 0.5;"><i class="bi bi-check-circle"></i> নিশ্চিত</span>
        </x-slot:meta>
    </x-frontend.page-header>

    <section class="cart-page-section order-confirm">
        <div class="container px-4 px-lg-5">

            @if (!empty($needsPhoneVerification))
                <div class="row justify-content-center">
                    <div class="col-lg-7 col-md-9">
                        <div class="checkout-form-panel">
                            <h5 class="checkout-form-heading">
                                <span class="checkout-form-heading-icon"><i class="bi bi-shield-lock"></i></span>
                                অর্ডার দেখতে যাচাই করুন
                            </h5>
                            <p class="mb-4"
                                style="color: var(--text-on-dark-muted); font-size: 0.9rem; line-height: 1.6;">
                                আপনার নিরাপত্তার জন্য, ট্র্যাকিংয়ের বিস্তারিত দেখতে অর্ডার <strong
                                    style="color: var(--text-on-dark);">#{{ $order->id }}</strong>-এর সাথে ব্যবহৃত ফোন
                                নম্বরটি নিশ্চিত করুন।
                            </p>

                            @if ($errors->any())
                                <div class="alert alert-danger border-0 mb-4"
                                    style="border-radius: 12px; padding: 14px 18px; font-size: 0.88rem;">
                                    {{ $errors->first() }}</div>
                            @endif

                            <form method="POST" action="{{ route('frontend.order.track.submit') }}">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->id }}">

                                <div class="checkout-input-wrap">
                                    <i class="bi bi-telephone checkout-input-icon"></i>
                                    <input type="tel" name="phone" id="oc_verify_phone"
                                        class="form-control checkout-input" placeholder="চেকআউটে ব্যবহৃত ফোন নম্বর"
                                        value="{{ old('phone') }}" required autofocus />
                                </div>

                                <button type="submit"
                                    class="btn checkout-btn-primary d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-search"></i> অর্ডারের বিস্তারিত দেখুন
                                </button>
                            </form>

                            <div class="text-center mt-4 pt-3" style="border-top: 1px solid var(--card-dark-border);">
                                <p class="mb-3" style="color: var(--text-on-dark-muted); font-size: 0.85rem;">আপনার কি
                                    মেম্বার অ্যাকাউন্ট আছে?</p>
                                <div class="d-flex justify-content-center flex-wrap gap-3">
                                    <a href="{{ route('frontend.member.login') }}" class="checkout-continue-link">
                                        <i class="bi bi-person me-1"></i> মেম্বার লগইন
                                    </a>
                                    <a href="{{ route('frontend.order.track') }}" class="checkout-continue-link">
                                        <i class="bi bi-search me-1"></i> অন্য অর্ডার ট্র্যাক করুন
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row g-4 align-items-start">

                    {{-- Left: order details --}}
                    <div class="col-lg-7">
                        <div class="checkout-form-panel">
                            <h5 class="checkout-form-heading">
                                <span class="checkout-form-heading-icon"><iconify-icon
                                        icon="solar:clipboard-list-linear"></iconify-icon></span>
                                অর্ডারের বিস্তারিত
                            </h5>

                            <div class="checkout-summary-row">
                                <span>অর্ডার নম্বর</span>
                                <span class="checkout-summary-val">#{{ $order->id }}</span>
                            </div>
                            <div class="checkout-summary-row">
                                <span>অর্ডারের সময়</span>
                                <span class="checkout-summary-val">{{ $order->created_at->format('d M Y, h:i A') }}</span>
                            </div>
                            <div class="checkout-summary-row">
                                <span>পেমেন্ট</span>
                                <span class="checkout-summary-val">{{ strtoupper($order->payment_method ?? 'N/A') }}</span>
                            </div>
                            <div class="checkout-summary-row">
                                <span>অবস্থা</span>
                                <span><span
                                        class="oc-badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span></span>
                            </div>
                            @if ($order->status === 'canceled' && !empty($order->status_remarks))
                                <div class="checkout-summary-row">
                                    <span>বাতিলের কারণ</span>
                                    <span class="checkout-summary-val"
                                        style="color: #ff6b6b; text-align: right;">{{ $order->status_remarks }}</span>
                                </div>
                            @endif
                            <div class="checkout-summary-divider"></div>
                            <div class="checkout-summary-row">
                                <span>ডেলিভারি ঠিকানা</span>
                                <span class="checkout-summary-val"
                                    style="text-align: right; max-width: 60%;">{{ $order->customer_address }}</span>
                            </div>
                            <div class="checkout-summary-row">
                                <span>গ্রাহকের ফোন</span>
                                <span class="checkout-summary-val">{{ $order->customer_phone }}</span>
                            </div>
                            <div class="checkout-summary-divider"></div>
                            <div class="checkout-summary-total-row">
                                <span>অর্ডারের মোট</span>
                                <strong
                                    class="checkout-summary-total-val">৳{{ number_format($order->final_amount, 2) }}</strong>
                            </div>
                        </div>

                        @if (!empty($items))
                            <div class="checkout-form-panel">
                                <h5 class="checkout-form-heading">
                                    <span class="checkout-form-heading-icon"><iconify-icon
                                            icon="solar:bag-3-linear"></iconify-icon></span>
                                    অর্ডার করা খাবার
                                </h5>

                                @foreach ($items as $item)
                                    @php
                                        $qty = $item['quantity'] ?? 1;
                                        $price = $item['price'] ?? 0;
                                        $title = $item['title'] ?? ($item['name'] ?? 'Item');
                                        $image = $item['image'] ?? null;
                                        $note = $item['note'] ?? '';
                                    @endphp
                                    <div class="checkout-order-item">
                                        @if ($image)
                                            <div class="checkout-order-img-wrap">
                                                <img src="{{ $image }}" alt="{{ $title }}"
                                                    class="checkout-order-img" />
                                            </div>
                                        @endif
                                        <div class="checkout-order-body">
                                            <div class="checkout-order-top">
                                                <p class="checkout-order-name">{{ $title }}</p>
                                                <span
                                                    class="checkout-order-tag">{{ $note }}{{ $note && $qty ? ' · ' : '' }}পরিমাণ:
                                                    {{ $qty }}</span>
                                            </div>
                                            <div class="checkout-order-bottom">
                                                <span class="checkout-order-price">৳{{ number_format($price, 2) }} ×
                                                    {{ $qty }}</span>
                                                <span
                                                    class="checkout-order-subtotal">৳{{ number_format($price * $qty, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="mt-3 pt-2">
                                    <div class="checkout-summary-row">
                                        <span>সাবটোটাল</span>
                                        <span
                                            class="checkout-summary-val">৳{{ number_format($order->total_amount, 2) }}</span>
                                    </div>
                                    @if ((float) $order->discount_amount > 0)
                                        <div class="checkout-summary-row fw-semibold">
                                            <span>ছাড়</span>
                                            <span>- ৳{{ number_format($order->discount_amount, 2) }}</span>
                                        </div>
                                    @endif
                                    <div class="checkout-summary-divider"></div>
                                    <div class="checkout-summary-total-row">
                                        <span>মোট পরিশোধ</span>
                                        <strong
                                            class="checkout-summary-total-val">৳{{ number_format($order->final_amount, 2) }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="d-flex flex-wrap gap-2 mt-1">
                            @auth('member')
                                <a href="{{ route('frontend.member.dashboard', ['order' => $order->id]) }}"
                                    class="btn checkout-btn-primary oc-action-btn d-flex align-items-center justify-content-center gap-2">
                                    <iconify-icon icon="solar:widget-5-linear"></iconify-icon> আমার ড্যাশবোর্ড
                                </a>
                            @endauth
                            <a href="{{ route('frontend.order.invoice.download', $order->id) }}"
                                class="btn checkout-btn-primary oc-action-btn d-flex align-items-center justify-content-center gap-2">
                                <iconify-icon icon="solar:download-linear"></iconify-icon> ইনভয়েস ডাউনলোড করুন
                            </a>
                            <a href="{{ route('frontend.completeMenu') }}"
                                class="btn checkout-btn-primary oc-action-btn d-flex align-items-center justify-content-center gap-2">
                                <iconify-icon icon="solar:chef-hat-linear"></iconify-icon> আবার অর্ডার করুন
                            </a>
                            <a href="{{ route('frontend.home') }}"
                                class="btn checkout-btn-primary oc-action-btn oc-action-btn-outline d-flex align-items-center justify-content-center gap-2">
                                <iconify-icon icon="solar:home-2-linear"></iconify-icon> হোমে ফিরে যান
                            </a>
                        </div>
                    </div>

                    {{-- Right: status + help --}}
                    <div class="col-lg-5">
                        <div class="checkout-summary-card checkout-summary-sticky">
                            <div class="checkout-summary-header">
                                <iconify-icon icon="solar:delivery-linear" class="me-2"
                                    style="color: var(--brand-secondary); font-size: 1.3rem;"></iconify-icon>
                                অর্ডারের অগ্রগতি
                            </div>
                            <div class="checkout-summary-body">
                                <div class="oc-timeline">
                                    @php
                                        $steps = [
                                            [
                                                'key' => 'pending',
                                                'label' => 'অর্ডার গ্রহণ করা হয়েছে',
                                                'desc' => 'আমরা আপনার অর্ডার পেয়েছি',
                                            ],
                                            [
                                                'key' => 'confirmed',
                                                'label' => 'নিশ্চিত হয়েছে',
                                                'desc' => 'রান্নাঘরে আপনার খাবার প্রস্তুত করা হচ্ছে',
                                            ],
                                            [
                                                'key' => 'completed',
                                                'label' => 'ডেলিভারি সম্পন্ন',
                                                'desc' => 'আপনার খাবার উপভোগ করুন!',
                                            ],
                                        ];
                                        $statusIndex = match ($order->status) {
                                            'pending' => 0,
                                            'confirmed' => 1,
                                            'completed' => 2,
                                            default => -1,
                                        };
                                        $isCanceled = $order->status === 'canceled';
                                    @endphp

                                    @if ($isCanceled)
                                        <div class="oc-timeline-step is-done">
                                            <div class="oc-timeline-dot"></div>
                                            <strong>অর্ডার গ্রহণ করা হয়েছে</strong>
                                            <span>আমরা আপনার অর্ডার পেয়েছি</span>
                                        </div>
                                        <div class="oc-timeline-step is-canceled">
                                            <div class="oc-timeline-dot"></div>
                                            <strong>অর্ডার বাতিল করা হয়েছে</strong>
                                            <span>এই অর্ডারটি ডেলিভারি করা হবে না।</span>
                                        </div>
                                        @if (!empty($order->status_remarks))
                                            <div class="oc-remarks">
                                                <small>মন্তব্য</small>
                                                <p>{{ $order->status_remarks }}</p>
                                            </div>
                                        @endif
                                    @else
                                        @foreach ($steps as $i => $step)
                                            @php
                                                $isDone = $i <= $statusIndex;
                                                $isActive = $i === $statusIndex && $order->status !== 'completed';
                                            @endphp
                                            <div
                                                class="oc-timeline-step {{ $isDone ? 'is-done' : '' }} {{ $isActive ? 'is-active' : '' }}">
                                                <div class="oc-timeline-dot"></div>
                                                <strong>{{ $step['label'] }}</strong>
                                                <span>{{ $step['desc'] }}</span>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="checkout-summary-card mt-4">
                            <div class="checkout-summary-header">
                                <i class="bi bi-phone me-2"
                                    style="color: var(--brand-secondary); font-size: 1.15rem;"></i>
                                সাহায্য প্রয়োজন?
                            </div>
                            <div class="checkout-summary-body">
                                <p
                                    style="color: var(--text-on-dark-muted); font-size: 0.88rem; line-height: 1.6; margin-bottom: 1rem;">
                                    অর্ডার <strong style="color: var(--text-on-dark);">#{{ $order->id }}</strong>-এর
                                    আপডেট বা কোনো বিষয়ে জানতে আমাদের কল করুন এবং আপনার অর্ডার নম্বরটি উল্লেখ করুন।
                                </p>
                                <a href="tel:{{ preg_replace('/\s+/', '', $contactPhone) }}"
                                    class="btn checkout-btn-primary oc-contact-btn">
                                    <iconify-icon icon="solar:phone-linear"></iconify-icon>
                                    {{ $contactPhone }} নম্বরে কল করুন
                                </a>
                            </div>
                        </div>

                        <div class="checkout-summary-card mt-4">
                            <div class="checkout-summary-header">
                                <iconify-icon icon="solar:bookmark-linear" class="me-2"
                                    style="color: var(--brand-secondary); font-size: 1.3rem;"></iconify-icon>
                                পরে এই অর্ডারটি দেখুন
                            </div>
                            <div class="checkout-summary-body">
                                <p
                                    style="color: var(--text-on-dark-muted); font-size: 0.88rem; line-height: 1.6; margin-bottom: 1rem;">
                                    আপনার <strong style="color: var(--text-on-dark);">অর্ডার #{{ $order->id }}</strong>
                                    এবং ফোন নম্বর <strong
                                        style="color: var(--text-on-dark);">{{ $order->customer_phone }}</strong> সংরক্ষণ
                                    করে রাখুন।
                                    যেকোনো সময় <a href="{{ route('frontend.order.track') }}"
                                        class="text-decoration-underline fw-semibold"
                                        style="color: var(--brand-secondary);">অর্ডার ট্র্যাক করুন</a> পেজে গিয়ে আবার এই
                                    অর্ডারটি দেখতে পারবেন।
                                </p>
                                <a href="{{ route('frontend.order.track', ['order' => $order->id]) }}"
                                    class="btn checkout-btn-primary oc-action-btn-outline d-flex align-items-center justify-content-center gap-2">
                                    <iconify-icon icon="solar:magnifer-linear"></iconify-icon>
                                    অর্ডার #{{ $order->id }} ট্র্যাক করুন
                                </a>
                            </div>
                        </div>

                        <div class="checkout-summary-card mt-4">
                            @guest('member')
                                <div class="checkout-summary-header">
                                    <iconify-icon icon="solar:card-2-linear" class="me-2"
                                        style="color: var(--brand-secondary); font-size: 1.3rem;"></iconify-icon>
                                    মেম্বার অ্যাকাউন্ট খুলুন
                                </div>
                                <div class="checkout-summary-body">
                                    <p
                                        style="color: var(--text-on-dark-muted); font-size: 0.88rem; line-height: 1.6; margin-bottom: 1rem;">
                                        একটি মেম্বারশিপ কার্ডের জন্য আবেদন করুন। এতে একটি ড্যাশবোর্ড থেকে আপনার সব অর্ডার
                                        ট্র্যাক করতে পারবেন এবং বিশেষ ছাড় উপভোগ করতে পারবেন।
                                    </p>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="{{ route('frontend.card.apply') }}"
                                            class="btn checkout-btn-primary oc-action-btn d-flex align-items-center justify-content-center gap-2">
                                            এখনই আবেদন করুন
                                        </a>
                                        <a href="{{ route('frontend.member.login') }}"
                                            class="btn checkout-btn-primary oc-action-btn oc-action-btn-outline d-flex align-items-center justify-content-center gap-2">
                                            মেম্বার লগইন
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="checkout-summary-header">
                                    <iconify-icon icon="solar:widget-5-linear" class="me-2"
                                        style="color: var(--brand-secondary); font-size: 1.3rem;"></iconify-icon>
                                    মেম্বার অ্যাকাউন্ট
                                </div>
                                <div class="checkout-summary-body">
                                    <p
                                        style="color: var(--text-on-dark-muted); font-size: 0.88rem; line-height: 1.6; margin-bottom: 1rem;">
                                        আপনার ড্যাশবোর্ড থেকে সব অর্ডার ও মেম্বারশিপের বিস্তারিত দেখুন।
                                    </p>
                                    <a href="{{ route('frontend.member.dashboard') }}"
                                        class="btn checkout-btn-primary oc-action-btn d-flex align-items-center justify-content-center gap-2">
                                        <iconify-icon icon="solar:widget-5-linear"></iconify-icon>
                                        ড্যাশবোর্ড খুলুন
                                    </a>
                                </div>
                            @endguest
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('front_js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (new URLSearchParams(window.location.search).get('clear_cart') === '1') {
                localStorage.removeItem('degchi_cart');
            }
        });
    </script>
@endpush
