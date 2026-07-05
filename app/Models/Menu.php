<?php

namespace App\Models;

use App\Models\Category;
use App\Models\MenuVariation; // Import the variation model
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    // price and image are removed from here because they are now moved to the MenuVariation model

    protected $fillable = ['category_id', 'name', 'slug', 'description', 'is_available', 'is_popular'];

    protected $casts = [
        'is_popular'   => 'boolean',
        'is_available' => 'boolean',
    ];
    /**
     * Get the category that owns the menu item.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the variations for the menu item (e.g., 1:2, 1:3).
     */
    public function variations()
    {
        // Using 'menu_id' as the foreign key in the menu_variations table
        return $this->hasMany(MenuVariation::class, 'menu_id');
    }
}
