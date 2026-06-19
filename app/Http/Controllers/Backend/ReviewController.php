<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:reviews-show')->only('index');
        $this->middleware('permission:reviews-moderate')->only(['approve', 'reject', 'delete']);
    }

    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $page = max(1, $request->get('page', 1));
            $search = trim($request->get('search', ''));
            $perPage = 10;

            $query = Review::query();

            // Search across name, email, and comment
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('comment', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%");
                });
            }

            $total = $query->count();
            $reviews = $query->orderByDesc('created_at')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();

            $rows = $reviews->map(function ($row, $index) use ($page, $perPage) {
                $image = $row->image 
                    ? '<img src="' . asset('storage/' . $row->image) . '" alt="' . htmlspecialchars($row->name) . '" class="name-avatar">'
                    : '<img src="https://i.pravatar.cc/36?u=' . urlencode($row->email ?? $row->name) . '" alt="' . htmlspecialchars($row->name) . '" class="name-avatar">';
                
                $nameWithImage = '<div class="name-with-image"><div>' . $image . '</div><div>' . htmlspecialchars($row->name) . '</div></div>';
                
                $emailDisplay = $row->email ? '<a href="mailto:' . htmlspecialchars($row->email) . '" class="email-link">' . htmlspecialchars($row->email) . '</a>' : '<span style="color: #ccc;">-</span>';
                
                $ratingDisplay = '<span class="rating-stars">' . str_repeat('<i class="ri-star-fill star-filled"></i>', (int)$row->rating) . str_repeat('<i class="ri-star-line star-empty"></i>', 5 - (int)$row->rating) . '</span>';
                
                $class = 'bg-warning';
                if ($row->status === 'approved') $class = 'bg-success';
                if ($row->status === 'rejected') $class = 'bg-danger';
                $statusBadge = '<span class="badge ' . $class . '">' . ucfirst($row->status) . '</span>';
                
                $commentPreview = htmlspecialchars(substr($row->comment ?? '', 0, 50)) . (strlen($row->comment ?? '') > 50 ? '...' : '');
                
                $createdDate = $row->created_at ? $row->created_at->format('M d, Y H:i') : '-';
                
                $buttons = '<div class="btn-group-action" role="group">';
                $buttons .= '<button type="button" class="btn btn-action btn-view" 
                    data-id="' . $row->id . '" 
                    data-image="' . ($row->image ? asset('storage/' . $row->image) : '') . '" 
                    data-name="' . htmlspecialchars($row->name) . '" 
                    data-email="' . htmlspecialchars($row->email ?? '-') . '" 
                    data-rating="' . (int)$row->rating . '" 
                    data-title="' . htmlspecialchars($row->title ?? '') . '" 
                    data-comment="' . htmlspecialchars($row->comment) . '" 
                    data-status="' . htmlspecialchars($row->status) . '" 
                    data-created="' . htmlspecialchars($createdDate) . '" 
                    data-approved="' . htmlspecialchars($row->approved_at ? $row->approved_at->format('M d, Y H:i') : '') . '" 
                    title="View Review"><i class="ri-eye-line"></i></button>';
                
                if ($row->status !== 'approved') {
                    $buttons .= '<button type="button" class="btn btn-action btn-approve" data-id="' . $row->id . '" title="Approve Review"><i class="ri-check-double-line"></i></button>';
                }
                
                if ($row->status !== 'rejected') {
                    $buttons .= '<button type="button" class="btn btn-action btn-delete" data-id="' . $row->id . '" title="Reject Review"><i class="ri-close-line"></i></button>';
                }
                
                $buttons .= '<button type="button" class="btn btn-action btn-delete" data-id="' . $row->id . '" title="Delete Review"><i class="ri-delete-bin-line"></i></button>';
                $buttons .= '</div>';
                
                return [
                    'DT_RowIndex' => (($page - 1) * $perPage) + $index + 1,
                    'name_with_image' => $nameWithImage,
                    'email_display' => $emailDisplay,
                    'rating_display' => $ratingDisplay,
                    'title' => htmlspecialchars($row->title ?? '-'),
                    'comment_preview' => $commentPreview,
                    'status_badge' => $statusBadge,
                    'created_at' => $createdDate,
                    'action' => $buttons,
                ];
            })->values();

            $totalPages = ceil($total / $perPage);

            return response()->json([
                'success' => true,
                'data' => $rows->toArray(),
                'pagination' => [
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => (int)$page,
                    'total_pages' => $totalPages,
                    'from' => $total === 0 ? 0 : (($page - 1) * $perPage) + 1,
                    'to' => min($page * $perPage, $total),
                ],
            ]);
        }

        // Non-AJAX request - return view
        $pending = Review::where('status', 'pending')->count();
        $approved = Review::where('status', 'approved')->count();
        $rejected = Review::where('status', 'rejected')->count();

        $reviews = Review::orderByDesc('created_at')->paginate(20);

        return view('backend.reviews.index', compact('reviews', 'pending', 'approved', 'rejected'));
    }

    public function approve(Review $review)
    {
        $review->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review approved successfully!',
        ]);
    }

    public function reject(Review $review)
    {
        $review->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review rejected.',
        ]);
    }

    public function delete(Review $review)
    {
        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully!',
        ]);
    }
}
