<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class MenuController extends Controller
{
    private const VARIATION_UPLOAD_DIR = 'uploads/menus/variations';

    public function __construct(protected UploadService $uploadService)
    {
        $this->middleware('permission:menu-list')->only(['index', 'edit']);
        $this->middleware('permission:menu-create')->only('store');
        $this->middleware('permission:menu-edit')->only(['update', 'togglePopular']);
        $this->middleware('permission:menu-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Menu::with(['category', 'variations'])->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image_preview', function ($row) {
                    $path = $row->variations->first()?->image;
                    $image = $path
                        ? (str_starts_with($path, 'http') ? $path : asset($path))
                        : asset('assets/placeholder/placeholder.png');

                    return '<img src="'.$image.'" width="50" height="50" class="rounded shadow-sm object-fit-cover" />';
                })
                ->addColumn('category_name', fn ($row) => $row->category->name ?? 'N/A')
                ->addColumn('price_range', function ($row) {
                    if ($row->variations->isEmpty()) {
                        return '<span class="text-muted">No variations</span>';
                    }
                    $prices = $row->variations->pluck('price');
                    $min = $prices->min();
                    $max = $prices->max();

                    return $min == $max
                        ? '৳'.number_format($min, 2)
                        : '৳'.number_format($min, 2).' - ৳'.number_format($max, 2);
                })
                ->addColumn('variations_count', fn ($row) => '<span class="badge bg-soft-info text-info">'.$row->variations->count().' variations</span>')
                ->addColumn('status', function ($row) {
                    return $row->is_available
                        ? '<span class="badge bg-success"><i class="ri-check-line me-1"></i>Available</span>'
                        : '<span class="badge bg-danger"><i class="ri-close-line me-1"></i>Out of Stock</span>';
                })
                ->addColumn('popular_status', function ($row) {
                    $isPopular = (bool) $row->is_popular;
                    $activeClass = $isPopular ? 'is-active' : '';
                    $icon = $isPopular ? 'ri-star-fill' : 'ri-star-line';
                    $title = $isPopular ? 'Marked as Popular (click to remove)' : 'Mark as Popular';

                    return '
                        <button type="button"
                            class="btn-star-toggle '.$activeClass.'"
                            data-id="'.$row->id.'"
                            data-popular="'.($isPopular ? 1 : 0).'"
                            title="'.$title.'"
                            data-bs-toggle="tooltip">
                            <i class="'.$icon.'"></i>
                        </button>';
                })
                ->addColumn('action', function ($row) {
                    return '
                    <div class="d-flex gap-1 flex-wrap">
                        <button class="btn btn-sm btn-soft-info view-details-btn" data-id="'.$row->id.'" title="View Details" data-bs-toggle="tooltip">
                            <i class="ri-eye-fill"></i>
                        </button>
                        <button class="btn btn-sm btn-soft-warning edit-btn" data-id="'.$row->id.'" title="Edit" data-bs-toggle="tooltip">
                            <i class="ri-pencil-fill"></i>
                        </button>
                        <button class="btn btn-sm btn-soft-danger delete-btn" data-id="'.$row->id.'" title="Delete" data-bs-toggle="tooltip">
                            <i class="ri-delete-bin-fill"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['action', 'image_preview', 'variations_count', 'status', 'price_range', 'popular_status'])
                ->make(true);
        }

        $categories = Category::all();

        return view('backend.menu.index', compact('categories'));
    }

    public function togglePopular($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->is_popular = ! $menu->is_popular;
        $menu->save();

        return response()->json([
            'success' => true,
            'is_popular' => $menu->is_popular,
            'message' => $menu->is_popular
                ? $menu->name.' marked as Popular'
                : $menu->name.' removed from Popular',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate($this->menuRules(creating: true));

        try {
            DB::transaction(function () use ($request) {
                $menu = Menu::create([
                    'category_id' => $request->category_id,
                    'name' => $request->name,
                    'slug' => Str::slug($request->name).'-'.rand(1000, 9999),
                    'description' => $request->description,
                    'is_available' => (int) $request->input('is_available', 1) === 1,
                ]);

                $this->syncVariations($menu, $request);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Menu item & variations saved!',
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'status' => 'error',
                'message' => 'Could not save menu item: '.$e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        $menu = Menu::with(['variations', 'category'])->findOrFail($id);

        return response()->json($menu);
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::with('variations')->findOrFail($id);
        $request->validate($this->menuRules(creating: false));

        try {
            DB::transaction(function () use ($request, $menu) {
                $menu->update([
                    'category_id' => $request->category_id,
                    'name' => $request->name,
                    'description' => $request->description,
                    'is_available' => (int) $request->input('is_available', 1) === 1,
                    'slug' => Str::slug($request->name).'-'.$menu->id,
                ]);

                $this->syncVariations($menu, $request, replaceExisting: true);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Menu item updated successfully!',
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'status' => 'error',
                'message' => 'Could not update menu item: '.$e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $menu = Menu::with('variations')->findOrFail($id);

        foreach ($menu->variations as $variation) {
            $this->deleteVariationImage($variation->image);
        }

        $menu->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Deleted successfully!',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function menuRules(bool $creating): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_available' => 'nullable|in:0,1,true,false',
            'variations' => 'required|array|min:1',
            'variations.*.name' => 'required|string|max:255',
            'variations.*.price' => 'required|numeric|min:0',
            'variations.*.old_image' => 'nullable|string',
            'variations.*.image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ];
    }

    /**
     * Create variation rows from the request. When replacing, remove old rows
     * and only delete images that are no longer referenced.
     */
    private function syncVariations(Menu $menu, Request $request, bool $replaceExisting = false): void
    {
        $incoming = array_values($request->input('variations', []));
        $keptImages = [];

        if ($replaceExisting) {
            foreach ($incoming as $index => $vData) {
                if ($request->hasFile("variations.$index.image")) {
                    continue;
                }
                $old = $vData['old_image'] ?? null;
                if (is_string($old) && $old !== '') {
                    $keptImages[] = $old;
                }
            }

            foreach ($menu->variations as $oldVar) {
                if ($oldVar->image && ! in_array($oldVar->image, $keptImages, true)) {
                    $this->deleteVariationImage($oldVar->image);
                }
            }

            $menu->variations()->delete();
        }

        foreach ($incoming as $index => $vData) {
            $imagePath = null;

            if ($request->hasFile("variations.$index.image")) {
                $imagePath = $this->storeVariationImage(
                    $request->file("variations.$index.image"),
                    $index
                );
            } elseif (! empty($vData['old_image'])) {
                $imagePath = $vData['old_image'];
            }

            $menu->variations()->create([
                'name' => $vData['name'],
                'price' => $vData['price'],
                'image' => $imagePath,
            ]);
        }
    }

    private function storeVariationImage(UploadedFile $file, int $index): string
    {
        $uploadDir = public_path(self::VARIATION_UPLOAD_DIR);

        if (! is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $imageName = $this->uploadService->uploadTo($file, $uploadDir);

        return self::VARIATION_UPLOAD_DIR.'/'.$imageName;
    }

    private function deleteVariationImage(?string $path): void
    {
        if (! $path || str_starts_with($path, 'http')) {
            return;
        }

        $fullPath = public_path($path);
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}
