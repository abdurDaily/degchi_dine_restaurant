<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    protected $fillable = [
        'author_id',
        'blog_category_id',
        'title',
        'slug',
        'content',
        'image',
        'is_active',
        'comments_enabled',
        'view_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'comments_enabled' => 'boolean',
        'view_count' => 'integer',
    ];

    protected $appends = [
        'image_url',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function blogCategory(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->latest();
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function getExcerptAttribute(): string
    {
        return Str::limit(strip_tags($this->content), 150);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        if (str_contains($this->image, '/')) {
            return asset($this->image);
        }

        return asset('uploads/posts/' . $this->image);
    }

    public function scopePublished($query)
    {
        return $query->where('is_active', true);
    }
}
