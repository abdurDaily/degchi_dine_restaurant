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
            // Add is_approved column after is_student
            // For regular members: default true (auto-approved)
            // For student members: default false (needs admin approval)
            $table->boolean('is_approved')->default(true)->after('is_student');
        });

        // Set is_approved to false for existing student members
        DB::table('members')
            ->where('is_student', true)
            ->update(['is_approved' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('is_approved');
        });
    }
};
