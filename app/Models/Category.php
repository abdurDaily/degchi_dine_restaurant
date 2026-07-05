<?php

namespace App\Models;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'branch_id',
        'name',
        'slug',
        'image',
        'status',
        'sort_order',
    ];
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }
}
