<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Category::with('branch')->latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('branch_name', function ($row) {
                    return $row->branch->name ?? '<span class="text-danger">No Branch</span>';
                })
                ->addColumn('image', function ($row) {
                    $url = $row->image ? asset($row->image) : 'https://via.placeholder.com/50';
                    return '<img src="' . $url . '" width="50" class="rounded shadow-sm" />';
                })
                ->addColumn('status', function ($row) {
                    return $row->status == 1
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-soft-info edit-btn" data-id="' . $row->id . '">
                            <i class="ri-pencil-fill"></i>
                        </button>
                        <button class="btn btn-sm btn-soft-danger delete-btn" data-id="' . $row->id . '">
                            <i class="ri-delete-bin-fill"></i>
                        </button>';
                })
                ->rawColumns(['branch_name', 'action', 'image', 'status'])
                ->make(true);
        }

        $branches = Branch::all();
        return view('backend.category.index', compact('branches'));
    }

    public function store(Request $request)
    {
        // 1. Validate - check these field names against your <input name="...">
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name'      => 'required|string|max:255',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status'    => 'required' // Make sure this is sent
        ]);

        try {
            $data = $request->only(['branch_id', 'name', 'status']);
            $data['slug'] = \Illuminate\Support\Str::slug($request->name);

            // 2. Handle Image Upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/categories'), $imageName);
                $data['image'] = 'uploads/categories/' . $imageName;
            }

            // 3. Create Record
            Category::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Category "' . $request->name . '" stored successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Category $category)
    {
        return response()->json($category);
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name'      => 'required|string|max:255',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('image')) {
            if ($category->image && file_exists(public_path($category->image))) {
                unlink(public_path($category->image));
            }
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/categories'), $imageName);
            $data['image'] = 'uploads/categories/' . $imageName;
        }

        $category->update($data);
        return response()->json(['status' => 'success', 'message' => 'Category updated successfully!']);
    }

    public function destroy(Category $category)
    {
        if ($category->image && file_exists(public_path($category->image))) {
            unlink(public_path($category->image));
        }
        $category->delete();
        return response()->json(['status' => 'success', 'message' => 'Category deleted!']);
    }
}
