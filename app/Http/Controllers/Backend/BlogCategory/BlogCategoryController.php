<?php

namespace App\Http\Controllers\Backend\BlogCategory;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class BlogCategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $categories = BlogCategory::query()->latest()->get();

            return DataTables::of($categories)
                ->addIndexColumn()
                ->editColumn('created_at', function (BlogCategory $category) {
                    return $category->created_at?->format('Y-m-d H:i');
                })
                ->addColumn('status', function (BlogCategory $category) {
                    $class = $category->is_active ? 'active' : 'inactive';
                    $label = $category->is_active ? 'Active' : 'Inactive';

                    return '<span class="status-badge ' . $class . '">' . $label . '</span>';
                })
                ->addColumn('action', function (BlogCategory $category) {
                    return view('backend.blogCategory.partials.action', compact('category'))->render();
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('backend.blogCategory.index');
    }

    public function store(Request $request)
    {
        $request->merge([
            'slug' => $request->filled('slug') ? $request->slug : null,
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:blog_categories,name',
            'slug' => 'nullable|string|max:150|unique:blog_categories,slug',
            'is_active' => 'nullable|in:0,1,true,false',
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['name']);
        $slug = $this->uniqueSlug($slug);

        BlogCategory::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
        ]);

        return response()->json(['success' => true, 'message' => 'Category created successfully']);
    }

    public function edit(BlogCategory $category)
    {
        return response()->json($category);
    }

    public function update(Request $request, BlogCategory $category)
    {
        $request->merge([
            'slug' => $request->filled('slug') ? $request->slug : null,
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:blog_categories,name,' . $category->id,
            'slug' => 'nullable|string|max:150|unique:blog_categories,slug,' . $category->id,
            'is_active' => 'nullable|in:0,1,true,false',
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['name']);
        $slug = $this->uniqueSlug($slug, $category->id);

        $category->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
        ]);

        return response()->json(['success' => true, 'message' => 'Category updated successfully']);
    }

    public function destroy(BlogCategory $category)
    {
        $category->delete();

        return response()->json(['success' => true, 'message' => 'Category deleted successfully']);
    }

    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug ?: 'category';
        $candidate = $base;
        $i = 1;

        while (
            BlogCategory::query()
                ->where('slug', $candidate)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $candidate = $base . '-' . $i++;
        }

        return $candidate;
    }
}
