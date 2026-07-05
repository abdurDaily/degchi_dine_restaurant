<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Member extends Authenticatable
{
    public const GOLDEN_UPGRADE_THRESHOLD = 2000;

    public const FIRST_ORDER_RATE_STANDARD = 0.30;

    public const FIRST_ORDER_RATE_STUDENT = 0.35;

    public const GOLDEN_DISCOUNT_RATE = 0.10;
    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'dob',
        'marriage_date',
        'address',
        'unique_card_number',
        'last4',
        'is_student',
        'approval_status',
        'profile_image_path',
        'student_card_path',
        'type',
        'status',
        'total_purchase',
        'first_order_discount_used',
        'expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_student' => 'boolean',
        'first_order_discount_used' => 'boolean',
        'total_purchase' => 'decimal:2',
        'dob' => 'date',
        'marriage_date' => 'date',
        'expires_at' => 'date',
        'password' => 'hashed',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isGolden(): bool
    {
        return $this->type === 'golden';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Upgrade member to Golden Card (10% on every order, 5-year validity).
     */
    public function upgradeToGolden(): bool
    {
        if ($this->isGolden()) {
            return false;
        }

        $this->update([
            'type' => 'golden',
            'expires_at' => now()->addYears(5),
        ]);

        return true;
    }

    /**
     * Golden card holders receive 10% off the order subtotal.
     */
    public function goldenDiscountAmount(float $orderTotal): float
    {
        if (!$this->isGolden() || $this->isExpired()) {
            return 0.0;
        }

        return round($orderTotal * self::GOLDEN_DISCOUNT_RATE, 2);
    }

    /**
     * Total purchase including orders not yet credited to the member profile.
     */
    public function liveTotalPurchase(): float
    {
        $uncreditedTotal = $this->orders()
            ->where('member_credited', false)
            ->whereNotIn('status', ['canceled'])
            ->sum('final_amount');

        return round((float) $this->total_purchase + (float) $uncreditedTotal, 2);
    }

    public function hasCompletedOrders(): bool
    {
        return $this->orders()->whereNotIn('status', ['canceled'])->exists();
    }

    /**
     * Whether the member can still receive the one-time first-order discount (30% / 35%).
     */
    public function canUseFirstOrderDiscount(): bool
    {
        if ($this->isGolden() || $this->isExpired()) {
            return false;
        }

        if ($this->first_order_discount_used || $this->hasCompletedOrders()) {
            return false;
        }

        return true;
    }

    public function firstOrderDiscountPercent(): ?int
    {
        if (!$this->canUseFirstOrderDiscount()) {
            return null;
        }

        if ($this->is_student && $this->approval_status !== 'approved') {
            return null;
        }

        return $this->is_student ? 35 : 30;
    }

    public function qualifiesForGoldenUpgrade(): bool
    {
        return !$this->isGolden()
            && $this->liveTotalPurchase() >= self::GOLDEN_UPGRADE_THRESHOLD;
    }

    /**
     * Resolve membership discount for checkout / order placement.
     *
     * @return array{eligible: bool, amount: float, rate: int, member_type: string, message: string, first_order_discount_used: bool}
     */
    public function resolveMemberDiscount(float $orderTotal, bool $autoUpgrade = true): array
    {
        $base = [
            'eligible' => false,
            'amount' => 0.0,
            'rate' => 0,
            'member_type' => $this->type,
            'first_order_discount_used' => $this->first_order_discount_used || $this->hasCompletedOrders(),
        ];

        if ($this->isExpired()) {
            $base['message'] = 'This membership card has expired. Validity is 1 year for standard and 5 years for golden.';

            return $base;
        }

        if ($this->isGolden()) {
            return array_merge($base, [
                'eligible' => true,
                'amount' => $this->goldenDiscountAmount($orderTotal),
                'rate' => 10,
                'member_type' => 'golden',
                'message' => 'Golden Card Holder: 10% discount applied to all food items.',
            ]);
        }

        $firstOrderPercent = $this->firstOrderDiscountPercent();
        if ($firstOrderPercent !== null) {
            return array_merge($base, [
                'eligible' => true,
                'amount' => round($orderTotal * ($firstOrderPercent / 100), 2),
                'rate' => $firstOrderPercent,
                'first_order_discount_used' => false,
                'message' => sprintf('%d%% first-order discount applied to all food items.', $firstOrderPercent),
            ]);
        }

        if ($this->canUseFirstOrderDiscount() && $this->is_student && $this->approval_status !== 'approved') {
            $statusMessage = $this->approval_status === 'rejected'
                ? 'Your student membership has been rejected by admin. Please contact support for assistance.'
                : 'Your student membership is pending admin approval. First-order discount will be available once approved.';

            return array_merge($base, [
                'message' => $statusMessage,
                'is_student' => true,
                'approval_status' => $this->approval_status,
            ]);
        }

        if ($this->qualifiesForGoldenUpgrade()) {
            if ($autoUpgrade) {
                $this->upgradeToGolden();
            }

            return array_merge($base, [
                'eligible' => true,
                'amount' => round($orderTotal * self::GOLDEN_DISCOUNT_RATE, 2),
                'rate' => 10,
                'member_type' => 'golden',
                'message' => 'Golden Card Holder: 10% discount applied to all food items.',
            ]);
        }

        $remaining = max(0, self::GOLDEN_UPGRADE_THRESHOLD - $this->liveTotalPurchase());

        return array_merge($base, [
            'message' => $remaining > 0
                ? 'No discount available. Spend ৳' . number_format($remaining, 2) . ' more to unlock Golden Card with 10% discount on every order.'
                : 'No discount available on this order.',
        ]);
    }

    /**
     * Normalize phone to last 10 digits for comparison.
     */
    public static function normalizePhone(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if (strlen($digits) > 10) {
            return substr($digits, -10);
        }

        return $digits;
    }

    /**
     * Check if a phone number is already registered (normalized match).
     */
    public static function phoneExists(string $phone): bool
    {
        $target = self::normalizePhone($phone);

        if ($target === '') {
            return false;
        }

        return static::query()
            ->whereNotNull('phone')
            ->get()
            ->contains(fn (Member $member) => self::normalizePhone($member->phone) === $target);
    }

    public static function findByPhoneOrCard(string $login): ?self
    {
        $login = trim($login);

        $member = static::where('unique_card_number', $login)->first();
        if ($member) {
            return $member;
        }

        $target = self::normalizePhone($login);
        if ($target === '') {
            return null;
        }

        return static::query()
            ->whereNotNull('phone')
            ->get()
            ->first(fn (Member $member) => self::normalizePhone($member->phone) === $target);
    }
}
