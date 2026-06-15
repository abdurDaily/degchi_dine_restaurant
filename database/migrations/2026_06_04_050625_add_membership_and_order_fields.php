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
        if (Schema::hasTable('members')) {
            Schema::table('members', function (Blueprint $table) {
                if (!Schema::hasColumn('members', 'student_card_path')) {
                    $table->string('student_card_path')->nullable()->after('is_student');
                }
                if (!Schema::hasColumn('members', 'expires_at')) {
                    $table->date('expires_at')->nullable()->after('first_order_discount_used');
                }
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'member_credited')) {
                    $table->boolean('member_credited')->default(false)->after('student_card_used');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('members')) {
            Schema::table('members', function (Blueprint $table) {
                if (Schema::hasColumn('members', 'student_card_path')) {
                    $table->dropColumn('student_card_path');
                }
                if (Schema::hasColumn('members', 'expires_at')) {
                    $table->dropColumn('expires_at');
                }
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'member_credited')) {
                    $table->dropColumn('member_credited');
                }
            });
        }
    }
};
