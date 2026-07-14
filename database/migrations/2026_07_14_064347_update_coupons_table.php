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
        Schema::table('coupons', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->text('description')->nullable()->after('name');
            $table->renameColumn('discount_value', 'discount_amount');
            $table->enum('discount_type', ['flat', 'percentage'])->default('flat')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn(['name', 'description']);
            $table->renameColumn('discount_amount', 'discount_value');
            $table->enum('discount_type', ['flat', 'percent'])->default('flat')->change();
        });
    }
};
