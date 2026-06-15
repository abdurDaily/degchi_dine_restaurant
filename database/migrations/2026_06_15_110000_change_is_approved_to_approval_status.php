<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Drop the old boolean column
            $table->dropColumn('is_approved');
        });

        Schema::table('members', function (Blueprint $table) {
            // Add new approval_status enum column
            // pending: Waiting for admin review
            // approved: Approved by admin, can use first-order discount
            // rejected: Rejected by admin, cannot use first-order discount
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])
                ->default('approved')
                ->after('is_student');
        });

        // Set approval_status for existing members
        DB::table('members')
            ->where('is_student', true)
            ->update(['approval_status' => 'pending']);

        DB::table('members')
            ->where('is_student', false)
            ->update(['approval_status' => 'approved']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('approval_status');
        });

        Schema::table('members', function (Blueprint $table) {
            $table->boolean('is_approved')->default(true)->after('is_student');
        });
    }
};
