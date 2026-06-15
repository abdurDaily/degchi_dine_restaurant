<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $ordersCount = Order::count();
        $membersCount = Member::count();

        return view('dashboard', compact('ordersCount', 'membersCount'));
    }
}
