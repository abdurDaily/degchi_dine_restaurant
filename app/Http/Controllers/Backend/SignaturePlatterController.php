<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SignaturePlatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SignaturePlatterController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $data = SignaturePlatter::latest()->get();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('thumbnail_preview', function ($row) {
                        $url = $row->thumbnail_image
                            ? (strpos($row->thumbnail_image, 'http') === 0
                                ? $row->thumbnail_image
                                : asset('uploads/platters/' . $row->thumbnail_image))
                            : asset('assets/placeholder/placeholder.png');
                        return '<img src="' . $url . '" width="60" height="60" class="rounded shadow-sm object-fit-cover" title="Thumbnail" />';
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
                    ->rawColumns(['thumbnail_preview', 'status', 'action'])
                    ->make(true);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        return view('backend.signature-platters.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'              => 'required|string|max:255',
            'subtitle'           => 'nullable|string|max:255',
            'description'        => 'nullable|string',
            'thumbnail_image'    => 'nullable|image|mimes:webp,png,jpg,jpeg|max:2048',
            'menu_card_image'    => 'nullable|image|mimes:webp,png,jpg,jpeg|max:2048',
            'status'             => 'required|in:0,1',
            'sort_order'         => 'nullable|integer|min:0',
            'features'           => 'nullable|array',
        ]);

        try {
            $data = $request->only(['title', 'subtitle', 'description', 'status', 'sort_order']);
            $data['sort_order'] = $data['sort_order'] ?? 0;

            // Handle features array
            if ($request->has('features')) {
                $features = array_values(array_filter($request->features));
                $data['features'] = $features ?: null;
            }

            // Handle thumbnail image upload
            if ($request->hasFile('thumbnail_image')) {
                $file      = $request->file('thumbnail_image');
                $imageName = time() . '_thumb_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/platters'), $imageName);
                $data['thumbnail_image'] = $imageName;
            }

            // Handle menu card image upload
            if ($request->hasFile('menu_card_image')) {
                $file      = $request->file('menu_card_image');
                $imageName = time() . '_menu_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/platters'), $imageName);
                $data['menu_card_image'] = $imageName;
            }

            SignaturePlatter::create($data);

            return response()->json(['status' => 'success', 'message' => 'Signature Platter created successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function edit(SignaturePlatter $signaturePlatter)
    {
        return response()->json($signaturePlatter);
    }

    public function update(Request $request, SignaturePlatter $signaturePlatter)
    {
        $request->validate([
            'title'              => 'required|string|max:255',
            'subtitle'           => 'nullable|string|max:255',
            'description'        => 'nullable|string',
            'thumbnail_image'    => 'nullable|image|mimes:webp,png,jpg,jpeg|max:2048',
            'menu_card_image'    => 'nullable|image|mimes:webp,png,jpg,jpeg|max:2048',
            'status'             => 'required|in:0,1',
            'sort_order'         => 'nullable|integer|min:0',
            'features'           => 'nullable|array',
        ]);

        try {
            $data = $request->only(['title', 'subtitle', 'description', 'status', 'sort_order']);
            $data['sort_order'] = $data['sort_order'] ?? $signaturePlatter->sort_order;

            // Handle features
            if ($request->has('features')) {
                $features = array_values(array_filter($request->features));
                $data['features'] = $features ?: null;
            }

            // Handle thumbnail image upload
            if ($request->hasFile('thumbnail_image')) {
                // Delete old thumbnail
                if ($signaturePlatter->thumbnail_image && file_exists(public_path('uploads/platters/' . $signaturePlatter->thumbnail_image))) {
                    unlink(public_path('uploads/platters/' . $signaturePlatter->thumbnail_image));
                }
                $file      = $request->file('thumbnail_image');
                $imageName = time() . '_thumb_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/platters'), $imageName);
                $data['thumbnail_image'] = $imageName;
            }

            // Handle menu card image upload
            if ($request->hasFile('menu_card_image')) {
                // Delete old menu card image
                if ($signaturePlatter->menu_card_image && file_exists(public_path('uploads/platters/' . $signaturePlatter->menu_card_image))) {
                    unlink(public_path('uploads/platters/' . $signaturePlatter->menu_card_image));
                }
                $file      = $request->file('menu_card_image');
                $imageName = time() . '_menu_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/platters'), $imageName);
                $data['menu_card_image'] = $imageName;
            }

            $signaturePlatter->update($data);

            return response()->json(['status' => 'success', 'message' => 'Signature Platter updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(SignaturePlatter $signaturePlatter)
    {
        // Delete thumbnail image
        if ($signaturePlatter->thumbnail_image && file_exists(public_path('uploads/platters/' . $signaturePlatter->thumbnail_image))) {
            unlink(public_path('uploads/platters/' . $signaturePlatter->thumbnail_image));
        }
        
        // Delete menu card image
        if ($signaturePlatter->menu_card_image && file_exists(public_path('uploads/platters/' . $signaturePlatter->menu_card_image))) {
            unlink(public_path('uploads/platters/' . $signaturePlatter->menu_card_image));
        }
        
        $signaturePlatter->delete();

        return response()->json(['status' => 'success', 'message' => 'Signature Platter deleted!']);
    }
}
