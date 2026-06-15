<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'payment_method')) {
            DB::statement("ALTER TABLE `orders` MODIFY `payment_method` ENUM('cod','bkash','sslcommerz','other') NOT NULL DEFAULT 'cod'");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'payment_method')) {
            DB::statement("ALTER TABLE `orders` MODIFY `payment_method` ENUM('cod','bkash','other') NOT NULL DEFAULT 'cod'");
        }
    }
};
