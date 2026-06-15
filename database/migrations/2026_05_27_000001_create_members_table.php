<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('unique_card_number')->unique();
            $table->string('last4', 4);
            $table->boolean('is_student')->default(false);
            $table->enum('type', ['membership', 'golden'])->default('membership');
            $table->enum('status', ['pending', 'active', 'suspended'])->default('active');
            $table->decimal('total_purchase', 10, 2)->default(0);
            $table->boolean('first_order_discount_used')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
