<?php

namespace App\Models;

use App\Notifications\MemberResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

class Member extends Authenticatable
{
    use Notifiable;

    public const GOLDEN_UPGRADE_THRESHOLD = 2000;

    public const FIRST_ORDER_RATE_STANDARD = 0.30;

    public const FIRST_ORDER_RATE_STUDENT = 0.35;

    public const GOLDEN_DISCOUNT_RATE = 0.10;

    /** Fallback welcome window (months) when a welcome offer row has no duration set. */
    public const WELCOME_DEFAULT_DURATION_MONTHS = 6;
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

    /**
     * Normalize the profile image path for the "public" disk.
     * Uploads store "{file}.png" inside the "profile_images/" folder, but the
     * stored value only keeps the bare filename — prefix the folder so every
     * display site (asset('storage/' . $member->profile_image_path)) works.
     */
    public function getProfileImagePathAttribute(?string $value): ?string
    {
        if (! $value || str_contains($value, '/')) {
            return $value;
        }

        return 'profile_images/' . $value;
    }

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
     * Upgrade member to Golden Card (live Golden % on every order, 5-year validity).
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
     * Golden card holders receive the configured Golden % off the order subtotal.
     */
    public function goldenDiscountAmount(float $orderTotal): float
    {
        if (!$this->isGolden() || $this->isExpired()) {
            return 0.0;
        }

        $percent = $this->goldenDiscountPercent();

        return $percent > 0 ? round($orderTotal * ($percent / 100), 2) : 0.0;
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
        return $this->tierOfferFor($this->is_student ? 'student' : 'membership');
    }

    /**
     * Active, in-date Golden offer row. Unlike the first-order tiers, the
     * seeded golden row historically carries 0% and only stores the upgrade
     * threshold, so it must NOT be filtered by discount_percent here.
     */
    public function goldenTierOffer(): ?Offer
    {
        return Offer::query()
            ->active()
            ->valid()
            ->where('applicable_to', 'golden')
            ->orderByDesc('min_total')
            ->first();
    }

    /**
     * A golden row exists at all (including a switched-off one). This lets us
     * tell "admin switched the built-in offer off" apart from "never configured".
     */
    public function goldenOfferIsConfigured(): bool
    {
        return Offer::query()->where('applicable_to', 'golden')->exists();
    }

    /**
     * The Golden benefit is available unless its built-in row was disabled.
     * With no row configured the legacy built-in behaviour is kept.
     */
    public function goldenBenefitsAvailable(): bool
    {
        return ! $this->goldenOfferIsConfigured() || $this->goldenTierOffer() !== null;
    }

    /**
     * Golden Card discount %, read from the live offers row when one is set.
     * A legacy 0% row (or no row at all) keeps the original 10% rate; a row that
     * exists but is inactive removes the benefit completely.
     */
    public function goldenDiscountPercent(): int
    {
        $offer = $this->goldenTierOffer();

        if ($offer && (int) $offer->discount_percent > 0) {
            return (int) $offer->discount_percent;
        }

        if ($offer || ! $this->goldenOfferIsConfigured()) {
            return (int) round(self::GOLDEN_DISCOUNT_RATE * 100);
        }

        return 0;
    }

    /**
     * Whole-purchase Golden upgrade requirement. Null means the benefit is
     * currently switched off, so no threshold should be advertised.
     */
    public function goldenUpgradeThreshold(): ?float
    {
        if (! $this->goldenBenefitsAvailable()) {
            return null;
        }

        $offer = $this->goldenTierOffer();
        $minimum = $offer?->minimumTotal();

        return $minimum !== null ? $minimum : (float) self::GOLDEN_UPGRADE_THRESHOLD;
    }

    /**
     * Highest-percentage active, in-date offer row for a member tier.
     */
    private function tierOfferFor(string $applicableTo): ?Offer
    {
        return Offer::query()
            ->active()
            ->valid()
            ->where('applicable_to', $applicableTo)
            ->where('discount_percent', '>', 0)
            ->orderByDesc('discount_percent')
            ->first();
    }

