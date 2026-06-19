<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:branch-list')->only(['index', 'edit']);
        $this->middleware('permission:branch-create')->only('store');
        $this->middleware('permission:branch-edit')->only('update');
        $this->middleware('permission:branch-delete')->only('destroy');
    }

    /**
     * Display branches list with DataTables
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Branch::latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<div class="d-flex gap-1 flex-wrap">
                        <button type="button" class="btn btn-sm btn-soft-info view-details-btn" data-id="' . $row->id . '" title="View Details">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-soft-warning edit-btn" data-id="' . $row->id . '" title="Edit">
                            <i class="ri-pencil-line"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-soft-success copy-link-btn" data-id="' . $row->id . '" data-slug="' . $row->slug . '" title="Copy Link">
                           <i class="ri-links-line"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-soft-danger delete-btn" data-id="' . $row->id . '" data-name="' . $row->name . '" title="Delete">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('backend.branch.index');
    }

    /**
     * Store a new branch
     */
    public function store(Request $request)
    {
        \Log::info('=== STORE METHOD CALLED ===');
        \Log::info('Request URL: ' . $request->fullUrl());
        \Log::info('Request input:', $request->all());
        
        try {
            $validated = $request->validate([
                'name'           => 'required|string|max:255|unique:branches,name',
                'phone'          => 'required|string|max:20',
                'location'       => 'required|string|max:500',
                'status'         => 'nullable|boolean',
                'foodpanda_url'  => 'nullable|url',
                'pathao_url'     => 'nullable|url',
                'foodi_url'      => 'nullable|url',
                'foodpanda_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
                'pathao_logo'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
                'foodi_logo'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            ]);

            \Log::info('Validation passed');

            $branchData = [
                'name'           => $validated['name'],
                'phone'          => $validated['phone'],
                'location'       => $validated['location'],
                'status'         => $validated['status'] ?? 1,
                'foodpanda_url'  => $validated['foodpanda_url'] ?? null,
                'pathao_url'     => $validated['pathao_url'] ?? null,
                'foodi_url'      => $validated['foodi_url'] ?? null,
            ];

            // Handle file uploads
            $this->handleLogoUploads($request, $branchData);

            \Log::info('Creating branch with data:', $branchData);

            $branch = Branch::create($branchData);

            \Log::info('Branch created successfully with ID: ' . $branch->id);

            return response()->json([
                'status' => 'success',
                'message' => 'Branch created successfully!',
                'data' => $branch
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed: ' . json_encode($e->errors()));
            return response()->json([
                'status' => 'error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in store method: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get branch data for editing
     */
    public function edit($id)
    {
        try {
            $branch = Branch::findOrFail($id);
            return response()->json($branch);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Branch not found'], 404);
        }
    }

    /**
     * Update branch
     */
    public function update(Request $request, $id)
    {
        \Log::info('=== UPDATE METHOD CALLED ===');
        \Log::info('ID: ' . $id);
        \Log::info('Request URL: ' . $request->fullUrl());
        
        try {
            // Log if ID looks suspicious
            if (!is_numeric($id) || (int)$id <= 0) {
                \Log::error('Invalid ID for update: ' . $id);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid branch ID'
                ], 400);
            }
            
            $branch = Branch::findOrFail($id);
            \Log::info('Branch found: ' . $branch->id);

            $validated = $request->validate([
                'name'           => 'required|string|max:255|unique:branches,name,' . $id,
                'phone'          => 'required|string|max:20',
                'location'       => 'required|string|max:500',
                'status'         => 'nullable|boolean',
                'foodpanda_url'  => 'nullable|url',
                'pathao_url'     => 'nullable|url',
                'foodi_url'      => 'nullable|url',
                'foodpanda_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
                'pathao_logo'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
                'foodi_logo'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            ]);

            \Log::info('Update validation passed');

            $branchData = [
                'name'           => $validated['name'],
                'phone'          => $validated['phone'],
                'location'       => $validated['location'],
                'status'         => $validated['status'] ?? 1,
                'foodpanda_url'  => $validated['foodpanda_url'] ?? null,
                'pathao_url'     => $validated['pathao_url'] ?? null,
                'foodi_url'      => $validated['foodi_url'] ?? null,
            ];

            // Handle file uploads
            $this->handleLogoUploads($request, $branchData, $branch);

            $branch->update($branchData);
            
            \Log::info('Branch updated successfully');

            return response()->json([
                'status' => 'success',
                'message' => 'Branch updated successfully!'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Branch not found for update: ' . $id);
            return response()->json([
                'status' => 'error',
                'message' => 'Branch not found'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Update validation error: ' . json_encode($e->errors()));
            return response()->json([
                'status' => 'error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error updating branch: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete branch
     */
    public function destroy($id)
    {
        try {
            $branch = Branch::findOrFail($id);

            DB::transaction(function () use ($branch) {
                // Delete logos
                $logoFields = ['foodpanda_logo', 'pathao_logo', 'foodi_logo'];
                foreach ($logoFields as $field) {
                    if ($branch->$field && file_exists(public_path('uploads/branches/' . $branch->$field))) {
                        unlink(public_path('uploads/branches/' . $branch->$field));
                    }
                }

                $branch->delete();
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Branch deleted successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting branch: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle logo file uploads
     */
    private function handleLogoUploads(Request $request, &$branchData, $branch = null)
    {
        $logoFields = ['foodpanda_logo', 'pathao_logo', 'foodi_logo'];
        $uploadDir = public_path('uploads/branches');

        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        foreach ($logoFields as $field) {
            // Check if file was uploaded
            if ($request->hasFile($field) && $request->file($field)->isValid()) {
                // Delete old file if updating
                if ($branch && isset($branch->$field) && $branch->$field) {
                    $oldPath = $uploadDir . '/' . $branch->$field;
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }

                // Upload new file
                try {
                    $file = $request->file($field);
                    $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $file->move($uploadDir, $filename);
                    $branchData[$field] = $filename;
                    \Log::info('Uploaded file for ' . $field . ': ' . $filename);
                } catch (\Exception $e) {
                    \Log::error('File upload error for ' . $field . ': ' . $e->getMessage());
                    // Continue without this file - don't break the update
                    if (!$branch) {
                        $branchData[$field] = null;
                    }
                }
            } else if ($branch) {
                // If updating and no new file uploaded, keep existing
                if (isset($branch->$field) && $branch->$field) {
                    $branchData[$field] = $branch->$field;
                }
            }
        }
    }
}
