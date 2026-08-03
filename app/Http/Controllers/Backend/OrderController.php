<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuVariation;
use App\Models\Order;
use App\Services\OrderPricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:orders-show')->only(['index', 'show', 'latestOrderId']);
        $this->middleware('permission:orders-edit')->only([
            'updateStatus',
            'menuPicker',
            'updateItemQuantity',
            'addItem',
        ]);
    }

    public function index(Request $request)
    {
        // AJAX counts-only refresh (called by JS polling & after status update)
        if ($request->ajax() && $request->boolean('counts_only')) {
            $counts = Order::selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();
            $counts['all'] = Order::count();
            return response()->json(['counts' => $counts]);
        }

        if ($request->ajax()) {
            $query = Order::with('member')->select(['id', 'member_id', 'unique_card_number', 'customer_name', 'customer_phone', 'total_amount', 'discount_amount', 'final_amount', 'status', 'created_at', 'viewed_at']);

            // Filter by status
            if ($request->filled('status_filter')) {
                $query->where('status', $request->status_filter);
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('member', fn($order) => $order->member?->name ?? '-')
                ->addColumn('card_number', fn($order) => $order->unique_card_number ?? '-')
                ->addColumn('total', fn($order) => '৳ ' . number_format($order->total_amount, 2))
                ->addColumn('discount', fn($order) => '৳ ' . number_format($order->discount_amount, 2))
                ->addColumn('final', fn($order) => '৳ ' . number_format($order->final_amount, 2))
                ->addColumn('status_name', fn($order) => ucfirst($order->status))
                ->addColumn('date', fn($order) => $order->created_at->format('Y-m-d H:i'))
                ->addColumn('is_new', fn($order) => is_null($order->viewed_at) ? 1 : 0)
                ->addColumn('action', function($order) {
                    return '<button class="btn btn-sm btn-info view-order-btn" data-id="' . $order->id . '" data-url="' . route('orders.show', $order->id) . '"><i class="fas fa-eye"></i> View</button>';
                })
                ->rawColumns(['member', 'card_number', 'total', 'discount', 'final', 'status_name', 'date', 'action'])
                ->make(true);
        }

        // Status counts for filter buttons
        $counts = Order::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
        $counts['all'] = Order::count();

        return view('backend.orders.index', compact('counts'));
    }

    /**
     * Returns the latest order ID — used by the frontend sound polling system.
     */
    public function latestOrderId()
    {
        $latest = Order::latest('id')->value('id');
        return response()->json(['latest_id' => $latest ?? 0]);
    }

    public function show(Order $order)
    {
        // Mark as viewed the first time an admin opens it
        if (is_null($order->viewed_at)) {
            $order->update(['viewed_at' => now()]);
        }

        $order->load('member');

        if (request()->ajax()) {
            return view('backend.orders.partials.details', compact('order'));
        }

        return view('backend.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,canceled',
            'payment_status' => 'required|in:unpaid,paid,failed,cancelled',
            'status_remarks' => 'nullable|string|max:1000',
        ]);

        $order->update([
            'status' => $request->status,
            'payment_status' => $request->payment_status,
            'status_remarks' => $request->status === 'canceled'
                ? $request->status_remarks
                : null,
        ]);

        if ($order->status === 'completed' || $order->payment_status === 'paid') {
            $order->creditMemberPurchase();
        }

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully.',
        ]);
    }

    /**
     * Menu items available to add to a pending order, grouped by category,
     * priced from today's catalog + today's best eligible offer for the
     * order's member (so admins see exactly what a customer would pay now).
     */
    public function menuPicker(Request $request, Order $order, OrderPricingService $pricing)
    {
        $search = trim((string) $request->get('q', ''));

        $query = Menu::query()
            ->where('is_available', true)
            ->with(['category', 'variations' => function ($q) {
                $q->orderBy('price');
            }]);

        if ($search !== '') {
            $query->where('name', 'like', '%'.$search.'%');
        }

        $member = $order->member;

        $menus = $query->orderBy('name')->get()
            ->map(function (Menu $menu) use ($member, $pricing) {
                $variations = $menu->variations->map(
                    fn (MenuVariation $variation) => $pricing->previewVariationPricing($variation, $member)
                )->values();

                return [
                    'menu_id' => $menu->id,
                    'name' => $menu->name,
                    'category' => $menu->category?->name ?? 'Uncategorized',
                    'variations' => $variations,
                ];
            })
            ->filter(fn (array $menu) => $menu['variations']->isNotEmpty())
            ->values();

        return response()->json([
            'success' => true,
            'menus' => $menus,
        ]);
    }

    /**
     * Update (or remove, when quantity is 0) a single order line's quantity.
     * Locked to pending orders — re-checked inside the transaction so a stale
     * page can't sneak an edit past a status change that happened meanwhile.
     */
    public function updateItemQuantity(Request $request, Order $order, OrderPricingService $pricing)
    {
        $request->validate([
            'index' => 'required|integer|min:0',
            'quantity' => 'required|integer|min:0|max:999',
        ]);

        return DB::transaction(function () use ($request, $order, $pricing) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This order is no longer pending and can\'t be edited.',
                ], 422);
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

            return $this->persistItemsAndRespond($order, array_values($items), $pricing);
        });
    }

    /**
     * Add a menu variation to a pending order (or merge into an existing line
     * for the same variation + offer). Always re-priced from the DB — never
     * trusts a client-supplied price.
     */
    public function addItem(Request $request, Order $order, OrderPricingService $pricing)
    {
        $request->validate([
            'variation_id' => 'required|integer|exists:menu_variations,id',
            'quantity' => 'nullable|integer|min:1|max:50',
        ]);

        return DB::transaction(function () use ($request, $order, $pricing) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This order is no longer pending and can\'t be edited.',
                ], 422);
            }

            $variation = MenuVariation::with('menu')->find($request->input('variation_id'));
            if (! $variation) {
                return response()->json(['success' => false, 'message' => 'Menu item not found.'], 404);
            }

            $quantity = max(1, (int) $request->input('quantity', 1));
            $member = $order->member;

            $newLine = $pricing->buildLineItem($variation, $quantity, $member);

            $items = $order->normalizedItems();
            $merged = false;

            foreach ($items as $i => $existing) {
                $sameVariation = (int) ($existing['variation_id'] ?? 0) === (int) $variation->id;
                $sameOffer = (int) ($existing['offer_id'] ?? 0) === (int) ($newLine['offer_id'] ?? 0);

                if ($sameVariation && $sameOffer) {
                    $items[$i] = $pricing->repriceLineItemForQuantity(
                        $existing,
                        max(1, (int) ($existing['quantity'] ?? $existing['qty'] ?? 1)) + $quantity
                    );
                    $merged = true;
                    break;
                }
            }

            if (! $merged) {
                $items[] = $newLine;
            }

            return $this->persistItemsAndRespond($order, array_values($items), $pricing);
        });
    }

    /**
     * Recalculate totals for the given items, persist them on the order, keep
     * the member's total_purchase in sync if this order was already credited,
     * and return fresh items table + summary HTML for the admin UI to swap in.
     */
    private function persistItemsAndRespond(Order $order, array $items, OrderPricingService $pricing)
    {
        $member = $order->member;
        // Reuse the order's stored delivery charge — never add a second fee when
        // qty/items change. Discounts still apply to food only.
        $deliveryCharge = (float) ($order->delivery_charge ?? 0);
        $totals = $pricing->recalculateTotals(
            $items,
            $member,
            (float) $order->coupon_discount,
            $deliveryCharge
        );

        $oldFinalAmount = (float) $order->final_amount;

        $order->items = $items;
        $order->total_amount = $totals['total_amount'];
        $order->discount_amount = $totals['discount_amount'];
        $order->coupon_discount = $totals['coupon_discount'];
        $order->delivery_charge = $totals['delivery_charge'];
        $order->final_amount = $totals['final_amount'];
        $order->save();

        // Safety net: if this order's amount was already credited to the member's
        // total_purchase, keep that figure in sync with the edited final_amount.
        if ($order->member_credited && $member) {
            $delta = round($totals['final_amount'] - $oldFinalAmount, 2);

            if (abs($delta) > 0.004) {
                $member->total_purchase = max(0, round((float) $member->total_purchase + $delta, 2));
                $member->save();

                if ($member->qualifiesForGoldenUpgrade()) {
                    $member->upgradeToGolden();
                }
            }
        }

        $order->refresh()->load('member');

        return response()->json([
            'success' => true,
            'message' => 'Order items updated.',
            'items_html' => view('backend.orders.partials.items-table', ['order' => $order])->render(),
            'summary_html' => view('backend.orders.partials.summary', ['order' => $order])->render(),
            'final_amount' => $order->final_amount,
        ]);
    }
}
