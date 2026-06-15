<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignaturePlatter extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'thumbnail_image',
        'menu_card_image',
        'features',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
    ];
}
