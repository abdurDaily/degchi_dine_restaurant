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
        Schema::table('branches', function (Blueprint $table) {
            $table->text('foodpanda_logo')->nullable()->after('foodpanda_url');
            $table->text('pathao_logo')->nullable()->after('pathao_url');
            $table->text('foodi_logo')->nullable()->after('foodi_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['foodpanda_logo', 'pathao_logo', 'foodi_logo']);
        });
    }
};
