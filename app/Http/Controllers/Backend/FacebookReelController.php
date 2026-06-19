<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\FacebookReel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FacebookReelController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:facebook-reels-list')->only(['index', 'edit']);
        $this->middleware('permission:facebook-reels-create')->only('store');
        $this->middleware('permission:facebook-reels-edit')->only('update');
        $this->middleware('permission:facebook-reels-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $data = FacebookReel::orderBy('sort_order')->orderBy('id', 'desc')->get();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('thumbnail_preview', function ($row) {
                        $url = $row->thumbnail
                            ? (strpos($row->thumbnail, 'http') === 0
                                ? $row->thumbnail
                                : asset('uploads/reels/' . $row->thumbnail))
                            : asset('assets/placeholder/placeholder.png');
                        return '<img src="' . $url . '" width="60" height="80" class="rounded shadow-sm object-fit-cover" />';
                    })
                    ->addColumn('facebook_link', function ($row) {
                        return '<a href="' . e($row->facebook_url) . '" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-soft-primary">
                            <i class="bi bi-facebook me-1"></i>View
                        </a>';
                    })
                    ->addColumn('status', function ($row) {
                        return $row->status
                            ? '<span class="badge bg-success">Active</span>'
                            : '<span class="badge bg-danger">Inactive</span>';
                    })
                    ->addColumn('action', function ($row) {
                        return '
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-soft-info edit-btn" data-id="' . $row->id . '" title="Edit">
                                    <i class="ri-pencil-fill"></i>
                                </button>
                                <button class="btn btn-sm btn-soft-danger delete-btn" data-id="' . $row->id . '" title="Delete">
                                    <i class="ri-delete-bin-fill"></i>
                                </button>
                            </div>';
                    })
                    ->rawColumns(['thumbnail_preview', 'facebook_link', 'status', 'action'])
                    ->make(true);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        return view('backend.facebook-reels.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'facebook_url' => 'required|url|max:500',
            'thumbnail'    => 'nullable|image|mimes:webp,png,jpg,jpeg|max:2048',
            'status'       => 'required|in:0,1',
            'sort_order'   => 'nullable|integer|min:0',
        ]);

        try {
            $data = $request->only(['title', 'facebook_url', 'status', 'sort_order']);
            $data['sort_order'] = $data['sort_order'] ?? 0;

            if ($request->hasFile('thumbnail')) {
                $file      = $request->file('thumbnail');
                $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/reels'), $imageName);
                $data['thumbnail'] = $imageName;
            }

            FacebookReel::create($data);

            return response()->json(['status' => 'success', 'message' => 'Facebook Reel added successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function edit(FacebookReel $facebookReel)
    {
        return response()->json($facebookReel);
    }

    public function update(Request $request, FacebookReel $facebookReel)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'facebook_url' => 'required|url|max:500',
            'thumbnail'    => 'nullable|image|mimes:webp,png,jpg,jpeg|max:2048',
            'status'       => 'required|in:0,1',
            'sort_order'   => 'nullable|integer|min:0',
        ]);

        try {
            $data = $request->only(['title', 'facebook_url', 'status', 'sort_order']);
            $data['sort_order'] = $data['sort_order'] ?? $facebookReel->sort_order;

            if ($request->hasFile('thumbnail')) {
                // Delete old thumbnail
                if ($facebookReel->thumbnail && file_exists(public_path('uploads/reels/' . $facebookReel->thumbnail))) {
                    unlink(public_path('uploads/reels/' . $facebookReel->thumbnail));
                }
                $file      = $request->file('thumbnail');
                $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/reels'), $imageName);
                $data['thumbnail'] = $imageName;
            }

            $facebookReel->update($data);

            return response()->json(['status' => 'success', 'message' => 'Facebook Reel updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(FacebookReel $facebookReel)
    {
        if ($facebookReel->thumbnail && file_exists(public_path('uploads/reels/' . $facebookReel->thumbnail))) {
            unlink(public_path('uploads/reels/' . $facebookReel->thumbnail));
        }
        $facebookReel->delete();

        return response()->json(['status' => 'success', 'message' => 'Facebook Reel deleted!']);
    }
}
