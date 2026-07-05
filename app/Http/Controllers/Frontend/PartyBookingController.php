<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PartyHallInfo;
use App\Models\PartyPackage;
use App\Models\PartyBookingInquiry;
use Illuminate\Http\Request;

class PartyBookingController extends Controller
{
    public function index()
    {
        $hallInfo = PartyHallInfo::where('status', true)->first();
        $packages = PartyPackage::where('status', true)
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get();

        return view('frontend.party-booking', compact('hallInfo', 'packages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'phone'            => 'required|string|max:20',
            'email'            => 'nullable|email|max:255',
            'event_date'       => 'required|date|after:today',
            'guest_count'      => 'required|integer|min:1|max:2000',
            'party_package_id' => 'nullable|exists:party_packages,id',
            'message'          => 'nullable|string|max:2000',
        ]);

        PartyBookingInquiry::create($request->only([
            'name', 'phone', 'email', 'event_date',
            'guest_count', 'party_package_id', 'message',
        ]));

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your booking inquiry has been submitted. We will contact you shortly.',
            ]);
        }

        return back()->with('success', 'Thank you! Your booking inquiry has been submitted. We will contact you shortly.');
    }
}
