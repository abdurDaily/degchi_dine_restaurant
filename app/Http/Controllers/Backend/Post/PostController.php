<?php

namespace App\Http\Controllers\Backend\Post;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class PostController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $posts = Post::query()
                ->with(['blogCategory', 'author'])
                ->latest()
                ->get();

            return DataTables::of($posts)
                ->addIndexColumn()
                ->addColumn('image', fn (Post $post) => $post->image)
                ->addColumn('blog_category', function (Post $post) {
                    return $post->blogCategory
                        ? ['id' => $post->blogCategory->id, 'name' => $post->blogCategory->name]
                        : null;
                })
                ->addColumn('author', function (Post $post) {
                    return $post->author
                        ? ['id' => $post->author->id, 'name' => $post->author->name]
                        : null;
                })
                ->make(true);
        }

        $categories = BlogCategory::query()
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->select('id', 'name')
            ->get();

        $authors = User::query()
            ->active()
            ->orderBy('name', 'asc')
            ->select('id', 'name')
            ->get();

        return view('backend.post.index', compact('categories', 'authors'));
    }

    // Store — same simple pattern as MenuController@store
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'blog_category_id' => 'nullable|integer|exists:blog_categories,id',
            'author_id' => 'nullable|integer|exists:users,id',
            'content' => 'required|string|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $imagePath = null;

                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $imageName = time().'_'.uniqid().'.'.$file->extension();
                    $file->move(public_path('uploads/posts'), $imageName);
                    $imagePath = $imageName;
                }

                $post = Post::create([
                    'title' => $request->title,
                    'slug' => $this->uniqueSlug(Str::slug($request->title)),
                    'blog_category_id' => $request->blog_category_id,
                    'author_id' => $request->author_id ?? Auth::id(),
                    'content' => $request->content,
                    'image' => $imagePath,
                    'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
                    'comments_enabled' => $request->has('comments_enabled') ? $request->boolean('comments_enabled') : true,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Post created successfully',
                    'post' => $post->fresh(['blogCategory', 'author']),
                ]);
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function edit(Post $post)
    {
        $post->load(['blogCategory', 'author']);

        return response()->json($post);
    }

    // Update — same simple pattern as MenuController@update
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'blog_category_id' => 'nullable|integer|exists:blog_categories,id',
            'author_id' => 'nullable|integer|exists:users,id',
            'content' => 'required|string|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:3072',
        ]);

        try {
            DB::transaction(function () use ($request, $post) {
                $imagePath = $post->image;

                // Remove existing image if requested
                if ($request->boolean('remove_image') && $post->image) {
                    if (file_exists(public_path('uploads/posts/'.$post->image))) {
                        unlink(public_path('uploads/posts/'.$post->image));
                    }
                    $imagePath = null;
                }

                // Replace with newly uploaded image
                if ($request->hasFile('image')) {
                    if ($post->image && file_exists(public_path('uploads/posts/'.$post->image))) {
                        unlink(public_path('uploads/posts/'.$post->image));
                    }

                    $file = $request->file('image');
                    $imageName = time().'_'.uniqid().'.'.$file->extension();
                    $file->move(public_path('uploads/posts'), $imageName);
                    $imagePath = $imageName;
                }

                $post->update([
                    'title' => $request->title,
                    'slug' => $this->uniqueSlug(Str::slug($request->title), $post->id),
                    'blog_category_id' => $request->blog_category_id,
                    'author_id' => $request->author_id ?? Auth::id(),
                    'content' => $request->content,
                    'image' => $imagePath,
                    'is_active' => $request->has('is_active') ? $request->boolean('is_active') : false,
                    'comments_enabled' => $request->has('comments_enabled') ? $request->boolean('comments_enabled') : true,
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Post updated successfully',
                'post' => $post->fresh(['blogCategory', 'author']),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Post $post)
    {
        if ($post->image && file_exists(public_path('uploads/posts/'.$post->image))) {
            unlink(public_path('uploads/posts/'.$post->image));
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully',
        ]);
    }

    public function toggleComments(Request $request, Post $post)
    {
        $post->comments_enabled = $request->boolean('status');
        $post->save();

        return response()->json([
            'success' => true,
            'message' => 'Comments '.($post->comments_enabled ? 'enabled' : 'disabled'),
        ]);
    }

    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug ?: 'post';
        $candidate = $base;
        $i = 1;

        while (
            Post::query()
                ->where('slug', $candidate)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $candidate = $base.'-'.$i++;
        }

        return $candidate;
    }
}