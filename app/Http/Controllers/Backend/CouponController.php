<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CouponController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:coupon-list')->only(['index', 'edit']);
        $this->middleware('permission:coupon-create')->only('store');
        $this->middleware('permission:coupon-edit')->only('update');
        $this->middleware('permission:coupon-delete')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Coupon::latest()->get();

            // Try to resolve currency code
            $currencyCode = '৳';
            if (session()->has('currency')) {
                $currency = \App\Models\Currency::find(session('currency'));
                if ($currency) {
                    $currencyCode = $currency->code;
                }
            } else {
                // Try from settings table
                $currencySetting = \App\Models\Setting::where('key', 'currency')->first();
                if ($currencySetting) {
                    $currency = \App\Models\Currency::find($currencySetting->value);
                    if ($currency) {
                        $currencyCode = $currency->code;
                    }
                }
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('discount_type', function ($row) {
                    if ($row->discount_type === 'percentage') {
                        return '<span class="badge bg-info-subtle text-info">Percentage</span>';
                    }
                    return '<span class="badge bg-primary-subtle text-primary">Flat</span>';
                })
                ->editColumn('discount_amount', function ($row) use ($currencyCode) {
                    if ($row->discount_type === 'percentage') {
                        return number_format($row->discount_amount, 2) . '%';
                    }
                    return $currencyCode . ' ' . number_format($row->discount_amount, 2);
                })
                ->editColumn('min_order_amount', function ($row) use ($currencyCode) {
                    return $currencyCode . ' ' . number_format($row->min_order_amount, 2);
                })
                ->addColumn('usage', function ($row) {
                    $limit = $row->usage_limit ?? 'Unlimited';
                    return $row->used_count . ' / ' . $limit;
                })
                ->editColumn('expires_at', function ($row) {
                    return $row->expires_at ? $row->expires_at->format('Y-m-d') : '<span class="text-muted">Never</span>';
                })
                ->editColumn('is_active', function ($row) {
                    return $row->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="d-flex gap-1 flex-wrap">
                        <button type="button" class="btn btn-sm btn-soft-warning edit-btn" data-id="' . $row->id . '" title="Edit">
                            <i class="ri-pencil-line"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-soft-danger delete-btn" data-id="' . $row->id . '" data-name="' . $row->name . '" title="Delete">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['discount_type', 'discount_amount', 'min_order_amount', 'expires_at', 'is_active', 'action'])
                ->make(true);
        }

        return view('backend.coupon.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:coupons,code',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:flat,percentage',
            'discount_amount' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date|after_or_equal:today',
        ];

        if ($request->discount_type === 'percentage') {
            $rules['discount_amount'] .= '|max:100';
        }

        $validated = $request->validate($rules);

        try {
            $validated['is_active'] = $request->has('is_active');
            $validated['used_count'] = 0;
            
            Coupon::create($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Coupon "' . $request->name . '" created successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coupon $coupon)
    {
        return response()->json($coupon);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'description' => 'nullable|string',
            'discount_type' => 'required|in:flat,percentage',
            'discount_amount' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
        ];

        if ($request->discount_type === 'percentage') {
            $rules['discount_amount'] .= '|max:100';
        }

        $validated = $request->validate($rules);

        try {
            $validated['is_active'] = $request->has('is_active');
            
            $coupon->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Coupon "' . $request->name . '" updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        try {
            $coupon->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Coupon deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
