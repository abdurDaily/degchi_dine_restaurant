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
        // If branch_id already exists with wrong constraint, fix it
        if (Schema::hasColumn('categories', 'branch_id')) {
            // Drop the old foreign key constraint if it exists
            try {
                Schema::table('categories', function (Blueprint $table) {
                    // Get the constraint name and drop it
                    $table->dropForeign(['branch_id']);
                });
            } catch (\Exception $e) {
                // Constraint might not exist, that's ok
            }
            
            // Make the column nullable if it isn't already
            try {
                DB::statement('ALTER TABLE categories MODIFY COLUMN branch_id BIGINT UNSIGNED NULL');
            } catch (\Exception $e) {
                // Column might already be nullable
            }
            
            // Re-add the foreign key with correct constraint
            try {
                Schema::table('categories', function (Blueprint $table) {
                    $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist
            }
        }
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
                });
            } catch (\Exception $e) {
                // Foreign key might not exist
            }
        }
    }
};
