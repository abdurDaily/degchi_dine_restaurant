<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('transaction_id')->nullable()->after('status');
            $table->string('payment_status')->default('unpaid')->after('transaction_id'); // unpaid, paid, failed, cancelled
            $table->timestamp('payment_date')->nullable()->after('payment_status');
            $table->text('payment_details')->nullable()->after('payment_date'); // JSON from SSLCommerz
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = ['transaction_id', 'payment_status', 'payment_date', 'payment_details'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
