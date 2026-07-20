<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MemberAuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('member')->check()) {
            return redirect()->route('frontend.member.dashboard');
        }

        return view('frontend.member.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = trim($request->login);
        $member = Member::findByPhoneOrCard($login);

        if (!$member || !$member->password || !Hash::check($request->password, $member->password)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid phone/card number or password.',
                ], 422);
            }

            return back()->withErrors(['login' => 'Invalid phone/card number or password.'])->withInput();
        }

        if ($member->status !== 'active') {
            $message = $member->accountRestrictedMessage();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message, 'account_restricted' => true], 403);
            }
            return back()->withErrors(['login' => $message])->withInput();
        }

        Auth::guard('member')->login($member, $request->boolean('remember'));

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Welcome back, ' . $member->name . '!',
                'redirect_url' => route('frontend.member.dashboard'),
            ]);
        }

        return redirect()->intended(route('frontend.member.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('member')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('frontend.home')->with('success', 'You have been logged out.');
    }

    public function dashboard(Request $request)
    {
        $member = Auth::guard('member')->user();
        $orders = Order::where('member_id', $member->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        $highlightOrder = null;
        if ($request->filled('order')) {
            $highlightOrder = Order::where('member_id', $member->id)
                ->where('id', $request->order)
                ->first();
        }

        return view('frontend.member.dashboard', compact('member', 'orders', 'highlightOrder'));
    }

    public function updateProfile(Request $request)
    {
        $member = Auth::guard('member')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) use ($member) {
                    $other = Member::findByPhoneOrCard($value);
                    if ($other && (int) $other->id !== (int) $member->id) {
                        $fail('This phone number is already registered to another member.');
                    }
                },
            ],
            'email' => 'nullable|email|max:255',
            'dob' => 'nullable|date',
            'marriage_date' => 'nullable|date',
            'address' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|file|image|mimes:webp,png,jpg,jpeg|max:2048',
        ]);

        $member->name = $validated['name'];
        $member->phone = $validated['phone'];
        $member->email = $validated['email'] ?? null;
        $member->dob = $validated['dob'] ?? null;
        $member->marriage_date = $validated['marriage_date'] ?? null;
        $member->address = $validated['address'] ?? null;
        $member->last4 = substr(preg_replace('/\D+/', '', $validated['phone']), -4) ?: $member->last4;

        if ($request->hasFile('profile_image')) {
            $member->profile_image_path = $request->file('profile_image')->store('profile_images', 'public');
        }

        // Card number is intentionally never updated from the dashboard.
        $member->save();

        return redirect()
            ->route('frontend.member.dashboard')
            ->with('success', 'Your profile has been updated successfully.');
    }

    public function showTrackOrder(Request $request)
    {
        $member = Auth::guard('member')->user();

        return view('frontend.track-order', [
            'prefillOrderId' => $request->query('order'),
            'prefillPhone' => old('phone', $member?->phone),
            'member' => $member,
        ]);
    }

    public function trackOrder(Request $request)
    {
        $member = Auth::guard('member')->user();

        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'phone' => $member ? 'nullable|string|max:20' : 'required|string|max:20',
        ]);

        $order = Order::findOrFail($request->order_id);

        if ($member) {
            if ((int) $order->member_id === (int) $member->id) {
                return redirect()->route('frontend.order.confirmation', $order);
            }

            $phoneToCheck = $request->filled('phone') ? $request->phone : $member->phone;
            if ($this->phonesMatch($order->customer_phone, $phoneToCheck)) {
                return redirect()->route('frontend.order.confirmation', $order);
            }

            return back()
                ->withInput()
                ->withErrors(['order_id' => 'This order was not found on your account. Check the order number or use the phone from checkout.']);
        }

        if (!$this->phonesMatch($order->customer_phone, $request->phone)) {
            return back()
                ->withInput()
                ->withErrors(['phone' => 'Phone number does not match this order. Please check and try again.']);
        }

        session([
            'guest_order_id' => $order->id,
            'guest_order_phone' => $order->customer_phone,
        ]);

        return redirect()->route('frontend.order.confirmation', $order);
    }

    public function orderConfirmation(Order $order)
    {
        $member = Auth::guard('member')->user();

        $needsPhoneVerification = false;

        if ($member) {
            if (!$this->memberCanViewOrder($order, $member)) {
                abort(403, 'You do not have access to view this order.');
            }
        } elseif (!$this->guestCanViewOrder($order)) {
            $needsPhoneVerification = true;
        }

        $contactPhone = Setting::where('setting_group', 'contact_section')
            ->where('key', 'contact_phone')
            ->value('value') ?? '+880 1234 567 890';

        $orderItems = $order->normalizedItems();

        return view('frontend.order-confirmation', compact(
            'order',
            'contactPhone',
            'orderItems',
            'member',
            'needsPhoneVerification'
        ));
    }

    private function memberCanViewOrder(Order $order, Member $member): bool
    {
        if ((int) $order->member_id === (int) $member->id) {
            return true;
        }

        return $this->phonesMatch($order->customer_phone, $member->phone);
    }

    private function guestCanViewOrder(Order $order): bool
    {
        if ((int) session('guest_order_id') === (int) $order->id) {
            return true;
        }

        if ($order->customer_phone && session('guest_order_phone')) {
            return $this->phonesMatch($order->customer_phone, session('guest_order_phone'));
        }

        return false;
    }

    private function phonesMatch(?string $stored, ?string $input): bool
    {
        $normalize = static fn (?string $phone) => preg_replace('/\D+/', '', (string) $phone);
        $a = $normalize($stored);
        $b = $normalize($input);

        if ($a === '' || $b === '') {
            return false;
        }

        if ($a === $b) {
            return true;
        }

        return str_ends_with($a, substr($b, -10)) || str_ends_with($b, substr($a, -10));
    }
}
