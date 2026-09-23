<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Menu;
use App\Models\MenuVariation;
use App\Models\Order;
use App\Services\OrderPricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

    /**
     * Abort access unless the current user is allowed to manage this order.
     * All-branch users (Super Admin / branch_id null) manage every order;
     * branch-restricted users only manage orders assigned to their branch.
     */
    private function canManageOrder(Order $order): void
    {
        $user = auth()->user();

        if ($user->hasAllBranchAccess()) {
            return;
        }

        abort_unless(
            (int) $order->branch_id === (int) $user->branch_id,
            403,
            'You can only manage orders assigned to your branch.'
        );
    }

    public function index(Request $request)
    {
        // AJAX counts-only refresh (called by JS polling & after status update)
        if ($request->ajax() && $request->boolean('counts_only')) {
            $counts = Order::forUserBranch()->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();
            $counts['all'] = Order::forUserBranch()->count();
            return response()->json(['counts' => $counts]);
        }

        // Top customers leaderboard for a selected month (AJAX)
        if ($request->ajax() && $request->filled('top_customers')) {
            $month = $request->input('month');

            if (preg_match('/^\d{4}-\d{2}$/', (string) $month) !== 1) {
                return response()->json(['html' => '']);
            }

            $start = Carbon::parse($month)->startOfMonth();
            $end   = Carbon::parse($month)->endOfMonth();

            $topCustomers = Order::forUserBranch()
                ->whereBetween('created_at', [$start, $end])
                ->where('status', '!=', 'canceled')
                ->selectRaw('COALESCE(NULLIF(NULLIF(customer_phone, ""), NULL), CONCAT("walkin:", customer_name)) as identity')
                ->selectRaw('MAX(customer_name) as customer_name')
                ->selectRaw('MAX(NULLIF(customer_phone, "")) as customer_phone')
                ->selectRaw('COUNT(*) as orders_count')
                ->selectRaw('SUM(final_amount) as total_spent')
                ->groupBy('identity')
                ->orderByDesc('total_spent')
                ->limit(10)
                ->get();

            $monthLabel = $start->format('F Y');

            $html = view('backend.orders.partials.top-customers', compact('topCustomers', 'monthLabel'))->render();

            return response()->json(['html' => $html]);
        }

        if ($request->ajax()) {
            $query = Order::forUserBranch()->with(['member', 'branch'])->select(['id', 'branch_id', 'member_id', 'unique_card_number', 'customer_name', 'customer_phone', 'total_amount', 'discount_amount', 'final_amount', 'status', 'created_at', 'viewed_at']);

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
                ->addColumn('status_name', function ($order) {
                    $label = e(ucfirst($order->status));

                    // Use dedicated badge classes (not bare text-danger / text-warning).
                    // A global modal-close handler clears span.text-danger validation
                    // errors and must never wipe these status chips.
                    return match ($order->status) {
                        'canceled' => '<span class="badge order-status-badge order-status-canceled">'.$label.'</span>',
                        'pending' => '<span class="badge order-status-badge order-status-pending">'.$label.'</span>',
                        'confirmed' => '<span class="badge order-status-badge order-status-confirmed">'.$label.'</span>',
                        'completed' => '<span class="badge order-status-badge order-status-completed">'.$label.'</span>',
                        default => $label,
                    };
                })
                ->addColumn('branch_name', function ($order) {
                    if ($order->branch_id === null) {
                        return '<span class="badge order-branch-badge bg-secondary">Unassigned</span>';
                    }

                    return '<span class="badge order-branch-badge order-branch-assigned">' . e($order->branch->name ?? 'Unknown') . '</span>';
                })
                ->addColumn('date', fn($order) => $order->created_at->format('Y-m-d H:i'))
                ->addColumn('is_new', fn($order) => is_null($order->viewed_at) ? 1 : 0)
                ->addColumn('action', function($order) {
                    return '<button class="btn btn-sm btn-info view-order-btn" data-id="' . $order->id . '" data-url="' . route('orders.show', $order->id) . '"><i class="fas fa-eye"></i> View</button>';
                })
                ->rawColumns(['member', 'card_number', 'total', 'discount', 'final', 'status_name', 'branch_name', 'date', 'action'])
                ->make(true);
        }

        // Status counts for filter buttons
        $counts = Order::forUserBranch()->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
        $counts['all'] = Order::forUserBranch()->count();

        return view('backend.orders.index', compact('counts'));
    }

    /**
     * Returns the latest order ID — used by the frontend sound polling system.
     */
    public function latestOrderId()
    {
        $latest = Order::forUserBranch()->latest('id')->value('id');
        return response()->json(['latest_id' => $latest ?? 0]);
    }

    public function show(Order $order)
    {
        $this->canManageOrder($order);

        // Mark as viewed the first time an admin opens it
        if (is_null($order->viewed_at)) {
            $order->update(['viewed_at' => now()]);
        }

        $order->load('member');

        $branches = Branch::orderBy('name')->get();

        if (request()->ajax()) {
            return view('backend.orders.partials.details', compact('order', 'branches'));
        }

        return view('backend.orders.show', compact('order', 'branches'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $this->canManageOrder($order);

        $user = auth()->user();

        $rules = [
            'status' => 'required|in:pending,confirmed,completed,canceled',
            'payment_status' => 'required|in:unpaid,paid,failed,cancelled',
            'status_remarks' => 'nullable|string|max:1000',
        ];

        // Only all-branch users (Super Admin / branch_id null) can assign or
        // re-assign an order to a branch. Every order must be assigned to a
        // branch before its status can be saved.
        if ($user->hasAllBranchAccess()) {
            $rules['branch_id'] = 'required|integer|exists:branches,id';
        }

        $validated = $request->validate($rules);

        $data = [
            'status' => $validated['status'],
            'payment_status' => $validated['payment_status'],
            'status_remarks' => $validated['status'] === 'canceled'
                ? $request->status_remarks
                : null,
        ];

        if ($user->hasAllBranchAccess()) {
            $data['branch_id'] = (int) $validated['branch_id'];
        }

        $order->update($data);

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
        $this->canManageOrder($order);

        $search = trim((string) $request->get('q', ''));

        $query = Menu::query()
            ->where('is_available', true)
            ->with(['category', 'variations' => function ($q) {
                $q->orderBy('price');
            }]);

        // Show only menus that belong to the order's assigned branch (plus
        // global menus). Unassigned orders (null branch) show the whole catalog.
        if ($order->branch_id) {
            $query->whereHas('category', function ($q) use ($order) {
                $q->where('branch_id', $order->branch_id)->orWhereNull('branch_id');
            });
        }

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
        $this->canManageOrder($order);

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
        $this->canManageOrder($order);

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
        $pricing->applyItemsToOrder($order, $items);
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
