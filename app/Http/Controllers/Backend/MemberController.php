<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:members-show')->only(['index', 'show']);
        $this->middleware('permission:members-edit')->only(['toggleStatus', 'updateStatus', 'syncPurchase', 'approve', 'reject', 'upgradeToGolden']);
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        // Default: Active members. Use status=all to show every status.
        $statusFilter = $request->query('status', 'active');
        if (! in_array($statusFilter, ['pending', 'active', 'suspended', 'all'], true)) {
            $statusFilter = 'active';
        }

        // Card/name/phone search must find members in any status (pending/rejected/suspended)
        if ($search !== '') {
            $statusFilter = 'all';
        }

        $typeFilter = $request->query('type', 'all');
        if (! in_array($typeFilter, ['all', 'membership', 'golden'], true)) {
            $typeFilter = 'all';
        }

        $studentFilter = $request->query('student', 'all');
        if (! in_array($studentFilter, ['all', 'yes', 'no'], true)) {
            $studentFilter = 'all';
        }

        $approvalFilter = $request->query('approval', 'all');
        if (! in_array($approvalFilter, ['all', 'pending', 'approved', 'rejected'], true)) {
            $approvalFilter = 'all';
        }

        $members = Member::query()
            ->withSum(['orders as computed_total_purchase' => function ($q) {
                $q->whereIn('status', ['confirmed', 'completed']);
            }], 'final_amount')
            ->withCount('orders')
            ->when($search !== '', function ($query) use ($search) {
                $like = '%'.$search.'%';
                $query->where(function ($q) use ($search, $like) {
                    $q->where('unique_card_number', 'like', $like)
                        ->orWhere('unique_card_number', $search)
                        ->orWhere('phone', 'like', $like)
                        ->orWhere('name', 'like', $like)
                        ->orWhere('email', 'like', $like);
                });
            })
            ->when($statusFilter !== 'all', function ($query) use ($statusFilter) {
                $query->where('status', $statusFilter);
            })
            ->when($typeFilter !== 'all', function ($query) use ($typeFilter) {
                $query->where('type', $typeFilter);
            })
            ->when($studentFilter === 'yes', function ($query) {
                $query->where('is_student', true);
            })
            ->when($studentFilter === 'no', function ($query) {
                $query->where('is_student', false);
            })
            ->when($approvalFilter !== 'all', function ($query) use ($approvalFilter) {
                $query->where('is_student', true)
                    ->where('approval_status', $approvalFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('backend.members.index', compact(
            'members',
            'search',
            'statusFilter',
            'typeFilter',
            'studentFilter',
            'approvalFilter'
        ));
    }

    /**
     * Get member details for AJAX modal.
     */
    public function show(Member $member)
    {
        $member->loadSum(['orders as computed_total_purchase' => function ($q) {
            $q->whereIn('status', ['confirmed', 'completed']);
        }], 'final_amount');
        $member->loadCount('orders');
        $member->load(['orders' => function ($q) {
            $q->latest()->take(10);
        }]);

        return response()->json([
            'success' => true,
            'member' => [
                'id' => $member->id,
                'name' => $member->name,
                'phone' => $member->phone,
                'email' => $member->email,
                'dob' => $member->dob?->format('Y-m-d'),
                'marriage_date' => $member->marriage_date?->format('Y-m-d'),
                'address' => $member->address,
                'unique_card_number' => $member->unique_card_number,
                'type' => $member->type,
                'status' => $member->status,
                'is_student' => $member->is_student,
                'approval_status' => $member->approval_status,
                'profile_image_url' => $member->profile_image_path ? asset('storage/' . $member->profile_image_path) : null,
                'student_card_url' => $member->student_card_path ? asset('storage/' . $member->student_card_path) : null,
                'total_purchase' => (float) ($member->computed_total_purchase ?? 0),
                'orders_count' => $member->orders_count,
                'first_order_discount_used' => $member->first_order_discount_used,
                'expires_at' => $member->expires_at?->format('Y-m-d'),
                'created_at' => $member->created_at->format('Y-m-d'),
                'recent_orders' => $member->orders->map(fn($o) => [
                    'id' => $o->id,
                    'final_amount' => number_format($o->final_amount, 2),
                    'status' => $o->status,
                    'date' => $o->created_at->format('Y-m-d'),
                ]),
            ],
        ]);
    }

    /**
     * Toggle member status (active/suspended).
     */
    public function toggleStatus(Member $member)
    {
        $member->status = $member->status === 'active' ? 'suspended' : 'active';
        $member->save();

        return response()->json([
            'success' => true,
            'message' => 'Member status changed to ' . ucfirst($member->status) . '.',
            'new_status' => $member->status,
            'counts' => $this->statusCounts(),
        ]);
    }

    /**
     * Set member account status: pending, active, or suspended.
     */
    public function updateStatus(Request $request, Member $member)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,active,suspended',
        ]);

        $member->status = $validated['status'];
        $member->save();

        return response()->json([
            'success' => true,
            'message' => 'Member status changed to ' . ucfirst($member->status) . '.',
            'new_status' => $member->status,
            'counts' => $this->statusCounts(),
        ]);
    }

    /**
     * Sync the stored total_purchase column with actual order amounts.
     */
    public function syncPurchase(Member $member)
    {
        $computedTotal = $member->orders()
            ->whereIn('status', ['confirmed', 'completed'])
            ->sum('final_amount');

        $member->total_purchase = $computedTotal;
        $member->save();

        return response()->json([
            'success' => true,
            'message' => 'Total purchase synced: ৳' . number_format($computedTotal, 2),
            'total_purchase' => (float) $computedTotal,
        ]);
    }

    /**
     * Approve a student member.
     */
    public function approve(Member $member)
    {
        if (!$member->is_student) {
            return response()->json([
                'success' => false,
                'message' => 'Only student members require approval.',
            ], 400);
        }

        if ($member->approval_status === 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Member is already approved.',
            ], 400);
        }

        $member->approval_status = 'approved';
        $member->save();

        return response()->json([
            'success' => true,
            'message' => 'Student member approved successfully. They can now use first-order discount.',
            'approval_status' => 'approved',
            'counts' => $this->approvalCounts(),
        ]);
    }

    /**
     * Upgrade a member to Golden Card (10% discount on every order, 5-year validity).
     */
    public function upgradeToGolden(Member $member)
    {
        if ($member->isGolden()) {
            return response()->json([
                'success' => false,
                'message' => 'Member already has a Golden Card.',
            ], 400);
        }

        $member->upgradeToGolden();

        return response()->json([
            'success' => true,
            'message' => 'Member upgraded to Golden Card. They now receive 10% off every order for 5 years.',
            'type' => 'golden',
            'expires_at' => $member->expires_at?->format('Y-m-d'),
        ]);
    }

    /**
     * Reject/Revoke approval for a student member.
     */
    public function reject(Member $member)
    {
        if (!$member->is_student) {
            return response()->json([
                'success' => false,
                'message' => 'Only student members have approval status.',
            ], 400);
        }

        $member->approval_status = 'rejected';
        $member->save();

        return response()->json([
            'success' => true,
            'message' => 'Student member rejected.',
            'approval_status' => 'rejected',
            'counts' => $this->approvalCounts(),
        ]);
    }

    private function approvalCounts(): array
    {
        return [
            'pending' => Member::where('is_student', true)->where('approval_status', 'pending')->count(),
            'approved' => Member::where('is_student', true)->where('approval_status', 'approved')->count(),
            'rejected' => Member::where('is_student', true)->where('approval_status', 'rejected')->count(),
        ];
    }

    private function statusCounts(): array
    {
        return [
            'pending' => Member::where('status', 'pending')->count(),
            'active' => Member::where('status', 'active')->count(),
            'suspended' => Member::where('status', 'suspended')->count(),
        ];
    }
}
