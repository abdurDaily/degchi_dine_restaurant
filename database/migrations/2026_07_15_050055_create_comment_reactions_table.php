<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comment_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->enum('reaction', ['like', 'dislike']);
            $table->timestamps();

            $table->unique(['comment_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_reactions');
    }
};
