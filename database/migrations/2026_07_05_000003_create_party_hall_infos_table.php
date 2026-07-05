<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('party_hall_infos', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Party Hall');
            $table->longText('description')->nullable();
            $table->string('video_url')->nullable();
            $table->string('gallery_images')->nullable(); // JSON array of image paths
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // Seed a default record
        \DB::table('party_hall_infos')->insert([
            'title'       => 'Degchi Dine Party Hall',
            'description' => 'Host your special occasions at our beautifully decorated party hall. Perfect for birthdays, corporate events, and family gatherings.',
            'status'      => true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('party_hall_infos');
    }
};
