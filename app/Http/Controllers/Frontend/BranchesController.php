<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;

class BranchesController extends Controller
{
    public function index()
    {
        $branches = Branch::where('status', 1)->orderBy('name')->get();
        return view('frontend.branches.index', compact('branches'));
    }

    public function show(Branch $branch)
    {
        // Get categories that belong to this branch OR are global (branch_id is null)
        // Also need to filter menus by branch
        $categories = Category::where('status', 1)
            ->where(function($query) use ($branch) {
                $query->where('branch_id', $branch->id)
                      ->orWhereNull('branch_id');
            })
            ->with(['menus' => function ($query) use ($branch) {
                // Only get menus that are available and have variations
                $query->where('is_available', 1)
                      ->with('variations');
            }])
            ->orderBy('name')
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
        \Log::info('Search Menu - Branch: ' . $branch->slug . ', Query: ' . $query);
        
        if (!$query || strlen($query) < 2) {
            return response()->json(['data' => []]);
        }

        // Search in available menus for this branch only
        $menus = Menu::where('is_available', 1)
            ->where('name', 'LIKE', "%{$query}%")
            ->with(['category', 'variations'])
            ->whereHas('variations') // Only menus with variations
            ->whereHas('category', function($q) use ($branch) {
                // Filter by branch categories
                $q->where(function($subQ) use ($branch) {
                    $subQ->where('branch_id', $branch->id)
                         ->orWhereNull('branch_id');
                });
            })
            ->limit(15)
            ->get();

        \Log::info('Found ' . $menus->count() . ' menus');

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

        \Log::info('Search results:', $results->toArray());
        return response()->json(['data' => $results]);
    }
}
