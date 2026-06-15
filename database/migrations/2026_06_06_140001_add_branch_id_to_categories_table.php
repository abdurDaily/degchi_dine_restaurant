<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration is now handled by 2026_06_06_150001_update_branch_id_in_categories
        // The branch_id column already exists, so we skip it here
        // New projects: create categories with branch_id nullable from the start
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('categories', 'branch_id')) {
            try {
                Schema::table('categories', function (Blueprint $table) {
                    $table->dropForeign(['branch_id']);
                    $table->dropColumn('branch_id');
                });
            } catch (\Exception $e) {
                // If dropping the foreign fails (constraint missing), attempt to drop the column only
                try {
                    Schema::table('categories', function (Blueprint $table) {
                        $table->dropColumn('branch_id');
                    });
                } catch (\Exception $e) {
                    // ignore: best-effort rollback
                }
            }
        }
    }
};