    protected function tierOfferIsConfigured(): bool
    {
        $applicableTo = $this->is_student ? 'student' : 'membership';

        return Offer::query()->where('applicable_to', $applicableTo)->exists();
    }

    /**
     * Active, in-date welcome (new-member) offer row, if one is configured.
     * Welcome offers carry applicable_to = 'welcome', so this query can never
     * return a membership / student / golden tier row or a food-menu promo.
     */
    public function welcomeTierOffer(): ?Offer
    {
        return Offer::query()
            ->active()
            ->valid()
            ->where('applicable_to', 'welcome')
            ->where('discount_percent', '>', 0)
            ->orderByDesc('discount_percent')
            ->first();
    }

    /**
     * The moment this member's welcome window ends: registration date plus the
     * offer's welcome_duration_months.
     *
     * Month arithmetic never overflows the target month (31 Mar + 6 months =
     * 30 Sep, never 1 Oct; 31 Aug + 6 months = 28/29 Feb). created_at and
     * now() are both in the application timezone (UTC), so the comparison is
     * consistent everywhere.
     */
    public function welcomeDeadline(?Offer $offer = null): ?Carbon
    {
        $offer = $offer ?? $this->welcomeTierOffer();

        if (! $offer || ! $this->created_at) {
            return null;
        }

        $months = max(1, (int) ($offer->welcome_duration_months ?: self::WELCOME_DEFAULT_DURATION_MONTHS));

        return $this->created_at->copy()->addMonthsNoOverflow($months);
    }

    /**
     * Whether the member is still inside their personal welcome window.
     * The window is [registration, registration + N months) — the discount
     * stops the exact moment the deadline is reached.
     */
    public function withinWelcomePeriod(?Offer $offer = null): bool
    {
        $deadline = $this->welcomeDeadline($offer);

        return $deadline !== null && now()->lt($deadline);
    }

    /**
     * Welcome discount % for this order, or null when it does not apply.
     *
     * Follows the same gating style as the existing first-order offers: the
     * campaign must be active and currently valid (offer is_active +
     * valid_from/valid_until), the member must be inside their personal
     * registration-based window, students must be admin-approved (pending
     * students cannot order until approval — the same rule first-order offers
     * already follow; their window still counts from registration), and the
     * cart must satisfy the offer's min_total.
     */
    public function welcomeDiscountPercent(?float $subtotal = null): ?int
    {
        if ($this->is_student && $this->approval_status !== 'approved') {
            return null;
        }

        $offer = $this->welcomeTierOffer();

        if (! $offer || ! $offer->isValid()) {
            return null;
        }

        if (! $this->withinWelcomePeriod($offer)) {
            return null;
        }

        if (! $offer->meetsMinimumTotal($subtotal)) {
            return null;
        }

        return (int) $offer->discount_percent;
    }

    /**
     * Whether the golden tier's configured offer row allows the current cart
     * (honouring its `min_total`). With no golden offer row configured the
     * built-in golden benefit is always available, as before.
     */
    private function goldenTierAllowsOrder(?float $subtotal): bool
    {
        $offer = $this->goldenTierOffer();

        return $offer ? $offer->meetsMinimumTotal($subtotal) : true;
    }

