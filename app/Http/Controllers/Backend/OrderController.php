<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
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
        return view('backend.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,canceled',
            'payment_status' => 'required|in:unpaid,paid,failed,cancelled',
        ]);

        $order->update([
            'status' => $request->status,
            'payment_status' => $request->payment_status,
        ]);

        if ($order->status === 'completed' || $order->payment_status === 'paid') {
            $order->creditMemberPurchase();
        }

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully.',
        ]);
    }
}
