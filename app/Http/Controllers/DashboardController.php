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
        $user = auth()->user();

        $stats = [
            'orders_total'    => 0,
            'orders_today'    => 0,
            'orders_pending'  => 0,
            'orders_new'      => 0,
            'orders_revenue'  => 0,
            'revenue_today'   => 0,
            'members_total'   => 0,
            'members_pending' => 0,
            'members_golden'  => 0,
            'reviews_total'   => 0,
            'reviews_pending' => 0,
            'menu_items'      => 0,
            'categories'      => 0,
            'offers_active'   => 0,
            'branches'        => 0,
        ];

        $orderStatusCounts = collect();
        $recentOrders = collect();
        $recentMembers = collect();
        $monthlyRevenue = collect();

        if ($user->can('orders-show')) {
            $orderStatusCounts = Order::query()
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            $stats['orders_total']   = Order::count();
            $stats['orders_today']   = Order::whereDate('created_at', today())->count();
            $stats['orders_pending'] = (int) ($orderStatusCounts['pending'] ?? 0);
            $stats['orders_new']     = Order::whereNull('viewed_at')->count();
            $stats['orders_revenue'] = (float) Order::whereIn('status', ['confirmed', 'completed'])->sum('final_amount');
            $stats['revenue_today']  = (float) Order::whereIn('status', ['confirmed', 'completed'])
                ->whereDate('created_at', today())
                ->sum('final_amount');

            $recentOrders = Order::query()
                ->latest()
                ->take(10)
                ->get(['id', 'customer_name', 'customer_phone', 'final_amount', 'status', 'payment_status', 'created_at', 'viewed_at']);

            $monthlyRevenue = Order::query()
                ->whereIn('status', ['confirmed', 'completed'])
                ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, SUM(final_amount) as total")
                ->groupBy('month_key')
                ->orderBy('month_key')
                ->pluck('total', 'month_key');
        }

        if ($user->can('members-show')) {
            $stats['members_total']   = Member::count();
            $stats['members_pending'] = Member::where('is_student', true)->where('approval_status', 'pending')->count();
            $stats['members_golden']  = Member::where('type', 'golden')->count();

            $recentMembers = Member::query()
                ->latest()
                ->take(6)
                ->get(['id', 'name', 'phone', 'unique_card_number', 'type', 'approval_status', 'is_student', 'created_at']);
        }

        if ($user->can('reviews-show')) {
            $stats['reviews_total']   = Review::count();
            $stats['reviews_pending'] = Review::where('status', 'pending')->count();
        }

        if ($user->can('menu-list')) {
            $stats['menu_items'] = Menu::count();
        }

        if ($user->can('category-list')) {
            $stats['categories'] = Category::count();
        }

        if ($user->can('offers-show')) {
            $stats['offers_active'] = Offer::where('is_active', true)->count();
        }

        if ($user->can('branch-list')) {
            $stats['branches'] = Branch::count();
        }

        $hasDashboardWidgets = $user->hasDashboardWidgets();

        return view('dashboard', compact(
            'stats',
            'recentOrders',
            'recentMembers',
            'orderStatusCounts',
            'monthlyRevenue',
            'hasDashboardWidgets'
        ));
    }
}
