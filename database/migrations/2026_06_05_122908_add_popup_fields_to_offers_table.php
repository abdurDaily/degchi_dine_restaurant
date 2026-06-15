<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->boolean('show_as_popup')->default(false)->after('is_active');
            $table->string('popup_image')->nullable()->after('show_as_popup');
            $table->string('popup_badge')->nullable()->after('popup_image');   // e.g. "Eid Special"
            $table->date('popup_expires_at')->nullable()->after('popup_badge'); // hide after this date
        });
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn(['show_as_popup', 'popup_image', 'popup_badge', 'popup_expires_at']);
        });
    }
};
