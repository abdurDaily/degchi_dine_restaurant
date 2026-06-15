<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only run for MySQL/MariaDB where enum types can be altered with ALTER TABLE
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            // Add 'sslcommerz' to the existing enum values
            DB::statement("ALTER TABLE `orders` MODIFY `payment_method` ENUM('cod','bkash','other','sslcommerz') NOT NULL DEFAULT 'cod'");
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            // Revert to original enum (remove sslcommerz)
            DB::statement("ALTER TABLE `orders` MODIFY `payment_method` ENUM('cod','bkash','other') NOT NULL DEFAULT 'cod'");
        }
    }
};
