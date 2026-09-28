<?php

namespace Tests\Unit;

use App\Models\Member;
use App\Models\Offer;
use Tests\TestCase;

/**
 * min_total is a whole-cart threshold on the order subtotal.
 *
 * These assertions deliberately touch no database: the models are instantiated
 * in memory so the suite can never wipe a developer's MySQL data.
 */
class OfferMinTotalTest extends TestCase
{
    private function offer(array $overrides = []): Offer
    {
        return new Offer(array_merge([
            'name' => 'Test Offer',
            'discount_percent' => 20,
            'offer_type' => 'all_items',
            'applicable_to' => 'all',
            'is_active' => true,
            'is_first_order' => false,
        ], $overrides));
    }

    private function member(array $overrides = []): Member
    {
        return new Member(array_merge([
            'name' => 'Test Member',
            'type' => 'membership',
            'status' => 'active',
            'is_student' => false,
            'approval_status' => 'approved',
            'first_order_discount_used' => false,
        ], $overrides));
    }

    // ---- Case 1: no minimum --------------------------------------------

    public function test_no_minimum_is_always_satisfied(): void
    {
        $offer = $this->offer(['min_total' => null]);

        $this->assertNull($offer->minimumTotal());
        $this->assertTrue($offer->meetsMinimumTotal(300.0));
        $this->assertTrue($offer->meetsMinimumTotal(0.0));
        $this->assertTrue($offer->meetsMinimumTotal(null));
        $this->assertTrue($offer->isEligibleForMember(null, 300.0));
    }

    public function test_zero_minimum_behaves_like_no_minimum(): void
    {
        $offer = $this->offer(['min_total' => 0]);

        $this->assertTrue($offer->meetsMinimumTotal(0.0));
        $this->assertTrue($offer->isEligibleForMember(null, 0.0));
    }

    public function test_zero_minimum_reports_no_minimum_so_no_locked_badge_is_rendered(): void
    {
        // "0" and "blank" are the same thing in the admin form, so every reader
        // (product card, cart, checkout, admin listing) must see one state:
        // null => no minimum => never a "Min ৳0 order" pill.
        $this->assertNull($this->offer(['min_total' => 0])->minimumTotal());
        $this->assertNull($this->offer(['min_total' => '0.00'])->minimumTotal());
        $this->assertNull($this->offer()->minimumTotal());

        // A real minimum still round-trips unchanged.
        $this->assertSame(2000.0, $this->offer(['min_total' => '2000.00'])->minimumTotal());
    }

    // ---- Case 2: below minimum -----------------------------------------

    public function test_below_minimum_is_rejected(): void
    {
        $offer = $this->offer(['min_total' => 2000]);

        $this->assertFalse($offer->meetsMinimumTotal(1500.0));
        $this->assertFalse($offer->isEligibleForMember(null, 1500.0));
        $this->assertFalse($offer->isVisibleOnMenuFor(null, 1500.0));
    }

    // ---- Case 3: exact minimum -----------------------------------------

    public function test_exact_minimum_is_accepted(): void
    {
        $offer = $this->offer(['min_total' => 2000]);

        $this->assertTrue($offer->meetsMinimumTotal(2000.0));
        $this->assertTrue($offer->isEligibleForMember(null, 2000.0));
        $this->assertTrue($offer->isVisibleOnMenuFor(null, 2000.0));
    }

    // ---- Case 4: above minimum -----------------------------------------

    public function test_above_minimum_is_accepted(): void
    {
        $offer = $this->offer(['min_total' => 2000]);

        $this->assertTrue($offer->meetsMinimumTotal(2500.0));
        $this->assertTrue($offer->isEligibleForMember(null, 2500.0));
    }

    // ---- No cart context: never invents a subtotal ----------------------

    public function test_null_subtotal_does_not_block_informational_display(): void
    {
        $offer = $this->offer(['min_total' => 2000]);

        $this->assertTrue($offer->meetsMinimumTotal(null));
        $this->assertTrue($offer->isEligibleForMember(null, null));
        $this->assertTrue($offer->isVisibleOnMenuFor(null, null));
        $this->assertSame(2000.0, $offer->minimumTotal());
    }

    // ---- Case 5: existing eligibility failures still win ---------------

    public function test_expired_offer_is_rejected_regardless_of_subtotal(): void
    {
        $offer = $this->offer([
            'min_total' => 2000,
            'valid_from' => now()->subDays(10),
            'valid_until' => now()->subDay(),
        ]);

        $this->assertFalse($offer->isValid());
        $this->assertFalse($offer->isEligibleForMember(null, 99999.0));
    }

    public function test_not_yet_started_offer_is_rejected_regardless_of_subtotal(): void
    {
        $offer = $this->offer([
            'min_total' => 2000,
            'valid_from' => now()->addDay(),
        ]);

        $this->assertFalse($offer->isEligibleForMember(null, 99999.0));
    }

