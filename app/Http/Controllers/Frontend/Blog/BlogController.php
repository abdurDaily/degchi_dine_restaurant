<?php

namespace App\Http\Controllers\Frontend\Blog;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\Comment;
use App\Models\CommentReaction;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::query()
            ->with(['blogCategory', 'author'])
            ->published()
            ->latest()
            ->paginate(12);

        $categories = BlogCategory::query()
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        return view('frontend.blog.blogIndex', compact('posts', 'categories'));
    }

    public function show(string $slug)
    {
        $post = Post::query()
            ->with([
                'blogCategory',
                'author',
                'comments' => function ($q) {
                    $q->where('is_active', true)
                        ->with([
                            'member',
                            'reactions',
                            'replies' => function ($q) {
                                $q->where('is_active', true)
                                    ->with(['member', 'reactions']);
                            },
                        ]);
                },
            ])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        $post->increment('view_count');

        $relatedPosts = Post::query()
            ->with(['blogCategory', 'author'])
            ->where('blog_category_id', $post->blog_category_id)
            ->where('id', '!=', $post->id)
            ->published()
            ->latest()
            ->limit(3)
            ->get();

        return view('frontend.blog.blogShow', compact('post', 'relatedPosts'));
    }

    public function comment(Request $request, string $slug)
    {
        if (! Auth::guard('member')->check()) {
            return response()->json(['error' => 'Please login as member to comment'], 401);
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $post = Post::query()
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        if (! $post->comments_enabled) {
            return response()->json(['error' => 'Comments are disabled for this post'], 403);
        }

        if (! empty($validated['parent_id'])) {
            Comment::query()
                ->where('id', $validated['parent_id'])
                ->where('post_id', $post->id)
                ->where('is_active', true)
                ->firstOrFail();
        }

        $comment = Comment::create([
            'post_id' => $post->id,
            'member_id' => Auth::guard('member')->id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'comment' => $validated['comment'],
            'is_active' => true,
        ]);

        $comment->load(['member', 'replies', 'reactions']);

        return response()->json([
            'success' => true,
            'comment' => view('frontend.blog.partials.comment', [
                'comment' => $comment,
                'postSlug' => $post->slug,
            ])->render(),
            'parent_id' => $comment->parent_id,
        ]);
    }

    public function react(Request $request, Comment $comment)
    {
        if (! Auth::guard('member')->check()) {
            return response()->json(['error' => 'Please login as member to react'], 401);
        }

        $validated = $request->validate([
            'reaction' => 'required|in:like,dislike',
        ]);

        $comment->loadMissing('post');

        if (! $comment->is_active || ! $comment->post?->comments_enabled) {
            return response()->json(['error' => 'Reactions are not available for this comment'], 403);
        }

        $memberId = Auth::guard('member')->id();

        $existing = CommentReaction::query()
            ->where('comment_id', $comment->id)
            ->where('member_id', $memberId)
            ->first();

        $removed = false;
        $updated = false;
        $created = false;

        if ($existing) {
            if ($existing->reaction === $validated['reaction']) {
                $existing->delete();
                $removed = true;
            } else {
                $existing->update(['reaction' => $validated['reaction']]);
                $updated = true;
            }
        } else {
            CommentReaction::create([
                'comment_id' => $comment->id,
                'member_id' => $memberId,
                'reaction' => $validated['reaction'],
            ]);
            $created = true;
        }

        $comment->load('reactions');

        return response()->json([
            'success' => true,
            'removed' => $removed,
            'updated' => $updated,
            'created' => $created,
            'likes' => $comment->likes_count,
            'dislikes' => $comment->dislikes_count,
        ]);
    }
}
