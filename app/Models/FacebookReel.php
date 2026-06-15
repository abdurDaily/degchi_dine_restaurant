<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacebookReel extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'facebook_url',
        'thumbnail',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