    /**
     * Membership/Student discount % for this order, respecting First Order Only on the offer.
     *
     * $subtotal is the whole cart/order subtotal used to honour the tier offer's
     * `min_total`. Pass null when no cart context is available.
     */
    public function firstOrderDiscountPercent(?float $subtotal = null): ?int
    {
        if ($this->isGolden() || $this->isExpired()) {
            return null;
        }

        if ($this->is_student && $this->approval_status !== 'approved') {
            return null;
        }

        $offer = $this->memberTierOffer();

        // Keep legacy defaults only when this tier has never been configured.
        // An inactive or expired row is an intentional admin disable, not a
        // missing configuration.
        if (! $offer) {
            if ($this->tierOfferIsConfigured()) {
                return null;
            }

            if (! $this->canUseFirstOrderDiscount()) {
                return null;
            }

            return $this->is_student
                ? (int) (self::FIRST_ORDER_RATE_STUDENT * 100)
                : (int) (self::FIRST_ORDER_RATE_STANDARD * 100);
        }

        // Cart-level minimum order total: below it the tier offer cannot apply.
        if (!$offer->meetsMinimumTotal($subtotal)) {
            return null;
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
        $threshold = $this->goldenUpgradeThreshold();

        if ($this->isGolden() || $threshold === null) {
            return false;
        }

        return $this->liveTotalPurchase() >= $threshold;
    }

    /**
     * Resolve membership discount for checkout / order placement.
     *
     * $subtotal is the whole cart/order subtotal (undiscounted) used to enforce
     * the tier offer's `min_total`. $orderTotal is the money basis the discount
     * is computed on. They differ because item-level offers are subtracted first.
     * Pass $subtotal as null when no cart context is available.
     *
     * @return array{eligible: bool, amount: float, rate: int, member_type: string, message: string, first_order_discount_used: bool, welcome_discount_applicable: bool}
     */
    public function resolveMemberDiscount(float $orderTotal, bool $autoUpgrade = false, ?float $subtotal = null): array
    {
        $this->syncFirstOrderDiscountFlag();

        $firstOrderAlreadyUsed = $this->first_order_discount_used || $this->hasCompletedOrders();

        $base = [
            'eligible' => false,
            'amount' => 0.0,
            'rate' => 0,
            'member_type' => $this->type,
            'first_order_discount_used' => $firstOrderAlreadyUsed,
            'welcome_discount_applicable' => false,
        ];

        if ($this->isExpired()) {
            $base['message'] = 'This membership card has expired. Validity is 1 year for standard and 5 years for golden.';

            return $base;
        }

        // Golden = every order (not limited by First Order Only on Membership/Student offers)
        if ($this->isGolden()) {
            // Switching the built-in Golden row off disables the benefit itself.
            if (! $this->goldenBenefitsAvailable()) {
                $base['message'] = 'The Golden Card discount is currently unavailable.';

                return $base;
            }

            // A configured golden offer row may carry a cart-level min_total.
            if (!$this->goldenTierAllowsOrder($subtotal)) {
                $minimum = $this->goldenTierOffer()->minimumTotal();

                $base['message'] = sprintf(
                    'Golden Card discount applies when your order total reaches ৳%s. Add more items to unlock it.',
                    number_format((float) $minimum, 2)
                );

                return $base;
            }

            $goldenPercent = $this->goldenDiscountPercent();

            if ($goldenPercent <= 0) {
                $base['message'] = 'The Golden Card discount is currently unavailable.';

                return $base;
            }

            $msg = sprintf('Golden Card Holder: %d%% discount on every order.', $goldenPercent);
            if ($firstOrderAlreadyUsed) {
                $msg .= ' (Your Membership/Student first-order discount was already used.)';
            }

            return array_merge($base, [
                'eligible' => true,
                'amount' => $this->goldenDiscountAmount($orderTotal),
                'rate' => $goldenPercent,
                'member_type' => 'golden',
                'message' => $msg,
            ]);
        }

        $tierPercent = $this->firstOrderDiscountPercent($subtotal);
        $welcomePercent = $this->welcomeDiscountPercent($subtotal);

        // Existing tier benefit wins ties — the welcome offer only takes over
        // here when a tier rate exists and the welcome rate is strictly higher.
        // When no tier rate applies at all, the welcome discount is handled by
        // the final fallback BELOW, so the pending-student and Golden-upgrade
        // branches in between keep their existing behaviour untouched.
        if ($tierPercent !== null && $tierPercent >= (int) ($welcomePercent ?? 0)) {
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

        if ($tierPercent !== null && $welcomePercent !== null && $welcomePercent > $tierPercent) {
            return $this->welcomeDiscountResult($base, $welcomePercent, $orderTotal);
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
            $goldenPercent = $this->goldenDiscountPercent();
            $threshold = $this->goldenUpgradeThreshold() ?? (float) self::GOLDEN_UPGRADE_THRESHOLD;

            if ($autoUpgrade) {
                $this->upgradeToGolden();

                return array_merge($base, [
                    'eligible' => true,
                    'amount' => round($orderTotal * ($goldenPercent / 100), 2),
                    'rate' => $goldenPercent,
                    'member_type' => 'golden',
                    'message' => sprintf('Golden Card unlocked: %d%% discount on every order.', $goldenPercent),
                ]);
            }

            $base['message'] = 'You have reached the Golden Card threshold (৳'
                .number_format($threshold, 0)
                .'). Complete this order to unlock '.$goldenPercent.'% on every future order. '
                .'Your Membership/Student first-order discount is already used.';

            return $base;
        }

        // Welcome offer fills the remaining gap: a member inside their window
        // who received no other benefit (e.g. first-order discount already
        // used) still gets the welcome rate. The Golden upgrade paths above
        // keep their existing behaviour untouched — they win over welcome.
        if ($welcomePercent !== null) {
            return $this->welcomeDiscountResult($base, $welcomePercent, $orderTotal);
        }

        $threshold = $this->goldenUpgradeThreshold();
        $goldenPercent = $this->goldenDiscountPercent();
        $remaining = $threshold !== null
            ? max(0, $threshold - $this->liveTotalPurchase())
            : 0;
        $unlockHint = $remaining > 0 && $goldenPercent > 0
            ? ' Order ৳' . number_format($remaining, 2)
                . ' more worth of food to unlock the Golden Card. Then you will get ' . $goldenPercent . '% off on every order.'
            : '';

        return array_merge($base, [
            'message' => $firstOrderAlreadyUsed
                ? 'Your Membership/Student first-order discount has already been used — no membership discount applies to this order.' . $unlockHint
                : 'No membership discount applies to this order.' . $unlockHint,
        ]);
    }

    /**
     * Successful resolveMemberDiscount result for an active welcome offer.
     *
     * Reports the member's REAL first_order_discount_used flag (instead of the
     * forced false the tier branch uses) so checkout can never re-enable
     * first-order-only food promos for a member who already used them, while
     * still allowing the welcome discount itself through.
     *
     * @param array<string, mixed> $base
     * @return array<string, mixed>
     */
    private function welcomeDiscountResult(array $base, int $percent, float $orderTotal): array
    {
        $deadline = $this->welcomeDeadline();

        return array_merge($base, [
            'eligible' => true,
            'amount' => round($orderTotal * ($percent / 100), 2),
            'rate' => $percent,
            'first_order_discount_used' => (bool) $this->first_order_discount_used,
            'welcome_discount_applicable' => true,
            'message' => sprintf(
                '%d%% new-member welcome discount applied on every order until %s.',
                $percent,
                $deadline ? $deadline->format('M d, Y') : 'the offer ends'
            ),
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

        if ($login === '') {
            return null;
        }

        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            return static::query()
                ->whereNotNull('email')
                ->whereRaw('LOWER(email) = ?', [strtolower($login)])
                ->first();
        }

        $member = static::where('unique_card_number', $login)->first();
        if ($member) {
            return $member;
        }

        $target = self::normalizePhone($login);
        if ($target === '' || strlen($target) < 10) {
            return null;
        }

        return static::query()
            ->whereNotNull('phone')
            ->get()
            ->first(fn (Member $member) => self::normalizePhone($member->phone) === $target);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new MemberResetPassword($token));
    }
}
