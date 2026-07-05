<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartyHallInfo extends Model
{
    protected $fillable = [
        'title',
        'description',
        'video_url',
        'gallery_images',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'gallery_images' => 'array',
    ];

    /**
     * Get gallery image URLs as an array.
     */
    public function getGalleryImageUrlsAttribute(): array
    {
        $images = $this->gallery_images ?? [];
        return collect($images)->map(function ($path) {
            return \Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])
                ? $path
                : asset($path);
        })->all();
    }
}
