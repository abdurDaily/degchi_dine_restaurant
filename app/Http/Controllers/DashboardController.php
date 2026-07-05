<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Member;
use App\Models\Menu;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // ================= DATE FILTER =================
        $from = $request->query('from');
        $to   = $request->query('to');

        $isFiltered = filled($from) || filled($to);

        $rangeFrom = null;
        $rangeTo   = null;

        if ($isFiltered) {
            $rangeFrom = $from ? Carbon::parse($from)->startOfDay() : Carbon::parse($to)->startOfDay();
            $rangeTo   = $to ? Carbon::parse($to)->endOfDay() : Carbon::parse($from)->endOfDay();

            // যদি from > to হয়ে যায় ভুলবশত, সোয়াপ করে দিন
            if ($rangeFrom->gt($rangeTo)) {
                [$rangeFrom, $rangeTo] = [$rangeTo->copy()->startOfDay(), $rangeFrom->copy()->endOfDay()];
            }
        }

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
            // Base order query — filtered হলে date range apply হবে, নাহলে সব order
            $orderBase = Order::query();
            if ($isFiltered) {
                $orderBase->whereBetween('created_at', [$rangeFrom, $rangeTo]);
            }

            $orderStatusCounts = (clone $orderBase)
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            $stats['orders_total']   = (clone $orderBase)->count();
            $stats['orders_pending'] = (int) ($orderStatusCounts['pending'] ?? 0);
            $stats['orders_new']     = (clone $orderBase)->whereNull('viewed_at')->count();
            $stats['orders_revenue'] = (float) (clone $orderBase)
                ->whereIn('status', ['confirmed', 'completed'])
                ->sum('final_amount');

            // "আজকের" মেট্রিক শুধু তখনই দেখানো হবে যখন কোনো ফিল্টার সিলেক্ট করা নেই
            if (! $isFiltered) {
                $stats['orders_today']  = Order::whereDate('created_at', today())->count();
                $stats['revenue_today'] = (float) Order::whereIn('status', ['confirmed', 'completed'])
                    ->whereDate('created_at', today())
                    ->sum('final_amount');
            }

            $recentOrdersQuery = Order::query();
            if ($isFiltered) {
                $recentOrdersQuery->whereBetween('created_at', [$rangeFrom, $rangeTo]);
            }
            $recentOrders = $recentOrdersQuery
                ->latest()
                ->take(10)
                ->get(['id', 'customer_name', 'customer_phone', 'final_amount', 'status', 'payment_status', 'created_at', 'viewed_at']);

            // মাসিক রেভিনিউ চার্ট — শুধু unfiltered (default) view-তে দেখানো হবে
            if (! $isFiltered) {
                $monthlyRevenue = Order::query()
                    ->whereIn('status', ['confirmed', 'completed'])
                    ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
                    ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, SUM(final_amount) as total")
                    ->groupBy('month_key')
                    ->orderBy('month_key')
                    ->pluck('total', 'month_key');
            }
        }

        if ($user->can('members-show')) {
            $memberBase = Member::query();
            if ($isFiltered) {
                $memberBase->whereBetween('created_at', [$rangeFrom, $rangeTo]);
            }

            $stats['members_total']   = (clone $memberBase)->count();
            $stats['members_pending'] = (clone $memberBase)->where('is_student', true)->where('approval_status', 'pending')->count();
            $stats['members_golden']  = (clone $memberBase)->where('type', 'golden')->count();

            $recentMembers = (clone $memberBase)
                ->latest()
                ->take(6)
                ->get(['id', 'name', 'phone', 'unique_card_number', 'type', 'approval_status', 'is_student', 'created_at']);
        }

        if ($user->can('reviews-show')) {
            $reviewBase = Review::query();
            if ($isFiltered) {
                $reviewBase->whereBetween('created_at', [$rangeFrom, $rangeTo]);
            }

            $stats['reviews_total']   = (clone $reviewBase)->count();
            $stats['reviews_pending'] = (clone $reviewBase)->where('status', 'pending')->count();
        }

        // ক্যাটালগ ডেটা — এগুলো "বর্তমান মোট সংখ্যা", ডেট ফিল্টার প্রযোজ্য নয়
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
            'hasDashboardWidgets',
            'isFiltered',
            'from',
            'to',
            'rangeFrom',
            'rangeTo'
        ));
    }
}