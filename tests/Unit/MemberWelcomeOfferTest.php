<?php

namespace Tests\Unit;

use App\Models\Coupon;
use App\Models\Member;
use App\Models\Offer;
use App\Services\OrderPricingService;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * New-member welcome offer: every order gets the offer's % for N months after
 * the member's own registration date (default 6), fully dynamic from the
 * backend offers CRUD.
 *
 * These assertions deliberately touch no database: the models are instantiated
 * in memory (with a Member subclass stubbing the few DB-backed lookups), so
 * the suite can never wipe or depend on a developer's MySQL data — the same
 * contract tests/Unit/OfferMinTotalTest follows.
 *
 * Pinned rules:
 *  - Window is [registration, registration + N months) with month-end clamping.
 *  - Campaign validity (is_active + valid_from/valid_until) gates the discount.
 *  - First-order / tier discounts keep precedence; welcome only wins when
 *    strictly higher or when no other member benefit applies.
 *  - Golden benefit and the Golden auto-upgrade path are untouched.
 *  - Pending students keep the existing approval message (no welcome shown).
 */
class MemberWelcomeOfferTest extends TestCase
{
    private const NOW = '2026-06-01 12:00:00';

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse(self::NOW));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    // ---- Factories -------------------------------------------------------

    private function welcomeOffer(array $overrides = []): Offer
    {
        return new Offer(array_merge([
            'name' => 'Welcome Offer',
            'discount_percent' => 10,
            'offer_type' => 'all_items',
            'applicable_to' => 'welcome',
            'is_active' => true,
            'is_first_order' => false,
            'welcome_duration_months' => 6,
            'min_total' => null,
            'valid_from' => null,
            'valid_until' => null,
        ], $overrides));
    }

    private function tierOffer(array $overrides = []): Offer
    {
        return new Offer(array_merge([
            'name' => 'Membership First Order',
            'discount_percent' => 30,
            'offer_type' => 'all_items',
            'applicable_to' => 'membership',
            'is_active' => true,
            'is_first_order' => true,
            'min_total' => null,
            'valid_from' => null,
            'valid_until' => null,
        ], $overrides));
    }

    private function member(array $overrides = [], ?string $registeredAt = null): WelcomeTestMember
    {
        $member = new WelcomeTestMember(array_merge([
            'name' => 'Test Member',
            'type' => 'membership',
            'status' => 'active',
            'is_student' => false,
            'approval_status' => 'approved',
            'first_order_discount_used' => false,
            'total_purchase' => 0,
            'expires_at' => null,
        ], $overrides));

        // created_at is not fillable — set it directly (still date-cast).
        $member->created_at = Carbon::parse($registeredAt ?? self::NOW);

        return $member;
    }

    // ---- A. Member window mechanics -------------------------------------

    public function test_member_inside_window_gets_welcome_discount_on_every_order(): void
    {
        $member = $this->member(
            ['first_order_discount_used' => true],
            '2026-05-10 09:00:00' // registered 3 weeks ago, inside the 6-month window
        );
        $member->stubWelcomeOffer = $this->welcomeOffer();

        $result = $member->resolveMemberDiscount(100.0, false, 100.0);

        $this->assertTrue($result['eligible']);
        $this->assertSame(10, $result['rate']);
        $this->assertSame(10.0, $result['amount']);
        $this->assertTrue($result['welcome_discount_applicable']);
        // The REAL flag must be reported so checkout never re-enables
        // first-order-only food promos.
        $this->assertTrue($result['first_order_discount_used']);
        $this->assertStringContainsString('10% new-member welcome discount', $result['message']);
        $this->assertStringContainsString('Nov 10, 2026', $result['message']);
    }

    public function test_existing_member_only_keeps_the_remaining_window(): void
    {
        // Registered 2 months ago: still ~4 months of welcome discount left.
        $member = $this->member(
            ['first_order_discount_used' => true],
            '2026-04-01 12:00:00'
        );
        $member->stubWelcomeOffer = $this->welcomeOffer();

        $this->assertTrue($member->withinWelcomePeriod());
        $this->assertSame(10, $member->resolveMemberDiscount(100.0, false, 100.0)['rate']);
    }

    public function test_member_past_window_gets_no_welcome_discount(): void
    {
        // Registered 7 months ago: window already over.
        $member = $this->member(
            ['first_order_discount_used' => true],
            '2025-11-01 12:00:00'
        );
        $member->stubWelcomeOffer = $this->welcomeOffer();

        $result = $member->resolveMemberDiscount(100.0, false, 100.0);

        $this->assertFalse($result['eligible']);
        $this->assertSame(0, $result['rate']);
        $this->assertFalse($result['welcome_discount_applicable']);
    }

    public function test_deadline_moment_is_exclusive(): void
    {
        // Registered exactly 6 months (to the second) before now → expired.
        $onDeadline = $this->member(['first_order_discount_used' => true], '2025-12-01 12:00:00');
        $onDeadline->stubWelcomeOffer = $this->welcomeOffer();
        $this->assertFalse($onDeadline->withinWelcomePeriod());
        $this->assertNull($onDeadline->welcomeDiscountPercent());

        // One second inside the window → still eligible.
        $inside = $this->member(['first_order_discount_used' => true], '2025-12-01 12:00:01');
        $inside->stubWelcomeOffer = $this->welcomeOffer();
        $this->assertTrue($inside->withinWelcomePeriod());
        $this->assertSame(10, $inside->welcomeDiscountPercent());
    }

    public function test_month_end_registration_clamps_instead_of_overflowing(): void
    {
        $offer = $this->welcomeOffer();

        $endOfMarch = $this->member([], '2026-03-31 10:00:00');
        $this->assertEquals(
            Carbon::parse('2026-09-30 10:00:00'),
            $endOfMarch->welcomeDeadline($offer),
            '31 Mar + 6 months must clamp to 30 Sep, never roll to 1 Oct.'
        );

        $endOfJanuary = $this->member([], '2026-01-31 23:30:00');
        $this->assertEquals(Carbon::parse('2026-07-31 23:30:00'), $endOfJanuary->welcomeDeadline($offer));

        $endOfAugust = $this->member([], '2026-08-31 08:00:00');
        $this->assertEquals(
            Carbon::parse('2027-02-28 08:00:00'),
            $endOfAugust->welcomeDeadline($offer),
            '31 Aug + 6 months clamps to 28 Feb (2027 is not a leap year).'
        );
    }

    public function test_default_window_is_six_months_when_duration_is_not_set(): void
    {
        $offer = $this->welcomeOffer(['welcome_duration_months' => null]);
        $member = $this->member([], '2026-01-15 08:00:00');

        $this->assertSame(Member::WELCOME_DEFAULT_DURATION_MONTHS, 6);
        $this->assertEquals(Carbon::parse('2026-07-15 08:00:00'), $member->welcomeDeadline($offer));
    }

    // ---- B. Campaign validity (admin-controlled) -------------------------

    public function test_inactive_campaign_never_reaches_members(): void
    {
        $member = $this->member(['first_order_discount_used' => true]);
        $member->stubWelcomeOffer = $this->welcomeOffer(['is_active' => false]);

        $this->assertNull($member->welcomeTierOffer());
        $this->assertFalse($member->resolveMemberDiscount(100.0, false, 100.0)['eligible']);
    }

    public function test_campaign_end_date_stops_the_discount_for_everyone(): void
    {
        // Member is inside their personal window, but the campaign itself
        // ended yesterday → no discount (documented strict-campaign rule).
        $member = $this->member(
            ['first_order_discount_used' => true],
            '2026-05-01 12:00:00'
        );
        $member->stubWelcomeOffer = $this->welcomeOffer(['valid_until' => now()->subDay()]);

        $this->assertFalse($member->withinWelcomePeriod());
        $this->assertFalse($member->resolveMemberDiscount(100.0, false, 100.0)['eligible']);
    }

    public function test_campaign_not_started_yet_is_ignored(): void
    {
        $member = $this->member(['first_order_discount_used' => true]);
        $member->stubWelcomeOffer = $this->welcomeOffer(['valid_from' => now()->addDay()]);

        $this->assertNull($member->welcomeDiscountPercent());
    }

    // ---- C. Precedence with the existing discount chain ------------------

    public function test_first_order_discount_still_wins_on_the_first_order(): void
    {
        $member = $this->member(); // first order not used yet
        $member->stubWelcomeOffer = $this->welcomeOffer();
        $member->stubTierOffer = $this->tierOffer(['discount_percent' => 30]);

        $result = $member->resolveMemberDiscount(100.0, false, 100.0);

        $this->assertTrue($result['eligible']);
        $this->assertSame(30, $result['rate']);
        $this->assertSame(30.0, $result['amount']);
        $this->assertFalse($result['welcome_discount_applicable']);
        $this->assertSame('30% first-order membership discount applied (one time only).', $result['message']);
    }

    public function test_builtin_first_order_rate_still_wins_when_tier_rows_are_missing(): void
    {
        // No tier offer rows at all → the built-in 30% first-order fallback
        // must still beat the welcome rate.
        $member = $this->member();
        $member->stubWelcomeOffer = $this->welcomeOffer();
        $member->stubTierOffer = null;

        $this->assertSame(30, $member->resolveMemberDiscount(100.0, false, 100.0)['rate']);
    }

    public function test_disabled_tier_offer_does_not_restore_the_builtin_discount(): void
    {
        $member = $this->member();
        $member->stubTierOffer = null;
        $member->stubTierOfferConfigured = true;

        $this->assertNull($member->firstOrderDiscountPercent());

        $result = $member->resolveMemberDiscount(100.0, false, 100.0);
        $this->assertFalse($result['eligible']);
        $this->assertSame(0, $result['rate']);
    }

    public function test_welcome_takes_over_once_the_first_order_discount_is_used(): void
    {
        $member = $this->member(['first_order_discount_used' => true]);
        $member->stubWelcomeOffer = $this->welcomeOffer();
        $member->stubTierOffer = $this->tierOffer(['discount_percent' => 30]);

        $result = $member->resolveMemberDiscount(100.0, false, 100.0);

        $this->assertTrue($result['eligible']);
        $this->assertSame(10, $result['rate']);
        $this->assertTrue($result['welcome_discount_applicable']);
        $this->assertStringContainsString('welcome discount', $result['message']);
    }

    public function test_higher_every_order_tier_offer_beats_welcome(): void
    {
        $member = $this->member(['first_order_discount_used' => true]);
        $member->stubWelcomeOffer = $this->welcomeOffer(['discount_percent' => 10]);
        $member->stubTierOffer = $this->tierOffer(['discount_percent' => 15, 'is_first_order' => false]);

        $result = $member->resolveMemberDiscount(100.0, false, 100.0);

        $this->assertSame(15, $result['rate']);
        $this->assertFalse($result['welcome_discount_applicable']);
        $this->assertSame('15% membership discount applied to all food items.', $result['message']);
    }

    public function test_welcome_wins_over_a_lower_every_order_tier_offer(): void
    {
        $member = $this->member(['first_order_discount_used' => true]);
        $member->stubWelcomeOffer = $this->welcomeOffer(['discount_percent' => 15]);
        $member->stubTierOffer = $this->tierOffer(['discount_percent' => 5, 'is_first_order' => false]);

        $result = $member->resolveMemberDiscount(100.0, false, 100.0);

        $this->assertSame(15, $result['rate']);
        $this->assertTrue($result['welcome_discount_applicable']);
    }

    public function test_golden_members_keep_their_golden_benefit(): void
    {
        $member = $this->member(['type' => 'golden', 'expires_at' => now()->addYears(3)]);
        $member->stubWelcomeOffer = $this->welcomeOffer();

        $result = $member->resolveMemberDiscount(100.0, false, 100.0);

        $this->assertTrue($result['eligible']);
        $this->assertSame(10, $result['rate']);
        $this->assertSame('golden', $result['member_type']);
        $this->assertFalse($result['welcome_discount_applicable']);
        $this->assertStringContainsString('Golden Card Holder', $result['message']);
    }

    public function test_golden_auto_upgrade_path_still_wins_over_welcome(): void
    {
        // Member already used the first-order discount and crossed the
        // ৳2,000 threshold: the order must still upgrade them to Golden.
        $member = $this->member(['first_order_discount_used' => true]);
        $member->stubWelcomeOffer = $this->welcomeOffer();
        $member->stubLiveTotalPurchase = 2000.0;

        $result = $member->resolveMemberDiscount(100.0, true, 100.0);

        $this->assertTrue($member->upgradedToGolden);
        $this->assertSame('golden', $result['member_type']);
        $this->assertSame(10, $result['rate']);
        $this->assertFalse($result['welcome_discount_applicable']);
    }

    public function test_golden_upgrade_display_message_is_unchanged_when_not_auto_upgrading(): void
    {
        $member = $this->member(['first_order_discount_used' => true]);
        $member->stubWelcomeOffer = $this->welcomeOffer();
        $member->stubLiveTotalPurchase = 2000.0;

        $result = $member->resolveMemberDiscount(100.0, false, 100.0);

        $this->assertFalse($result['eligible']);
        $this->assertFalse($member->upgradedToGolden);
        $this->assertStringContainsString('Golden Card threshold', $result['message']);
        $this->assertFalse($result['welcome_discount_applicable']);
    }

    // ---- D. Gating rules -------------------------------------------------

    public function test_pending_student_keeps_the_existing_approval_message(): void
    {
        $member = $this->member(['is_student' => true, 'approval_status' => 'pending']);
        $member->stubWelcomeOffer = $this->welcomeOffer();

        $result = $member->resolveMemberDiscount(100.0, false, 100.0);

        $this->assertFalse($result['eligible']);
        $this->assertSame(0, $result['rate']);
        $this->assertFalse($result['welcome_discount_applicable']);
        $this->assertStringContainsString('pending admin approval', $result['message']);
    }

    public function test_approved_student_gets_the_welcome_discount(): void
    {
        $member = $this->member([
            'is_student' => true,
            'approval_status' => 'approved',
            'first_order_discount_used' => true,
        ]);
        $member->stubWelcomeOffer = $this->welcomeOffer();

        $this->assertSame(10, $member->resolveMemberDiscount(100.0, false, 100.0)['rate']);
    }

    public function test_min_total_gates_the_welcome_discount(): void
    {
        $member = $this->member(['first_order_discount_used' => true]);
        $member->stubWelcomeOffer = $this->welcomeOffer(['min_total' => '2000.00']);

        // Below the whole-cart minimum → no welcome discount.
        $this->assertNull($member->welcomeDiscountPercent(1500.0));
        $this->assertFalse($member->resolveMemberDiscount(1500.0, false, 1500.0)['eligible']);

        // At the minimum → applies.
        $this->assertSame(10, $member->welcomeDiscountPercent(2000.0));
        $this->assertSame(10, $member->resolveMemberDiscount(2500.0, false, 2500.0)['rate']);
    }

    public function test_expired_membership_blocks_the_welcome_discount(): void
    {
        $member = $this->member([
            'first_order_discount_used' => true,
            'expires_at' => now()->subDay(),
        ]);
        $member->stubWelcomeOffer = $this->welcomeOffer();

        $result = $member->resolveMemberDiscount(100.0, false, 100.0);

        $this->assertFalse($result['eligible']);
        $this->assertStringContainsString('expired', $result['message']);
    }

    public function test_historical_orders_do_not_affect_the_welcome_window(): void
    {
        // The welcome window is purely time-based: order history (which only
        // consumes the one-time first-order slot) never removes it.
        $member = $this->member(['first_order_discount_used' => true]);
        $member->stubWelcomeOffer = $this->welcomeOffer();
        $member->stubHasCompletedOrders = true;

        $this->assertSame(10, $member->resolveMemberDiscount(100.0, false, 100.0)['rate']);
    }

    // ---- E. Stacking inside OrderPricingService --------------------------

    public function test_welcome_stacks_with_item_offers_and_coupons(): void
    {
        $member = $this->member(['first_order_discount_used' => true]);
        $member->stubWelcomeOffer = $this->welcomeOffer();

        $items = [[
            'original_price' => 1000.0,
            'price' => 1000.0,
            'quantity' => 1,
            'offer_discount' => 100.0, // 10% item-level promo, subtracted first
        ]];

        $totals = (new OrderPricingService())->recalculateTotals($items, $member, 50.0, 60.0);

        $this->assertSame(1000.0, $totals['total_amount']);
        $this->assertSame(100.0, $totals['offer_discount']);
        $this->assertSame(90.0, $totals['member_discount']); // 10% of the ৳900 after item offers
        $this->assertSame(50.0, $totals['coupon_discount']);
        $this->assertSame(240.0, $totals['discount_amount']);
        $this->assertSame(760.0, $totals['food_total']);
        $this->assertSame(820.0, $totals['final_amount']); // food + ৳60 delivery
    }

    public function test_combined_discounts_never_exceed_the_food_subtotal(): void
    {
        $member = $this->member(['first_order_discount_used' => true]);
        $member->stubWelcomeOffer = $this->welcomeOffer();

        $items = [[
            'original_price' => 1000.0,
            'price' => 1000.0,
            'quantity' => 1,
            'offer_discount' => 100.0,
        ]];

        // Absurd coupon: total discount must cap at the subtotal; delivery is
        // never discounted.
        $totals = (new OrderPricingService())->recalculateTotals($items, $member, 5000.0, 60.0);

        $this->assertSame(1000.0, $totals['discount_amount']);
        $this->assertSame(0.0, $totals['food_total']);
        $this->assertSame(60.0, $totals['final_amount']);
    }

    public function test_coupon_rules_are_unaffected_by_the_welcome_offer(): void
    {
        $percentage = new Coupon([
            'name' => 'Ten', 'code' => 'TEN', 'discount_type' => 'percentage',
            'discount_amount' => 10, 'min_order_amount' => 500, 'is_active' => true,
        ]);
        $this->assertTrue($percentage->isValid(600.0));
        $this->assertFalse($percentage->isValid(400.0));
        $this->assertSame(60.0, $percentage->calculateDiscount(600.0));

        $flat = new Coupon([
            'name' => 'Flat', 'code' => 'FLAT', 'discount_type' => 'flat',
            'discount_amount' => 50, 'min_order_amount' => 0, 'is_active' => true,
            'usage_limit' => 1, 'used_count' => 1,
        ]);
        $this->assertFalse($flat->isValid(600.0), 'Used-up coupon stays invalid.');
        $this->assertSame(50.0, $flat->calculateDiscount(600.0));
    }

    // ---- F. Regression: no welcome offer configured ----------------------

    public function test_without_a_welcome_offer_everything_behaves_like_before(): void
    {
        // (a) fresh member + tier row → classic first-order result
        $fresh = $this->member();
        $fresh->stubTierOffer = $this->tierOffer();

        $firstOrder = $fresh->resolveMemberDiscount(100.0, false, 100.0);
        $this->assertSame(30, $firstOrder['rate']);
        $this->assertSame('30% first-order membership discount applied (one time only).', $firstOrder['message']);
        $this->assertFalse($firstOrder['welcome_discount_applicable']);

        // (b) member who used the first-order discount → the same "no
        //     discount" fallback message as before, new flag stays false
        $used = $this->member(['first_order_discount_used' => true]);
        $used->stubTierOffer = $this->tierOffer();

        $fallback = $used->resolveMemberDiscount(100.0, false, 100.0);
        $this->assertFalse($fallback['eligible']);
        $this->assertSame(0, $fallback['rate']);
        $this->assertFalse($fallback['welcome_discount_applicable']);
        $this->assertStringContainsString('Your Membership/Student first-order discount has already been used', $fallback['message']);

        // (c) golden → unchanged
        $golden = $this->member(['type' => 'golden', 'expires_at' => now()->addYears(3)]);
        $goldenResult = $golden->resolveMemberDiscount(100.0, false, 100.0);
        $this->assertSame(10, $goldenResult['rate']);
        $this->assertSame('golden', $goldenResult['member_type']);
    }
}

