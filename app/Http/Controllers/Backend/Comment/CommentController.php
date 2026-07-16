<?php

namespace App\Http\Controllers\Backend\Comment;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $comments = Comment::with(['post', 'member', 'replies', 'reactions'])->latest()->get();

            return DataTables::of($comments)
                ->addIndexColumn()
                ->addColumn('post_title', function (Comment $comment) {
                    if (!$comment->post) {
                        return '—';
                    }

                    return e($comment->post->title);
                })
                ->addColumn('member_name', function (Comment $comment) {
                    return $comment->member?->name ?? 'Unknown';
                })
                ->addColumn('replies_count', function (Comment $comment) {
                    return $comment->replies->count();
                })
                ->addColumn('likes_count', function (Comment $comment) {
                    return $comment->reactions->where('reaction', 'like')->count();
                })
                ->addColumn('dislikes_count', function (Comment $comment) {
                    return $comment->reactions->where('reaction', 'dislike')->count();
                })
                ->make(true);
        }

        return redirect()->route('admin.posts.index');
    }

    public function toggleActive(Comment $comment)
    {
        $comment->is_active = !$comment->is_active;
        $comment->save();

        return response()->json([
            'success' => true,
            'message' => 'Comment ' . ($comment->is_active ? 'shown' : 'hidden'),
        ]);
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully',
        ]);
    }
}
