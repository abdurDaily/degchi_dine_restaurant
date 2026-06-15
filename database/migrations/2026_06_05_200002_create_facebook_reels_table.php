<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facebook_reels', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('facebook_url');
            $table->string('thumbnail')->nullable(); // image: webp, png, jpg
            $table->boolean('status')->default(1);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facebook_reels');
    }
};
