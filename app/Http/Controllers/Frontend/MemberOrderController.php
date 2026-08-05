<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderPricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MemberOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:member');
    }

    /**
     * Cancel a pending order owned by the logged-in member.
     */
    public function cancel(Request $request, Order $order)
    {
        $member = Auth::guard('member')->user();
        if ($denied = $this->denyUnlessOwnedPending($order, $member)) {
            return $denied;
        }

        $request->validate([
            'remarks' => 'nullable|string|max:500',
        ]);

        $order->status = 'canceled';
        $order->status_remarks = $request->filled('remarks')
            ? $request->input('remarks')
            : 'Canceled by member';
        $order->save();

        return response()->json([
            'success' => true,
            'canceled' => true,
            'message' => 'Order #'.$order->id.' has been canceled.',
            'order' => $this->orderPayload($order->fresh()),
        ]);
    }

    /**
     * Update quantity (or remove when quantity is 0) on a pending order item.
     */
    public function updateItemQuantity(Request $request, Order $order, OrderPricingService $pricing)
    {
        $member = Auth::guard('member')->user();
        if ($denied = $this->denyUnlessOwnedPending($order, $member)) {
            return $denied;
        }

        $request->validate([
            'index' => 'required|integer|min:0',
            'quantity' => 'required|integer|min:0|max:999',
        ]);

        return DB::transaction(function () use ($request, $order, $pricing) {
            $order = Order::whereKey($order->id)->lockForUpdate()->with('member')->firstOrFail();

            if ($order->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This order is no longer pending and can\'t be edited.',
                ], 422);
            }

            if ((int) $order->member_id !== (int) Auth::guard('member')->id()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
            }

            $items = $order->normalizedItems();
            $index = (int) $request->input('index');

            if (! array_key_exists($index, $items)) {
                return response()->json(['success' => false, 'message' => 'Item not found.'], 404);
            }

            $quantity = (int) $request->input('quantity');

            if ($quantity <= 0) {
                array_splice($items, $index, 1);
            } else {
                $items[$index] = $pricing->repriceLineItemForQuantity($items[$index], $quantity);
            }

            $items = array_values($items);

            // Removing the last item cancels the pending order.
            if (count($items) === 0) {
                $oldFinal = (float) $order->final_amount;
                $order->items = [];
                $order->total_amount = 0;
                $order->discount_amount = 0;
                $order->coupon_discount = 0;
                $order->final_amount = 0;
                $order->status = 'canceled';
                $order->status_remarks = 'Canceled by member (all items removed)';
                $order->save();

                if ($order->member_credited && $order->member && abs($oldFinal) > 0.004) {
                    $order->member->total_purchase = max(0, round((float) $order->member->total_purchase - $oldFinal, 2));
                    $order->member->save();
                }

                return response()->json([
                    'success' => true,
                    'canceled' => true,
                    'message' => 'All items removed — order #'.$order->id.' was canceled.',
                    'order' => $this->orderPayload($order->fresh()),
                ]);
            }

            $pricing->applyItemsToOrder($order, $items);
            $order->refresh()->load('member');

            return response()->json([
                'success' => true,
                'canceled' => false,
                'message' => $quantity <= 0 ? 'Item removed from your order.' : 'Quantity updated.',
                'order' => $this->orderPayload($order),
            ]);
        });
    }

    private function denyUnlessOwnedPending(Order $order, $member)
    {
        if (! $member || (int) $order->member_id !== (int) $member->id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only manage your own orders.',
            ], 403);
        }

        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending orders can be changed.',
            ], 422);
        }

        return null;
    }

    private function orderPayload(Order $order): array
    {
        $items = [];

        foreach ($order->normalizedItems() as $index => $item) {
            $price = (float) ($item['original_price'] ?? $item['price'] ?? 0);
            $qty = max(1, (int) ($item['quantity'] ?? $item['qty'] ?? 1));
            $offerDiscount = (float) ($item['offer_discount'] ?? 0);
            $offerPercent = (int) ($item['offer_percent'] ?? 0);
            $discountedUnit = $offerPercent > 0 && $qty > 0
                ? max(0, $price - ($offerDiscount / $qty))
                : $price;
            $lineSubtotal = max(0, ($price * $qty) - $offerDiscount);

            $items[] = [
                'index' => $index,
                'title' => $item['title'] ?? $item['name'] ?? 'Item',
                'note' => $item['note'] ?? null,
                'quantity' => $qty,
                'price' => $price,
                'discounted_unit' => round($discountedUnit, 2),
                'line_subtotal' => round($lineSubtotal, 2),
                'offer_percent' => $offerPercent,
                'image' => $item['image'] ?? null,
            ];
        }

        $offerDiscountTotal = round(collect($items)->sum(function ($row) use ($order) {
            $raw = $order->normalizedItems()[$row['index']] ?? [];

            return (float) ($raw['offer_discount'] ?? 0);
        }), 2);

        return [
            'id' => $order->id,
            'status' => $order->status,
            'status_remarks' => $order->status_remarks,
            'payment_method' => $order->payment_method,
            'total_amount' => (float) $order->total_amount,
            'discount_amount' => (float) $order->discount_amount,
            'offer_discount' => $offerDiscountTotal,
            'coupon_discount' => (float) ($order->coupon_discount ?? 0),
            'delivery_charge' => (float) ($order->delivery_charge ?? 0),
            'final_amount' => (float) $order->final_amount,
            'display_subtotal' => round(max(0, (float) $order->total_amount - $offerDiscountTotal), 2),
            'items' => $items,
        ];
    }
}
