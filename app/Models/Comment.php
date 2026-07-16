<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    /** @use HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory;

    protected $fillable = [
        'post_id',
        'member_id',
        'parent_id',
        'comment',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'likes_count',
        'dislikes_count',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')
            ->where('is_active', true)
            ->latest();
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(CommentReaction::class);
    }

    public function getLikesCountAttribute(): int
    {
        if ($this->relationLoaded('reactions')) {
            return $this->reactions->where('reaction', 'like')->count();
        }

        return $this->reactions()->where('reaction', 'like')->count();
    }

    public function getDislikesCountAttribute(): int
    {
        if ($this->relationLoaded('reactions')) {
            return $this->reactions->where('reaction', 'dislike')->count();
        }

        return $this->reactions()->where('reaction', 'dislike')->count();
    }
}
