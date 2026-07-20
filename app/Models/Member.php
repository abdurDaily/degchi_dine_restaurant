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

    public const ACCOUNT_RESTRICTED_MESSAGE = 'Your account temporary suspand. contact our help line';

    /**
     * Active account only — pending/suspended cannot order or comment.
     * Student pending/rejected approval is also blocked.
     */
    public function canOrderAndComment(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->is_student && $this->approval_status !== 'approved') {
            return false;
        }

        return true;
    }

    public function accountRestrictedMessage(): string
    {
        return self::ACCOUNT_RESTRICTED_MESSAGE;
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
        // Orders linked to this member account
        if ($this->orders()->whereNotIn('status', ['canceled'])->exists()) {
            return true;
        }

        // Orders placed with this membership card (even if member_id was missing)
        if ($this->unique_card_number) {
            return Order::query()
                ->whereNotIn('status', ['canceled'])
                ->where('unique_card_number', $this->unique_card_number)
                ->exists();
        }

        return false;
    }

    /**
     * Persist first-order-used when historical orders exist but the flag was never set.
     */
    public function syncFirstOrderDiscountFlag(): void
    {
        if ($this->first_order_discount_used) {
            return;
        }

        if ($this->hasCompletedOrders()) {
            static::where('id', $this->id)->update(['first_order_discount_used' => true]);
            $this->first_order_discount_used = true;
        }
    }

    /**
     * Whether the member can still receive the one-time first-order discount (30% / 35%).
     */
    public function canUseFirstOrderDiscount(): bool
    {
        $this->syncFirstOrderDiscountFlag();

        if ($this->isGolden() || $this->isExpired()) {
            return false;
        }

        if ($this->first_order_discount_used || $this->hasCompletedOrders()) {
            return false;
        }

        return true;
    }

    /**
     * Active Membership / Student first-order offer from the offers table (system rows).
     */
    public function memberTierOffer(): ?Offer
    {
        $applicable = $this->is_student ? 'student' : 'membership';

        return Offer::query()
            ->active()
            ->valid()
            ->where('applicable_to', $applicable)
            ->where('discount_percent', '>', 0)
            ->orderByDesc('discount_percent')
            ->first();
    }

    /**
     * Membership/Student discount % for this order, respecting First Order Only on the offer.
     */
    public function firstOrderDiscountPercent(): ?int
    {
        if ($this->isGolden() || $this->isExpired()) {
            return null;
        }

        if ($this->is_student && $this->approval_status !== 'approved') {
            return null;
        }

        $offer = $this->memberTierOffer();

        // Fallback to built-in rates when system offer rows are missing (always first-order only)
        if (! $offer) {
            if (! $this->canUseFirstOrderDiscount()) {
                return null;
            }

            return $this->is_student
                ? (int) (self::FIRST_ORDER_RATE_STUDENT * 100)
                : (int) (self::FIRST_ORDER_RATE_STANDARD * 100);
        }

        // First Order Only ON → only while member has never ordered
        if ($offer->is_first_order) {
            if (! $this->canUseFirstOrderDiscount()) {
                return null;
            }

            return (int) $offer->discount_percent;
        }

        // First Order Only OFF → every order for this membership/student tier
        return (int) $offer->discount_percent;
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
    public function resolveMemberDiscount(float $orderTotal, bool $autoUpgrade = false): array
    {
        $this->syncFirstOrderDiscountFlag();

        $firstOrderAlreadyUsed = $this->first_order_discount_used || $this->hasCompletedOrders();

        $base = [
            'eligible' => false,
            'amount' => 0.0,
            'rate' => 0,
            'member_type' => $this->type,
            'first_order_discount_used' => $firstOrderAlreadyUsed,
        ];

        if ($this->isExpired()) {
            $base['message'] = 'This membership card has expired. Validity is 1 year for standard and 5 years for golden.';

            return $base;
        }

        // Golden = every order (not limited by First Order Only on Membership/Student offers)
        if ($this->isGolden()) {
            $msg = 'Golden Card Holder: 10% discount on every order.';
            if ($firstOrderAlreadyUsed) {
                $msg .= ' (Your Membership/Student first-order discount was already used.)';
            }

            return array_merge($base, [
                'eligible' => true,
                'amount' => $this->goldenDiscountAmount($orderTotal),
                'rate' => 10,
                'member_type' => 'golden',
                'message' => $msg,
            ]);
        }

        $tierPercent = $this->firstOrderDiscountPercent();
        if ($tierPercent !== null) {
            $offer = $this->memberTierOffer();
            $isFirstOrderOffer = $offer ? (bool) $offer->is_first_order : true;

            return array_merge($base, [
                'eligible' => true,
                'amount' => round($orderTotal * ($tierPercent / 100), 2),
                'rate' => $tierPercent,
                'first_order_discount_used' => false,
                'message' => $isFirstOrderOffer
                    ? sprintf('%d%% first-order %s discount applied (one time only).', $tierPercent, $this->is_student ? 'student' : 'membership')
                    : sprintf('%d%% %s discount applied to all food items.', $tierPercent, $this->is_student ? 'student' : 'membership'),
            ]);
        }

        if ($this->is_student && $this->approval_status !== 'approved' && ! $firstOrderAlreadyUsed) {
            $statusMessage = $this->approval_status === 'rejected'
                ? 'Your student membership has been rejected by admin. Please contact support for assistance.'
                : 'Your student membership is pending admin approval. First-order discount will be available once approved.';

            return array_merge($base, [
                'message' => $statusMessage,
                'is_student' => true,
                'approval_status' => $this->approval_status,
            ]);
        }

        // Eligible for golden by spend — only upgrade when explicitly allowed (after order credit)
        if ($this->qualifiesForGoldenUpgrade()) {
            if ($autoUpgrade) {
                $this->upgradeToGolden();

                return array_merge($base, [
                    'eligible' => true,
                    'amount' => round($orderTotal * self::GOLDEN_DISCOUNT_RATE, 2),
                    'rate' => 10,
                    'member_type' => 'golden',
                    'message' => 'Golden Card unlocked: 10% discount on every order.',
                ]);
            }

            $base['message'] = 'You have reached the Golden Card threshold (৳'
                .number_format(self::GOLDEN_UPGRADE_THRESHOLD, 0)
                .'). Complete this order to unlock 10% on every future order. '
                .'Your Membership/Student first-order discount is already used.';

            return $base;
        }

        $remaining = max(0, self::GOLDEN_UPGRADE_THRESHOLD - $this->liveTotalPurchase());

        return array_merge($base, [
            'message' => $firstOrderAlreadyUsed
                ? ($remaining > 0
                    ? 'First-order membership/student discount already used — no membership discount on this order. Spend ৳'
                        .number_format($remaining, 2)
                        .' more to unlock Golden Card (10% on every order).'
                    : 'First-order membership/student discount already used — no membership discount on this order.')
                : ($remaining > 0
                    ? 'No membership discount on this order. Spend ৳'.number_format($remaining, 2).' more to unlock Golden Card (10% on every order).'
                    : 'No membership discount on this order.'),
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
