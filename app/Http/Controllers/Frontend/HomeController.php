<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Branch;
use App\Models\Category;
use App\Models\FacebookReel;
use App\Models\Member;
use App\Models\Menu;
use App\Models\MenuVariation;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Review;
use App\Models\Setting;
use App\Models\SignaturePlatter;
use App\Services\SSLCommerzService;
use App\Support\OrderRedirect;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function home()
    {
        // Cache categories for 5 minutes to improve performance
        $categories = cache()->remember('home_categories', 300, function () {
            return Category::where('status', 1)
                ->with([
                    'menus' => function ($query) {
                        $query->where('is_available', 1)
                            ->select(['id', 'category_id', 'name', 'slug', 'description', 'is_available'])
                            ->with([
                                'variations' => function ($query) {
                                    $query->select(['id', 'menu_id', 'name', 'price', 'image'])
                                        ->with([
                                            'offers' => function ($q) {
                                                $q->where('is_active', true)
                                                    ->where(function ($subQ) {
                                                        $subQ->whereNull('valid_from')->orWhere('valid_from', '<=', now());
                                                    })
                                                    ->where(function ($subQ) {
                                                        $subQ->whereNull('valid_until')->orWhere('valid_until', '>=', now());
                                                    })
                                                    ->select(['offers.id', 'offers.name', 'offers.discount_percent', 'offers.popup_badge']);
                                            },
                                        ]);
                                },
                            ]);
                    },
                ])
                ->select(['id', 'name', 'status', 'branch_id', 'image'])
                ->orderBy('name')
                ->get();
        });

        // Simple pagination wrapper
        $perPage = 10;
        $page = request()->get('page', 1);
        $paginatedCategories = new LengthAwarePaginator(
            $categories->forPage($page, $perPage),
            $categories->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        $branches = cache()->remember('home_branches', 600, function () {
            return Branch::orderBy('name')->select(['id', 'name', 'location', 'phone', 'slug', 'foodpanda_url', 'pathao_url', 'foodi_url', 'foodpanda_logo', 'pathao_logo', 'foodi_logo'])->get();
        });

        $signaturePlatters = SignaturePlatter::where('status', 1)
            ->orderBy('sort_order')
            ->get();

        $facebookReels = FacebookReel::where('status', true)
            ->orderBy('sort_order')
            ->get();

        $aboutSettings = Setting::where('setting_group', 'about_section')
            ->get()
            ->keyBy('key');

        $contactSettings = Setting::where('setting_group', 'contact_section')
            ->get()
            ->keyBy('key');

        // Fetch last 10 approved reviews
        $reviews = Review::where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Cache popup offer for 5 minutes
        $popupOffer = cache()->remember('home_popup_offer', 300, function () {
            return Offer::where('is_active', true)
                ->where('show_as_popup', true)
                ->where(function ($q) {
                    $q->whereNull('popup_expires_at')
                        ->orWhere('popup_expires_at', '>=', now()->toDateString());
                })
                ->with('menuVariations.menu.category') // Load relationships for redirect
                ->select(['id', 'name', 'description', 'discount_percent', 'popup_image', 'popup_badge', 'popup_expires_at', 'offer_type'])
                ->latest()
                ->first();
        });

        return view('index', [
            'categories' => $paginatedCategories,
            'branches' => $branches,
            'signaturePlatters' => $signaturePlatters,
            'facebookReels' => $facebookReels,
            'aboutSettings' => $aboutSettings,
            'contactSettings' => $contactSettings,
            'popupOffer' => $popupOffer,
            'reviews' => $reviews,
        ]);
    }

    public function addToCart()
    {
        return view('frontend.addtocart');
    }

    public function about()
    {
        $aboutSettings = Setting::where('setting_group', 'about_section')
            ->get()
            ->keyBy('key');

        $videos = FacebookReel::where('status', true)
            ->orderBy('sort_order')
            ->get();

        return view('frontend.about', compact('aboutSettings', 'videos'));
    }

    public function cardApply()
    {
        return view('frontend.apply');
    }

    public function checkout()
    {
        $activeOffers = Offer::where('is_active', true)
            ->where('discount_percent', '>', 0)
            ->where(function ($q) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('valid_until')->orWhere('valid_until', '>=', now());
            })
            ->with([
                'menuVariations' => function ($q) {
                    $q->select(['menu_variations.id', 'menu_id', 'name', 'price']);
                },
            ])
            ->orderBy('discount_percent', 'desc')
            ->get();

        $loggedInMember = Auth::guard('member')->user();
        $loggedInMemberDiscount = $loggedInMember
            ? $loggedInMember->resolveMemberDiscount(1)
            : null;

        return view('frontend.checkout', compact('activeOffers', 'loggedInMember', 'loggedInMemberDiscount'));
    }

    public function cards()
    {
        $offers = Offer::active()->orderBy('created_at', 'desc')->get();

        return view('frontend.cards', compact('offers'));
    }

    public function registerMember(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    if (Member::phoneExists($value)) {
                        $fail('This phone number is already registered. Please sign in at Member Login or use a different number.');
                    }
                },
            ],
            'email' => 'nullable|email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'dob' => 'required|date',
            'marriage_date' => 'nullable|date',
            'address' => 'required|string|max:1000',
            'is_student' => 'sometimes|boolean',
            'terms' => 'accepted',
            'profile_image' => 'nullable|file|image|mimes:webp,png,jpg|max:2048',
        ];

        if ($request->boolean('is_student')) {
            $rules['student_card'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:4096';
        }

        $request->validate($rules);

        $studentCardPath = null;
        if ($request->boolean('is_student') && $request->hasFile('student_card')) {
            $studentCardPath = $request->file('student_card')->store('student_cards', 'public');
        }

        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            $profileImagePath = $request->file('profile_image')->store('profile_images', 'public');
        }

        $member = Member::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => $request->password,
            'dob' => $request->dob,
            'marriage_date' => $request->marriage_date,
            'address' => $request->address,
            'last4' => substr(preg_replace('/\D+/', '', $request->phone), -4),
            'is_student' => $request->boolean('is_student'),
            'approval_status' => $request->boolean('is_student') ? 'pending' : 'approved',
            'student_card_path' => $studentCardPath,
            'profile_image_path' => $profileImagePath,
            'type' => 'membership',
            'status' => 'active',
            'expires_at' => now()->addYear(),
        ]);

        $member->unique_card_number = sprintf('MEM%s_%s', str_pad($member->id, 4, '0', STR_PAD_LEFT), $member->last4);
        $member->save();

        Auth::guard('member')->login($member);

        // Send welcome SMS with card details
        $this->sendWelcomeSms($member);

        $message = 'Membership registered successfully. Your card number is '.$member->unique_card_number;

        if ($member->is_student) {
            $message .= '. Your student membership will be reviewed by admin. First-order discount will be available once approved.';
        } else {
            $message .= '. You can now use 30% first-order discount!';
        }

        $message .= ' Use your phone number and password to access your member dashboard anytime.';

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'card' => $member->unique_card_number,
                'is_student' => $member->is_student,
                'approval_status' => $member->approval_status,
                'redirect_url' => route('frontend.member.dashboard'),
            ]);
        }

        return redirect()->route('frontend.member.dashboard')->with('success', $message);
    }

    public function checkMemberPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
        ]);

        $exists = Member::phoneExists($request->phone);

        return response()->json([
            'available' => ! $exists,
            'message' => $exists
                ? 'This phone number is already registered. Please sign in or use a different number.'
                : 'Phone number is available.',
        ]);
    }

    /**
     * Send welcome SMS to newly registered member
     *
     * @param  Member  $member
     * @return array
     */
    private function sendWelcomeSms($member)
    {
        try {
            // Format phone number to international format
            $phone = format_phone($member->phone);

            // Send welcome SMS
            $response = send_welcome_sms($phone, $member->name);

            if ($response['success']) {
                Log::info('Welcome SMS sent to member', [
                    'member_id' => $member->id,
                    'phone' => $phone,
                    'name' => $member->name,
                ]);
            } else {
                Log::warning('Failed to send welcome SMS to member', [
                    'member_id' => $member->id,
                    'phone' => $phone,
                    'error' => $response['error'] ?? 'Unknown error',
                ]);
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('Exception while sending welcome SMS', [
                'member_id' => $member->id,
                'exception' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function applyGoldenCard(Request $request)
    {
        $request->validate([
            'unique_card_number' => 'required|string',
        ]);

        $member = Member::where('unique_card_number', $request->unique_card_number)->first();

        if (! $member) {
            $msg = 'Membership card number not found.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 404);
            }

            return back()->withErrors(['unique_card_number' => $msg]);
        }

        // Calculate total purchase dynamically from confirmed/completed orders
        $totalPurchase = $member->orders()
            ->whereIn('status', ['completed', 'confirmed'])
            ->sum('final_amount');
        if ($totalPurchase < Member::GOLDEN_UPGRADE_THRESHOLD) {
            $msg = 'You are not eligible for a Golden Card yet. Your total purchase is ৳'.number_format($totalPurchase, 2).', but the eligibility requirement is ৳'.number_format(Member::GOLDEN_UPGRADE_THRESHOLD, 2).'.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return back()->withErrors(['unique_card_number' => $msg]);
        }

        if ($member->type === 'golden') {
            $msg = 'You already have a Golden Card!';
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }

            return back()->with('success', $msg);
        }

        $member->upgradeToGolden();

        $msg = 'Congratulations! Your membership has been upgraded to Golden Card. Your card is now valid for 5 years with a 10% flat discount.';
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return back()->with('success', $msg);
    }

    public function storeOrder(Request $request)
    {
        // 1. Validate the Request
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string',
            'order_total' => 'required|numeric|min:1',
            'items' => 'required|json',
            'payment_method' => 'required|in:cod,sslcommerz',
            'member_card_number' => 'nullable|string|exists:members,unique_card_number',
            'student_card' => 'sometimes|boolean',
        ]);

        // 2. Generate unique Transaction ID
        $tranId = uniqid('ORDER_');

        $member = Auth::guard('member')->user();
        if ($request->filled('member_card_number')) {
            $member = Member::where('unique_card_number', $request->member_card_number)->first();
        }

        // 3. Prepare Order payload and save to Database
        // Map payment method to a safe DB value if enum doesn't support it yet
        $paymentMethod = $request->payment_method;
        if (Schema::hasColumn('orders', 'payment_method')) {
            $col = DB::select("SHOW COLUMNS FROM `orders` LIKE 'payment_method'");
            if (! empty($col) && isset($col[0]->Type) && strpos($col[0]->Type, 'enum(') === 0) {
                $typeDef = $col[0]->Type; // e.g. enum('cod','bkash','sslcommerz','other')
                if (strpos($typeDef, "'{$paymentMethod}'") === false) {
                    // fallback to 'other' when DB enum doesn't include the requested value
                    $paymentMethod = 'other';
                }
            }
        }

        $discountAmount = 0;
        $consumesFirstOrderSlot = false;
        if ($member) {
            $consumesFirstOrderSlot = $member->canUseFirstOrderDiscount();
            $memberDiscount = $member->resolveMemberDiscount((float) $request->order_total);
            $discountAmount = $memberDiscount['amount'];
        }

        // Apply promotional offers discount
        // Check for offers on specific items and calculate discount accordingly
        $items = json_decode($request->items, true);
        $offerDiscount = 0;
        $itemDiscountDetails = [];

        if (is_array($items) && ! empty($items)) {
            foreach ($items as &$item) {
                $menuVariationId = $item['variation_id'] ?? $item['id'] ?? null;
                if (! $menuVariationId) {
                    continue;
                }

                $variation = MenuVariation::find($menuVariationId);
                if (! $variation) {
                    continue;
                }

                // Get active, valid offers for this variation
                $applicableOffers = $variation->activeOffers()
                    ->where('is_active', true)
                    ->orderBy('discount_percent', 'desc')
                    ->get();

                if ($applicableOffers->isNotEmpty()) {
                    $bestOffer = Offer::bestEligibleForMember($applicableOffers, $member);

                    if ($bestOffer) {
                        $itemTotal = ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
                        $itemDiscount = round($itemTotal * ($bestOffer->discount_percent / 100), 2);

                        $offerDiscount += $itemDiscount;
                        $itemDiscountDetails[] = [
                            'variation_id' => $menuVariationId,
                            'offer_id' => $bestOffer->id,
                            'offer_name' => $bestOffer->name,
                            'discount_percent' => $bestOffer->discount_percent,
                            'discount_amount' => $itemDiscount,
                        ];

                        $item['offer_id'] = $bestOffer->id;
                        $item['offer_discount'] = $itemDiscount;
                    }
                }
            }
        }

        // Use the higher discount: member discount or accumulated item offers
        if ($offerDiscount > $discountAmount) {
            $discountAmount = $offerDiscount;
        }

        $orderData = [
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_address' => $request->customer_address,
            'total_amount' => $request->order_total,
            'discount_amount' => $discountAmount,
            'final_amount' => max(0, $request->order_total - $discountAmount),
            'items' => json_encode($items ?? json_decode($request->items, true)),
            'payment_method' => $paymentMethod,
            'status' => 'pending',
            'student_card_used' => $request->boolean('student_card'),
        ];

        if ($member) {
            $orderData['member_id'] = $member->id;
            $orderData['unique_card_number'] = $member->unique_card_number;
        }

        // Conditionally include transaction_id and payment_status only if the columns exist
        if (Schema::hasColumn('orders', 'transaction_id')) {
            $orderData['transaction_id'] = $tranId;
        }

        if (Schema::hasColumn('orders', 'payment_status')) {
            $orderData['payment_status'] = 'unpaid';
        }

        $order = Order::create($orderData);

        if ($member && $consumesFirstOrderSlot) {
            $member->update(['first_order_discount_used' => true]);
        }

        // ============================================
        // 4. Handle Payment Logic
        // ============================================

        // --- CASH ON DELIVERY ---
        if ($request->payment_method === 'cod') {
            $order->creditMemberPurchase();

            return OrderRedirect::respond(
                $request,
                $order,
                'Order placed successfully via Cash on Delivery!',
                true
            );
        }

        // --- SSLCOMMERZ (EASYCHECKOUT POPUP) ---
        if ($request->payment_method === 'sslcommerz') {
            try {
                $sslcommerz = new SSLCommerzService;

                $post_data = [
                    'total_amount' => $order->final_amount,
                    'currency' => 'BDT',
                    'tran_id' => $tranId,
                    'success_url' => route('payment.success'),
                    'fail_url' => route('payment.fail'),
                    'cancel_url' => route('payment.cancel'),
                    'ipn_url' => route('payment.ipn'),
                    'cus_name' => $order->customer_name,
                    'cus_email' => 'customer@example.com',
                    'cus_phone' => $order->customer_phone,
                    'cus_add1' => $order->customer_address,
                    'cus_city' => 'Dhaka',
                    'cus_country' => 'Bangladesh',
                    'shipping_method' => 'NO',
                    'product_name' => 'Food Order',
                    'product_category' => 'Food',
                    'product_profile' => 'general',
                ];

                $sslResponse = $sslcommerz->initiatePayment($post_data);

                if (! empty($sslResponse['success']) && ! empty($sslResponse['gateway_url'])) {
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => true,
                            'redirect_url' => $sslResponse['gateway_url'],
                            'order_id' => $order->id,
                        ]);
                    }

                    return redirect()->away($sslResponse['gateway_url']);
                }

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $sslResponse['message'] ?? 'Payment initialization failed.',
                    ], 422);
                }

                return back()->withErrors(['payment' => $sslResponse['message'] ?? 'Payment initialization failed.']);
            } catch (\Exception $e) {
                Log::error('SSLCommerz Init Error: '.$e->getMessage());
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'System error during payment initiation.'], 500);
                }

                return back()->withErrors(['payment' => 'System error during payment initiation.']);
            }
        }
    }

    public function checkMemberCard(Request $request)
    {
        $request->validate([
            'member_card_number' => 'required|string|exists:members,unique_card_number',
        ]);

        $member = Member::where('unique_card_number', $request->member_card_number)->first();
        if (! $member) {
            return response()->json([
                'eligible' => false,
                'message' => 'Membership card not found.',
            ], 404);
        }

        $orderTotal = (float) $request->query('order_total', 0);
        $discount = $member->resolveMemberDiscount($orderTotal > 0 ? $orderTotal : 1);

        $response = [
            'eligible' => $discount['eligible'],
            'member_name' => $member->name,
            'member_type' => $discount['member_type'] ?? $member->type,
            'total_purchase' => $member->liveTotalPurchase(),
            'discount_rate' => $discount['rate'],
            'first_order_discount_used' => $discount['first_order_discount_used'],
            'message' => $discount['message'],
        ];

        if (isset($discount['is_student'])) {
            $response['is_student'] = $discount['is_student'];
        } else {
            $response['is_student'] = $member->is_student;
        }

        if (isset($discount['approval_status'])) {
            $response['approval_status'] = $discount['approval_status'];
        } elseif ($member->is_student) {
            $response['approval_status'] = $member->approval_status;
        }

        return response()->json($response);
    }

    public function completeMenu(Request $request)
    {
        // 1. Active categories
        $categories = Category::where('status', 1)->get();

        // 2. Dynamic min/max price range
        $minPriceLimit = (float) (MenuVariation::min('price') ?? 0);
        $maxPriceLimit = (float) (MenuVariation::max('price') ?? 1000);

        // 3. Filter params
        $selectedCategories = collect($request->query('categories', []))
            ->filter()
            ->map(fn ($slug) => (string) $slug)
            ->values()
            ->all();

        $legacyCategory = $request->query('category');
        if (empty($selectedCategories) && $legacyCategory) {
            $selectedCategories = [(string) $legacyCategory];
        }

        $selectedCategorySlug = $selectedCategories[0] ?? null;

        $offerFilter = $request->query('offer'); // নির্দিষ্ট offer ID দিয়ে filter
        $offerOnly = filter_var($request->query('offerFilter', false), FILTER_VALIDATE_BOOLEAN);

        // Fresh visit কিনা চেক করা হচ্ছে (কোনো filter param URL-এ আছে কিনা)
        $hasAnyFilterParam = $request->has('categories')
            || $request->has('category')
            || $request->has('offerFilter')
            || $request->has('popularFilter')
            || $request->has('min_price')
            || $request->has('max_price');

        // Fresh visit হলে Popular ডিফল্ট ফিল্টার হবে, নাহলে query থেকে নেওয়া হবে
        $popularOnly = $hasAnyFilterParam
            ? filter_var($request->query('popularFilter', false), FILTER_VALIDATE_BOOLEAN)
            : true;

        $minPrice = $request->query('min_price', $minPriceLimit);
        $maxPrice = $request->query('max_price', $maxPriceLimit);

        // 4. Base query
        $query = Menu::where('is_available', 1)
            ->with([
                'variations' => function ($q) {
                    $q->with([
                        'offers' => function ($offerQuery) {
                            $offerQuery->where('is_active', true)
                                ->where(function ($q) {
                                    $q->whereNull('valid_from')->orWhere('valid_from', '<=', now());
                                })
                                ->where(function ($q) {
                                    $q->whereNull('valid_until')->orWhere('valid_until', '>=', now());
                                })
                                ->select(['offers.id', 'offers.name', 'offers.discount_percent']);
                        },
                    ]);
                },
                'category',
            ]);

        // Category filter
        if (! empty($selectedCategories)) {
            $query->whereHas('category', function ($q) use ($selectedCategories) {
                $q->whereIn('slug', $selectedCategories);
            });
        }

        // Specific offer ID filter
        if ($offerFilter) {
            $query->whereHas('variations.offers', function ($q) use ($offerFilter) {
                $q->where('offers.id', $offerFilter);
            });
        }

        // Generic "has an active offer" filter
        if ($offerOnly) {
            $query->whereHas('variations.offers', function ($q) {
                $q->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('valid_from')->orWhere('valid_from', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('valid_until')->orWhere('valid_until', '>=', now());
                    });
            });
        }

        // Popular items filter (ডিফল্ট বা ইউজার-সিলেক্টেড)
        if ($popularOnly) {
            $query->where('is_popular', 1);
        }

        // Price filter
        $query->whereHas('variations', function ($q) use ($minPrice, $maxPrice) {
            $q->whereBetween('price', [$minPrice, $maxPrice]);
        });

        // 5. Paginate
        $menus = $query->orderBy('name')->paginate(9)->withQueryString();

        // 6. Active offer details if filtering by offer
        $activeOfferDetails = null;
        if ($offerFilter) {
            $activeOfferDetails = Offer::where('id', $offerFilter)
                ->where('is_active', true)
                ->select(['id', 'name', 'discount_percent', 'description'])
                ->first();
        }

        // 7. AJAX response
        if ($request->ajax()) {
            return view('frontend.partials.menu_grid', compact('menus'))->render();
        }

        return view('frontend.completeMenu', compact(
            'categories',
            'menus',
            'minPriceLimit',
            'maxPriceLimit',
            'selectedCategorySlug',
            'selectedCategories',
            'minPrice',
            'maxPrice',
            'offerFilter',
            'offerOnly',
            'popularOnly',
            'activeOfferDetails'
        ));
    }

    public function partyBooking()
    {
        $branches = Branch::where('status', 1)->get();

        return view('frontend.party_booking', compact('branches'));
    }

    public function storePartyBooking(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'total_members' => 'required|integer|min:1',
            'booking_date' => 'required|date|after_or_equal:today',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $existingPendingBooking = Booking::where('phone', $request->phone)
            ->where('status', 'pending')
            ->first();

        if ($existingPendingBooking) {
            return back()->withInput()->withErrors(['phone' => 'You already have a pending booking request. Please wait for our confirmation before booking again.']);
        }

        Booking::create($validated);

        return back()->with('success', 'Your party booking request has been submitted successfully! We will contact you soon.');
    }
}
