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
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'twitter_card',
        'twitter_title',
        'twitter_description',
        'twitter_image',
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
        return $this->absoluteUrl($this->image);
    }

    public function scopePublished($query)
    {
        return $query->where('is_active', true);
    }

    public function seoTitle(): string
    {
        return $this->meta_title ?: $this->title;
    }

    public function seoDescription(): string
    {
        return $this->meta_description ?: $this->excerpt;
    }

    public function seoKeywords(): ?string
    {
        return $this->meta_keywords ?: null;
    }

    public function ogTitle(): string
    {
        return $this->og_title ?: $this->seoTitle();
    }

    public function ogDescription(): string
    {
        return $this->og_description ?: $this->seoDescription();
    }

    public function ogImageUrl(): ?string
    {
        return $this->absoluteUrl($this->og_image) ?: $this->image_url;
    }

    public function twitterTitle(): string
    {
        return $this->twitter_title ?: $this->ogTitle();
    }

    public function twitterDescription(): string
    {
        return $this->twitter_description ?: $this->ogDescription();
    }

    public function twitterImageUrl(): ?string
    {
        return $this->absoluteUrl($this->twitter_image) ?: $this->ogImageUrl();
    }

    public function twitterCardType(): string
    {
        return $this->twitter_card ?: 'summary_large_image';
    }

    protected function absoluteUrl(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        if (str_contains($value, '/')) {
            return asset($value);
        }

        return asset('uploads/posts/' . $value);
    }
}
