<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with('branch')->latest()->paginate(15);
        return view('backend.bookings.index', compact('bookings'));
    }

    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,due,partial_due,paid',
            'note' => 'nullable|string'
        ]);

        $booking->update([
            'status' => $request->status,
            'note' => $request->note
        ]);

        return back()->with('success', 'Booking updated successfully.');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return back()->with('success', 'Booking deleted successfully.');
    }
}