    public function test_inactive_offer_is_not_visible_even_when_minimum_is_met(): void
    {
        $offer = $this->offer(['min_total' => 2000, 'is_active' => false]);

        $this->assertFalse($offer->isVisibleOnMenuFor(null, 5000.0));
    }

    public function test_student_offer_requires_approved_student_regardless_of_subtotal(): void
    {
        $offer = $this->offer([
            'min_total' => 2000,
            'applicable_to' => 'student',
            'is_first_order' => true,
        ]);

        $nonStudent = $this->member(['is_student' => false]);

        // Subtotal clears the minimum, but the member type rule still rejects.
        $this->assertFalse($offer->isEligibleForMember($nonStudent, 99999.0));
    }

    public function test_golden_offer_requires_golden_member_regardless_of_subtotal(): void
    {
        $offer = $this->offer([
            'min_total' => 2000,
            'applicable_to' => 'golden',
        ]);

        $standard = $this->member(['type' => 'membership']);

        $this->assertFalse($offer->isEligibleForMember($standard, 99999.0));
    }

    // ---- Case 6: best selection cannot pick a disqualified offer --------

    public function test_best_offer_skips_offers_below_their_minimum(): void
    {
        $biggest = $this->offer(['name' => '30% but needs 5000', 'discount_percent' => 30, 'min_total' => 5000]);
        $reachable = $this->offer(['name' => '20% needs 2000', 'discount_percent' => 20, 'min_total' => 2000]);
        $unrestricted = $this->offer(['name' => '10% no minimum', 'discount_percent' => 10, 'min_total' => null]);

        $best = Offer::bestEligibleForMember([$biggest, $reachable, $unrestricted], null, 2500.0);

        $this->assertNotNull($best);
        $this->assertSame('20% needs 2000', $best->name);

        $best = Offer::bestEligibleForMember([$biggest, $reachable, $unrestricted], null, 6000.0);
        $this->assertSame('30% but needs 5000', $best->name);

        $best = Offer::bestEligibleForMember([$biggest, $reachable, $unrestricted], null, 300.0);
        $this->assertSame('10% no minimum', $best->name);
    }

    // ---- Float tolerance -------------------------------------------------

    public function test_subtotal_floating_point_noise_does_not_block(): void
    {
        $offer = $this->offer(['min_total' => 2000]);

        $this->assertTrue($offer->meetsMinimumTotal(1999.9999));
        $this->assertFalse($offer->meetsMinimumTotal(1999.99));
    }

    public function test_decimal_string_from_database_is_handled(): void
    {
        // The model casts min_total to decimal:2, so it arrives as a string.
        $offer = $this->offer();
        $offer->setRawAttributes(['min_total' => '2000.00'], true);

        $this->assertTrue($offer->meetsMinimumTotal(2000.0));
        $this->assertFalse($offer->meetsMinimumTotal(1999.99));
        $this->assertSame(2000.0, $offer->minimumTotal());
    }

    public function test_cart_line_uses_list_price_until_order_minimum_is_met(): void
    {
        $offer = $this->offer(['min_total' => 2000, 'discount_percent' => 20]);
        $listPrice = 500.0;

        $below = $offer->meetsMinimumTotal(500.0);
        $this->assertFalse($below);
        $this->assertSame(500.0, $below ? round($listPrice * 0.8, 2) : $listPrice);

        $met = $offer->meetsMinimumTotal(2000.0);
        $this->assertTrue($met);
        $this->assertSame(400.0, $met ? round($listPrice * 0.8, 2) : $listPrice);
    }

    /**
     * The cart / checkout badge contract, expressed in PHP so the gate that the
     * JS mirrors is pinned down: locked below the minimum, unlocked at or above,
     * and never a badge when there is no minimum.
     */
    public function test_minimum_gate_that_the_cart_badge_renders_from(): void
    {
        $offer = $this->offer(['min_total' => 2000, 'discount_percent' => 20]);

        $lockedMinimum = function (float $subtotal) use ($offer) {
            $minimum = $offer->minimumTotal();

            return $minimum !== null && $subtotal + 0.005 < $minimum ? $minimum : null;
        };

        $this->assertSame(2000.0, $lockedMinimum(500.0), 'Badge shown: "Min 2000 order".');
        $this->assertSame(2000.0, $lockedMinimum(1999.99));
        $this->assertNull($lockedMinimum(2000.0), 'Exact minimum unlocks the offer.');
        $this->assertNull($lockedMinimum(2500.0));

        $noMinimum = $this->offer(['min_total' => null, 'discount_percent' => 20]);
        $this->assertNull($noMinimum->minimumTotal());
    }
}