/**
 * Member stub: replaces only the DB-backed lookups so the whole discount
 * resolution chain runs in memory. The production query semantics that the
 * stubs stand in for (active + valid offer rows) are mirrored, not skipped.
 */
class WelcomeTestMember extends Member
{
    public ?Offer $stubWelcomeOffer = null;
    public ?Offer $stubTierOffer = null;
    public bool $stubTierOfferConfigured = false;
    public bool $stubGoldenOfferConfigured = false;
    public bool $stubHasCompletedOrders = false;
    public float $stubLiveTotalPurchase = 0.0;
    public bool $upgradedToGolden = false;

    public function welcomeTierOffer(): ?Offer
    {
        // Mirrors Offer::active()->valid() from the production query.
        if (! $this->stubWelcomeOffer || ! $this->stubWelcomeOffer->is_active || ! $this->stubWelcomeOffer->isValid()) {
            return null;
        }

        return $this->stubWelcomeOffer;
    }

    public function memberTierOffer(): ?Offer
    {
        return $this->stubTierOffer;
    }

    public function tierOfferIsConfigured(): bool
    {
        return $this->stubTierOfferConfigured || $this->stubTierOffer !== null;
    }

    public function goldenTierOffer(): ?Offer
    {
        return null;
    }

    public function goldenOfferIsConfigured(): bool
    {
        // No golden row was ever configured in these in-memory scenarios, so the
        // built-in 10% / ৳2,000 legacy behaviour stays pinned (no DB lookup).
        return $this->stubGoldenOfferConfigured;
    }

    public function hasCompletedOrders(): bool
    {
        return $this->stubHasCompletedOrders;
    }

    public function syncFirstOrderDiscountFlag(): void
    {
        // No DB round-trip in tests.
    }

    public function liveTotalPurchase(): float
    {
        return $this->stubLiveTotalPurchase;
    }

    public function upgradeToGolden(): bool
    {
        $this->type = 'golden';
        $this->upgradedToGolden = true;

        return true;
    }
}
