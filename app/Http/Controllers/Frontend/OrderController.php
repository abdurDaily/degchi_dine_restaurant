<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
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

        return view('frontend.invoice', compact('order'));
    }

    public function downloadInvoice(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            abort(403);
        }

        $pdf = PDF::loadView('frontend.invoice', ['order' => $order]);
        $filename = 'invoice-' . $order->id . '.pdf';
        return $pdf->download($filename);
    }
}
