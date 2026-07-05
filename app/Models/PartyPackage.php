<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartyPackage extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'price_label',
        'includes',
        'min_guests',
        'max_guests',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'price' => 'decimal:2',
    ];

    /**
     * Get includes as an array (stored as comma-separated or JSON).
     */
    public function getIncludesListAttribute(): array
    {
        if (!$this->includes) return [];
        // Try JSON first
        $decoded = json_decode($this->includes, true);
        if (is_array($decoded)) return $decoded;
        // Fallback: comma-separated
        return array_map('trim', explode(',', $this->includes));
    }
}
