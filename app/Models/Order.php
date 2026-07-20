<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'member_id',
        'unique_card_number',
        'customer_name',
        'customer_phone',
        'customer_address',
        'payment_method',
        'status',
        'status_remarks',
        'transaction_id',
        'payment_status',
        'payment_date',
        'payment_details',
        'viewed_at',
        'total_amount',
        'discount_amount',
        'final_amount',
        'student_card_used',
        'items',
        'member_credited',
        'coupon_code',
        'coupon_discount',
    ];

    protected $casts = [
        'total_amount'    => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount'    => 'decimal:2',
        'coupon_discount' => 'decimal:2',
        'student_card_used' => 'boolean',
        'member_credited'   => 'boolean',
        'items'           => 'array',
        'payment_date'    => 'datetime',
        'viewed_at'       => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Credit order amount to member's total purchase and handle card upgrades.
     */
    public function creditMemberPurchase(): void
    {
        if ($this->member_id && !$this->member_credited) {
            $member = $this->member;
            if ($member) {
                $member->total_purchase += (float) $this->final_amount;

                // First credited order consumes the one-time first-order discount slot
                if (!$member->first_order_discount_used) {
                    $member->first_order_discount_used = true;
                }

                // Auto-upgrade to Golden Card when total purchase reaches ৳2,000
                if (!$member->isGolden() && $member->total_purchase >= Member::GOLDEN_UPGRADE_THRESHOLD) {
                    $member->upgradeToGolden();
                }

                $member->save();

                $this->update(['member_credited' => true]);
            }
        }
    }

    /**
     * Accessor for payment status badge.
     */
    public function getPaymentBadgeAttribute(): string
    {
        return match ($this->payment_status) {
            'paid'      => '<span class="badge bg-success">Paid</span>',
            'failed'    => '<span class="badge bg-danger">Failed</span>',
            'cancelled' => '<span class="badge bg-warning text-dark">Cancelled</span>',
            default     => '<span class="badge bg-secondary">Unpaid</span>',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'confirmed'  => '<span class="badge bg-info">Confirmed</span>',
            'completed'  => '<span class="badge bg-success">Completed</span>',
            'canceled'   => '<span class="badge bg-danger">Canceled</span>',
            default      => '<span class="badge bg-warning text-dark">Pending</span>',
        };
    }

    /**
     * Normalized line items (handles legacy double-encoded JSON strings).
     */
    public function normalizedItems(): array
    {
        $items = $this->items;

        // Array cast can leave a JSON string when data was double-encoded on save
        if (is_string($items)) {
            $decoded = json_decode($items, true);
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }
            $items = $decoded;
        }

        if (! is_array($items)) {
            return [];
        }

        // Re-index and ensure each row is an array (not stdClass)
        return array_values(array_map(function ($item) {
            return is_array($item) ? $item : (array) $item;
        }, $items));
    }
}



/**
 * "/card-apply" page have to add a input filed for accept student card image. cz if student card can show then first time he will get 35% discount. and if he is not  student then he will get 30% discount. but it nullable. 
 * 
 * "/members" page "Total Purchase" column not count the amount. need to fix it also add a "Action" column. whereas what you feel better for improve the user experience 
 * 
 * 
 */