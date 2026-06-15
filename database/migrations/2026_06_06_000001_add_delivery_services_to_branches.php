<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->text('foodpanda_url')->nullable()->after('phone');
            $table->text('pathao_url')->nullable()->after('foodpanda_url');
            $table->text('foodi_url')->nullable()->after('pathao_url');
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['foodpanda_url', 'pathao_url', 'foodi_url']);
        });
    }
};
