<?php

namespace App\Services;

use App\Models\Member;
use App\Models\Offer;
use Illuminate\Support\Collection;

/**
 * Single source of truth for the four built-in offers.
 *
 * The rows are still ordinary offers rows edited from the admin offers screen;
 * this service only gives controllers/views one stable shape to read them by:
 *
 *  - membership_first   (Membership First Order)
 *  - student_first      (Student Membership First Order)
 *  - welcome            (New Member Welcome)
 *  - golden             (Golden Upgrade Threshold / Golden Card benefit)
 *
 * An admin-disabled (is_active = false) or out-of-date row reports `active =
 * false`. Views must use that flag before printing any percentage or ৳ amount,
 * so switching a built-in offer off removes its related numbers everywhere.
 * A row that has never been created falls back to the legacy constants; a row
 * that exists but is switched off deliberately does NOT get a fallback.
 */
class SystemOfferService
{
    public const CACHE_KEY = 'system_offer_settings';

    public const CACHE_SECONDS = 60;

    /**
     * Forget the cached snapshot. Called by OfferController after every write.
     */
    public static function forgetCache(): void
    {
        cache()->forget(self::CACHE_KEY);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function settings(): array
    {
        return cache()->remember(self::CACHE_KEY, self::CACHE_SECONDS, function () {
            return self::build();
        });
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private static function build(): array
    {
        $rows = Offer::query()
            ->whereIn('applicable_to', ['membership', 'student', 'welcome', 'golden'])
            ->get();

        $membership = self::activeRow($rows, 'membership', true);
        $student = self::activeRow($rows, 'student', true);
        $welcome = self::activeRow($rows, 'welcome', null);
        $golden = self::activeRow($rows, 'golden', null);

        $membershipPercent = self::percentOf($membership);
        $studentPercent = self::percentOf($student);
        $welcomePercent = self::percentOf($welcome);

        $goldenPercent = self::percentOf($golden);
        // Seeded databases store 0% on the golden row because the benefit used
        // to be a built-in rate. Keep that legacy 0% row working, while an
        // explicit higher value now becomes the admin-configured benefit.
        if ($golden && $goldenPercent === null) {
            $goldenPercent = (int) (Member::GOLDEN_DISCOUNT_RATE * 100);
        }

        return [
            'membership_first' => [
                'key' => 'membership_first',
                'label' => 'Membership First Order',
                'configured' => self::configured($rows, 'membership', true),
                'active' => $membership !== null,
                'is_active' => $membership !== null,
                'offer_id' => self::anyRow($rows, 'membership', true)?->id,
                'percent' => $membershipPercent ?? ($membership === null && ! self::configured($rows, 'membership', true)
                    ? (int) (Member::FIRST_ORDER_RATE_STANDARD * 100) : null),
                'first_order' => true,
            ],
            'student_first' => [
                'key' => 'student_first',
                'label' => 'Student Membership First Order',
                'configured' => self::configured($rows, 'student', true),
                'active' => $student !== null,
                'is_active' => $student !== null,
                'offer_id' => self::anyRow($rows, 'student', true)?->id,
                'percent' => $studentPercent ?? ($student === null && ! self::configured($rows, 'student', true)
                    ? (int) (Member::FIRST_ORDER_RATE_STUDENT * 100) : null),
                'first_order' => true,
            ],
            'welcome' => [
                'key' => 'welcome',
                'label' => 'New Member Welcome',
                'configured' => self::configured($rows, 'welcome', null),
                'active' => $welcome !== null,
                'is_active' => $welcome !== null,
                'offer_id' => self::anyRow($rows, 'welcome', null)?->id,
                'percent' => $welcomePercent,
                'months' => $welcome
                    ? max(1, (int) ($welcome->welcome_duration_months ?: Member::WELCOME_DEFAULT_DURATION_MONTHS))
                    : null,
                'first_order' => false,
            ],
            'golden' => [
                'key' => 'golden',
                'label' => 'Golden Upgrade Threshold',
                'configured' => self::configured($rows, 'golden', null),
                'active' => $golden !== null,
                'is_active' => $golden !== null,
                'offer_id' => self::anyRow($rows, 'golden', null)?->id,
                'percent' => $golden ? ($goldenPercent ?? 0) : 0,
                'threshold' => $golden
                    ? ($golden->minimumTotal() ?? (float) Member::GOLDEN_UPGRADE_THRESHOLD)
                    : null,
                'first_order' => false,
            ],
        ];
    }

    /**
     * Highest-percentage active, in-date row for a built-in offer type.
     */
    private static function activeRow(Collection $rows, string $applicableTo, ?bool $firstOrder): ?Offer
    {
        return $rows
            ->where('applicable_to', $applicableTo)
            ->when($firstOrder !== null, fn (Collection $collection) => $collection->where('is_first_order', $firstOrder))
            ->filter(fn (Offer $offer) => $offer->is_active && $offer->isValid())
            ->sortByDesc('discount_percent')
            ->first();
    }

    /**
     * Any row for a built-in type, including switched-off ones. Used for edit
     * links and for distinguishing "never created" from "switched off", so a
     * disabled row never silently reverts to its legacy default percentage.
     */
    private static function anyRow(Collection $rows, string $applicableTo, ?bool $firstOrder): ?Offer
    {
        return $rows
            ->where('applicable_to', $applicableTo)
            ->when($firstOrder !== null, fn (Collection $collection) => $collection->where('is_first_order', $firstOrder))
            ->sortByDesc('discount_percent')
            ->first();
    }

    private static function configured(Collection $rows, string $applicableTo, ?bool $firstOrder): bool
    {
        return self::anyRow($rows, $applicableTo, $firstOrder) !== null;
    }

    /**
     * A positive configured percentage, or null when the row has no usable rate.
     */
    private static function percentOf(?Offer $offer): ?int
    {
        if (! $offer || (int) $offer->discount_percent <= 0) {
            return null;
        }

        return (int) $offer->discount_percent;
    }
}
