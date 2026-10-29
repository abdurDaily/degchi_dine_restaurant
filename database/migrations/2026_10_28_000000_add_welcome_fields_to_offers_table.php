<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Welcome (new-member) offer support.
 *
 * A welcome offer is an offers row with applicable_to = 'welcome'. Each member's
 * eligibility window is computed from their own registration date
 * (members.created_at + welcome_duration_months), so no extra per-member state
 * is needed. NULL means "not a welcome offer" (the model falls back to 6 months
 * only when the row is of type welcome and the column is empty).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->unsignedTinyInteger('welcome_duration_months')
                ->nullable()
                ->after('is_first_order');
        });

        // Default welcome offer row — the 4th built-in row alongside
        // "Membership First Order", "Student Membership First Order" and
        // "Golden Upgrade Threshold". Every newly registered member gets this
        // row's discount on every order until their own N-month anniversary.
        // Guarded so an environment that already configured a welcome offer
        // (e.g. manually through the admin panel) never gets a duplicate.
        if (! DB::table('offers')->where('applicable_to', 'welcome')->exists()) {
            DB::table('offers')->insert([
                'name' => 'New Member Welcome',
                'description' => '10% discount on every order for the first 6 months after registration.',
                'discount_percent' => 10,
                'applicable_to' => 'welcome',
                'min_total' => null,
                'is_first_order' => false,
                'is_active' => true,
                'welcome_duration_months' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Remove exactly what up() seeded (a renamed/edited row is left alone).
        DB::table('offers')
            ->where('applicable_to', 'welcome')
            ->where('name', 'New Member Welcome')
            ->delete();

        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn('welcome_duration_months');
        });
    }
};
