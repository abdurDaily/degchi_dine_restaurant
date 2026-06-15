<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Member;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::approved()
            ->with('member')
            ->orderByDesc('approved_at')
            ->paginate(12);

        return view('frontend.reviews', compact('reviews'));
    }

    public function contact()
    {
        return view('frontend.contact');
    }

    public function verifyMember(Request $request)
    {
        $validated = $request->validate([
            'card_number' => 'required|string|max:100',
        ]);

        // Find member by unique_card_number
        $member = Member::where('unique_card_number', $validated['card_number'])->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid membership card number. Please check and try again.',
            ], 422);
        }

        if (!$member->profile_image_path) {
            return response()->json([
                'success' => false,
                'message' => 'Please upload a profile image to your membership account first.',
            ], 422);
        }

        // Check if member already has a pending or approved review
        $existingReview = Review::where('member_id', $member->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted a review. Thank you!',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Membership verified! Now share your review below.',
            'member' => [
                'id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
            ],
        ]);
    }

    public function store(Request $request)
    {
        // Validate member card number
        $validated = $request->validate([
            'member_card_number' => 'required|string|max:100',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:150',
            'comment' => 'required|string|min:10|max:1000',
        ]);

        // Find member by unique_card_number
        $member = Member::where('unique_card_number', $validated['member_card_number'])->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid membership card number. Please check and try again.',
            ], 422);
        }

        if (!$member->profile_image_path) {
            return response()->json([
                'success' => false,
                'message' => 'Please upload a profile image to your membership account first.',
            ], 422);
        }

        // Check if member already has a pending or approved review
        $existingReview = Review::where('member_id', $member->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted a review. Thank you!',
            ], 422);
        }

        // Create review with member data
        Review::create([
            'member_id' => $member->id,
            'name' => $member->name,
            'email' => $member->email,
            'rating' => $validated['rating'],
            'title' => $validated['title'],
            'comment' => $validated['comment'],
            'image' => $member->profile_image_path,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you! Your review has been submitted for approval.',
        ]);
    }
}
