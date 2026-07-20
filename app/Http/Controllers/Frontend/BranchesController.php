<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BranchesController extends Controller
{
    public function index()
    {
        $branches = Branch::query()
            ->where('status', 1)
            ->orderBy('name', 'asc')
            ->get();
        return view('frontend.branches.index', compact('branches'));
    }

    public function show(Branch $branch)
    {
        // Get categories that belong to this branch OR are global (branch_id is null)
        // Also need to filter menus by branch
        $categories = Category::query()
            ->where('status', 1)
            ->where(function ($query) use ($branch) {
                $query->where('branch_id', $branch->id)
                      ->orWhereNull('branch_id');
            })
            ->with(['menus' => function ($query) {
                // Only get menus that are available and have variations
                $query->where('is_available', 1)
                      ->with([
                          'variations' => function ($q) {
                              $q->with([
                                  'offers' => function ($offerQuery) {
                                      $offerQuery->where('is_active', true)
                                          ->where(function ($subQ) {
                                              $subQ->whereNull('valid_from')->orWhere('valid_from', '<=', now());
                                          })
                                          ->where(function ($subQ) {
                                              $subQ->whereNull('valid_until')->orWhere('valid_until', '>=', now());
                                          })
                                          ->select([
                                              'offers.id',
                                              'offers.name',
                                              'offers.discount_percent',
                                              'offers.is_first_order',
                                              'offers.applicable_to',
                                              'offers.offer_type',
                                              'offers.is_active',
                                              'offers.valid_from',
                                              'offers.valid_until',
                                          ]);
                                  },
                              ]);
                          },
                      ]);
            }])
            ->orderBy('name', 'asc')
            ->get()
            // Filter to only categories that have menus
            ->filter(function ($category) {
                return $category->menus->isNotEmpty();
            })
            ->values();

        $deliveryServices = [];
        if ($branch->foodpanda_url) {
            $deliveryServices['foodpanda'] = [
                'name' => 'FoodPanda',
                'url' => $branch->foodpanda_url,
                'icon' => 'icon-foodpanda'
            ];
        }
        if ($branch->pathao_url) {
            $deliveryServices['pathao'] = [
                'name' => 'Pathao',
                'url' => $branch->pathao_url,
                'icon' => 'icon-pathao'
            ];
        }
        if ($branch->foodi_url) {
            $deliveryServices['foodi'] = [
                'name' => 'Foodi',
                'url' => $branch->foodi_url,
                'icon' => 'icon-foodi'
            ];
        }

        return view('frontend.branches.show', compact('branch', 'categories', 'deliveryServices'));
    }

    public function searchMenu(Request $request, Branch $branch)
    {
        $query = $request->get('q');
        Log::info('Search Menu - Branch: ' . $branch->slug . ', Query: ' . $query);

        if (!$query || strlen($query) < 2) {
            return response()->json(['data' => []]);
        }

        // Search in available menus for this branch only
        $menus = Menu::query()
            ->where('is_available', 1)
            ->where('name', 'LIKE', "%{$query}%")
            ->with(['category', 'variations'])
            ->whereHas('variations') // Only menus with variations
            ->whereHas('category', function ($q) use ($branch) {
                // Filter by branch categories
                $q->where(function ($subQ) use ($branch) {
                    $subQ->where('branch_id', $branch->id)
                         ->orWhereNull('branch_id');
                });
            })
            ->limit(15)
            ->get();

        Log::info('Found ' . $menus->count() . ' menus');

        $results = $menus->map(function ($menu) {
            return [
                'id' => $menu->id,
                'name' => $menu->name,
                'category' => $menu->category->name ?? 'Uncategorized',
                'price' => $menu->variations->min('price') ?? 0,
                'image' => $menu->variations->first()?->image,
                'description' => $menu->description,
            ];
        });

        Log::info('Search results:', $results->toArray());
        return response()->json(['data' => $results]);
    }
}
