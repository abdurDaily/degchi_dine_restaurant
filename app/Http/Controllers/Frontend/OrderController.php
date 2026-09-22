<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)->orderBy('created_at', 'desc')->paginate(12);
        return view('frontend.orders', compact('orders'));
    }

    public function invoice(Request $request, Order $order)
    {
        // Ensure ownership
        if ($order->user_id !== $request->user()->id) {
            abort(403);
        }

        $contact = Setting::where('setting_group', 'contact_section')->pluck('value', 'key')->all();

        return view('frontend.invoice', compact('order', 'contact'));
    }

    public function downloadInvoice(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            abort(403);
        }

        $contact = Setting::where('setting_group', 'contact_section')->pluck('value', 'key')->all();

        $pdf = PDF::loadView('frontend.partials.invoice-sheet', ['order' => $order, 'contact' => $contact]);
        $filename = 'invoice-' . $order->id . '.pdf';
        return $pdf->download($filename);
    }
}
