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

        if ($member->status === 'suspended') {
            $message = 'Your membership account is suspended. Please contact support.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 403);
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

        $orderItems = is_array($order->items) ? $order->items : json_decode($order->items ?? '[]', true);

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
