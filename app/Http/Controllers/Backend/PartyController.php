<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\PartyHallInfo;
use App\Models\PartyPackage;
use App\Models\PartyBookingInquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PartyController extends Controller
{
    // ----------------------------------------------------------------
    // PARTY HALL INFO (CMS)
    // ----------------------------------------------------------------

    public function hallIndex()
    {
        $hallInfo = PartyHallInfo::first() ?? new PartyHallInfo();
        $packages = PartyPackage::orderBy('sort_order')->orderBy('price')->get();
        return view('backend.party.hall', compact('hallInfo', 'packages'));
    }

    public function hallUpdate(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url'   => 'nullable|url|max:500',
            'gallery'     => 'nullable|array',
            'gallery.*'   => 'image|mimes:jpg,jpeg,png,webp|max:4096',
            'status'      => 'boolean',
        ]);

        $hallInfo = PartyHallInfo::firstOrNew(['id' => 1]);
        $hallInfo->title       = $request->title;
        $hallInfo->description = $request->description;
        $hallInfo->video_url   = $request->video_url;
        $hallInfo->status      = $request->boolean('status', true);

        // Handle gallery image uploads
        $existingImages = $hallInfo->gallery_images ?? [];
        if ($request->hasFile('gallery')) {
            $newImages = [];
            foreach ($request->file('gallery') as $file) {
                $path = $file->store('party_gallery', 'public');
                $newImages[] = 'storage/' . $path;
            }
            $existingImages = array_merge($existingImages, $newImages);
        }

        // Remove images if requested
        if ($request->has('remove_images')) {
            $toRemove = (array) $request->remove_images;
            $existingImages = array_values(array_filter($existingImages, fn($img) => !in_array($img, $toRemove)));
        }

        $hallInfo->gallery_images = $existingImages ?: null;
        $hallInfo->save();

        return response()->json(['status' => 'success', 'message' => 'Party hall info updated successfully!']);
    }

    // ----------------------------------------------------------------
    // PARTY PACKAGES CRUD
    // ----------------------------------------------------------------

    public function packageStore(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'price_label' => 'required|string|max:100',
            'description' => 'nullable|string',
            'includes'    => 'nullable|string',
            'min_guests'  => 'nullable|integer|min:1',
            'max_guests'  => 'nullable|integer|min:1',
            'sort_order'  => 'nullable|integer',
        ]);

        $package = PartyPackage::create($request->only([
            'name', 'price', 'price_label', 'description',
            'includes', 'min_guests', 'max_guests', 'sort_order',
        ]));

        return response()->json([
            'status'  => 'success',
            'message' => 'Package "' . $package->name . '" created!',
            'package' => $package,
        ]);
    }

    public function packageUpdate(Request $request, PartyPackage $package)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'price_label' => 'required|string|max:100',
        ]);

        $package->update($request->only([
            'name', 'price', 'price_label', 'description',
            'includes', 'min_guests', 'max_guests', 'sort_order', 'status',
        ]));

        return response()->json(['status' => 'success', 'message' => 'Package updated!']);
    }

    public function packageDestroy(PartyPackage $package)
    {
        $package->delete();
        return response()->json(['status' => 'success', 'message' => 'Package deleted!']);
    }

    // ----------------------------------------------------------------
    // BOOKING INQUIRIES
    // ----------------------------------------------------------------

    public function bookingsIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = PartyBookingInquiry::with('package')->latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('package_name', fn($row) => $row->package?->name ?? '<span class="text-muted">None</span>')
                ->addColumn('event_date_fmt', fn($row) => $row->event_date?->format('d M Y'))
                ->addColumn('status_badge', function ($row) {
                    $colors = ['pending' => 'warning', 'confirmed' => 'success', 'cancelled' => 'danger'];
                    $color  = $colors[$row->status] ?? 'secondary';
                    return '<span class="badge bg-' . $color . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                    <div class="d-flex gap-1">
                        <select class="form-select form-select-sm status-select" data-id="' . $row->id . '" style="width:130px;">
                            <option value="pending"   ' . ($row->status === 'pending'   ? 'selected' : '') . '>Pending</option>
                            <option value="confirmed" ' . ($row->status === 'confirmed' ? 'selected' : '') . '>Confirmed</option>
                            <option value="cancelled" ' . ($row->status === 'cancelled' ? 'selected' : '') . '>Cancelled</option>
                        </select>
                    </div>';
                })
                ->rawColumns(['package_name', 'status_badge', 'action'])
                ->make(true);
        }

        return view('backend.party.bookings');
    }

    public function bookingUpdateStatus(Request $request, PartyBookingInquiry $booking)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,cancelled']);
        $booking->update(['status' => $request->status]);
        return response()->json(['status' => 'success', 'message' => 'Booking status updated!']);
    }
}
