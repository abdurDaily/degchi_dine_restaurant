<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add offer_type to offers table
        Schema::table('offers', function (Blueprint $table) {
            $table->enum('offer_type', ['all_items', 'specific_items'])->default('all_items')->after('applicable_to');
            $table->dateTime('valid_from')->nullable()->after('popup_expires_at');
            $table->dateTime('valid_until')->nullable()->after('valid_from');
        });

        // Create pivot table for many-to-many relationship between offers and menu variations
        Schema::create('menu_variation_offer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_variation_id')->constrained('menu_variations')->onDelete('cascade');
            $table->foreignId('offer_id')->constrained('offers')->onDelete('cascade');
            $table->timestamps();

            // Unique constraint to prevent duplicate relationships
            $table->unique(['menu_variation_id', 'offer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_variation_offer');

        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn(['offer_type', 'valid_from', 'valid_until']);
        });
    }
};
