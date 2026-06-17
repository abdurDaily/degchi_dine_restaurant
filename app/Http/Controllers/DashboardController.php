<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Member;
use App\Models\Menu;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Review;

class DashboardController extends Controller
{
    public function index()
    {
        $orderStatusCounts = Order::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'orders_total'      => Order::count(),
            'orders_today'      => Order::whereDate('created_at', today())->count(),
            'orders_pending'    => (int) ($orderStatusCounts['pending'] ?? 0),
            'orders_new'        => Order::whereNull('viewed_at')->count(),
            'orders_revenue'    => (float) Order::whereIn('status', ['confirmed', 'completed'])->sum('final_amount'),
            'revenue_today'     => (float) Order::whereIn('status', ['confirmed', 'completed'])
                ->whereDate('created_at', today())
                ->sum('final_amount'),
            'members_total'     => Member::count(),
            'members_pending'   => Member::where('is_student', true)->where('approval_status', 'pending')->count(),
            'members_golden'    => Member::where('type', 'golden')->count(),
            'reviews_total'     => Review::count(),
            'reviews_pending'   => Review::where('status', 'pending')->count(),
            'menu_items'        => Menu::count(),
            'categories'        => Category::count(),
            'offers_active'     => Offer::where('is_active', true)->count(),
            'branches'          => Branch::count(),
        ];

        $recentOrders = Order::query()
            ->latest()
            ->take(10)
            ->get(['id', 'customer_name', 'customer_phone', 'final_amount', 'status', 'payment_status', 'created_at', 'viewed_at']);

        $recentMembers = Member::query()
            ->latest()
            ->take(6)
            ->get(['id', 'name', 'phone', 'unique_card_number', 'type', 'approval_status', 'is_student', 'created_at']);

        $monthlyRevenue = Order::query()
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, SUM(final_amount) as total")
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->pluck('total', 'month_key');

        return view('dashboard', compact('stats', 'recentOrders', 'recentMembers', 'orderStatusCounts', 'monthlyRevenue'));
    }
}
