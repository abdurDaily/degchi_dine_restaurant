<?php

namespace App\Support;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderRedirect
{
    public static function url(Order $order): string
    {
        if (auth('member')->check()) {
            return route('frontend.member.dashboard', ['order' => $order->id]);
        }

        session([
            'guest_order_id' => $order->id,
            'guest_order_phone' => $order->customer_phone,
        ]);

        return route('frontend.order.confirmation', $order) . '?clear_cart=1';
    }

    public static function respond(Request $request, Order $order, string $message, bool $clearCart = true): JsonResponse|RedirectResponse
    {
        $redirectUrl = self::url($order);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'order_id' => $order->id,
                'clear_cart' => $clearCart,
                'redirect_url' => $redirectUrl,
            ]);
        }

        return redirect($redirectUrl)->with('success', $message);
    }
}
